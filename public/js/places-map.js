// Map of places (Leaflet + OpenStreetMap) with live search, category filter and "Near me"
const map = L.map('map').setView([16.8, 96.15], 13); // Yangon

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors',
}).addTo(map);

const markers = L.layerGroup().addTo(map);
const searchUrl = document.getElementById('map').dataset.url;
const searchInput = document.getElementById('search');
const categoryButtons = document.querySelectorAll('#category-filter button');
const nearButton = document.getElementById('near-me');
const nearStatus = document.getElementById('near-status');

let selectedCategory = '';
let myLocation = null; // set when "Near me" is on
let typingTimer;

// stop user-typed text from being run as HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showPins(pins) {
    markers.clearLayers();

    pins.forEach(place => {
        L.circleMarker([place.lat, place.lng], {
            radius: 9,
            color: '#fff',
            weight: 2,
            fillColor: place.color,
            fillOpacity: 0.9,
        })
            .bindPopup(`<strong>${escapeHtml(place.name)}</strong><br>${escapeHtml(place.category)}<br><a href="${place.url}">View place</a>`)
            .addTo(markers);
    });

    const points = pins.map(p => [p.lat, p.lng]);

    // show the user's own position as a blue dot
    if (myLocation) {
        L.circleMarker([myLocation.lat, myLocation.lng], { radius: 7, color: '#0d6efd', fillOpacity: 1 })
            .bindPopup('You are here')
            .addTo(markers);
        points.push([myLocation.lat, myLocation.lng]);
    }

    if (points.length > 0) {
        map.fitBounds(points, { padding: [30, 30], maxZoom: 16 });
    }
}

// --- localStorage: remember the last search and category on this device ---
function saveFilters() {
    try {
        localStorage.setItem('placeFilters', JSON.stringify({ search: searchInput.value, category: selectedCategory }));
    } catch (e) {
        // storage blocked (e.g. private mode), the page still works
    }
}

function selectButton(category) {
    categoryButtons.forEach(b => {
        const active = b.dataset.category === category;
        b.classList.toggle('btn-dark', active);
        b.classList.toggle('btn-outline-dark', !active);
    });
}

function restoreFilters() {
    try {
        const saved = JSON.parse(localStorage.getItem('placeFilters'));
        if (saved) {
            searchInput.value = saved.search || '';
            selectedCategory = saved.category || '';
            selectButton(selectedCategory);
        }
    } catch (e) {
        // ignore broken or blocked storage
    }
}

// ask Laravel for places matching the filters, then update the page
function loadPlaces() {
    const params = new URLSearchParams({ search: searchInput.value, category: selectedCategory });
    if (myLocation) {
        params.append('lat', myLocation.lat);
        params.append('lng', myLocation.lng);
    }

    fetch(`${searchUrl}?${params}`)
        .then(response => response.json())
        .then(data => {
            showPins(data.pins);
            document.getElementById('place-list').innerHTML = data.html;
            document.getElementById('result-count').textContent = data.count;
        });

    saveFilters();
}

// wait until the user stops typing for 300ms
searchInput.addEventListener('input', () => {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(loadPlaces, 300);
});

categoryButtons.forEach(button => {
    button.addEventListener('click', () => {
        selectedCategory = button.dataset.category;
        selectButton(selectedCategory);
        loadPlaces();
    });
});

// --- Geolocation: "Near me" turns on/off ---
nearButton.addEventListener('click', () => {
    if (myLocation) {
        myLocation = null;
        nearButton.classList.replace('btn-primary', 'btn-outline-primary');
        nearStatus.textContent = '';
        loadPlaces();
        return;
    }

    if (!navigator.geolocation) {
        nearStatus.textContent = 'Your browser does not support location.';
        return;
    }

    nearStatus.textContent = 'Finding your location...';

    navigator.geolocation.getCurrentPosition(
        position => {
            myLocation = { lat: position.coords.latitude, lng: position.coords.longitude };
            nearButton.classList.replace('btn-outline-primary', 'btn-primary');
            nearStatus.textContent = 'Showing places within 10 km of you, closest first.';
            loadPlaces();
        },
        () => {
            nearStatus.textContent = 'Could not get your location. Please allow location access.';
        }
    );
});

restoreFilters();
loadPlaces();
