(function (window, document) {
    'use strict';

    var PLUGIN_NAME = 'CloudGamepadTesterWP';
    var SUPPORTS_GAMEPAD = !!(navigator.getGamepads || navigator.webkitGetGamepads);
    var POLL_INTERVAL = 1000 / 60;
    var BUTTON_THRESHOLD = 0.1;
    var LATENCY_SAMPLES = 12;

    var BUTTON_DEFINITIONS = [
        { index: 0, label: 'A', detail: 'Face Button' },
        { index: 1, label: 'B', detail: 'Face Button' },
        { index: 2, label: 'X', detail: 'Face Button' },
        { index: 3, label: 'Y', detail: 'Face Button' },
        { index: 4, label: 'LB', detail: 'Left Bumper' },
        { index: 5, label: 'RB', detail: 'Right Bumper' },
        { index: 6, label: 'LT', detail: 'Left Trigger' },
        { index: 7, label: 'RT', detail: 'Right Trigger' },
        { index: 8, label: 'View', detail: 'Back / Select' },
        { index: 9, label: 'Menu', detail: 'Start / Options' },
        { index: 10, label: 'LS', detail: 'Left Stick Press' },
        { index: 11, label: 'RS', detail: 'Right Stick Press' },
        { index: 12, label: 'D-Pad Up', detail: 'Directional Pad' },
        { index: 13, label: 'D-Pad Down', detail: 'Directional Pad' },
        { index: 14, label: 'D-Pad Left', detail: 'Directional Pad' },
        { index: 15, label: 'D-Pad Right', detail: 'Directional Pad' },
        { index: 16, label: 'Guide', detail: 'Xbox Button' }
    ];

    var Instances = new Map();
    var rafId = null;

    function toArrayLike(list) {
        var result = [];
        if (!list) {
            return result;
        }
        for (var i = 0; i < list.length; i += 1) {
            if (list[i]) {
                result.push(list[i]);
            }
        }
        return result;
    }

    function getGamepads() {
        if (navigator.getGamepads) {
            return toArrayLike(navigator.getGamepads());
        }
        if (navigator.webkitGetGamepads) {
            return toArrayLike(navigator.webkitGetGamepads());
        }
        return [];
    }

    function createInstance(root) {
        if (!root || Instances.has(root)) {
            return;
        }

        var instanceId = root.getAttribute('data-instance') || ('cgtwp-' + Math.random().toString(16).slice(2));
        root.setAttribute('data-cgtwp-mounted', '1');

        var refs = buildTemplate(root, instanceId);

        var instance = {
            id: instanceId,
            root: root,
            refs: refs,
            selectedIndex: null,
            latencySamples: [],
            lastPollTime: 0,
            deviceSignature: '',
            hasShownUnsupported: !SUPPORTS_GAMEPAD,
            highlightState: new Map()
        };

        Instances.set(root, instance);

        if (!SUPPORTS_GAMEPAD) {
            showUnsupported(instance);
            return;
        }

        refs.deviceSelect.addEventListener('change', function (event) {
            var value = event.target.value;
            instance.selectedIndex = value === '' ? null : parseInt(value, 10);
        });

        refs.refreshBtn.addEventListener('click', function () {
            manualScan();
        });

        refs.rumbleBtn.addEventListener('click', function () {
            triggerRumble(instance);
        });

        manualScan();
    }

    function showUnsupported(instance) {
        instance.refs.statusText.textContent = 'Gamepad API is not supported in this browser.';
        instance.refs.statusDot.classList.remove('cgtwp-online');
        instance.refs.deviceSelect.disabled = true;
        instance.refs.refreshBtn.disabled = true;
        instance.refs.rumbleBtn.disabled = true;
        instance.refs.emptyState.textContent = 'Your current browser does not expose the Gamepad API. Please switch to a supported browser (Chrome, Edge, or Firefox) to access the cloud gamepad tester.';
        instance.refs.emptyState.classList.remove('cgtwp-hidden');
    }

    function buildTemplate(root, instanceId) {
        var gradientId = instanceId + '-body-gradient';
        var faceGlowId = instanceId + '-face-glow';

        var buttonGridHtml = BUTTON_DEFINITIONS.map(function (button) {
            return '<div class="cgtwp-button-item" data-button-index="' + button.index + '">' +
                '<div class="cgtwp-button-name">' + button.label + '</div>' +
                '<div class="cgtwp-button-index">Index ' + button.index + ' · ' + button.detail + '</div>' +
                '</div>';
        }).join('');

        var template = '' +
            '<div class="cgtwp-shell">' +
            '  <div class="cgtwp-header">' +
            '    <div class="cgtwp-status">' +
            '      <span class="cgtwp-status-dot"></span>' +
            '      <span class="cgtwp-status-text">Connect your controller to begin testing</span>' +
            '    </div>' +
            '    <div class="cgtwp-header-actions">' +
            '      <select class="cgtwp-device-select" aria-label="Controller selector"></select>' +
            '      <button type="button" class="cgtwp-refresh-btn">Refresh Controllers</button>' +
            '    </div>' +
            '  </div>' +
            '  <div class="cgtwp-body">' +
            '    <div class="cgtwp-visual-column">' +
            '      <div class="cgtwp-controller-wrapper">' +
            '        <svg class="cgtwp-controller-svg" viewBox="0 0 600 360" xmlns="http://www.w3.org/2000/svg" role="presentation" aria-hidden="true">' +
            '          <defs>' +
            '            <linearGradient id="' + gradientId + '" x1="0%" y1="0%" x2="100%" y2="100%">' +
            '              <stop offset="0%" stop-color="#202b48" stop-opacity="0.95"></stop>' +
            '              <stop offset="60%" stop-color="#161d2f" stop-opacity="0.96"></stop>' +
            '              <stop offset="100%" stop-color="#111524" stop-opacity="0.98"></stop>' +
            '            </linearGradient>' +
            '            <radialGradient id="' + faceGlowId + '" cx="50%" cy="50%" r="50%">' +
            '              <stop offset="0%" stop-color="#7d8fff" stop-opacity="0.45"></stop>' +
            '              <stop offset="100%" stop-color="#7d8fff" stop-opacity="0"></stop>' +
            '            </radialGradient>' +
            '          </defs>' +
            '          <g class="cgtwp-controller">
              <path class="cgtwp-controller-body" style="fill: url(#' + gradientId + ') !important;" d="M86 74c-24 30-32 75-26 118 5 36 24 68 54 90l28 20c32 22 77 10 94-24l12-25c6-12 18-19 31-19s25 7 31 19l12 25c17 34 62 46 94 24l28-20c30-22 49-54 54-90 6-43-2-88-26-118-17-21-46-31-72-24l-46 12c-12 3-25 2-36-3l-25-11c-28-12-60-12-88 0l-25 11c-11 5-24 6-36 3l-46-12c-26-7-55 3-72 24z"></path>
              <path class="cgtwp-outline" d="M86 74c-24 30-32 75-26 118 5 36 24 68 54 90l28 20c32 22 77 10 94-24l12-25c6-12 18-19 31-19s25 7 31 19l12 25c17 34 62 46 94 24l28-20c30-22 49-54 54-90 6-43-2-88-26-118-17-21-46-31-72-24l-46 12c-12 3-25 2-36-3l-25-11c-28-12-60-12-88 0l-25 11c-11 5-24 6-36 3l-46-12c-26-7-55 3-72 24z"></path>
              <path class="cgtwp-button-sprite cgtwp-trigger-zone" data-button-index="6" d="M130 56c-10 3-18 10-22 20-4 11-4 24-4 36 0 5 0 11 1 17l48-14c5-1 8-6 8-11 0-17-1-33-5-44-3-8-14-10-26-7z"></path>
              <path class="cgtwp-button-sprite cgtwp-trigger-zone" data-button-index="7" d="M470 56c10 3 18 10 22 20 4 11 4 24 4 36 0 5 0 11-1 17l-48-14c-5-1-8-6-8-11 0-17 1-33 5-44 3-8 14-10 26-7z"></path>
              <path class="cgtwp-button-sprite cgtwp-bumper" data-button-index="4" d="M150 110l80-16c6-1 12 3 13 9l4 28-77 16c-6 1-12-3-13-9l-6-19c-2-6 2-12 8-14z"></path>
              <path class="cgtwp-button-sprite cgtwp-bumper" data-button-index="5" d="M450 110l-80-16c-6-1-12 3-13 9l-4 28 77 16c6 1 12-3 13-9l6-19c2-6-2-12-8-14z"></path>
              <g class="cgtwp-face-group" style="fill: url(#' + faceGlowId + ') !important; opacity: 0.35 !important;">
                <circle cx="430" cy="160" r="70"></circle>
              </g>
              <circle class="cgtwp-button-sprite cgtwp-face-button cgtwp-button-a" data-button-index="0" cx="430" cy="210" r="24"></circle>
              <circle class="cgtwp-button-sprite cgtwp-face-button cgtwp-button-b" data-button-index="1" cx="472" cy="170" r="24"></circle>
              <circle class="cgtwp-button-sprite cgtwp-face-button cgtwp-button-x" data-button-index="2" cx="388" cy="170" r="24"></circle>
              <circle class="cgtwp-button-sprite cgtwp-face-button cgtwp-button-y" data-button-index="3" cx="430" cy="130" r="24"></circle>
              <circle class="cgtwp-button-sprite cgtwp-stick-disc" data-button-index="10" cx="190" cy="150" r="42"></circle>
              <circle class="cgtwp-button-sprite cgtwp-stick-disc" data-button-index="11" cx="340" cy="210" r="42"></circle>
              <path class="cgtwp-button-sprite cgtwp-dpad" data-button-index="12" d="M200 220h-34c-5 0-8 4-8 8v22c0 4 3 8 8 8h34c4 0 8-4 8-8v-22c0-4-4-8-8-8z"></path>
              <path class="cgtwp-button-sprite cgtwp-dpad" data-button-index="13" d="M200 292h-34c-5 0-8 4-8 8v22c0 4 3 8 8 8h34c4 0 8-4 8-8v-22c0-4-4-8-8-8z"></path>
              <path class="cgtwp-button-sprite cgtwp-dpad" data-button-index="14" d="M140 252v34c0 4 4 8 8 8h22c5 0 8-4 8-8v-34c0-4-3-8-8-8h-22c-4 0-8 4-8 8z"></path>
              <path class="cgtwp-button-sprite cgtwp-dpad" data-button-index="15" d="M212 252v34c0 4-4 8-8 8h-22c-5 0-8-4-8-8v-34c0-4 3-8 8-8h22c4 0 8 4 8 8z"></path>
              <circle class="cgtwp-button-sprite" data-button-index="8" cx="260" cy="150" r="16"></circle>
              <circle class="cgtwp-button-sprite" data-button-index="9" cx="320" cy="150" r="16"></circle>
              <circle class="cgtwp-button-sprite" data-button-index="16" cx="290" cy="150" r="14"></circle>
            </g>' +
            '        </svg>' +
            '      </div>' +
            '      <div class="cgtwp-stick-panels">' +
            '        <div class="cgtwp-stick-monitor cgtwp-stick-left">' +
            '          <div class="cgtwp-stick-label">Left Stick</div>' +
            '          <div class="cgtwp-stick-surface">' +
            '            <div class="cgtwp-stick-crosshair"></div>' +
            '            <div class="cgtwp-stick-indicator"></div>' +
            '          </div>' +
            '          <div class="cgtwp-stick-coords">' +
            '            <span class="cgtwp-stick-x">X: 0.00</span>' +
            '            <span class="cgtwp-stick-y">Y: 0.00</span>' +
            '          </div>' +
            '        </div>' +
            '        <div class="cgtwp-stick-monitor cgtwp-stick-right">' +
            '          <div class="cgtwp-stick-label">Right Stick</div>' +
            '          <div class="cgtwp-stick-surface">' +
            '            <div class="cgtwp-stick-crosshair"></div>' +
            '            <div class="cgtwp-stick-indicator"></div>' +
            '          </div>' +
            '          <div class="cgtwp-stick-coords">' +
            '            <span class="cgtwp-stick-x">X: 0.00</span>' +
            '            <span class="cgtwp-stick-y">Y: 0.00</span>' +
            '          </div>' +
            '        </div>' +
            '      </div>' +
            '      <div class="cgtwp-trigger-panels">' +
            '        <div class="cgtwp-trigger cgtwp-trigger-left">' +
            '          <div class="cgtwp-trigger-label">Left Trigger</div>' +
            '          <div class="cgtwp-trigger-bar"><div class="cgtwp-trigger-fill"></div></div>' +
            '          <div class="cgtwp-trigger-value">0%</div>' +
            '        </div>' +
            '        <div class="cgtwp-trigger cgtwp-trigger-right">' +
            '          <div class="cgtwp-trigger-label">Right Trigger</div>' +
            '          <div class="cgtwp-trigger-bar"><div class="cgtwp-trigger-fill"></div></div>' +
            '          <div class="cgtwp-trigger-value">0%</div>' +
            '        </div>' +
            '      </div>' +
            '    </div>' +
            '    <div class="cgtwp-data-column">' +
            '      <div class="cgtwp-panel">' +
            '        <div class="cgtwp-panel-title">Button Map</div>' +
            '        <div class="cgtwp-button-grid">' + buttonGridHtml + '</div>' +
            '      </div>' +
            '      <div class="cgtwp-panel">' +
            '        <div class="cgtwp-panel-title">Diagnostics</div>' +
            '        <div class="cgtwp-diagnostics">' +
            '          <div class="cgtwp-diagnostic-item">' +
            '            <span class="cgtwp-diagnostic-label">Input Latency</span>' +
            '            <span class="cgtwp-diagnostic-value cgtwp-latency-value">-- ms</span>' +
            '          </div>' +
            '          <div class="cgtwp-diagnostic-item">' +
            '            <span class="cgtwp-diagnostic-label">Mapping</span>' +
            '            <span class="cgtwp-diagnostic-value cgtwp-mapping-value">Unknown</span>' +
            '          </div>' +
            '        </div>' +
            '        <button type="button" class="cgtwp-rumble-btn">Trigger Vibration Test</button>' +
            '        <div class="cgtwp-rumble-note">*Requires controller rumble support. A short vibration pulse will be triggered.</div>' +
            '      </div>' +
            '    </div>' +
            '  </div>' +
            '  <div class="cgtwp-empty-state cgtwp-hidden">Press any button on your controller to wake it up and start sending input data.</div>' +
            '</div>';

        root.innerHTML = template;

        var statusDot = root.querySelector('.cgtwp-status-dot');
        var statusText = root.querySelector('.cgtwp-status-text');
        var deviceSelect = root.querySelector('.cgtwp-device-select');
        var refreshBtn = root.querySelector('.cgtwp-refresh-btn');
        var rumbleBtn = root.querySelector('.cgtwp-rumble-btn');
        var latencyValue = root.querySelector('.cgtwp-latency-value');
        var mappingValue = root.querySelector('.cgtwp-mapping-value');
        var emptyState = root.querySelector('.cgtwp-empty-state');

        var svgButtonNodes = root.querySelectorAll('svg [data-button-index]');
        var svgButtonsMap = new Map();
        svgButtonNodes.forEach(function (node) {
            var idx = parseInt(node.getAttribute('data-button-index'), 10);
            if (!svgButtonsMap.has(idx)) {
                svgButtonsMap.set(idx, []);
            }
            svgButtonsMap.get(idx).push(node);
        });

        var gridButtonNodes = root.querySelectorAll('.cgtwp-button-item');
        var gridButtonsMap = new Map();
        gridButtonNodes.forEach(function (node) {
            var idx = parseInt(node.getAttribute('data-button-index'), 10);
            if (!gridButtonsMap.has(idx)) {
                gridButtonsMap.set(idx, []);
            }
            gridButtonsMap.get(idx).push(node);
        });

        return {
            statusDot: statusDot,
            statusText: statusText,
            deviceSelect: deviceSelect,
            refreshBtn: refreshBtn,
            rumbleBtn: rumbleBtn,
            latencyValue: latencyValue,
            mappingValue: mappingValue,
            emptyState: emptyState,
            svgButtons: svgButtonsMap,
            gridButtons: gridButtonsMap,
            leftStick: root.querySelector('.cgtwp-stick-left'),
            rightStick: root.querySelector('.cgtwp-stick-right'),
            leftTrigger: root.querySelector('.cgtwp-trigger-left'),
            rightTrigger: root.querySelector('.cgtwp-trigger-right')
        };
    }

    function manualScan() {
        if (!SUPPORTS_GAMEPAD) {
            return;
        }
        var pads = getGamepads();
        Instances.forEach(function (instance) {
            syncGamepads(instance, pads);
        });
    }

    function syncGamepads(instance, pads) {
        var descriptor = pads.map(function (pad) {
            return pad.index + ':' + pad.id + ':' + pad.mapping;
        }).join('|');

        if (descriptor !== instance.deviceSignature) {
            instance.deviceSignature = descriptor;
            rebuildDeviceSelect(instance, pads);
        }

        if (instance.selectedIndex === null) {
            var firstPad = pads.length > 0 ? pads[0] : null;
            instance.selectedIndex = firstPad ? firstPad.index : null;
        } else {
            var stillConnected = pads.some(function (pad) { return pad.index === instance.selectedIndex; });
            if (!stillConnected) {
                instance.selectedIndex = pads.length > 0 ? pads[0].index : null;
            }
        }

        updateSelectValue(instance);
        updateStatus(instance, pads);
    }

    function rebuildDeviceSelect(instance, pads) {
        var select = instance.refs.deviceSelect;
        var previousValue = select.value;
        select.innerHTML = '';

        var placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = pads.length > 0 ? 'Select controller' : 'No controllers detected';
        placeholder.disabled = pads.length > 0;
        placeholder.selected = instance.selectedIndex === null;
        select.appendChild(placeholder);

        pads.forEach(function (pad) {
            var option = document.createElement('option');
            option.value = pad.index;
            option.textContent = formatPadLabel(pad, pads.length);
            if (pad.index === instance.selectedIndex) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        select.disabled = pads.length === 0;
        if (pads.length === 0) {
            select.value = '';
        } else if (previousValue && select.querySelector('option[value="' + previousValue + '"]')) {
            select.value = previousValue;
        }
    }

    function updateSelectValue(instance) {
        var select = instance.refs.deviceSelect;
        if (instance.selectedIndex === null) {
            select.value = '';
        } else {
            var value = String(instance.selectedIndex);
            var optionExists = !!select.querySelector('option[value="' + value + '"]');
            if (optionExists) {
                select.value = value;
            }
        }
    }

    function updateStatus(instance, pads) {
        var selectedPad = pads.find(function (pad) { return pad.index === instance.selectedIndex; }) || null;

        if (!selectedPad) {
            instance.refs.statusDot.classList.remove('cgtwp-online');
            instance.refs.statusText.textContent = pads.length > 0 ? 'Select a controller to begin testing' : 'Waiting for controller connection';
            instance.refs.emptyState.classList.remove('cgtwp-hidden');
            instance.refs.rumbleBtn.disabled = true;
            instance.refs.latencyValue.textContent = '-- ms';
            instance.refs.latencyValue.classList.remove('cgtwp-alert');
            instance.refs.mappingValue.textContent = 'Unknown';
            instance.latencySamples = [];
            resetSticks(instance);
            resetTriggers(instance);
            resetButtons(instance);
            return;
        }

        instance.refs.statusDot.classList.add('cgtwp-online');
        var connectionSuffix = pads.length > 1 ? ' (' + pads.length + ' controllers detected)' : '';
        instance.refs.statusText.textContent = 'Connected: ' + selectedPad.id + connectionSuffix;
        instance.refs.emptyState.classList.add('cgtwp-hidden');
        instance.refs.mappingValue.textContent = selectedPad.mapping || 'standard';
        instance.refs.rumbleBtn.disabled = !hasRumble(selectedPad);
    }

    function resetSticks(instance) {
        updateStick(instance.refs.leftStick, 0, 0);
        updateStick(instance.refs.rightStick, 0, 0);
    }

    function resetTriggers(instance) {
        updateTrigger(instance.refs.leftTrigger, 0);
        updateTrigger(instance.refs.rightTrigger, 0);
    }

    function resetButtons(instance) {
        instance.highlightState.clear();
        instance.refs.svgButtons.forEach(function (nodes) {
            nodes.forEach(function (node) {
                node.classList.remove('cgtwp-active');
            });
        });
        instance.refs.gridButtons.forEach(function (nodes) {
            nodes.forEach(function (node) {
                node.classList.remove('cgtwp-active');
            });
        });
    }

    function formatPadLabel(pad, total) {
        var label = pad.id || 'Gamepad ' + pad.index;
        if (pad.mapping) {
            label += ' · ' + pad.mapping;
        }
        label += ' · Port ' + pad.index;
        if (total > 1) {
            label += ' (' + total + ' connected)';
        }
        return label;
    }

    function startPolling() {
        if (rafId !== null) {
            return;
        }
        var previous = performance.now();
        function loop(now) {
            rafId = window.requestAnimationFrame(loop);
            if (now - previous < POLL_INTERVAL) {
                return;
            }
            previous = now;
            pollInstances();
        }
        rafId = window.requestAnimationFrame(loop);
    }

    function pollInstances() {
        if (!SUPPORTS_GAMEPAD) {
            return;
        }
        var pads = getGamepads();
        Instances.forEach(function (instance) {
            syncGamepads(instance, pads);
            var pad = pads.find(function (item) { return item.index === instance.selectedIndex; }) || null;
            if (!pad) {
                return;
            }
            updateGamepadState(instance, pad);
        });
    }

    function updateGamepadState(instance, pad) {
        updateButtons(instance, pad.buttons);
        updateAxes(instance, pad);
        updateLatency(instance, pad);
    }

    function updateButtons(instance, buttons) {
        if (!buttons) {
            return;
        }
        buttons.forEach(function (button, index) {
            var analog = getAnalogValue(button);
            instance.highlightState.set(index, analog);
            var highlight = button.pressed || analog >= BUTTON_THRESHOLD;
            toggleButtonHighlight(instance, index, highlight);
        });
    }

    function toggleButtonHighlight(instance, index, active) {
        var svgNodes = instance.refs.svgButtons.get(index) || [];
        var gridNodes = instance.refs.gridButtons.get(index) || [];
        var isActive = !!active;
        svgNodes.forEach(function (node) {
            if (isActive) {
                node.classList.add('cgtwp-active');
            } else {
                node.classList.remove('cgtwp-active');
            }
        });
        gridNodes.forEach(function (node) {
            if (isActive) {
                node.classList.add('cgtwp-active');
            } else {
                node.classList.remove('cgtwp-active');
            }
        });
    }

    function updateAxes(instance, pad) {
        var axes = pad.axes || [];
        var leftX = axes[0] || 0;
        var leftY = axes[1] || 0;
        var rightX = axes[2] || 0;
        var rightY = axes[3] || 0;

        updateStick(instance.refs.leftStick, leftX, leftY);
        updateStick(instance.refs.rightStick, rightX, rightY);

        var leftTriggerValue = getAnalogValue(pad.buttons ? pad.buttons[6] : null);
        var rightTriggerValue = getAnalogValue(pad.buttons ? pad.buttons[7] : null);

        if (leftTriggerValue === 0 && axes.length >= 5) {
            leftTriggerValue = convertAxisToTrigger(axes[4]);
        }
        if (rightTriggerValue === 0 && axes.length >= 6) {
            rightTriggerValue = convertAxisToTrigger(axes[5]);
        }

        instance.highlightState.set(6, leftTriggerValue);
        instance.highlightState.set(7, rightTriggerValue);
        toggleButtonHighlight(instance, 6, leftTriggerValue >= BUTTON_THRESHOLD);
        toggleButtonHighlight(instance, 7, rightTriggerValue >= BUTTON_THRESHOLD);

        updateTrigger(instance.refs.leftTrigger, leftTriggerValue);
        updateTrigger(instance.refs.rightTrigger, rightTriggerValue);
    }

    function updateStick(stickRoot, rawX, rawY) {
        if (!stickRoot) {
            return;
        }
        var indicator = stickRoot.querySelector('.cgtwp-stick-indicator');
        var xLabel = stickRoot.querySelector('.cgtwp-stick-x');
        var yLabel = stickRoot.querySelector('.cgtwp-stick-y');

        var x = clamp(rawX, -1, 1);
        var y = clamp(rawY, -1, 1);

        if (indicator) {
            indicator.style.setProperty('transform', 'translate(calc(-50% + ' + (x * 40) + '%), calc(-50% + ' + (-y * 40) + '%))', 'important');
        }
        if (xLabel) {
            xLabel.textContent = 'X: ' + x.toFixed(2);
        }
        if (yLabel) {
            yLabel.textContent = 'Y: ' + y.toFixed(2);
        }
    }

    function updateTrigger(triggerRoot, value) {
        if (!triggerRoot) {
            return;
        }
        var fill = triggerRoot.querySelector('.cgtwp-trigger-fill');
        var display = triggerRoot.querySelector('.cgtwp-trigger-value');
        var normalized = clamp(value, 0, 1);
        if (fill) {
            fill.style.setProperty('width', Math.round(normalized * 100) + '%', 'important');
        }
        if (display) {
            display.textContent = Math.round(normalized * 100) + '%';
        }
    }

    function updateLatency(instance, pad) {
        var latencyValue = instance.refs.latencyValue;
        if (!latencyValue) {
            return;
        }
        if (typeof pad.timestamp !== 'number' || pad.timestamp === 0) {
            latencyValue.textContent = '< 1 ms';
            latencyValue.classList.remove('cgtwp-alert');
            return;
        }
        var now = performance.now();
        var latency = Math.max(0, now - pad.timestamp);
        instance.latencySamples.push(latency);
        if (instance.latencySamples.length > LATENCY_SAMPLES) {
            instance.latencySamples.shift();
        }
        var average = instance.latencySamples.reduce(function (sum, value) { return sum + value; }, 0) / instance.latencySamples.length;
        var formatted = average.toFixed(1) + ' ms';
        latencyValue.textContent = formatted;
        if (average > 40) {
            latencyValue.classList.add('cgtwp-alert');
        } else {
            latencyValue.classList.remove('cgtwp-alert');
        }
    }

    function triggerRumble(instance) {
        var pad = getSelectedPad(instance);
        if (!pad || !hasRumble(pad)) {
            return;
        }
        try {
            if (typeof pad.vibrationActuator.playEffect === 'function') {
                pad.vibrationActuator.playEffect('dual-rumble', {
                    startDelay: 0,
                    duration: 600,
                    weakMagnitude: 0.7,
                    strongMagnitude: 1
                });
            } else if (typeof pad.vibrationActuator.pulse === 'function') {
                pad.vibrationActuator.pulse(1, 600);
            }
        } catch (error) {
            console.warn('Unable to trigger controller vibration', error);
        }
    }

    function hasRumble(pad) {
        return pad && pad.vibrationActuator && (typeof pad.vibrationActuator.playEffect === 'function' || typeof pad.vibrationActuator.pulse === 'function');
    }

    function getSelectedPad(instance) {
        if (!SUPPORTS_GAMEPAD) {
            return null;
        }
        var pads = getGamepads();
        for (var i = 0; i < pads.length; i += 1) {
            if (pads[i].index === instance.selectedIndex) {
                return pads[i];
            }
        }
        return null;
    }

    function clamp(value, min, max) {
        if (value < min) {
            return min;
        }
        if (value > max) {
            return max;
        }
        return value;
    }

    function getAnalogValue(button) {
        if (!button) {
            return 0;
        }
        if (typeof button.value === 'number') {
            return clamp(button.value, 0, 1);
        }
        return button.pressed ? 1 : 0;
    }

    function convertAxisToTrigger(value) {
        if (typeof value !== 'number') {
            return 0;
        }
        return clamp((value + 1) / 2, 0, 1);
    }

    function handleGamepadConnected() {
        manualScan();
    }

    function handleGamepadDisconnected() {
        manualScan();
    }

    function bootInstances() {
        var roots = document.querySelectorAll('.cgtwp-root:not([data-cgtwp-mounted])');
        roots.forEach(createInstance);
    }

    function boot() {
        bootInstances();
        startPolling();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    window.addEventListener('gamepadconnected', handleGamepadConnected);
    window.addEventListener('gamepaddisconnected', handleGamepadDisconnected);

    window[PLUGIN_NAME] = window[PLUGIN_NAME] || {
        boot: boot,
        init: createInstance
    };

    document.dispatchEvent(new CustomEvent('cloudGamepadTesterReady', { detail: window[PLUGIN_NAME] }));
})(window, document);
