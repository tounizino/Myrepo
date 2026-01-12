# Cloud Gaming Readiness Test (WordPress Plugin)

A professional-grade WordPress plugin that provides a **Cloud Gaming Readiness Test** focused on cloud-gaming-relevant networking factors:

- Average latency (HTTP-based “ping”)
- Latency variation (jitter)
- Packet loss / failed requests
- Connection stability and spike detection
- Short-burst vs sustained performance indicators

## Installation

1. Copy the `cloud-gaming-readiness-test/` folder into your WordPress installation:
   - `wp-content/plugins/cloud-gaming-readiness-test/`
2. Activate the plugin in **WP Admin → Plugins**.

## Usage

### Shortcode

Embed anywhere:

```text
[cloud_gaming_readiness_test]
```

Optional platform preselect:

```text
[cloud_gaming_readiness_test platform="generic"]
```

### Block

In the block editor, insert:

- **Cloud Gaming Readiness Test**

## Admin Configuration

Go to:

- **WP Admin → CG Readiness → Platforms**

Add platforms and endpoints.

### Endpoint Requirements (Critical)

Because browsers cannot perform true ICMP ping, this plugin uses **real browser HTTP timing** to measure the user’s device-to-endpoint performance.

For accurate results, endpoints should:

- Be reachable over HTTPS
- Respond quickly (204 or tiny JSON)
- **Allow CORS** (Access-Control-Allow-Origin: * or your site domain)
- Be hosted close to your cloud gaming edge / region

## Notes

- Result storage is **optional** and disabled by default.
- When enabled, results are stored with anonymized hashes (no PII stored).

## Development

This repository contains the plugin source code only. It uses WordPress-native APIs and avoids heavy dependencies.
