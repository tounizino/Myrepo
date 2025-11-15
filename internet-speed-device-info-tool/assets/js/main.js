(function($) {
    'use strict';

    let testRunning = false;

    function initializeMap(container, lat, lng, config) {
        if (!container || typeof L === 'undefined') {
            return;
        }

        try {
            const mapId = container.id || 'isdit-map-' + Date.now();
            container.id = mapId;
            container.classList.remove('isdit-map--empty');
            container.innerHTML = '';

            const mapInstance = L.map(mapId, {
                center: [lat, lng],
                zoom: 13,
                zoomControl: true,
                scrollWheelZoom: false,
                attributionControl: true,
            });

            const tileUrl = (config && config.mapTileUrl) || 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
            const attribution = (config && config.mapAttribution) || '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors';

            L.tileLayer(tileUrl, {
                attribution: attribution,
                maxZoom: 19,
            }).addTo(mapInstance);

            L.marker([lat, lng]).addTo(mapInstance);
        } catch (error) {
            console.error('Map initialization failed', error);
            showMapUnavailable(container);
        }
    }

    function formatNumber(value, decimals = 2) {
        if (value === undefined || value === null || isNaN(value)) {
            return '--';
        }
        return parseFloat(value).toFixed(decimals);
    }

    function updateText(element, text) {
        if (!element) {
            return;
        }
        element.textContent = text;
    }

    function updateProgressBar(bar, percent) {
        if (!bar) {
            return;
        }
        const clamped = Math.max(0, Math.min(100, percent));
        bar.style.width = clamped + '%';
    }

    function setStatus(element, message, state = 'info') {
        if (!element) {
            return;
        }
        element.textContent = message || '';
        element.classList.remove('isdit-status--loading', 'isdit-status--success', 'isdit-status--error');
        if (state === 'loading') {
            element.classList.add('isdit-status--loading');
        } else if (state === 'success') {
            element.classList.add('isdit-status--success');
        } else if (state === 'error') {
            element.classList.add('isdit-status--error');
        }
    }

    function updateDeviceInfo(container, info) {
        if (!container || !info) {
            return;
        }

        const safe = (value) => value || 'Unknown';

        updateText(container.querySelector('[data-device-type]'), safe(info.type));
        updateText(container.querySelector('[data-os]'), safe(info.os));
        updateText(container.querySelector('[data-browser]'), safe(info.browser));
        updateText(container.querySelector('[data-screen]'), safe(info.screen));
        updateText(container.querySelector('[data-viewport]'), safe(info.viewport));
        updateText(container.querySelector('[data-cpu]'), safe(info.cpu));
        updateText(container.querySelector('[data-memory]'), safe(info.memory));
        updateText(container.querySelector('[data-connection]'), safe(info.connection));
        updateText(container.querySelector('[data-useragent]'), safe(info.userAgent));
    }

    function updateIPInfo(container, ipData) {
        if (!container || !ipData) {
            return;
        }

        const safe = (value) => value || 'Unknown';
        const location = ipData.location || {};

        updateText(container.querySelector('[data-ip-address]'), safe(ipData.ip));
        updateText(container.querySelector('[data-country]'), safe(location.country));
        updateText(container.querySelector('[data-region]'), safe(location.region));
        updateText(container.querySelector('[data-city]'), safe(location.city));
        updateText(container.querySelector('[data-isp]'), safe(location.isp));
        updateText(container.querySelector('[data-organization]'), safe(location.organization));
        updateText(container.querySelector('[data-timezone]'), safe(location.timezone));
    }

    function markIpUnavailable(container) {
        if (!container) {
            return;
        }
        const elements = container.querySelectorAll('[data-ip-address], [data-country], [data-region], [data-city], [data-isp], [data-organization], [data-timezone]');
        elements.forEach((el) => updateText(el, 'Unavailable'));
    }

    function showMapUnavailable(container) {
        if (!container) {
            return;
        }
        container.classList.add('isdit-map--empty');
        container.innerHTML = '<span>Location unavailable</span>';
    }

    function resetReadings(wrapper) {
        if (!wrapper) {
            return;
        }
        updateText(wrapper.querySelector('[data-download]'), '--');
        updateText(wrapper.querySelector('[data-upload]'), '--');
        updateText(wrapper.querySelector('[data-latency]'), '--');
        const jitterEl = wrapper.querySelector('[data-jitter]');
        if (jitterEl) {
            updateText(jitterEl, '--');
        }
    }

    async function runSpeedTest(wrapper, speedTest) {
        if (testRunning) {
            return;
        }

        const button = wrapper.querySelector('[data-speed-test-start]');
        const status = wrapper.querySelector('.isdit-status');
        const progressBar = wrapper.querySelector('[data-progress-bar]');
        const downloadEl = wrapper.querySelector('[data-download]');
        const uploadEl = wrapper.querySelector('[data-upload]');
        const latencyEl = wrapper.querySelector('[data-latency]');
        const jitterEl = wrapper.querySelector('[data-jitter]');

        if (!speedTest || typeof speedTest.isReady !== 'function' || !speedTest.isReady()) {
            setStatus(status, 'Speed test is currently unavailable. Please try again later.', 'error');
            return;
        }

        testRunning = true;
        if (button) {
            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
        }

        resetReadings(wrapper);
        updateProgressBar(progressBar, 0);
        setStatus(status, 'Preparing speed diagnostics...', 'loading');

        try {
            const latencyResult = await speedTest.testLatency(5);
            updateText(latencyEl, formatNumber(latencyResult.latency, 0));
            if (jitterEl) {
                updateText(jitterEl, formatNumber(latencyResult.jitter, 0));
            }
            updateProgressBar(progressBar, 10);

            setStatus(status, 'Measuring download speed...', 'loading');
            const downloadSpeed = await speedTest.testDownload((progress) => {
                updateProgressBar(progressBar, 10 + (progress * 0.45));
            });
            updateText(downloadEl, formatNumber(downloadSpeed));
            updateProgressBar(progressBar, 55);

            setStatus(status, 'Measuring upload speed...', 'loading');
            const uploadSpeed = await speedTest.testUpload((progress) => {
                updateProgressBar(progressBar, 55 + (progress * 0.45));
            });
            updateText(uploadEl, formatNumber(uploadSpeed));

            updateProgressBar(progressBar, 100);
            setStatus(status, 'Speed test complete. Here are your results.', 'success');
        } catch (error) {
            console.error('Speed test error', error);
            updateProgressBar(progressBar, 0);
            setStatus(status, 'Unable to complete the speed test. Please try again.', 'error');
        } finally {
            testRunning = false;
            if (button) {
                button.disabled = false;
                button.removeAttribute('aria-busy');
            }
        }
    }

    function loadDeviceInfo(wrapper, speedTest) {
        const deviceCard = wrapper.querySelector('.device-info');
        if (!deviceCard || !speedTest) {
            return;
        }
        const deviceInfo = speedTest.getDeviceInfo();
        updateDeviceInfo(deviceCard, deviceInfo);
    }

    function loadNetworkInfo(wrapper, speedTest, config) {
        const ipCard = wrapper.querySelector('.ip-info');
        const mapContainer = wrapper.querySelector('[data-map]');

        if (ipCard) {
            const placeholders = ipCard.querySelectorAll('[data-ip-address], [data-country], [data-region], [data-city], [data-isp], [data-organization], [data-timezone]');
            placeholders.forEach((el) => updateText(el, 'Loading...'));
        }

        speedTest.getIPInfo().then((ipData) => {
            if (!ipData) {
                markIpUnavailable(ipCard);
                showMapUnavailable(mapContainer);
                return;
            }

            if (ipCard) {
                updateIPInfo(ipCard, ipData);
            }

            const location = ipData.location || {};
            if (mapContainer) {
                if (location.latitude && location.longitude) {
                    initializeMap(mapContainer, location.latitude, location.longitude, config);
                } else {
                    showMapUnavailable(mapContainer);
                }
            }
        }).catch((error) => {
            console.error('Failed to retrieve IP information', error);
            markIpUnavailable(ipCard);
            showMapUnavailable(mapContainer);
        });
    }

    function initializeWidget(wrapper) {
        const status = wrapper.querySelector('.isdit-status');

        if (typeof SpeedTest === 'undefined') {
            setStatus(status, 'Speed test scripts failed to load. Please refresh the page.', 'error');
            return;
        }

        if (typeof isdtAjax === 'undefined') {
            setStatus(status, 'Configuration data is missing. Please contact the site administrator.', 'error');
            return;
        }

        const config = isdtAjax.config || {};
        const speedTest = new SpeedTest({
            ajaxUrl: isdtAjax.ajaxUrl || '',
            nonce: isdtAjax.nonce || '',
            config: config,
        });

        if (speedTest.isReady()) {
            setStatus(status, 'Click "Start Speed Test" to begin.', 'info');
        } else {
            setStatus(status, 'Speed test is currently unavailable.', 'error');
        }

        const button = wrapper.querySelector('[data-speed-test-start]');
        if (button) {
            const handleRun = function(event) {
                if (event) {
                    event.preventDefault();
                }
                runSpeedTest(wrapper, speedTest);
            };
            button.addEventListener('click', handleRun);
            button.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    handleRun(event);
                }
            });
        }

        const content = wrapper.querySelector('.isdit-content');
        const showInfo = content && content.dataset.showInfo === '1';

        if (showInfo) {
            loadDeviceInfo(wrapper, speedTest);
            loadNetworkInfo(wrapper, speedTest, config);
        }
    }

    function initialize() {
        const wrappers = document.querySelectorAll('.isdit-wrapper');
        if (!wrappers.length) {
            return;
        }
        wrappers.forEach((wrapper) => {
            initializeWidget(wrapper);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }

})(jQuery);
