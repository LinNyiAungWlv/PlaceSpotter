// Place detail page: small map with one pin showing where the place is.

const mapBox = document.getElementById('place-map');
const position = [parseFloat(mapBox.dataset.lat), parseFloat(mapBox.dataset.lng)];

const placeMap = L.map(mapBox, {
    // scrolling the page over the map should scroll the page, not zoom the map
    scrollWheelZoom: false,
    // on phones a swipe over this small map should scroll the page too
    dragging: !L.Browser.mobile,
}).setView(position, 16);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
}).addTo(placeMap);

// the pin can't be dragged, this map is only for looking
L.marker(position).addTo(placeMap);
