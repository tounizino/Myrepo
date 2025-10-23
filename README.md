# Cloud Gamepad Tester WP

A premium WordPress-ready toolkit for showcasing and testing cloud gaming controllers directly in the browser. The plugin delivers a responsive, Xbox-inspired visualization that reacts in real time to button, trigger, and analog stick input using the Gamepad API.

## Installation

1. Copy the `wp-cloud-gamepad-tester` directory into your WordPress installation under `wp-content/plugins/`.
2. Activate **Cloud Gamepad Tester WP** from the WordPress Plugins screen.
3. Add the shortcode `[cloud_gamepad_tester]` anywhere inside a post or page to render the interactive tester.

## Feature Highlights

- **Full Gamepad API coverage** – buttons, triggers, sticks, D-pad, and connection status are monitored in real time.
- **Xbox-style visualization** – high-end SVG controller art with animated highlights for each interaction.
- **Analog fidelity** – smooth stick tracking with coordinate readouts and live trigger pressure bars.
- **Diagnostics suite** – latency monitor, controller mapping display, and connection summaries.
- **Immersive extras** – vibration/rumble test button, multiple controller awareness, and graceful reconnection handling.
- **WordPress friendly** – fully namespaced CSS/JS, no global leaks, and styles enforced with `!important` to avoid theme interference.

## Browser Support

- Works in modern browsers with Gamepad API support (Chrome, Edge, Firefox, and most Chromium-based browsers).
- Safari and older browsers without Gamepad API support will display an informative fallback message.

## Project Structure

```
wp-cloud-gamepad-tester/
├── wp-cloud-gamepad-tester.php   # WordPress plugin bootstrap + shortcode
├── assets/
│   ├── css/cloud-gamepad-tester.css  # Encapsulated styles (all `!important`)
│   └── js/cloud-gamepad-tester.js    # Namespaced tester logic
```

Drop the plugin into your WordPress instance, add the shortcode to any page, and you’re ready to showcase a best-in-class cloud gaming controller tester.
