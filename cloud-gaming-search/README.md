# Cloud Gaming Search Engine

A premium cloud gaming discovery system built inside WordPress. Features a Netflix-style UI, RAWG-powered game search, and multi-platform cloud availability detection.

## Features

- **🎮 Netflix-style Game Grid** — Premium card UI with hover animations and smooth transitions
- **🔍 Instant Search** — Debounced search with live suggestions dropdown
- **☁️ Cloud Availability Matrix** — Check availability across GeForce NOW, Xbox Cloud Gaming, PlayStation Plus, Amazon Luna, Boosteroid, AirGPU, and Shadow PC
- **🏆 Top 100 Cloud Games** — Preloaded, cached home page content
- **🌓 Dark/Light Theme** — Smooth animated toggle with localStorage persistence
- **📱 Fully Responsive** — Works beautifully on all screen sizes
- **⚡ Performance Optimized** — Lazy loading, caching, debounced searches, minimal API calls

## Installation

1. Upload the `cloud-gaming-search` folder to `/wp-content/plugins/`
2. Activate the plugin from the WordPress admin
3. Go to **Cloud Gaming** in the admin menu
4. Enter your **RAWG API key** (get one free at [rawg.io/apidocs](https://rawg.io/apidocs))
5. Configure cloud platforms as desired
6. Add the shortcode `[cloud_game_search]` to any page or post

## Shortcode

```
[cloud_game_search]
[cloud_game_search show_top100="true" theme="dark"]
[cloud_game_search show_top100="false"]
```

## REST API Endpoints

| Endpoint | Description |
|----------|-------------|
| `GET /wp-json/cloud-gaming/v1/search?q=cyberpunk` | Search games |
| `GET /wp-json/cloud-gaming/v1/game?id=3498` | Full game details |
| `GET /wp-json/cloud-gaming/v1/availability?title=Cyberpunk 2077` | Cloud availability |
| `GET /wp-json/cloud-gaming/v1/top100?page=1&per_page=20` | Top 100 games |
| `GET /wp-json/cloud-gaming/v1/platforms` | Supported platforms |
| `GET /wp-json/cloud-gaming/v1/health` | System health check |

## Requirements

- WordPress 5.0+
- PHP 7.4+
- RAWG API key

## Architecture

```
cloud-gaming-search/
├── cloud-gaming-search.php     # Main plugin file
├── includes/
│   ├── class-cache.php         # Caching layer (WordPress transients)
│   ├── class-rawg.php          # RAWG API integration
│   ├── class-cloud-engine.php  # Cloud availability intelligence
│   ├── class-scraper-adapters.php # Platform adapter system
│   ├── class-top100.php        # Top 100 curation engine
│   └── class-rest.php          # REST API endpoints
├── assets/
│   ├── css/
│   │   ├── style.css           # Frontend design system
│   │   └── admin.css           # Admin panel styles
│   └── js/
│       ├── app.js              # SPA frontend application
│       └── admin.js            # Admin panel logic
└── README.md
```

## Cloud Availability System

The plugin uses a **Platform Adapter System** where each cloud provider has its own module:

- **Official data** — Admin-maintained game lists per platform
- **Curated catalogs** — Built-in lists of popular titles verified on each platform
- **Fuzzy matching** — Title normalization and smart matching
- **Fallback** — Returns "unknown" when data is uncertain (never guesses)

### Supported Platforms

- GeForce NOW
- Xbox Cloud Gaming
- Playstation Plus Cloud
- Amazon Luna
- Boosteroid
- AirGPU (always available)
- Shadow PC (always available)

## Extending

The architecture supports easy addition of:
- User accounts and favorites
- Affiliate links (Steam, Xbox, GFN)
- AI game recommendations
- Browser extension integration
- Game comparison tool

## Credits

Built for [Cloud Loadout](https://cloudloadout.com) — Your cloud gaming discovery platform.