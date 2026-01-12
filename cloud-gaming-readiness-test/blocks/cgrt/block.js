(function (blocks, element, blockEditor, components) {
	const { registerBlockType } = blocks;
	const { createElement } = element;
	const { InspectorControls } = blockEditor;
	const { PanelBody, TextControl } = components;

	registerBlockType('cgrt/cloud-gaming-readiness-test', {
		title: 'Cloud Gaming Readiness Test',
		description: 'Professional cloud gaming network diagnostic test.',
		category: 'widgets',
		icon: 'performance',
		keywords: ['cloud', 'gaming', 'readiness', 'network', 'latency'],
		attributes: {
			platform: {
				type: 'string',
				default: 'auto',
			},
		},
		edit: function (props) {
			const { attributes, setAttributes } = props;

			return createElement(
				'div',
				{ className: props.className },
				[
					createElement(
						InspectorControls,
						{ key: 'inspector' },
						createElement(
							PanelBody,
							{ title: 'Settings', initialOpen: true },
							createElement(TextControl, {
								label: 'Platform (slug)',
								value: attributes.platform,
								onChange: function (value) {
									setAttributes({ platform: value });
								},
								help: 'Platform slug (e.g., "auto", "geforce-now"). Leave "auto" to allow user selection.',
							})
						)
					),
					createElement(
						'div',
						{
							key: 'preview',
							style: {
								border: '1px solid #ddd',
								borderRadius: '8px',
								padding: '20px',
								background: '#f9fafb',
								textAlign: 'center',
							},
						},
						[
							createElement('div', { style: { fontSize: '18px', fontWeight: 600 } }, 'Cloud Gaming Readiness Test'),
							createElement('div', { style: { marginTop: '8px', color: '#6b7280', fontSize: '14px' } }, 'Front-end block preview'),
							createElement(
								'div',
								{ style: { marginTop: '12px', fontSize: '13px', color: '#9ca3af' } },
								`Platform: ${attributes.platform}`
							),
						]
					),
				]
			);
		},
		save: function () {
			// Render via PHP callback.
			return null;
		},
	});
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components);
