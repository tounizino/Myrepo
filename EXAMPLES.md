# 💡 Usage Examples & Tips

Real-world examples and best practices for using the Cloud Gaming Sidebar Widget.

## 🎯 Example 1: Basic HTML Website

**Scenario:** Adding widget to a static HTML website sidebar.

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cloud Gaming Blog</title>
    
    <!-- Widget Styles -->
    <link rel="stylesheet" href="css/cloud-gaming-widget-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        main {
            flex: 1;
            margin-right: 30px;
        }
        aside {
            width: 380px;
        }
    </style>
</head>
<body>
    <main>
        <h1>Welcome to My Cloud Gaming Blog</h1>
        <p>Your main content here...</p>
    </main>
    
    <aside>
        <!-- Cloud Gaming Widget -->
        <div id="cloud-gaming-widget" class="cloud-gaming-widget theme-dark">
            <!-- Widget content here -->
        </div>
    </aside>
    
    <script src="js/cloud-gaming-widget-script.js"></script>
</body>
</html>
```

---

## 🎯 Example 2: WordPress Theme Integration

**Scenario:** Adding widget to WordPress theme's sidebar.

### Step 1: Add Files to Theme
```
your-theme/
├── functions.php
├── sidebar.php
└── assets/
    ├── css/
    │   └── cloud-gaming-widget-styles.css
    ├── js/
    │   └── cloud-gaming-widget-script.js
    └── php/
        └── cloud-gaming-widget.php
```

### Step 2: Enqueue Styles and Scripts (functions.php)
```php
function enqueue_cloud_gaming_widget() {
    wp_enqueue_style(
        'cloud-gaming-widget',
        get_template_directory_uri() . '/assets/css/cloud-gaming-widget-styles.css',
        array(),
        '1.0.0'
    );
    
    wp_enqueue_script(
        'cloud-gaming-widget',
        get_template_directory_uri() . '/assets/js/cloud-gaming-widget-script.js',
        array(),
        '1.0.0',
        true
    );
    
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
    );
}
add_action('wp_enqueue_scripts', 'enqueue_cloud_gaming_widget');
```

### Step 3: Register Widget
```php
require_once get_template_directory() . '/assets/php/cloud-gaming-widget.php';
```

---

## 🎯 Example 3: Elementor Integration

**Scenario:** Using widget with Elementor page builder.

1. Add **HTML Widget** to sidebar
2. Paste widget code from `widget-standalone.html`
3. Adjust widget settings:
   - Width: 380px
   - Margin: 0 auto
   - Padding: As needed

**Custom CSS for Elementor:**
```css
.elementor-widget-html .cloud-gaming-widget {
    max-width: 100%;
    margin: 0 auto;
}
```

---

## 🎯 Example 4: React/Next.js Integration

**Scenario:** Adding widget to React application.

### Step 1: Create Component
```jsx
// components/CloudGamingWidget.jsx
import React, { useEffect, useState } from 'react';
import '../styles/cloud-gaming-widget-styles.css';

const CloudGamingWidget = ({ theme = 'dark' }) => {
    const [currentTheme, setCurrentTheme] = useState(theme);
    
    useEffect(() => {
        // Load Font Awesome
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
        document.head.appendChild(link);
        
        return () => {
            document.head.removeChild(link);
        };
    }, []);
    
    return (
        <div className={`cloud-gaming-widget theme-${currentTheme}`}>
            {/* Widget content */}
        </div>
    );
};

export default CloudGamingWidget;
```

### Step 2: Use Component
```jsx
// pages/index.jsx
import CloudGamingWidget from '../components/CloudGamingWidget';

export default function Home() {
    return (
        <div className="layout">
            <main>{/* Main content */}</main>
            <aside>
                <CloudGamingWidget theme="dark" />
            </aside>
        </div>
    );
}
```

---

## 🎯 Example 5: Dynamic Link Configuration

**Scenario:** Managing widget links from a configuration file.

### config.json
```json
{
    "widgetTools": [
        {
            "id": "latency",
            "title": "Cloud Platforms Latency Tester",
            "description": "Test your connection speed",
            "icon": "fa-tachometer-alt",
            "url": "https://example.com/tools/latency-tester"
        },
        {
            "id": "network",
            "title": "Network Performance Monitor",
            "description": "Monitor your network stats",
            "icon": "fa-chart-line",
            "url": "https://example.com/tools/network-monitor"
        }
        // ... more tools
    ]
}
```

### JavaScript
```javascript
async function loadWidget() {
    const config = await fetch('/config.json').then(r => r.json());
    const toolsList = document.querySelector('.tools-list');
    
    toolsList.innerHTML = config.widgetTools.map(tool => `
        <li class="tool-item" data-tool="${tool.id}">
            <a href="${tool.url}" class="tool-link">
                <div class="tool-icon">
                    <i class="fas ${tool.icon}"></i>
                </div>
                <div class="tool-content">
                    <h4 class="tool-title">${tool.title}</h4>
                    <p class="tool-description">${tool.description}</p>
                </div>
                <div class="tool-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
        </li>
    `).join('');
}

loadWidget();
```

---

## 🎯 Example 6: Analytics Tracking

**Scenario:** Track widget interactions with Google Analytics.

```javascript
document.addEventListener('DOMContentLoaded', function() {
    const toolItems = document.querySelectorAll('.tool-item');
    
    toolItems.forEach(item => {
        item.addEventListener('click', function(e) {
            const toolName = item.getAttribute('data-tool');
            
            // Google Analytics 4
            if (typeof gtag !== 'undefined') {
                gtag('event', 'widget_tool_click', {
                    'event_category': 'sidebar_widget',
                    'event_label': toolName,
                    'value': 1
                });
            }
            
            // Google Analytics Universal
            if (typeof ga !== 'undefined') {
                ga('send', 'event', 'Widget', 'Click', toolName);
            }
            
            console.log('Tool clicked:', toolName);
        });
    });
});
```

---

## 🎯 Example 7: Custom Theme Colors

**Scenario:** Creating a custom red/black gaming theme.

```css
/* Add to your stylesheet after loading cloud-gaming-widget-styles.css */

