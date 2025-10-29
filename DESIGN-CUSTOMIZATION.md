# Design Customization Guide

## Quick Customization Checklist

This guide shows you exactly where to make visual changes to match your brand.

---

## 1. Color Scheme (CSS Variables)

**Location:** Lines 44-60 in HTML file

### Current Light Theme:
```css
:root {
    --color-primary: #3b82f6;        /* Blue */
    --color-primary-dark: #2563eb;   /* Darker blue */
    --color-success: #10b981;        /* Green */
    --color-warning: #f59e0b;        /* Orange */
    --color-danger: #ef4444;         /* Red */
    --color-info: #06b6d4;           /* Cyan */
    --color-bg: #ffffff;             /* White background */
    --color-bg-secondary: #f9fafb;   /* Light gray background */
    --color-text: #1f2937;           /* Dark gray text */
    --color-text-secondary: #6b7280; /* Medium gray text */
    --color-border: #e5e7eb;         /* Light border */
}
```

### Example: Gaming Red/Black Theme
```css
:root {
    --color-primary: #dc2626;        /* Gaming red */
    --color-primary-dark: #991b1b;   
    --color-success: #22c55e;        
    --color-warning: #f59e0b;        
    --color-danger: #ef4444;         
    --color-info: #06b6d4;           
    --color-bg: #0f172a;             /* Dark navy background */
    --color-bg-secondary: #1e293b;   /* Slightly lighter navy */
    --color-text: #f8fafc;           /* Light text */
    --color-text-secondary: #94a3b8; 
    --color-border: #334155;         /* Dark border */
}
```

### Example: Purple/Neon Theme
```css
:root {
    --color-primary: #8b5cf6;        /* Purple */
    --color-primary-dark: #7c3aed;   
    --color-success: #10b981;        
    --color-warning: #fbbf24;        
    --color-danger: #f43f5e;         
    --color-info: #14b8a6;           
    --color-bg: #ffffff;             
    --color-bg-secondary: #faf5ff;   /* Light purple tint */
    --color-text: #1f2937;           
    --color-text-secondary: #6b7280; 
    --color-border: #ddd6fe;         /* Purple border */
}
```

---

## 2. Typography

### Font Stack (Line 62)
```css
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}
```

### Custom Font Options:

**Gaming/Tech Fonts:**
```css
/* Option 1: Google Fonts - Inter (modern, clean) */
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Option 2: Google Fonts - Rajdhani (gaming style) */
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
body {
    font-family: 'Rajdhani', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Option 3: Monospace for tech look */
body {
    font-family: 'Courier New', 'Monaco', monospace;
}
```

### Font Sizes:
```css
/* Base size - Line 62 */
body { font-size: 16px; }  /* Increase to 18px for better readability */

/* Heading sizes - Tailwind classes used throughout */
/* H1: text-2xl md:text-3xl (24px/30px mobile, 30px/36px desktop) */
/* H2: text-2xl (24px/30px) */
/* H3: text-xl (20px/28px) */
```

---

## 3. Header Customization

**Location:** Lines 274-287

### Current Header:
```html
<h1 class="text-2xl md:text-3xl font-bold text-gray-900">
    🎮 Cloud Gaming Connectivity Tool
</h1>
```

### Customization Options:

**Option 1: Remove emoji, add logo**
```html
<div class="flex items-center gap-3">
    <img src="/path/to/logo.png" alt="Your Brand" class="h-10 w-10">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
            Cloud Gaming Connectivity Tool
        </h1>
        <p class="text-sm text-gray-600">by YourBrandName</p>
    </div>
</div>
```

**Option 2: Gradient text**
```html
<h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
    Cloud Gaming Connectivity Tool
</h1>
```

**Option 3: Different emoji or icon**
```html
<!-- Gaming controller -->
🎮 Cloud Gaming Connectivity Tool

<!-- Network icon -->
🌐 Cloud Gaming Connectivity Tool

<!-- Speedometer -->
⚡ Cloud Gaming Connectivity Tool

<!-- Custom SVG icon -->
<svg class="w-8 h-8 text-blue-600" ...>...</svg>
```

---

## 4. Button Styles

**Location:** Lines 71-88

### Current Primary Button:
```css
.btn-primary {
    background-color: var(--color-primary);
    color: white;
}
```

### Custom Button Styles:

**Option 1: Gradient button**
```css
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-primary:hover:not(:disabled) {
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    transform: translateY(-2px);
}
```

**Option 2: Outlined button**
```css
.btn-primary {
    background-color: transparent;
    color: var(--color-primary);
    border: 2px solid var(--color-primary);
}

.btn-primary:hover:not(:disabled) {
    background-color: var(--color-primary);
    color: white;
}
```

