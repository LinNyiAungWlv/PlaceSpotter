// Loads the current weather for this place from Laravel (which calls Open-Meteo)
const weatherBox = document.getElementById('weather');

fetch(weatherBox.dataset.url)
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            weatherBox.textContent = data.error;
            return;
        }

        weatherBox.className = 'alert ' + (data.good_time ? 'alert-success' : 'alert-warning');
        weatherBox.innerHTML = `
            <div class="fs-5">${data.icon} ${data.temperature}°C · ${data.description}</div>
            <div class="small text-muted mb-1">Wind ${data.wind} km/h · live from Open-Meteo</div>
            <strong>Good time to visit?</strong> ${data.tip}
        `;
    })
    .catch(() => {
        weatherBox.textContent = 'Weather is not available right now.';
    });
