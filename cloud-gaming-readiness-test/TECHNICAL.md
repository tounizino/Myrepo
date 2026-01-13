# Technical Documentation: Cloud Gaming Readiness Test

## Architecture

This plugin is built as a WordPress-native, single-page-application-style diagnostic tool with a backend-assisted measurement system.

### Plugin Structure

```
cloud-gaming-readiness-test/
├── cloud-gaming-readiness-test.php   # Main plugin file
├── includes/
│   ├── class-cgrt-database.php       # Database schema & queries
│   ├── class-cgrt-admin.php          # Admin dashboard
│   ├── class-cgrt-frontend.php       # Shortcode & enqueue
│   ├── class-cgrt-api.php            # REST API endpoints
│   ├── class-cgrt-block.php          # Gutenberg block
│   ├── class-cgrt-score-calculator.php # Scoring logic
│   └── class-cgrt-test-engine.php    # Verdict generation
├── templates/
│   ├── admin-dashboard.php
│   ├── admin-platforms.php
│   ├── admin-edit-platform.php
│   ├── admin-edit-endpoint.php
│   └── admin-settings.php
├── assets/
│   ├── admin/
│   │   ├── admin.css
│   │   └── admin.js
│   └── frontend/
│       ├── frontend.css
│       └── frontend.js
├── blocks/
│   └── cgrt/
│       └── block.js
├── uninstall.php
├── readme.txt
└── TECHNICAL.md
```

## Database Schema

### Tables

#### `wp_cgrt_platforms`

Stores cloud gaming platforms/services.

| Column        | Type          | Notes                                      |
|---------------|---------------|--------------------------------------------|
| id            | bigint(20)    | Primary key                                |
| name          | varchar(191)  | Display name                               |
| slug          | varchar(191)  | Unique slug for shortcode targeting        |
| enabled       | tinyint(1)    | Enable/disable platform                    |
| weights_json  | longtext      | Optional per-platform custom weights (JSON)|
| created_at    | datetime      |                                            |
| updated_at    | datetime      |                                            |

#### `wp_cgrt_endpoints`

Stores test endpoints (regions) per platform.

| Column        | Type          | Notes                                      |
|---------------|---------------|--------------------------------------------|
| id            | bigint(20)    | Primary key                                |
| platform_id   | bigint(20)    | Foreign key to platforms                   |
| region        | varchar(191)  | Display label (e.g., "US-East")            |
| endpoint_url  | text          | Full URL (must support CORS)              |
| method        | varchar(16)   | HEAD or GET                                |
| enabled       | tinyint(1)    | Enable/disable endpoint                    |
| timeout_ms    | int(11)       | Per-request timeout (500-10000 ms)         |
| notes         | text          | Admin-only notes                           |
| created_at    | datetime      |                                            |
| updated_at    | datetime      |                                            |

#### `wp_cgrt_results`

Stores test results (optional, disabled by default).

| Column        | Type          | Notes                                      |
|---------------|---------------|--------------------------------------------|
| id            | bigint(20)    | Primary key                                |
| created_at    | datetime      | Test timestamp                             |
| platform_id   | bigint(20)    | Platform tested                            |
| endpoint_id   | bigint(20)    | Endpoint used                              |
| score         | int(11)       | Final score (0-100)                        |
| tier          | varchar(32)   | Tier label                                 |
| metrics_json  | longtext      | Full metrics (JSON)                        |
| ua_hash       | char(64)      | HMAC-SHA256 of User-Agent (anonymized)     |
| ip_hash       | char(64)      | HMAC-SHA256 of IP (anonymized)             |

**Privacy:** No PII is stored. Hashes use `wp_salt('auth')` as HMAC key.

### Settings (wp_options)

Stored as `cgrt_settings` (JSON).

```json
{
  "test_duration_seconds": 18,
  "sample_interval_ms": 350,
  "endpoint_pick_pings": 5,
  "spike_ms": 35,
  "store_results": false,
  "tiers": {
    "excellent": 85,
    "good": 70,
    "fair": 55,
    "poor": 0
  },
  "default_weights": {
    "latency": 0.40,
    "jitter": 0.25,
    "loss": 0.25,
    "stability": 0.10
  }
}
```

## REST API

### `GET /wp-json/cgrt/v1/config`

Returns test configuration for the front-end.

**Response:**

