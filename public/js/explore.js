// Explore page: shows every place as a pin on a Leaflet map.
// The places come from the JSON endpoints (/search and /search/nearby) using fetch, so the page itself stays light.

const mapElement = document.getElementById('map');
const errorElement = document.getElementById('map-error');
const placeList = document.getElementById('place-list');
const listStatus = document.getElementById('list-status');
const listStatusText = document.getElementById('list-status-text');
const nearMeButton = document.getElementById('near-me');

const NEARBY_KM = 5;

// start on Yangon, then zoom to the pins once they are loaded
// (zoomSnap 0.5 lets fitBounds use half zoom levels for a closer fit)
const map = L.map(mapElement, { zoomSnap: 0.5 }).setView([16.8, 96.15], 12);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
}).addTo(map);

const markers = L.featureGroup().addTo(map);
const youAreHere = L.featureGroup().addTo(map); // blue dot + accuracy circle after "Near me"

function showError(message) {
    errorElement.textContent = message;
    errorElement.classList.toggle('d-none', !message);
}

// creates an element with a class and (optional) text, text is always set with textContent
function el(tag, className, text) {
    const element = document.createElement(tag);
    element.className = className;

    if (text !== undefined) {
        element.textContent = text;
    }

    return element;
}

// category icon followed by its name, e.g. [icon] Café
function categoryLabel(category, className) {
    const label = el('span', className);
    label.append(el('i', 'bi ' + category.icon), ' ' + category.name);

    return label;
}

// 0.78 -> "780 m away", 1.234 -> "1.2 km away"
function formatDistance(km) {
    if (km < 0.1) {
        return 'Under 100 m away';
    }

    if (km < 1) {
        return Math.round(km * 100) * 10 + ' m away';
    }

    return km.toFixed(1) + ' km away';
}

// Builds the popup with createElement + textContent instead of innerHTML,
// so a place name like "<script>" is shown as text and never runs.
function buildPopup(place) {
    const box = document.createElement('div');

    if (place.photo_url) {
        const img = el('img', 'popup-photo');
        img.src = place.photo_url;
        img.alt = 'Photo of ' + place.name;
        box.appendChild(img);
    }

    const link = el('a', 'fw-semibold', place.name);
    link.href = place.url;
    box.appendChild(link);

    box.appendChild(categoryLabel(place.category, 'd-block small text-muted'));

    if (place.distance_km !== undefined) {
        box.appendChild(el('div', 'small', formatDistance(place.distance_km)));
    }

    return box;
}

// Same card as the one in home.blade.php, built with textContent (no innerHTML).
function buildCard(place) {
    const column = el('div', 'col-sm-6 col-lg-4');
    const card = el('div', 'card h-100 shadow-sm');

    if (place.photo_url) {
        const img = el('img', 'card-img-top place-photo');
        img.src = place.photo_url;
        img.alt = 'Photo of ' + place.name;
        card.appendChild(img);
    } else {
        const empty = el('div', 'card-img-top place-photo place-photo-empty');
        empty.appendChild(el('i', 'bi ' + place.category.icon));
        card.appendChild(empty);
    }

    const body = el('div', 'card-body');
    body.appendChild(categoryLabel(place.category, 'badge text-bg-light border mb-2'));

    const title = el('h2', 'h5 card-title');
    // stretched-link makes the whole card clickable
    const link = el('a', 'stretched-link text-reset text-decoration-none', place.name);
    link.href = place.url;
    title.appendChild(link);
    body.appendChild(title);

    body.appendChild(el('p', 'card-text text-muted small', place.excerpt));
    card.appendChild(body);

    const footer = el('div', 'card-footer bg-white text-muted small', 'by ' + place.author);

    // after "Near me": the distance on the right
    if (place.distance_km !== undefined) {
        footer.classList.add('d-flex', 'justify-content-between', 'gap-2');
        const distance = el('span', 'text-nowrap');
        distance.append(el('i', 'bi bi-geo-alt'), ' ' + formatDistance(place.distance_km));
        footer.appendChild(distance);
    }

    card.appendChild(footer);
    column.appendChild(card);

    return column;
}

function showCards(places) {
    placeList.replaceChildren(...places.map(buildCard));
}

