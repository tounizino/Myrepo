(function() {
    'use strict';

    const CGA_Frontend = {
        init: function() {
            this.handleThemeMode();
            this.addHoverEffects();
            this.initTooltips();
        },

        handleThemeMode: function() {
            const containers = document.querySelectorAll('.cga-container[data-theme="auto"]');
            
            containers.forEach(function(container) {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
                const updateTheme = function(e) {
                    if (e.matches) {
                        container.dataset.theme = 'dark';
                    } else {
                        container.dataset.theme = 'light';
                    }
                };
                
                prefersDark.addListener(updateTheme);
                updateTheme(prefersDark);
            });
        },

        addHoverEffects: function() {
            // Add subtle parallax effect on card hover
            const cards = document.querySelectorAll('.cga-card');
            
            cards.forEach(function(card) {
                card.addEventListener('mousemove', function(e) {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    
                    const rotateX = (y - centerY) / 20;
                    const rotateY = (centerX - x) / 20;
                    
                    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
                });
                
                card.addEventListener('mouseleave', function() {
                    card.style.transform = '';
                });
            });
        },

        initTooltips: function() {
            // Initialize tooltips if any
            const tooltipElements = document.querySelectorAll('[data-tooltip]');
            
            tooltipElements.forEach(function(element) {
                element.addEventListener('mouseenter', function() {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'cga-tooltip';
                    tooltip.textContent = element.dataset.tooltip;
                    document.body.appendChild(tooltip);
                    
                    const rect = element.getBoundingClientRect();
                    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
                    tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
                    
                    element._tooltip = tooltip;
                });
                
                element.addEventListener('mouseleave', function() {
                    if (element._tooltip) {
                        element._tooltip.remove();
                        element._tooltip = null;
                    }
                });
            });
        }
    };

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            CGA_Frontend.init();
        });
    } else {
        CGA_Frontend.init();
    }
})();
