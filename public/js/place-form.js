// Add/edit place form: pick the location on a small map, and preview the photo.
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

// ---- Photo: preview, resize and "Take photo" button ----

const photoInput = document.getElementById('photo');
const cameraInput = document.getElementById('camera-input');
const takePhotoButton = document.getElementById('take-photo');
const photoPreview = document.getElementById('photo-preview');
const previewImage = photoPreview.querySelector('img');
const photoInfo = document.getElementById('photo-info');
const photoError = document.getElementById('photo-error');
const currentPhoto = document.getElementById('current-photo'); // only there when editing a place with a photo

let previewUrl = null;

// e.g. "3.4 MB" or "640 KB"
function formatSize(bytes) {
    if (bytes >= 1024 * 1024) {
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    return Math.round(bytes / 1024) + ' KB';
}

function showPhotoError(message) {
    photoError.textContent = message;
    photoError.classList.toggle('d-none', !message);
}

// ---- shrinking big photos before upload ----

const MAX_SIDE = 1280; // longest side in pixels, big enough for the detail page
const SMALL_FILE = 1024 * 1024; // photos already small (1 MB and up to 1280px) are left as they are

const submitButton = photoInput.form.querySelector('button[type="submit"]');
const submitLabel = Array.from(submitButton.childNodes); // icon + text, put back after

let pickCount = 0; // which pick is the latest, so an old slow resize can't overwrite a newer photo

// stops the form being sent while the photo is still being made smaller
function setPreparing(preparing) {
    submitButton.disabled = preparing;

    if (preparing) {
        submitButton.textContent = 'Preparing photo…';
    } else {
        submitButton.replaceChildren(...submitLabel);
    }
}

// loads a file into an <img>, gives null if the browser can't read it
function loadImage(file) {
    return new Promise(function (resolve) {
        const url = URL.createObjectURL(file);
        const image = new Image();

        image.onload = function () {
            URL.revokeObjectURL(url);
            resolve(image);
        };
        image.onerror = function () {
            URL.revokeObjectURL(url);
            resolve(null);
        };

        image.src = url;
    });
}

// gives { file, width, height } with a smaller JPEG copy of the photo,
// or null when the original should be kept (already small, a GIF, or the browser can't read it)
async function shrinkPhoto(file) {
    if (file.type === 'image/gif') {
        return null; // resizing would lose the animation
    }

    const image = await loadImage(file);

    if (!image) {
        return null;
    }

    // naturalWidth/Height already follow the phone's rotation info, so portrait photos stay upright
    const longest = Math.max(image.naturalWidth, image.naturalHeight);

    if (longest <= MAX_SIDE && file.size <= SMALL_FILE) {
        return null;
    }

    const scale = Math.min(1, MAX_SIDE / longest);
    const canvas = document.createElement('canvas');
    canvas.width = Math.round(image.naturalWidth * scale);
    canvas.height = Math.round(image.naturalHeight * scale);

    const context = canvas.getContext('2d');
    // JPEG has no transparency, so a white background instead of black
    context.fillStyle = '#fff';
    context.fillRect(0, 0, canvas.width, canvas.height);
    context.drawImage(image, 0, 0, canvas.width, canvas.height);

    const blob = await new Promise(function (resolve) {
        canvas.toBlob(resolve, 'image/jpeg', 0.85);
    });

    // keep the original if it failed, or if it wasn't resized and the copy isn't any smaller
    if (!blob || (scale === 1 && blob.size >= file.size)) {
        return null;
    }

    // e.g. IMG_2041.HEIC -> IMG_2041.jpg
    const name = file.name.replace(/\.[^.]+$/, '') + '.jpg';

    return {
        file: new File([blob], name, { type: 'image/jpeg', lastModified: Date.now() }),
        width: canvas.width,
        height: canvas.height,
    };
}

// forgets the chosen photo, when editing the current photo shows again
function clearPhoto() {
    pickCount++; // a resize still running for the old photo is ignored
    setPreparing(false);
    photoInput.value = '';

    if (previewUrl) {
        URL.revokeObjectURL(previewUrl); // free the memory used by the old preview
        previewUrl = null;
    }

    previewImage.removeAttribute('src');
    photoPreview.classList.add('d-none');

    if (currentPhoto) {
        currentPhoto.classList.remove('d-none');
    }
}

photoInput.addEventListener('change', async function () {
    const file = photoInput.files[0];

    // a new file was picked, so hide any old error
    showPhotoError('');
    photoInput.classList.remove('is-invalid');

    if (!file) {
        clearPhoto();
        return;
    }

    // the server checks this too, this just tells the user straight away
    if (!file.type.startsWith('image/') || file.type === 'image/svg+xml') {
        clearPhoto();
        showPhotoError('"' + file.name + '" is not a photo. Choose a JPG or PNG image.');
        return;
    }

    const pick = ++pickCount;
    setPreparing(true);

    const smaller = await shrinkPhoto(file);

    if (pick !== pickCount) {
        return; // another photo was picked (or Remove clicked) while this one was resizing
    }

    setPreparing(false);

    let chosen = file;
    let info = file.name + ' · ' + formatSize(file.size);

    if (smaller) {
        // swap the big file in the input for the smaller one, so that is what gets uploaded
        // (setting files from code does not fire "change" again)
        const transfer = new DataTransfer();
        transfer.items.add(smaller.file);
        photoInput.files = transfer.files;

        chosen = smaller.file;
        info = smaller.file.name + ' · ' + formatSize(file.size) + ' → ' + formatSize(smaller.file.size)
            + ' (' + smaller.width + '×' + smaller.height + ')';
    }

    if (previewUrl) {
        URL.revokeObjectURL(previewUrl);
    }

    // a temporary local URL, so the photo can be shown without uploading it
    previewUrl = URL.createObjectURL(chosen);
    previewImage.src = previewUrl;
    photoInfo.textContent = info;
    photoPreview.classList.remove('d-none');

    if (currentPhoto) {
        currentPhoto.classList.add('d-none');
    }
});

document.getElementById('remove-photo').addEventListener('click', clearPhoto);

// phones only (touch screen): "Take photo" opens the camera straight away.
// The normal input has no capture attribute, so people can still pick a photo from the gallery.
if (window.matchMedia('(pointer: coarse)').matches) {
    takePhotoButton.classList.remove('d-none');

    takePhotoButton.addEventListener('click', function () {
        cameraInput.click();
    });

    // move the photo into the real input (the one that is sent with the form)
    cameraInput.addEventListener('change', function () {
        if (cameraInput.files.length === 0) {
            return;
        }

        const transfer = new DataTransfer();
        transfer.items.add(cameraInput.files[0]);
        photoInput.files = transfer.files;
        cameraInput.value = '';

        photoInput.dispatchEvent(new Event('change'));
    });
}
