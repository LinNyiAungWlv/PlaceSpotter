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

// ---- "Use my location" button (device GPS) ----

const locationButton = document.getElementById('use-location');
const locationStatus = document.getElementById('location-status');

function showStatus(message, isError) {
    locationStatus.textContent = message;
    locationStatus.className = 'small mb-2 ' + (isError ? 'text-danger' : 'text-success');
}

// shows the spinner and disables the button while the phone looks for its position
function setLoading(loading) {
    locationButton.disabled = loading;
    locationButton.querySelector('.spinner-border').classList.toggle('d-none', !loading);
    locationButton.querySelector('.bi').classList.toggle('d-none', loading);
}

function onLocationFound(position) {
    const latlng = L.latLng(position.coords.latitude, position.coords.longitude);

    movePin(latlng);
    fillInputs(latlng);
    formMap.setView(latlng, 17);

    setLoading(false);
    showStatus('Location set (accurate to about ' + Math.round(position.coords.accuracy) + ' m). You can drag the pin to adjust it.', false);
}

function onLocationError(error) {
    const messages = {
        [error.PERMISSION_DENIED]: 'Location access was blocked. Allow it in your browser settings, or pick the spot on the map.',
        [error.POSITION_UNAVAILABLE]: "Couldn't find your location. Try again outside, or pick the spot on the map.",
        [error.TIMEOUT]: 'Finding your location took too long. Try again, or pick the spot on the map.',
    };

    setLoading(false);
    showStatus(messages[error.code] || "Couldn't get your location. Pick the spot on the map instead.", true);
}

if (!('geolocation' in navigator)) {
    // old browser: no button, the map still works
    locationButton.classList.add('d-none');
} else {
    locationButton.addEventListener('click', function () {
        // browsers only allow location on https (or localhost)
        if (!window.isSecureContext) {
            showStatus('Location needs a secure (https) connection. Pick the spot on the map instead.', true);
            return;
        }

        setLoading(true);
        showStatus('Finding your location…', false);
        locationStatus.classList.replace('text-success', 'text-muted');

        navigator.geolocation.getCurrentPosition(onLocationFound, onLocationError, {
            enableHighAccuracy: true, // ask for GPS, not just a rough guess
            timeout: 10000, // give up after 10 seconds
            maximumAge: 0, // don't reuse an old position
        });
    });
}
