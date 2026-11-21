// Add/edit place form: pick the location on a small map.
// The map and the latitude/longitude inputs are kept in sync both ways,
// so the form still works by typing if the map can't load.

const latInput = document.getElementById('latitude');
const lngInput = document.getElementById('longitude');

const YANGON = [16.8, 96.15];

// reads the inputs, returns [lat, lng] or null if they are empty or out of range
function inputPosition() {
    const lat = parseFloat(latInput.value);
    const lng = parseFloat(lngInput.value);

    if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
        return null;
    }

    return [lat, lng];
}

// editing a place, or coming back after a validation error, the inputs already have a spot:
// start the map zoomed in there, otherwise show the whole of Yangon
const start = inputPosition();
const formMap = L.map('location-map').setView(start || YANGON, start ? 16 : 12);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
}).addTo(formMap);

let pin = null;

// writes a position into the inputs (7 decimals, same as the database columns)
function fillInputs(latlng) {
    latInput.value = latlng.lat.toFixed(7);
    lngInput.value = latlng.lng.toFixed(7);
}

// puts the pin at a position, creating it the first time
function movePin(position) {
    if (pin) {
        pin.setLatLng(position);
        return;
    }

    pin = L.marker(position, { draggable: true }).addTo(formMap);

    // dragging the pin updates the inputs
    pin.on('dragend', function () {
        fillInputs(pin.getLatLng());
    });
}

// clicking the map moves the pin there
formMap.on('click', function (event) {
    movePin(event.latlng);
    fillInputs(event.latlng);
});

// typing coordinates moves the pin
function onInputChange() {
    const position = inputPosition();

    if (position) {
        movePin(position);
        formMap.panTo(position);
    }
}

latInput.addEventListener('change', onInputChange);
lngInput.addEventListener('change', onInputChange);

// show the pin at the starting spot (if there is one)
if (start) {
    movePin(start);
}
