# 🤝 Contributing to Cloud Gaming Dashboard

Thank you for considering contributing to the Cloud Gaming Status & Launcher Dashboard! We welcome contributions from the community.

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Pull Request Process](#pull-request-process)
- [Bug Reports](#bug-reports)
- [Feature Requests](#feature-requests)

## 📜 Code of Conduct

### Our Pledge

We are committed to providing a welcoming and inspiring community for all. Please be respectful and considerate in your interactions.

### Our Standards

**Positive behavior includes:**
- Using welcoming and inclusive language
- Being respectful of differing viewpoints
- Gracefully accepting constructive criticism
- Focusing on what is best for the community
- Showing empathy towards other community members

**Unacceptable behavior includes:**
- Harassment, trolling, or discriminatory comments
- Publishing others' private information
- Professional or personal attacks
- Other conduct which could reasonably be considered inappropriate

## 🎯 How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues to avoid duplicates.

**Great bug reports include:**
- Clear, descriptive title
- Exact steps to reproduce
- Expected vs actual behavior
- Screenshots if applicable
- Browser/environment details
- Console errors

**Template:**
```markdown
**Bug Description**
A clear description of what the bug is.

**Steps To Reproduce**
1. Go to '...'
2. Click on '....'
3. Scroll down to '....'
4. See error

**Expected Behavior**
What you expected to happen.

**Screenshots**
If applicable, add screenshots.

**Environment**
- WordPress version:
- PHP version:
- Browser:
- Plugin version:

**Additional Context**
Any other relevant information.
```

### Suggesting Features

We love feature suggestions! Please provide:
- Clear use case
- Expected behavior
- Why this would be useful
- Mockups or examples (if applicable)

**Template:**
```markdown
**Feature Description**
Clear description of the feature.

**Use Case**
Why this feature would be useful.

**Proposed Solution**
How you envision this working.

**Alternatives Considered**
Other approaches you've thought about.

**Additional Context**
Mockups, examples, or related issues.
```

### Code Contributions

1. **Fork the repository**
2. **Create a feature branch** (`git checkout -b feature/AmazingFeature`)
3. **Make your changes**
4. **Commit your changes** (`git commit -m 'Add some AmazingFeature'`)
5. **Push to the branch** (`git push origin feature/AmazingFeature`)
6. **Open a Pull Request**

## 🛠️ Development Setup

### Prerequisites

- Git
- Modern web browser
- Code editor (VS Code recommended)
- WordPress installation (for plugin development)
- PHP 7.4+ and MySQL (for WordPress)

### Local Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/cloudloadout/cloud-gaming-dashboard.git
   cd cloud-gaming-dashboard
   ```

2. **For WordPress Development**
   ```bash
   # Copy to WordPress plugins directory
   cp -r . /path/to/wordpress/wp-content/plugins/cloud-gaming-dashboard/
   ```

3. **For Standalone Development**
   - Open `demo.html` in a browser
   - Or use a local server:
     ```bash
     python -m http.server 8000
     # Visit http://localhost:8000/demo.html
     ```

### Development Workflow

1. Create a feature branch
2. Make changes
3. Test thoroughly
4. Submit PR

## 📝 Coding Standards

### JavaScript

**Style Guide:**
- Use ES6+ features
- Use `const` and `let` (never `var`)
- Use arrow functions where appropriate
- Use template literals for strings
- Add JSDoc comments for functions
- Keep functions small and focused
- Use meaningful variable names

**Example:**
```javascript
/**
 * Check the status of a gaming service
 * @param {Object} service - The service to check
 * @returns {Promise<Object>} Status object
 */
async checkService(service) {
    try {
        const response = await fetch(service.statusUrl);
        const data = await response.json();
        return this.processStatus(data);
    } catch (error) {
        console.error(`Failed to check ${service.name}:`, error);
        return { status: 'unknown' };
    }
}
```

### CSS

**Style Guide:**
- Use meaningful class names
- Follow BEM methodology where appropriate
- Use `!important` sparingly (only in scoped styles)
- Group related properties
- Add comments for complex styles
- Use CSS Grid and Flexbox
- Mobile-first responsive design

**Example:**
```css
/* Status Card Component */
.cgd-status-card {
    background: rgba(255, 255, 255, 0.95) !important;
    border-radius: 16px !important;
    padding: 20px !important;
    transition: all 0.3s ease !important;
}

.cgd-status-card:hover {
    transform: translateY(-5px) !important;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3) !important;
}
```

### PHP (WordPress)

**Style Guide:**
- Follow WordPress Coding Standards
- Sanitize all inputs
- Escape all outputs
- Use nonces for security
- Add PHPDoc comments
- Use meaningful function names

**Example:**
```php
/**
 * Check service status via AJAX
 * 
 * @since 1.0.0
 * @return void
 */
public function checkServiceStatus() {
    // Verify nonce
    check_ajax_referer('cloud-gaming-nonce', 'nonce');
    
    // Sanitize input
    $service = sanitize_text_field($_POST['service']);
    
    // Fetch status
    $status = $this->fetchServiceStatus($service);
    
    // Return JSON
    wp_send_json_success($status);
}
```

### Commit Messages

**Format:**
```
<type>(<scope>): <subject>

<body>

<footer>
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation
- `style`: Formatting
- `refactor`: Code restructuring
- `test`: Adding tests
- `chore`: Maintenance

**Example:**
```
feat(dashboard): add historical status tracking

- Add database table for status history
- Implement chart visualization
- Add export functionality

Closes #123
```

## 🔍 Testing

### Manual Testing Checklist

- [ ] Test on Chrome, Firefox, Safari, Edge
- [ ] Test on mobile devices
- [ ] Test with slow network
- [ ] Test dark mode
- [ ] Test search functionality
- [ ] Test favorites system
- [ ] Test status updates
- [ ] Test WordPress integration
- [ ] Check console for errors
- [ ] Verify accessibility

### Browser Testing

Ensure compatibility with:
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers

## 🔄 Pull Request Process

### Before Submitting

1. **Update documentation** if needed
2. **Test thoroughly** across browsers
3. **Follow coding standards**
4. **Write clear commit messages**
5. **Update CHANGELOG.md** if applicable

### PR Description Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
- [ ] Tested on Chrome
- [ ] Tested on Firefox
- [ ] Tested on Safari
- [ ] Tested on mobile
- [ ] Tested dark mode

## Screenshots
If applicable

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Comments added for complex code
- [ ] Documentation updated
- [ ] No new warnings
- [ ] Added tests (if applicable)

## Related Issues
Closes #(issue)
```

### Review Process

1. Maintainer reviews PR
2. Feedback provided if needed
3. Changes requested or approved
4. PR merged to main branch
5. Contributor credited in CHANGELOG

## 🐛 Bug Triage

### Priority Levels

- **Critical**: Crashes, data loss, security issues
- **High**: Major features broken
- **Medium**: Minor features affected
- **Low**: Cosmetic issues

### Labels

- `bug`: Something isn't working
- `enhancement`: New feature or request
- `documentation`: Documentation improvements
- `good first issue`: Good for newcomers
- `help wanted`: Extra attention needed

## 💡 Feature Development

### Process

1. **Discuss**: Open an issue to discuss the feature
2. **Design**: Plan the implementation
3. **Develop**: Write the code
4. **Test**: Thoroughly test the feature
5. **Document**: Update documentation
6. **Submit**: Create a pull request

### Guidelines

- Keep features modular
- Maintain backward compatibility
- Consider performance impact
- Ensure mobile compatibility
- Add proper error handling
- Include documentation

## 📚 Resources

### Useful Links

- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [JavaScript Style Guide](https://github.com/airbnb/javascript)
- [CSS Guidelines](https://cssguidelin.es/)
- [Semantic Versioning](https://semver.org/)
- [Keep a Changelog](https://keepachangelog.com/)

### Learning Resources

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [MDN Web Docs](https://developer.mozilla.org/)
- [Web Accessibility](https://www.w3.org/WAI/)

## 🎓 First Time Contributors

Welcome! Here's how to get started:

1. **Find an issue** labeled `good first issue`
2. **Comment** that you'd like to work on it
3. **Wait for assignment** to avoid duplicate work
4. **Ask questions** if you need help
5. **Submit your PR** when ready

### Good First Issues

Look for:
- Documentation improvements
- CSS/styling enhancements
- Adding new services
- Translating text
- Fixing typos

## 🙏 Recognition

Contributors are credited in:
- CHANGELOG.md
- README.md (Contributors section)
- Release notes

## 📞 Getting Help

- **GitHub Issues**: Technical questions
- **Email**: support@cloudloadout.com
- **Documentation**: https://cloudloadout.com/docs

## 📄 License

By contributing, you agree that your contributions will be licensed under the GPL-2.0 License.

---

**Thank you for contributing to Cloud Gaming Dashboard!** 🎮☁️

Your efforts help make cloud gaming more accessible for everyone.
