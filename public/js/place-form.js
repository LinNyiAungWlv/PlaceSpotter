// Mobile features on the add/edit place form: Geolocation + Camera

// --- Geolocation: fill in latitude and longitude ---
const latInput = document.getElementById('latitude');
const lngInput = document.getElementById('longitude');
const locationStatus = document.getElementById('location-status');

function useMyLocation() {
    if (!navigator.geolocation) {
        locationStatus.textContent = 'Your browser does not support location.';
        return;
    }

    locationStatus.textContent = 'Finding your location...';

    navigator.geolocation.getCurrentPosition(
        position => {
            latInput.value = position.coords.latitude.toFixed(7);
            lngInput.value = position.coords.longitude.toFixed(7);
            locationStatus.textContent = 'Location added ✔';
        },
        () => {
            locationStatus.textContent = 'Could not get your location. Please type it in.';
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}

document.getElementById('use-location').addEventListener('click', useMyLocation);

// on a new place, get the location automatically
if (latInput.value === '' && lngInput.value === '') {
    useMyLocation();
}

// --- Camera: take a photo and show a preview ---
const cameraInput = document.getElementById('camera');
const photoInput = document.getElementById('photo');
const preview = document.getElementById('photo-preview');

document.getElementById('take-photo').addEventListener('click', () => cameraInput.click());

function showPreview(file) {
    preview.src = URL.createObjectURL(file);
    preview.classList.remove('d-none');
}

// copy the camera photo into the real photo field so it gets uploaded
cameraInput.addEventListener('change', () => {
    if (cameraInput.files.length > 0) {
        photoInput.files = cameraInput.files;
        showPreview(cameraInput.files[0]);
    }
});

photoInput.addEventListener('change', () => {
    if (photoInput.files.length > 0) {
        showPreview(photoInput.files[0]);
    }
});
