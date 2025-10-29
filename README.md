# Cloud Gaming Connectivity Tool

- Paste `cloud-gaming-connectivity-tool.html` directly into a WordPress post (HTML view) or upload it to your site and embed via `<iframe>` using the snippet in the ticket response.
- Update the `STUN_SERVERS` array in the inline script to add/replace STUN or TURN services (recommended to mirror your production infrastructure).
- Toggle `BACKEND_PORT_CHECK_URL` to point at the provided Node/Express `/api/port-check` endpoint if you deploy the optional verification backend.
- Extend advanced diagnostics by filling in the placeholder functions (`startLatencyTest`, `checkIPv6`, `detectVPN`, `detectRouter`) with your preferred monitoring logic.
- Keep Tailwind via CDN, or swap to a local build by compiling Tailwind classes and replacing the CDN `<script>` with your bundled CSS for performance-sensitive deployments.