**Option 3: Gaming-style glow**
```css
.btn-primary {
    background-color: #ef4444;
    color: white;
    box-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.btn-primary:hover:not(:disabled) {
    box-shadow: 0 0 30px rgba(239, 68, 68, 0.8);
}
```

---

## 5. Card Design

**Location:** Lines 67-74

### Current Cards:
```css
.card {
    background: var(--color-bg);
    border: 1px solid var(--color-border);
    border-radius: 0.5rem;
    box-shadow: var(--shadow-md);
}
```

### Customization Options:

**Option 1: Flat design (no shadow)**
```css
.card {
    background: var(--color-bg);
    border: 2px solid var(--color-border);
    border-radius: 0.5rem;
    box-shadow: none;
}
```

**Option 2: Elevated cards with strong shadow**
```css
.card {
    background: var(--color-bg);
    border: none;
    border-radius: 1rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}
```

**Option 3: Neon/gaming border**
```css
.card {
    background: var(--color-bg);
    border: 2px solid transparent;
    border-radius: 0.5rem;
    box-shadow: var(--shadow-md);
    background-image: 
        linear-gradient(var(--color-bg), var(--color-bg)),
        linear-gradient(135deg, #667eea, #764ba2);
    background-origin: border-box;
    background-clip: padding-box, border-box;
}
```

---

## 6. Status Badge Colors

**Location:** Lines 105-110

### Current Badges:
```css
.status-open { background-color: #d1fae5; color: #065f46; }
.status-closed { background-color: #fee2e2; color: #991b1b; }
.status-filtered { background-color: #fef3c7; color: #92400e; }
```

### Custom Badge Styles:

**Option 1: Solid colored badges**
```css
.status-open { 
    background-color: #10b981; 
    color: white; 
    font-weight: 700;
}
.status-closed { 
    background-color: #ef4444; 
    color: white; 
    font-weight: 700;
}
```

**Option 2: Outlined badges**
```css
.status-open { 
    background-color: transparent; 
    color: #10b981; 
    border: 2px solid #10b981;
}
```

---

## 7. Icon Customization

### Section Icons (Lines 336-338, 446-448, etc.)

Replace emoji/SVG icons with your own:

**Current:**
```html
<svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path ...></path>
</svg>
```

**Custom options:**
1. Use [Heroicons](https://heroicons.com/) - already used
2. Use [FontAwesome](https://fontawesome.com/)
3. Use custom SVG files
4. Use icon fonts (Material Icons, etc.)

**Example with FontAwesome:**
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Replace SVG with -->
<i class="fas fa-network-wired text-4xl text-blue-600"></i>
```

---

## 8. Background Patterns

Add texture to the page background:

**Location:** After line 62 (body styles)

**Option 1: Subtle grid pattern**
```css
body {
    background-color: var(--color-bg-secondary);
    background-image: 
        linear-gradient(0deg, transparent 24%, rgba(0,0,0,.02) 25%, rgba(0,0,0,.02) 26%, transparent 27%, transparent 74%, rgba(0,0,0,.02) 75%, rgba(0,0,0,.02) 76%, transparent 77%, transparent),
        linear-gradient(90deg, transparent 24%, rgba(0,0,0,.02) 25%, rgba(0,0,0,.02) 26%, transparent 27%, transparent 74%, rgba(0,0,0,.02) 75%, rgba(0,0,0,.02) 76%, transparent 77%, transparent);
    background-size: 50px 50px;
}
```

**Option 2: Gradient overlay**
```css
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Then add to main container */
main {
    background-color: rgba(255, 255, 255, 0.95);
    border-radius: 1rem;
}
```

**Option 3: Gaming-style particles (CSS only)**
```css
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 20%, rgba(236, 72, 153, 0.1) 0%, transparent 50%);
    pointer-events: none;
    z-index: -1;
}
```

---

## 9. Responsive Breakpoints

Tailwind breakpoints used:
- `sm:` - 640px
- `md:` - 768px
- `lg:` - 1024px
- `xl:` - 1280px

### Adjust Container Width:

**Location:** Lines 275, 290

**Current:**
```html
<div class="container mx-auto px-4 py-4 md:py-6 max-w-6xl">
```

**Options:**
```html
<!-- Narrower (better for reading) -->
<div class="container mx-auto px-4 py-4 md:py-6 max-w-4xl">

<!-- Wider (more spacious) -->
<div class="container mx-auto px-4 py-4 md:py-6 max-w-7xl">

<!-- Full width -->
<div class="container mx-auto px-4 py-4 md:py-6">
```

---

## 10. Animation & Transitions

### Add Entrance Animations:

**Location:** Add before closing `</style>` tag (around line 218)

```css
/* Fade in on load */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.5s ease-out;
}

/* Stagger animation for multiple cards */
section:nth-child(1) .card { animation-delay: 0.1s; }
section:nth-child(2) .card { animation-delay: 0.2s; }
section:nth-child(3) .card { animation-delay: 0.3s; }
```

### Add Hover Effects:

```css
/* Card lift on hover (already included) */
.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

