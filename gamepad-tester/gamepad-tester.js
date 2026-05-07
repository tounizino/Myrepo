class GamepadDiagnostics {
    constructor() {
        this.gamepadIndex = null;
        this.rafId = null;
        this.frameCount = 0;
        this.lastPollingUpdate = 0;
        
        // Advanced Stats
        this.stats = {
            l: { maxDist: 0, points: [], precision: new Set(), drift: 0 },
            r: { maxDist: 0, points: [], precision: new Set(), drift: 0 },
            pollingVariances: []
        };
        this.maxHistory = 150;

        // UI Elements
        this.elements = {
            status: document.getElementById('gt-connection-status'),
            pollingRate: document.getElementById('gt-polling-rate'),
            deviceType: document.getElementById('gt-device-type'),
            ctrlId: document.getElementById('gt-ctrl-id'),
            battery: document.getElementById('gt-ctrl-battery'),
            inputLog: document.getElementById('gt-input-log'),
            vibeBtn: document.getElementById('gt-test-vibration'),
            
            stickL: document.querySelector('#gt-stick-l .gt-stick-dot'),
            stickR: document.querySelector('#gt-stick-r .gt-stick-dot'),
            
            lX: document.getElementById('gt-l-x'),
            lY: document.getElementById('gt-l-y'),
            lCirc: document.getElementById('gt-l-circ'),
            rX: document.getElementById('gt-r-x'),
            rY: document.getElementById('gt-r-y'),
            rCirc: document.getElementById('gt-r-circ'),
            
            ltFill: document.getElementById('gt-lt-fill'),
            rtFill: document.getElementById('gt-rt-fill'),
            
            canvasL: document.getElementById('gt-canvas-l'),
            canvasR: document.getElementById('gt-canvas-r')
        };

        this.ctxL = this.elements.canvasL.getContext('2d');
        this.ctxR = this.elements.canvasR.getContext('2d');

        this.init();
    }

    init() {
        window.addEventListener("gamepadconnected", (e) => this.onConnect(e));
        window.addEventListener("gamepaddisconnected", (e) => this.onDisconnect(e));
        this.elements.vibeBtn.addEventListener('click', () => this.testVibration());
        this.drawCanvas(this.ctxL, []);
        this.drawCanvas(this.ctxR, []);
        
        // Scan for gamepads immediately
        const gps = navigator.getGamepads();
        for (let i = 0; i < gps.length; i++) {
            if (gps[i]) {
                this.onConnect({ gamepad: gps[i] });
                break;
            }
        }
    }

    onConnect(e) {
        if (this.gamepadIndex === null) {
            this.gamepadIndex = e.gamepad.index;
            this.updateStaticUI(e.gamepad);
            this.startLoop();
            this.logInput(`Connected: ${e.gamepad.id}`);
        }
    }

    onDisconnect(e) {
        if (this.gamepadIndex === e.gamepad.index) {
            this.logInput(`Disconnected: ${e.gamepad.id}`);
            this.gamepadIndex = null;
            this.stopLoop();
            this.resetUI();
        }
    }

    updateStaticUI(gamepad) {
        this.elements.status.textContent = "Controller Connected";
        this.elements.status.className = "gt-status gt-connected";
        this.elements.deviceType.textContent = this.detectType(gamepad.id);
        this.elements.ctrlId.textContent = gamepad.id.split('(')[0].trim();
    }

    resetUI() {
        this.elements.status.textContent = "No Controller Detected";
        this.elements.status.className = "gt-status gt-disconnected";
        this.elements.pollingRate.textContent = "0 Hz";
        this.elements.deviceType.textContent = "Unknown";
        this.elements.ctrlId.textContent = "-";
        this.elements.battery.textContent = "N/A";
        this.elements.stickL.style.transform = `translate(-50%, -50%)`;
        this.elements.stickR.style.transform = `translate(-50%, -50%)`;
        document.querySelectorAll('.gt-btn, .gt-dpad div').forEach(el => el.classList.remove('active'));
        this.elements.ltFill.style.width = '0%';
        this.elements.rtFill.style.width = '0%';
    }

    detectType(id) {
        id = id.toLowerCase();
        if (id.includes('xbox')) return 'Xbox Controller';
        if (id.includes('dualsense')) return 'DualSense';
        if (id.includes('dualshock') || id.includes('playstation')) return 'PlayStation';
        if (id.includes('nintendo') || id.includes('pro controller')) return 'Switch Pro';
        return 'Standard Gamepad';
    }

    startLoop() {
        this.lastPollingUpdate = performance.now();
        this.frameCount = 0;
        const loop = (time) => {
            this.update(time);
            this.rafId = requestAnimationFrame(loop);
        };
        this.rafId = requestAnimationFrame(loop);
    }

    stopLoop() {
        if (this.rafId) cancelAnimationFrame(this.rafId);
    }

    update(time) {
        const gamepads = navigator.getGamepads();
        const gamepad = gamepads[this.gamepadIndex];
        if (!gamepad) return;

        this.frameCount++;
        if (time - this.lastPollingUpdate > 1000) {
            this.pollingRate = Math.round((this.frameCount * 1000) / (time - this.lastPollingUpdate));
            this.elements.pollingRate.textContent = `${this.pollingRate} Hz`;
            this.frameCount = 0;
            this.lastPollingUpdate = time;
        }

        if (gamepad.battery) {
            this.elements.battery.textContent = `${Math.round(gamepad.battery.level * 100)}%`;
        }

        this.processAxes(gamepad.axes);
        this.processButtons(gamepad.buttons);
    }

    processAxes(axes) {
        const lx = axes[0] || 0;
        const ly = axes[1] || 0;
        const rx = axes[2] || 0;
        const ry = axes[3] || 0;

        // Visuals - apply a small deadzone for the UI dot but show raw data in stats
        const uiDeadzone = 0.01;
        const ulx = Math.abs(lx) < uiDeadzone ? 0 : lx;
        const uly = Math.abs(ly) < uiDeadzone ? 0 : ly;
        const urx = Math.abs(rx) < uiDeadzone ? 0 : rx;
        const ury = Math.abs(ry) < uiDeadzone ? 0 : ry;

        this.elements.stickL.style.transform = `translate(calc(-50% + ${ulx * 35}px), calc(-50% + ${uly * 35}px))`;
        this.elements.stickR.style.transform = `translate(calc(-50% + ${urx * 35}px), calc(-50% + ${ury * 35}px))`;

        this.elements.lX.textContent = lx.toFixed(4);
        this.elements.lY.textContent = ly.toFixed(4);
        this.elements.rX.textContent = rx.toFixed(4);
        this.elements.rY.textContent = ry.toFixed(4);

        this.updateStickStats('l', lx, ly);
        this.updateStickStats('r', rx, ry);

        this.drawCanvas(this.ctxL, this.stats.l.points);
        this.drawCanvas(this.ctxR, this.stats.r.points);
    }

    updateStickStats(side, x, y) {
        const dist = Math.sqrt(x*x + y*y);
        const s = this.stats[side];
        
        // Trail
        s.points.push({x, y});
        if (s.points.length > this.maxHistory) s.points.shift();

        // Precision (unique values seen)
        s.precision.add(`${x.toFixed(3)},${y.toFixed(3)}`);
        if (s.precision.size > 5000) s.precision.clear(); // Reset if too big

        // Circularity
        if (dist > 0.75) {
            const error = Math.abs(1 - dist) * 100;
            this.elements[`${side}Circ`].textContent = `${error.toFixed(1)}%`;
        } else {
            this.elements[`${side}Circ`].textContent = "0.0%";
        }
    }

    processButtons(buttons) {
        buttons.forEach((btn, i) => {
            const el = document.querySelector(`[data-btn="${i}"]`);
            const wasActive = el?.classList.contains('active');
            const isActive = btn.pressed || btn.value > 0.1;

            if (el) {
                if (isActive) el.classList.add('active');
                else el.classList.remove('active');
            }

            if (i === 6) this.elements.ltFill.style.width = `${btn.value * 100}%`;
            if (i === 7) this.elements.rtFill.style.width = `${btn.value * 100}%`;

            if (isActive && !wasActive) {
                this.logInput(`Button ${i} [${btn.value.toFixed(2)}]`);
            }
        });
    }

    logInput(msg) {
        const entry = document.createElement('div');
        entry.className = 'gt-log-entry';
        const now = new Date();
        const time = now.toTimeString().split(' ')[0] + '.' + now.getMilliseconds().toString().padStart(3, '0');
        entry.innerHTML = `<span class="gt-log-time">${time}</span> <span class="gt-log-action">${msg}</span>`;
        this.elements.inputLog.prepend(entry);
        if (this.elements.inputLog.childNodes.length > 30) this.elements.inputLog.removeChild(this.elements.inputLog.lastChild);
    }

    drawCanvas(ctx, points) {
        const w = 160, h = 160, center = 80, scale = 65;
        ctx.clearRect(0, 0, w, h);

        // Background grid
        ctx.strokeStyle = '#f1f5f9';
        ctx.beginPath();
        for(let i=0; i<=w; i+=20) { ctx.moveTo(i, 0); ctx.lineTo(i, h); ctx.moveTo(0, i); ctx.lineTo(w, i); }
        ctx.stroke();

        // Crosshair
        ctx.strokeStyle = '#e2e8f0';
        ctx.setLineDash([2, 2]);
        ctx.beginPath();
        ctx.moveTo(center, 0); ctx.lineTo(center, h);
        ctx.moveTo(0, center); ctx.lineTo(w, center);
        ctx.stroke();
        ctx.setLineDash([]);

        // Target Circle (1.0 distance)
        ctx.strokeStyle = '#cbd5e1';
        ctx.beginPath();
        ctx.arc(center, center, scale, 0, Math.PI * 2);
        ctx.stroke();

        // Trail
        if (points.length > 1) {
            ctx.beginPath();
            ctx.strokeStyle = 'rgba(59, 130, 246, 0.2)';
            ctx.lineWidth = 2;
            points.forEach((p, i) => {
                const px = center + p.x * scale;
                const py = center + p.y * scale;
                if (i === 0) ctx.moveTo(px, py);
                else ctx.lineTo(px, py);
            });
            ctx.stroke();
        }

        // Active Dots
        points.forEach((p, i) => {
            const alpha = i / points.length;
            ctx.fillStyle = `rgba(59, 130, 246, ${alpha * 0.5})`;
            ctx.beginPath();
            ctx.arc(center + p.x * scale, center + p.y * scale, 2, 0, Math.PI * 2);
            ctx.fill();
        });

        // Current Position
        if (points.length > 0) {
            const last = points[points.length - 1];
            ctx.fillStyle = '#1e293b';
            ctx.beginPath();
            ctx.arc(center + last.x * scale, center + last.y * scale, 4, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    testVibration() {
        const gamepad = navigator.getGamepads()[this.gamepadIndex];
        if (gamepad?.vibrationActuator) {
            gamepad.vibrationActuator.playEffect("dual-rumble", {
                startDelay: 0, duration: 400, weakMagnitude: 0.8, strongMagnitude: 0.5,
            });
        } else {
            this.logInput("Vibration not supported");
        }
    }
}

document.addEventListener('DOMContentLoaded', () => new GamepadDiagnostics());