// puts a pin for each place, then zooms so all pins (and the user's position, if given) are visible
function showPlaces(places, here) {
    markers.clearLayers();

    places.forEach(function (place) {
        L.marker([place.lat, place.lng])
            .bindPopup(buildPopup(place))
            .addTo(markers);
    });

    const bounds = L.latLngBounds([]);

    if (places.length > 0) {
        bounds.extend(markers.getBounds());
    }

    if (here) {
        bounds.extend(here);
    }

    // (extra space at the top because the pin icon sticks up above its point)
    if (bounds.isValid()) {
        map.fitBounds(bounds, {
            paddingTopLeft: [30, 50],
            paddingBottomRight: [30, 20],
            maxZoom: 16,
        });
    }
}

// fetches JSON from one of our endpoints, throws if the server says no
async function getJson(url) {
    const response = await fetch(url, {
        headers: { Accept: 'application/json' },
    });

    if (!response.ok) {
        throw new Error('Server returned ' + response.status);
    }

    return (await response.json()).data;
}

// all places (the page starts like this). rebuildCards is used by "Show all places".
async function loadPlaces(rebuildCards) {
    try {
        const places = await getJson(mapElement.dataset.searchUrl);
        showPlaces(places);

        if (rebuildCards) {
            showCards(places);
        }
    } catch (error) {
        console.error(error);
        showError("Couldn't load places on the map. Please refresh the page to try again.");
    }
}

loadPlaces(false);

// ---- "Near me" button (device GPS) ----

// shows the spinner and disables the button while waiting for the GPS and the server
function setNearMeLoading(loading) {
    nearMeButton.disabled = loading;
    nearMeButton.querySelector('.spinner-border').classList.toggle('d-none', !loading);
    nearMeButton.querySelector('.bi').classList.toggle('d-none', loading);
}

// blue dot where the user is, with a light circle showing how accurate the GPS is
function showYouAreHere(here, accuracy) {
    youAreHere.clearLayers();

    L.circle(here, {
        radius: accuracy,
        color: '#0d6efd',
        weight: 1,
        fillOpacity: 0.1,
        interactive: false,
    }).addTo(youAreHere);

    L.circleMarker(here, {
        radius: 8,
        color: '#fff',
        weight: 2,
        fillColor: '#0d6efd',
        fillOpacity: 1,
    }).bindTooltip('You are here').addTo(youAreHere);
}

async function onNearMeFound(position) {
    const lat = position.coords.latitude;
    const lng = position.coords.longitude;
    const here = L.latLng(lat, lng);

    // URLSearchParams builds "?lat=..&lng=..&km=5" with the values properly encoded
    const url = mapElement.dataset.nearbyUrl + '?' + new URLSearchParams({ lat: lat, lng: lng, km: NEARBY_KM });

    try {
        const places = await getJson(url);

        showYouAreHere(here, position.coords.accuracy);
        showPlaces(places, here); // the server already sorted them, nearest first
        showCards(places);

        if (places.length === 0) {
            listStatusText.textContent = 'No places within ' + NEARBY_KM + ' km of you.';
        } else {
            listStatusText.textContent = places.length + (places.length === 1 ? ' place' : ' places')
                + ' within ' + NEARBY_KM + ' km of you, nearest first.';
        }

        listStatus.classList.remove('d-none');
    } catch (error) {
        console.error(error);
        showError("Couldn't load places near you. Please try again.");
    }

    setNearMeLoading(false);
}

function onNearMeError(error) {
    const messages = {
        [error.PERMISSION_DENIED]: 'Location access was blocked. Allow it in your browser settings to see places near you.',
        [error.POSITION_UNAVAILABLE]: "Couldn't find your location. Please try again.",
        [error.TIMEOUT]: 'Finding your location took too long. Please try again.',
    };

    setNearMeLoading(false);
    showError(messages[error.code] || "Couldn't get your location.");
}

if (!('geolocation' in navigator)) {
    // old browser: no button, the rest of the page still works
    nearMeButton.classList.add('d-none');
} else {
    nearMeButton.addEventListener('click', function () {
        showError('');

        // browsers only allow location on https (or localhost)
        if (!window.isSecureContext) {
            showError('"Near me" needs a secure (https) connection.');
            return;
        }

        setNearMeLoading(true);

        navigator.geolocation.getCurrentPosition(onNearMeFound, onNearMeError, {
            enableHighAccuracy: true,
            timeout: 10000, // give up after 10 seconds
            maximumAge: 60000, // a position from the last minute is fine for finding places
        });
    });
}

// back to every place, without the distances
document.getElementById('show-all').addEventListener('click', function () {
    showError('');
    listStatus.classList.add('d-none');
    youAreHere.clearLayers();
    loadPlaces(true);
});
