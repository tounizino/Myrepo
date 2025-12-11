/**
 * Cloud Gaming Availability - Frontend
 */

jQuery(document).ready(function($) {
	'use strict';

	/**
	 * Platform filtering functionality
	 */
	$(document).on('click', '.cga-filter-btn', function(e) {
		e.preventDefault();
		const $btn = $(this);
		const $container = $btn.closest('.cga-container');
		const filter = $btn.data('filter');

		// Update active button state
		$container.find('.cga-filter-btn').removeClass('cga-active');
		$btn.addClass('cga-active');

		// Filter platform items
		if ('all' === filter) {
			$container.find('.cga-platform-item').removeClass('cga-hidden');
		} else {
			$container.find('.cga-platform-item').each(function() {
				const $item = $(this);
				if ($item.data('platform') === filter) {
					$item.removeClass('cga-hidden');
				} else {
					$item.addClass('cga-hidden');
				}
			});
		}
	});

	/**
	 * Smooth animations on page load
	 */
	$(window).on('load', function() {
		$('.cga-platform-item').each(function(index) {
			const $item = $(this);
			setTimeout(function() {
				$item.addClass('cga-animated');
			}, index * 50);
		});
	});

	/**
	 * Handle theme switching if user has preference
	 */
	const cgaInitTheme = function() {
		const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
		if (prefersDark) {
			// Theme switching logic could be enhanced here
		}
	};

	cgaInitTheme();

	/**
	 * Monitor system theme preference changes
	 */
	if (window.matchMedia) {
		window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', cgaInitTheme);
	}

	/**
	 * Lazy load platform logos
	 */
	if ('IntersectionObserver' in window) {
		const imageObserver = new IntersectionObserver((entries, observer) => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					const img = entry.target;
					if (img.dataset.src) {
						img.src = img.dataset.src;
						img.removeAttribute('data-src');
						observer.unobserve(img);
					}
				}
			});
		});

		document.querySelectorAll('.cga-platform-logo[data-src]').forEach(img => {
			imageObserver.observe(img);
		});
	}

	/**
	 * Keyboard accessibility for filter buttons
	 */
	$(document).on('keypress', '.cga-filter-btn', function(e) {
		if (13 === e.which || 32 === e.which) {
			e.preventDefault();
			$(this).click();
		}
	});

	/**
	 * Analytics tracking for play button clicks (optional)
	 */
	$(document).on('click', '.cga-play-button:not(.cga-unavailable)', function(e) {
		const platformName = $(this).closest('.cga-platform-item').find('.cga-platform-name').text();
		if (window.ga) {
			ga('send', 'event', 'Cloud Gaming', 'Play Button Click', platformName);
		}
	});

	/**
	 * Responsive grid adjustment
	 */
	const cgaAdjustGrid = function() {
		const width = $(window).width();
		$('.cga-platforms-grid').each(function() {
			const $grid = $(this);
			// Remove previous responsive classes
			$grid.removeClass('cga-responsive-mobile cga-responsive-tablet');

			if (width <= 480) {
				$grid.addClass('cga-responsive-mobile');
			} else if (width <= 768) {
				$grid.addClass('cga-responsive-tablet');
			}
		});
	};

	cgaAdjustGrid();
	$(window).on('resize', cgaAdjustGrid);

	/**
	 * Add touch-friendly hover states for mobile
	 */
	if ('ontouchstart' in window) {
		$(document).on('touchstart', '.cga-platform-item', function() {
			$(this).addClass('cga-touch-hover');
		});

		$(document).on('touchend', '.cga-platform-item', function() {
			$(this).removeClass('cga-touch-hover');
		});
	}

	/**
	 * Platform item interaction handler
	 */
	$(document).on('click', '.cga-platform-item', function(e) {
		// Don't trigger if clicking on the play button
		if (!$(e.target).closest('.cga-play-button').length) {
			const $playBtn = $(this).find('.cga-play-button:not(.cga-unavailable)');
			if ($playBtn.length) {
				$playBtn.focus();
			}
		}
	});

	/**
	 * Handle unavailable platform button click
	 */
	$(document).on('click', '.cga-play-button.cga-unavailable', function(e) {
		e.preventDefault();
		return false;
	});
});
