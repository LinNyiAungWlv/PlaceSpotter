// Explore page: shows every place as a pin on a Leaflet map.
// The places come from the JSON endpoint (/search) using fetch, so the page itself stays light.

const mapElement = document.getElementById('map');
const errorElement = document.getElementById('map-error');

// start on Yangon, then zoom to the pins once they are loaded
// (zoomSnap 0.5 lets fitBounds use half zoom levels for a closer fit)
const map = L.map(mapElement, { zoomSnap: 0.5 }).setView([16.8, 96.15], 12);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
}).addTo(map);

const markers = L.featureGroup().addTo(map);

// Builds the popup with createElement + textContent instead of innerHTML,
// so a place name like "<script>" is shown as text and never runs.
function buildPopup(place) {
    const box = document.createElement('div');

    if (place.photo_url) {
        const img = document.createElement('img');
        img.src = place.photo_url;
        img.alt = 'Photo of ' + place.name;
        img.className = 'popup-photo';
        box.appendChild(img);
    }

    const link = document.createElement('a');
    link.href = place.url;
    link.className = 'fw-semibold';
    link.textContent = place.name;
    box.appendChild(link);

    const category = document.createElement('div');
    category.className = 'small text-muted';
    const icon = document.createElement('i');
    icon.className = 'bi ' + place.category.icon;
    category.append(icon, ' ' + place.category.name);
    box.appendChild(category);

    return box;
}

function showPlaces(places) {
    markers.clearLayers();

    places.forEach(function (place) {
        L.marker([place.lat, place.lng])
            .bindPopup(buildPopup(place))
            .addTo(markers);
    });

    // zoom the map so all pins are visible
    // (extra space at the top because the pin icon sticks up above its point)
    if (places.length > 0) {
        map.fitBounds(markers.getBounds(), {
            paddingTopLeft: [30, 50],
            paddingBottomRight: [30, 20],
            maxZoom: 16,
        });
    }
}

async function loadPlaces() {
    try {
        const response = await fetch(mapElement.dataset.searchUrl, {
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
            throw new Error('Server returned ' + response.status);
        }

        const json = await response.json();
        showPlaces(json.data);
    } catch (error) {
        console.error(error);
        errorElement.textContent = "Couldn't load places on the map. Please refresh the page to try again.";
        errorElement.classList.remove('d-none');
    }
}

loadPlaces();
