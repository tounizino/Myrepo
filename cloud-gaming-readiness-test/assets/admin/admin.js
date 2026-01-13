jQuery(document).ready(function ($) {
	'use strict';

	// Auto-populate slug from name on platform edit
	const $nameInput = $('#name');
	const $slugInput = $('#slug');

	if ($nameInput.length && $slugInput.length && !$slugInput.val()) {
		$nameInput.on('input', function () {
			const name = $(this).val();
			const slug = name
				.toLowerCase()
				.replace(/[^a-z0-9\s-]/g, '')
				.trim()
				.replace(/\s+/g, '-');
			$slugInput.val(slug);
		});
	}
});
