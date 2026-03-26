(function($) {
    'use strict';

    const CGTFrontend = {
        init: function() {
            this.initThemeToggle();
            this.initCardInteractions();
        },
        
        initThemeToggle: function() {
            const settings = window.cgtFrontend?.settings || {};
            const theme = settings.theme || 'auto';
            
            if (theme === 'auto') {
                const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                const setTheme = (e) => {
                    document.documentElement.setAttribute('data-theme', e.matches ? 'dark' : 'light');
                };
                setTheme(mediaQuery);
                mediaQuery.addEventListener('change', setTheme);
            } else {
                document.documentElement.setAttribute('data-theme', theme);
            }
            
            this.createThemeToggleButton();
        },
        
        createThemeToggleButton: function() {
            // Check if toggle already exists
            if (document.querySelector('.cgt-theme-toggle')) {
                return;
            }
            
            const toggle = document.createElement('button');
            toggle.className = 'cgt-theme-toggle';
            toggle.setAttribute('aria-label', 'Toggle theme');
            toggle.innerHTML = '<svg class="cgt-icon-light" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>';
            
            toggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('cgt-theme', newTheme);
            });
            
            document.body.appendChild(toggle);
            
            // Restore saved theme
            const savedTheme = localStorage.getItem('cgt-theme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            }
        },
        
        initCardInteractions: function() {
            const cards = document.querySelectorAll('.cgt-platform-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-2px)';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0)';
                });
            });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => CGTFrontend.init());
    } else {
        CGTFrontend.init();
    }
    
    window.CGTFrontend = CGTFrontend;

})(jQuery);