.theme-red {
    background: linear-gradient(160deg, #dc2626, #1a1a1a);
    color: #fff;
}

.theme-red .header-icon {
    background: rgba(220, 38, 38, 0.2);
    color: #fca5a5;
}

.theme-red .tool-link {
    background: rgba(20, 20, 20, 0.6);
    color: #fff;
    border: 1px solid rgba(220, 38, 38, 0.3);
}

.theme-red .tool-item:hover .tool-link {
    background: rgba(220, 38, 38, 0.3);
}

.theme-red .tool-icon {
    background: rgba(220, 38, 38, 0.15);
    color: #ef4444;
}

.theme-red .stat-item {
    background: rgba(220, 38, 38, 0.2);
    color: #fca5a5;
}
```

**Usage:**
```html
<div class="cloud-gaming-widget theme-red">
```

---

## 🎯 Example 8: Lazy Loading Icons

**Scenario:** Improve performance by lazy loading Font Awesome.

```javascript
// Load Font Awesome only when widget is in viewport
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
            document.head.appendChild(link);
            
            observer.unobserve(entry.target);
        }
    });
});

const widget = document.querySelector('.cloud-gaming-widget');
if (widget) {
    observer.observe(widget);
}
```

---

## 🎯 Example 9: Multi-Language Support

**Scenario:** Translating widget text for international audiences.

```javascript
const translations = {
    en: {
        title: 'Cloud Gaming Tools',
        subtitle: 'Essential tools for gamers',
        tools: {
            latency: {
                title: 'Cloud Platforms Latency Tester',
                description: 'Test your connection speed'
            }
            // ... more tools
        }
    },
    es: {
        title: 'Herramientas de Juego en la Nube',
        subtitle: 'Herramientas esenciales para jugadores',
        tools: {
            latency: {
                title: 'Probador de Latencia',
                description: 'Prueba tu velocidad de conexión'
            }
        }
    },
    fr: {
        title: 'Outils de Jeu Cloud',
        subtitle: 'Outils essentiels pour les joueurs',
        tools: {
            latency: {
                title: 'Testeur de Latence',
                description: 'Testez votre vitesse de connexion'
            }
        }
    }
};

function translateWidget(lang = 'en') {
    const t = translations[lang];
    
    document.querySelector('.widget-title').textContent = t.title;
    document.querySelector('.widget-subtitle').textContent = t.subtitle;
    
    // Translate each tool
    Object.keys(t.tools).forEach(toolId => {
        const toolItem = document.querySelector(`[data-tool="${toolId}"]`);
        if (toolItem) {
            toolItem.querySelector('.tool-title').textContent = t.tools[toolId].title;
            toolItem.querySelector('.tool-description').textContent = t.tools[toolId].description;
        }
    });
}

// Usage: translateWidget('es');
```

---

## 🎯 Example 10: Theme Persistence

**Scenario:** Remember user's theme preference.

```javascript
function switchTheme(theme) {
    const widget = document.getElementById('cloud-gaming-widget');
    widget.className = 'cloud-gaming-widget theme-' + theme;
    
    // Save preference
    localStorage.setItem('widgetTheme', theme);
    
    // Animate transition
    widget.style.opacity = '0';
    setTimeout(() => {
        widget.style.transition = 'opacity 0.4s';
        widget.style.opacity = '1';
    }, 50);
}

// Load saved theme on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('widgetTheme') || 'dark';
    switchTheme(savedTheme);
});
```

---

## 💡 Pro Tips

### 1. Optimize for Speed
```html
<!-- Use preconnect for Font Awesome -->
<link rel="preconnect" href="https://cdnjs.cloudflare.com">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
```

### 2. Add Loading State
```css
.cloud-gaming-widget.loading {
    opacity: 0.5;
    pointer-events: none;
}

.cloud-gaming-widget.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 40px;
    height: 40px;
    margin: -20px 0 0 -20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
```

### 3. Keyboard Navigation
```javascript
document.querySelectorAll('.tool-link').forEach(link => {
    link.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            link.click();
        }
    });
});
```

### 4. Add Tooltips
```html
<div class="tool-icon" title="Test your connection to cloud gaming servers">
    <i class="fas fa-tachometer-alt"></i>
</div>
```

### 5. Print-Friendly Version
```css
@media print {
    .cloud-gaming-widget {
        page-break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ccc;
    }
    
    .tool-arrow,
    .widget-footer {
        display: none;
    }
}
```

---

## 🎨 Customization Snippets

### Rounded Corners
```css
.cloud-gaming-widget {
    border-radius: 30px;
}

.tool-item {
    border-radius: 24px;
}
```

### Larger Icons
```css
.tool-icon {
    width: 56px;
    height: 56px;
    font-size: 1.5rem;
}
```

### Compact Mode
```css
.cloud-gaming-widget.compact .tool-link {
    padding: 0.75rem 1rem;
}

.cloud-gaming-widget.compact .tool-icon {
    width: 36px;
    height: 36px;
    font-size: 1rem;
}

.cloud-gaming-widget.compact .tool-description {
    display: none;
}
```

---

**More examples coming soon! Feel free to customize these examples for your specific needs.**
