// Map of all places using Leaflet + OpenStreetMap
const map = L.map('map').setView([16.8, 96.15], 13); // Yangon

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors',
}).addTo(map);

const markers = L.layerGroup().addTo(map);

// stop user-typed text from being run as HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showPins(places) {
    markers.clearLayers();

    places.forEach(place => {
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

    if (places.length > 0) {
        map.fitBounds(places.map(p => [p.lat, p.lng]), { padding: [30, 30] });
    }
}

// load the pins from Laravel as JSON
fetch(document.getElementById('map').dataset.url)
    .then(response => response.json())
    .then(showPins);
