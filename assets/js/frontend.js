/**
 * Cloud Gaming Tracker - Frontend JavaScript
 * Version: 3.1.0
 */
(function($) {
	'use strict';

	var CGTFrontend = {
		init: function() {
			this.initThemeToggle();
			this.initCardInteractions();
		},

		/**
		 * Initialize theme toggle based on settings
		 */
		initThemeToggle: function() {
			var settings = window.cgtFrontend && window.cgtFrontend.settings ? window.cgtFrontend.settings : {};
			var theme = settings.theme || 'auto';

			// Set theme based on setting
			if (theme === 'auto') {
				var mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
				var setTheme = function(e) {
					CGTFrontend.setThemeAttribute(e.matches ? 'dark' : 'light');
				};
				setTheme(mediaQuery);
				mediaQuery.addEventListener('change', setTheme);
			} else {
				CGTFrontend.setThemeAttribute(theme);
			}

			// Create toggle button
			this.createThemeToggleButton();
		},

		/**
		 * Set data-theme attribute on wrapper
		 */
		setThemeAttribute: function(theme) {
			$('.cgt-wrapper').attr('data-theme', theme);
		},

		/**
		 * Create floating theme toggle button
		 */
		createThemeToggleButton: function() {
			// Check if button already exists
			if ($('.cgt-theme-toggle').length > 0) {
				return;
			}

			var toggle = document.createElement('button');
			toggle.className = 'cgt-theme-toggle';
			toggle.setAttribute('aria-label', 'Toggle theme');
			toggle.innerHTML = '<svg class="cgt-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>';

			toggle.addEventListener('click', function() {
				var $wrapper = $('.cgt-wrapper');
				var currentTheme = $wrapper.attr('data-theme');
				var newTheme = currentTheme === 'dark' ? 'light' : 'dark';
				
				CGTFrontend.setThemeAttribute(newTheme);
				localStorage.setItem('cgt-theme', newTheme);
			});

			document.body.appendChild(toggle);

			// Check for saved preference
			var savedTheme = localStorage.getItem('cgt-theme');
			if (savedTheme) {
				CGTFrontend.setThemeAttribute(savedTheme);
			}
		},

		/**
		 * Add hover interactions to cards
		 */
		initCardInteractions: function() {
			$('.cgt-platform-card').on('mouseenter', function() {
				$(this).css('transform', 'translateY(-2px)');
			}).on('mouseleave', function() {
				$(this).css('transform', 'translateY(0)');
			});
		}
	};

	// Initialize on DOM ready
	$(document).ready(function() {
		CGTFrontend.init();
	});

	// Expose to global
	window.CGTFrontend = CGTFrontend;

})(jQuery);