/* Button pulse animation */
@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
    50% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
}

.btn-primary {
    animation: pulse 2s infinite;
}
```

---

## 11. Dark Mode Support

### Add Dark Mode Toggle:

**Location:** After line 286 (in header)

```html
<button id="darkModeToggle" class="ml-4 p-2 rounded-lg hover:bg-gray-100" aria-label="Toggle dark mode">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
    </svg>
</button>
```

### Add Dark Mode CSS:

**Location:** After line 60 (CSS variables)

```css
/* Dark mode variables */
@media (prefers-color-scheme: dark) {
    :root {
        --color-bg: #1f2937;
        --color-bg-secondary: #111827;
        --color-text: #f9fafb;
        --color-text-secondary: #d1d5db;
        --color-border: #374151;
    }
}

/* Manual dark mode class */
.dark-mode {
    --color-bg: #1f2937;
    --color-bg-secondary: #111827;
    --color-text: #f9fafb;
    --color-text-secondary: #d1d5db;
    --color-border: #374151;
}
```

### JavaScript Toggle:

**Location:** Before closing `</script>` tag (around line 1619)

```javascript
// Dark mode toggle
document.getElementById('darkModeToggle')?.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
});

// Load saved preference
if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark-mode');
}
```

---

## 12. Custom Logo Upload

**Location:** Lines 274-286 (header section)

Replace text with logo:

```html
<div class="flex items-center gap-4">
    <img 
        src="/path/to/your-logo.png" 
        alt="Your Brand Logo" 
        class="h-12 w-auto"
        loading="eager"
    >
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
            Cloud Gaming Connectivity Tool
        </h1>
        <p class="text-sm text-gray-600">Powered by YourBrand</p>
    </div>
</div>
```

---

## 13. Footer Customization

**Location:** Lines 1496-1518

### Add Social Media Links:

```html
<div class="flex justify-center gap-4 mt-4">
    <a href="https://twitter.com/yourbrand" class="text-gray-400 hover:text-blue-400" aria-label="Twitter">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
        </svg>
    </a>
    <a href="https://facebook.com/yourbrand" class="text-gray-400 hover:text-blue-400" aria-label="Facebook">
        <!-- Facebook icon SVG -->
    </a>
    <a href="https://youtube.com/yourbrand" class="text-gray-400 hover:text-blue-400" aria-label="YouTube">
        <!-- YouTube icon SVG -->
    </a>
</div>
```

---

## 14. Mobile-Specific Adjustments

### Increase Touch Targets:

**Location:** Line 71 (button styles)

```css
.btn {
    padding: 0.875rem 1.75rem;  /* Larger on mobile */
    min-height: 44px;           /* Apple HIG recommendation */
    min-width: 44px;
}

@media (min-width: 768px) {
    .btn {
        padding: 0.75rem 1.5rem;  /* Smaller on desktop */
    }
}
```

### Adjust Font Sizes for Mobile:

```css
@media (max-width: 768px) {
    body { font-size: 14px; }
    h1 { font-size: 1.5rem !important; }
    h2 { font-size: 1.25rem !important; }
}
```

---

## 15. Print Styles (For Documentation)

**Location:** Before closing `</style>` tag

```css
@media print {
    header { position: relative !important; }
    .card { page-break-inside: avoid; }
    button, .spinner { display: none !important; }
    .collapsible-content { max-height: none !important; }
}
```

---

## Quick Reference: Most Commonly Changed Elements

| Element | Location | What to Change |
|---------|----------|----------------|
| Primary Color | Line 46 | `--color-primary` |
| Font Family | Line 62 | `font-family` |
| Logo | Line 278 | Replace H1 with `<img>` |
| Button Style | Lines 71-88 | `.btn-primary` class |
| Card Shadow | Line 69 | `box-shadow` property |
| Container Width | Line 290 | `max-w-6xl` class |
| Footer Links | Line 1509 | Add your links |
| Emoji Icons | Throughout | Search for 🎮 🟢 etc. |

---

## Testing Your Customizations

1. **Cross-browser testing:**
   - Chrome, Firefox, Safari, Edge
   
2. **Mobile testing:**
   - iPhone (Safari)
   - Android (Chrome)
   - Tablet sizes

3. **Accessibility testing:**
   - Color contrast (use WebAIM checker)
   - Keyboard navigation
   - Screen reader compatibility

4. **Performance testing:**
   - PageSpeed Insights
   - Lighthouse audit

---

## Need More Help?

- Tailwind CSS Docs: https://tailwindcss.com/docs
- Color Palette Generator: https://coolors.co/
- Accessibility Checker: https://webaim.org/resources/contrastchecker/
- SVG Icons: https://heroicons.com/
