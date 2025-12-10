/**
 * Cloud Gaming Availability - Admin
 */

jQuery(document).ready(function($) {
	'use strict';

	/**
	 * Logo upload handler
	 */
	let currentPlatform = '';

	$(document).on('click', '.cga-upload-logo-btn', function(e) {
		e.preventDefault();
		currentPlatform = $(this).data('platform');

		const file_frame = wp.media.frames.file_frame = wp.media({
			title: cgaAdmin.platforms[currentPlatform].name + ' ' + 'Logo Upload',
			button: {
				text: 'Use this image',
			},
			multiple: false,
		});

		file_frame.on('select', function() {
			const attachment = file_frame.state().get('selection').first().toJSON();
			const $logoInput = $('[name="cga_platform_logos[' + currentPlatform + ']"]');
			const $preview = $logoInput.closest('.cga-logo-item').find('.cga-logo-preview');

			// Update logo preview
			$preview.html('<img src="' + attachment.url + '" alt="' + cgaAdmin.platforms[currentPlatform].name + '">');

			// Update input value
			$logoInput.val(attachment.url);

			// Show delete button
			const $deleteBtn = $logoInput.closest('.cga-logo-item').find('.cga-delete-logo-btn');
			if ($deleteBtn.length === 0) {
				const $uploadBtn = $logoInput.closest('.cga-logo-item').find('.cga-upload-logo-btn');
				$uploadBtn.after('<button type="button" class="button button-danger cga-delete-logo-btn" data-platform="' + currentPlatform + '">Delete</button>');
			} else {
				$deleteBtn.show();
			}

			// Show success message
			showAdminNotice('Logo updated successfully!', 'success');
		});

		file_frame.open();
	});

	/**
	 * Logo delete handler
	 */
	$(document).on('click', '.cga-delete-logo-btn', function(e) {
		e.preventDefault();
		const $btn = $(this);
		const platform = $btn.data('platform');
		const $logoInput = $('[name="cga_platform_logos[' + platform + ']"]');
		const $preview = $logoInput.closest('.cga-logo-item').find('.cga-logo-preview');

		// Confirm deletion
		if (!confirm('Are you sure you want to delete this logo?')) {
			return;
		}

		// Reset preview
		$preview.html('<div class="cga-logo-placeholder"></div>');

		// Clear input
		$logoInput.val('');

		// Hide delete button
		$btn.hide();

		// Show success message
		showAdminNotice('Logo deleted successfully!', 'success');
	});

	/**
	 * Settings form submission
	 */
	$(document).on('submit', '.cga-settings-form', function(e) {
		// Validate color inputs
		const $colorInputs = $(this).find('input[type="color"]');
		let isValid = true;

		$colorInputs.each(function() {
			const $input = $(this);
			const val = $input.val();

			if (!val.match(/^#[0-9a-f]{6}$/i)) {
				$input.css('border-color', 'red');
				isValid = false;
			}
		});

		if (!isValid) {
			e.preventDefault();
			showAdminNotice('Please fix color input errors!', 'error');
			return false;
		}
	});

	/**
	 * Checkbox metabox functionality
	 */
	$(document).on('click', '.cga-checkbox-label input[type="checkbox"]', function() {
		const $label = $(this).closest('.cga-checkbox-label');
		if ($(this).is(':checked')) {
			$label.addClass('cga-checked');
		} else {
			$label.removeClass('cga-checked');
		}
	});

	/**
	 * Initialize metabox checkboxes
	 */
	$('.cga-checkbox-label input[type="checkbox"]:checked').closest('.cga-checkbox-label').addClass('cga-checked');

	/**
	 * Show admin notice
	 */
	function showAdminNotice(message, type) {
		const className = 'notice-' + (type === 'success' ? 'success' : 'error');
		const $notice = $('<div class="notice ' + className + ' is-dismissible"><p>' + message + '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss</span></button></div>');

		// Insert notice at the top of the page
		$('.cga-settings-wrap').prepend($notice);

		// Handle dismiss
		$notice.on('click', '.notice-dismiss', function() {
			$notice.fadeOut(200, function() {
				$notice.remove();
			});
		});

		// Auto-remove success notices after 3 seconds
		if (type === 'success') {
			setTimeout(function() {
				$notice.fadeOut(200, function() {
					$notice.remove();
				});
			}, 3000);
		}
	}

	/**
	 * Platform selection UI improvements
	 */
	$(document).on('change', '.cga-metabox-content input[type="checkbox"]', function() {
		const checkedCount = $('.cga-metabox-content input[type="checkbox"]:checked').length;
		const totalCount = $('.cga-metabox-content input[type="checkbox"]').length;

		if (checkedCount > 0) {
			$('.cga-platform-checkboxes').addClass('cga-has-selection');
		} else {
			$('.cga-platform-checkboxes').removeClass('cga-has-selection');
		}
	});

	/**
	 * Color preview
	 */
	$(document).on('change', 'input[type="color"]', function() {
		const $input = $(this);
		const color = $input.val();

		// Update preview if needed
		if ($input.attr('id') === 'cga_button_color') {
			// Could update a preview of buttons here
		}
	});

	/**
	 * Settings section expand/collapse
	 */
	$(document).on('click', '.cga-settings-section h2', function() {
		$(this).siblings('.form-table').slideToggle(200);
	});

	/**
	 * Number input validation
	 */
	$(document).on('change', 'input[type="number"]', function() {
		const $input = $(this);
		const min = parseInt($input.attr('min')) || 0;
		const max = parseInt($input.attr('max')) || 999;
		let val = parseInt($input.val()) || 0;

		if (val < min) {
			val = min;
		} else if (val > max) {
			val = max;
		}

		$input.val(val);
	});

	/**
	 * Bulk platform selection
	 */
	const cgaAddBulkActions = function() {
		const $checkboxes = $('.cga-checkbox-label input[type="checkbox"]');
		if ($checkboxes.length === 0) return;

		const $wrapper = $checkboxes.closest('.cga-platform-checkboxes');
		if ($wrapper.find('.cga-bulk-actions').length > 0) return;

		const $bulkActions = $('<div class="cga-bulk-actions"><button type="button" class="button cga-select-all">Select All</button><button type="button" class="button cga-deselect-all">Deselect All</button></div>');
		$wrapper.before($bulkActions);

		$(document).on('click', '.cga-select-all', function(e) {
			e.preventDefault();
			$wrapper.find('input[type="checkbox"]').prop('checked', true).closest('.cga-checkbox-label').addClass('cga-checked');
		});

		$(document).on('click', '.cga-deselect-all', function(e) {
			e.preventDefault();
			$wrapper.find('input[type="checkbox"]').prop('checked', false).closest('.cga-checkbox-label').removeClass('cga-checked');
		});
	};

	cgaAddBulkActions();

	/**
	 * Logo grid responsiveness
	 */
	const cgaAdjustLogoGrid = function() {
		const width = $(window).width();
		const $grid = $('.cga-logos-grid');

		if (width <= 480) {
			$grid.css('grid-template-columns', '1fr');
		} else if (width <= 768) {
			$grid.css('grid-template-columns', 'repeat(auto-fill, minmax(120px, 1fr))');
		} else {
			$grid.css('grid-template-columns', 'repeat(auto-fill, minmax(150px, 1fr))');
		}
	};

	cgaAdjustLogoGrid();
	$(window).on('resize', cgaAdjustLogoGrid);

	/**
	 * Keyboard shortcuts
	 */
	$(document).on('keydown', function(e) {
		// Alt + S to submit form
		if (e.altKey && 83 === e.which) {
			e.preventDefault();
			$('.cga-settings-form').find('[type="submit"]').click();
		}
	});
});
