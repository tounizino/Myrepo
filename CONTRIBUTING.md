# Contributing to Ultimate Blocks for Cloud Gaming

Thank you for your interest in contributing! This document provides guidelines for contributing to the plugin.

## Getting Started

### Prerequisites

- WordPress 6.0 or higher
- PHP 7.4 or higher
- Node.js and npm (for development)
- Git

### Development Setup

1. Clone the repository
```bash
git clone https://github.com/your-repo/ultimate-blocks-cloud-gaming.git
cd ultimate-blocks-cloud-gaming
```

2. Install development dependencies (if using npm)
```bash
npm install
```

3. Symlink to WordPress plugins directory
```bash
ln -s $(pwd) /path/to/wordpress/wp-content/plugins/ultimate-blocks-cloud-gaming
```

## Code Standards

### PHP Code Standards

Follow WordPress Coding Standards:
- Use proper indentation (4 spaces, no tabs)
- Use meaningful variable and function names
- Add inline comments for complex logic
- Use WordPress functions when available
- Sanitize input, escape output

Example:
```php
function ubcg_sanitize_input($input) {
    return sanitize_text_field($input);
}
```

### JavaScript Code Standards

- Use modern ES6+ syntax
- Use strict mode
- Add JSDoc comments
- Handle errors gracefully

Example:
```javascript
'use strict';

/**
 * Process form submission
 * @param {Event} e - Form submit event
 */
function handleSubmit(e) {
    e.preventDefault();
    // Your code here
}
```

### CSS Code Standards

- Use BEM naming convention: `.ubcg-block__element--modifier`
- Mobile-first approach
- Use CSS custom properties (variables)
- Avoid !important unless absolutely necessary

Example:
```css
.ubcg-post-card {
    /* Block */
}

.ubcg-post-card__title {
    /* Element */
}

.ubcg-post-card--featured {
    /* Modifier */
}
```

## File Structure

```
ultimate-blocks-cloud-gaming/
├── admin/
│   └── settings-page.php
├── assets/
│   ├── css/
│   │   ├── frontend.css
│   │   ├── admin.css
│   │   └── editor.css
│   └── js/
│       ├── frontend.js
│       ├── admin.js
│       └── blocks.js
├── blocks/
│   └── [block-name]/
│       └── block.json
├── includes/
│   ├── class-block-renderer.php
│   ├── class-post-queries.php
│   ├── class-ajax-handlers.php
│   ├── class-helpers.php
│   └── widgets/
│       └── class-widget-*.php
├── languages/
├── ultimate-blocks-cloud-gaming.php
├── uninstall.php
├── README.md
└── .gitignore
```

## Making Changes

### 1. Create a Branch

```bash
git checkout -b feature/your-feature-name
```

Branch naming:
- `feature/` - New features
- `fix/` - Bug fixes
- `docs/` - Documentation changes
- `refactor/` - Code refactoring

### 2. Make Your Changes

- Write clean, documented code
- Follow coding standards
- Test thoroughly

### 3. Test Your Changes

- Test in different browsers
- Test on mobile devices
- Test with different WordPress themes
- Verify no JavaScript errors
- Check PHP error logs

### 4. Commit Your Changes

Use clear, descriptive commit messages:

```bash
git add .
git commit -m "Add: New feature for X"
```

Commit message prefixes:
- `Add:` - New features
- `Fix:` - Bug fixes
- `Update:` - Updates to existing features
- `Remove:` - Removed features
- `Refactor:` - Code refactoring
- `Docs:` - Documentation changes

### 5. Submit a Pull Request

1. Push your branch to GitHub
```bash
git push origin feature/your-feature-name
```

2. Create a Pull Request on GitHub
3. Describe your changes
4. Link any related issues
5. Wait for review

## Adding New Blocks

### 1. Create Block Directory

```bash
mkdir blocks/your-block-name
```

### 2. Create block.json

```json
{
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 2,
    "name": "ubcg/your-block-name",
    "version": "1.0.0",
    "title": "Your Block Title",
    "category": "widgets",
    "icon": "admin-generic",
    "textdomain": "ubcg"
}
```

### 3. Add Render Function

In `includes/class-block-renderer.php`:

```php
public static function render_your_block_name($attributes) {
    $defaults = array(
        'option1' => 'default_value'
    );
    
    $atts = wp_parse_args($attributes, $defaults);
    
    ob_start();
    
    // Your rendering code here
    
    return ob_get_clean();
}
```

### 4. Register Block

In `ultimate-blocks-cloud-gaming.php`, add to `$blocks` array:

```php
$blocks = array(
    // ... existing blocks
    'your-block-name'
);
```

### 5. Add JavaScript

In `assets/js/blocks.js`:

```javascript
registerBlockType('ubcg/your-block-name', {
    title: __('Your Block Title', 'ubcg'),
    description: __('Block description', 'ubcg'),
    icon: 'admin-generic',
    category: 'widgets',
    attributes: {
        // Define attributes
    },
    edit: function(props) {
        // Editor interface
    },
    save: function() {
        return null; // Server-side rendered
    }
});
```

### 6. Add Styles

In `assets/css/frontend.css`:

```css
.ubcg-your-block-name {
    /* Your styles */
}
```

## Testing

### Manual Testing Checklist

- [ ] Block appears in editor
- [ ] Block settings work correctly
- [ ] Block renders on frontend
- [ ] Responsive on mobile/tablet/desktop
- [ ] Works with default WordPress themes
- [ ] No JavaScript console errors
- [ ] No PHP errors in debug.log
- [ ] Accessible (keyboard navigation, screen readers)
- [ ] SEO-friendly markup

### Browser Testing

Test in:
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

### WordPress Testing

Test with:
- WordPress 6.0+
- Classic Editor plugin
- Different themes (TwentyTwentyFour, Astra, GeneratePress)

## Documentation

### Updating Documentation

When adding features:
1. Update README.md
2. Update INSTALLATION.md if needed
3. Add examples to EXAMPLES.md
4. Update CHANGELOG.md

### Writing Examples

Provide:
- Use case description
- Configuration example
- Code snippet if applicable
- Screenshot reference

## Reporting Issues

### Bug Reports

Include:
- WordPress version
- PHP version
- Theme name and version
- Other active plugins
- Steps to reproduce
- Expected behavior
- Actual behavior
- Screenshots/videos if applicable

### Feature Requests

Include:
- Use case description
- Expected behavior
- Mockups/examples if available
- Priority level

## Code Review Process

1. Automated checks run on PR
2. Maintainer reviews code
3. Feedback provided
4. Changes requested if needed
5. Approved and merged

## Release Process

1. Update version numbers
2. Update CHANGELOG.md
3. Test thoroughly
4. Create release tag
5. Upload to WordPress.org
6. Announce release

## Community Guidelines

- Be respectful and inclusive
- Help others when possible
- Give constructive feedback
- Follow the code of conduct

## Questions?

- Email: support@example.com
- Discussions: GitHub Discussions
- Slack: [Your Slack channel]

## License

By contributing, you agree that your contributions will be licensed under the GPL v2 or later license.

---

Thank you for contributing to Ultimate Blocks for Cloud Gaming!
