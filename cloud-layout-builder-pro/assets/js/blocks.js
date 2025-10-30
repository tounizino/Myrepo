/**
 * Cloud Layout Builder Pro - Gutenberg Blocks
 */
(function() {
    const { registerBlockType } = wp.blocks;
    const { ServerSideRender } = wp.serverSideRender || wp.components;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, SelectControl, RangeControl, ToggleControl, TextControl } = wp.components;
    const { __ } = wp.i18n;

    // Latest Articles Block
    registerBlockType('cloud-layout-builder/latest-articles', {
        title: __('Latest Articles Grid', 'cloud-layout-builder-pro'),
        icon: 'grid-view',
        category: 'cloud-layout-builder',
        attributes: {
            postsToShow: { type: 'number', default: 9 },
            columns: { type: 'number', default: 3 },
            layout: { type: 'string', default: 'grid' }
        },
        edit: function(props) {
            return [
                <InspectorControls key="inspector">
                    <PanelBody title={__('Settings', 'cloud-layout-builder-pro')}>
                        <RangeControl
                            label={__('Posts to Show', 'cloud-layout-builder-pro')}
                            value={props.attributes.postsToShow}
                            onChange={(value) => props.setAttributes({ postsToShow: value })}
                            min={3}
                            max={24}
                        />
                        <RangeControl
                            label={__('Columns', 'cloud-layout-builder-pro')}
                            value={props.attributes.columns}
                            onChange={(value) => props.setAttributes({ columns: value })}
                            min={1}
                            max={4}
                        />
                        <SelectControl
                            label={__('Layout', 'cloud-layout-builder-pro')}
                            value={props.attributes.layout}
                            options={[
                                { label: 'Grid', value: 'grid' },
                                { label: 'Masonry', value: 'masonry' }
                            ]}
                            onChange={(value) => props.setAttributes({ layout: value })}
                        />
                    </PanelBody>
                </InspectorControls>,
                <div key="preview" className="clbp-block-preview">
                    {ServerSideRender ? (
                        <ServerSideRender block="cloud-layout-builder/latest-articles" attributes={props.attributes} />
                    ) : (
                        <div className="clbp-block-placeholder">
                            <p>{__('Latest Articles Grid', 'cloud-layout-builder-pro')}</p>
                        </div>
                    )}
                </div>
            ];
        },
        save: function() {
            return null;
        }
    });

    // Featured Hero Block
    registerBlockType('cloud-layout-builder/featured-hero', {
        title: __('Featured Hero', 'cloud-layout-builder-pro'),
        icon: 'cover-image',
        category: 'cloud-layout-builder',
        attributes: {
            postSource: { type: 'string', default: 'latest' }
        },
        edit: function(props) {
            return <div className="clbp-block-preview">
                <div className="clbp-block-placeholder">
                    <p>{__('Featured Hero Article', 'cloud-layout-builder-pro')}</p>
                </div>
            </div>;
        },
        save: function() {
            return null;
        }
    });

})();
