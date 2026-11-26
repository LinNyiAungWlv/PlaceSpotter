// Map of places (Leaflet + OpenStreetMap) with live search and category filter
const map = L.map('map').setView([16.8, 96.15], 13); // Yangon

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors',
}).addTo(map);

const markers = L.layerGroup().addTo(map);
const searchUrl = document.getElementById('map').dataset.url;
const searchInput = document.getElementById('search');
const categoryButtons = document.querySelectorAll('#category-filter button');

let selectedCategory = '';
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

    if (pins.length > 0) {
        map.fitBounds(pins.map(p => [p.lat, p.lng]), { padding: [30, 30], maxZoom: 16 });
    }
}

// ask Laravel for places matching the search + category, then update the page
function loadPlaces() {
    const params = new URLSearchParams({ search: searchInput.value, category: selectedCategory });

    fetch(`${searchUrl}?${params}`)
        .then(response => response.json())
        .then(data => {
            showPins(data.pins);
            document.getElementById('place-list').innerHTML = data.html;
            document.getElementById('result-count').textContent = data.count;
        });
}

// wait until the user stops typing for 300ms
searchInput.addEventListener('input', () => {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(loadPlaces, 300);
});

categoryButtons.forEach(button => {
    button.addEventListener('click', () => {
        selectedCategory = button.dataset.category;

        // highlight the selected button
        categoryButtons.forEach(b => b.classList.replace('btn-dark', 'btn-outline-dark'));
        button.classList.replace('btn-outline-dark', 'btn-dark');

        loadPlaces();
    });
});

loadPlaces();