```json
{
  "platforms": [
    {
      "id": 1,
      "name": "Generic Cloud Gaming",
      "slug": "generic",
      "weights": { "latency": 0.4, "jitter": 0.25, "loss": 0.25, "stability": 0.1 },
      "endpoints": [
        {
          "id": 1,
          "region": "US-East",
          "url": "https://edge.example.com/ping",
          "method": "HEAD",
          "timeout_ms": 2500
        }
      ]
    }
  ],
  "settings": {
    "testDurationSeconds": 18,
    "sampleIntervalMs": 350,
    "endpointPickPings": 5,
    "spikeMs": 35,
    "tiers": { "excellent": 85, "good": 70, "fair": 55, "poor": 0 }
  }
}
```

### `POST /wp-json/cgrt/v1/submit`

Submit test result (optional, respects `store_results` setting).

**Request body:**

```json
{
  "platformId": 1,
  "endpointId": 1,
  "score": 78,
  "tier": "Good",
  "metrics": {
    "latency_ms": 42.3,
    "jitter_ms": 8.1,
    "loss_pct": 0.5,
    "volatility": 0.12
  }
}
```

## Front-end Test Logic

### Measurement Approach

Because browsers cannot perform ICMP ping, we use **HTTP timing** (Fetch API with `performance.now()`).

1. Send HTTP request (HEAD or GET) to endpoint
2. Measure round-trip time (RTT)
3. Timeout indicates packet loss
4. Repeat over test duration (e.g., 18 seconds, 350ms intervals)

### Metrics

| Metric                 | Calculation                                      |
|------------------------|--------------------------------------------------|
| Latency (median)       | `median(okRtts)`                                 |
| Latency (avg)          | `mean(okRtts)`                                   |
| Latency (p95)          | `percentile(okRtts, 0.95)`                       |
| Jitter                 | `stddev(okRtts)`                                 |
| Packet loss (%)        | `(totalSamples - okSamples) / totalSamples * 100`|
| Volatility             | `f(spikeRate, stddev)`                           |
| Degradation            | `mean(last20%) - mean(first20%)`                 |

### Scoring

Each metric is converted to a 0-100 subscore using **cloud-gaming-tuned piecewise curves**.

**Latency score:**

```
≤20ms → 100
≤30ms → 90
≤50ms → 75
≤80ms → 55
≤120ms → 30
≤180ms → 10
>180ms → 0
```

**Jitter score:**

```
≤2ms → 100
≤5ms → 88
≤10ms → 70
≤20ms → 45
≤30ms → 28
>30ms → 0
```

**Loss score:**

```
0% → 100
0.5% → 86
1% → 72
2% → 55
5% → 25
≥10% → 0
```

**Stability score:**

```
volatility_index: 0..1
stability_score: (1 - volatility) * 100
```

**Final score:**

```
score = (latency_score * w_lat) + (jitter_score * w_jit) + (loss_score * w_loss) + (stability_score * w_stab)
```

Weights are normalized to sum to 1.

### Tier Classification

```
score ≥ 85 → Excellent
score ≥ 70 → Good
score ≥ 55 → Fair
score < 55 → Poor
```

## Verdict Generation

The plugin generates plain-English recommendations based on metrics:

- High latency → suggest wired connection, check routing
- High jitter → Wi-Fi interference, QoS, close background apps
- Packet loss → network congestion, ISP issues
- High volatility → instability warnings, spike detection

## Admin Customization

### Per-Platform Weights

Admins can override default weights per platform. Example:

- Platform: "Competitive FPS"
  - Latency: 0.50
  - Jitter: 0.30
  - Loss: 0.15
  - Stability: 0.05

### Tier Thresholds

Admins can adjust tier cutoffs. Example:

- Excellent: 90
- Good: 75
- Fair: 60
- Poor: <60

### Test Behavior

- Duration: 10-120 seconds
- Sample interval: 100-2000 ms
- Endpoint pick pings: 3-20
- Spike threshold: 10-200 ms

## Security

- All inputs sanitized and escaped
- Nonce verification on forms
- `wp_json_encode()` / `wp_json_decode()` for JSON
- CORS handled by endpoint owner (not plugin)
- Result storage uses anonymized hashes (HMAC-SHA256 with `wp_salt('auth')`)

## Performance

- Minimal server load (client-side measurement)
- No heavy dependencies (vanilla JS, native WordPress APIs)
- Efficient database queries (indexed columns)
- Optional result storage (disabled by default)

## Browser Requirements

- Modern browser with Fetch API and `performance.now()`
- CORS-enabled endpoints (critical)
- JavaScript enabled

## Future Extensibility

- Additional metrics (upload/download simulation)
- WebRTC-based latency (STUN/TURN)
- Historical trend tracking
- Region-auto-selection (geolocation)
- White-label branding options

## License

GPLv2 or later
