/**
 * Ultimate Blocks for Cloud Gaming - Gutenberg Blocks
 */

(function(wp) {
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var PanelBody = wp.components.PanelBody;
    var RangeControl = wp.components.RangeControl;
    var ToggleControl = wp.components.ToggleControl;
    var SelectControl = wp.components.SelectControl;
    var ServerSideRender = wp.serverSideRender;
    var __ = wp.i18n.__;
    var el = wp.element.createElement;
    var Fragment = wp.element.Fragment;

    // Latest Posts Grid Block
    registerBlockType('ubcg/latest-posts-grid', {
        title: __('Latest Posts Grid', 'ubcg'),
        description: __('Display latest articles in a responsive grid with pagination', 'ubcg'),
        icon: 'grid-view',
        category: 'widgets',
        attributes: {
            postsPerPage: { type: 'number', default: 9 },
            columns: { type: 'number', default: 3 },
            showImage: { type: 'boolean', default: true },
            showDate: { type: 'boolean', default: true },
            showExcerpt: { type: 'boolean', default: true },
            showAuthor: { type: 'boolean', default: false },
            showCategory: { type: 'boolean', default: true },
            pagination: { type: 'boolean', default: true },
            orderBy: { type: 'string', default: 'date' },
            order: { type: 'string', default: 'DESC' }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el(
                Fragment,
                {},
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        { title: __('Grid Settings', 'ubcg'), initialOpen: true },
                        el(RangeControl, {
                            label: __('Posts Per Page', 'ubcg'),
                            value: attributes.postsPerPage,
                            onChange: function(value) { setAttributes({ postsPerPage: value }); },
                            min: 1,
                            max: 50
                        }),
                        el(RangeControl, {
                            label: __('Columns', 'ubcg'),
                            value: attributes.columns,
                            onChange: function(value) { setAttributes({ columns: value }); },
                            min: 1,
                            max: 4
                        }),
                        el(SelectControl, {
                            label: __('Order By', 'ubcg'),
                            value: attributes.orderBy,
                            onChange: function(value) { setAttributes({ orderBy: value }); },
                            options: [
                                { label: __('Date', 'ubcg'), value: 'date' },
                                { label: __('Title', 'ubcg'), value: 'title' },
                                { label: __('Random', 'ubcg'), value: 'rand' }
                            ]
                        }),
                        el(SelectControl, {
                            label: __('Order', 'ubcg'),
                            value: attributes.order,
                            onChange: function(value) { setAttributes({ order: value }); },
                            options: [
                                { label: __('Descending', 'ubcg'), value: 'DESC' },
                                { label: __('Ascending', 'ubcg'), value: 'ASC' }
                            ]
                        })
                    ),
                    el(
                        PanelBody,
                        { title: __('Display Options', 'ubcg'), initialOpen: false },
                        el(ToggleControl, {
                            label: __('Show Featured Image', 'ubcg'),
                            checked: attributes.showImage,
                            onChange: function(value) { setAttributes({ showImage: value }); }
                        }),
                        el(ToggleControl, {
                            label: __('Show Date', 'ubcg'),
                            checked: attributes.showDate,
                            onChange: function(value) { setAttributes({ showDate: value }); }
                        }),
                        el(ToggleControl, {
                            label: __('Show Excerpt', 'ubcg'),
                            checked: attributes.showExcerpt,
                            onChange: function(value) { setAttributes({ showExcerpt: value }); }
                        }),
                        el(ToggleControl, {
                            label: __('Show Author', 'ubcg'),
                            checked: attributes.showAuthor,
                            onChange: function(value) { setAttributes({ showAuthor: value }); }
                        }),
                        el(ToggleControl, {
                            label: __('Show Category', 'ubcg'),
                            checked: attributes.showCategory,
                            onChange: function(value) { setAttributes({ showCategory: value }); }
                        }),
                        el(ToggleControl, {
                            label: __('Enable Pagination', 'ubcg'),
                            checked: attributes.pagination,
                            onChange: function(value) { setAttributes({ pagination: value }); }
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'ubcg/latest-posts-grid',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null;
        }
    });

    // Featured Posts Block
    registerBlockType('ubcg/featured-posts', {
        title: __('Featured Posts', 'ubcg'),
        description: __('Showcase featured content prominently', 'ubcg'),
        icon: 'star-filled',
        category: 'widgets',
        attributes: {
            postsPerPage: { type: 'number', default: 3 },
            layout: { type: 'string', default: 'horizontal' },
            showImage: { type: 'boolean', default: true },
            showDate: { type: 'boolean', default: true },
            showExcerpt: { type: 'boolean', default: true }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el(
                Fragment,
                {},
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        { title: __('Featured Posts Settings', 'ubcg'), initialOpen: true },
                        el(RangeControl, {
                            label: __('Number of Posts', 'ubcg'),
                            value: attributes.postsPerPage,
                            onChange: function(value) { setAttributes({ postsPerPage: value }); },
                            min: 1,
                            max: 10
                        }),
                        el(SelectControl, {
                            label: __('Layout', 'ubcg'),
                            value: attributes.layout,
                            onChange: function(value) { setAttributes({ layout: value }); },
                            options: [
                                { label: __('Horizontal', 'ubcg'), value: 'horizontal' },
                                { label: __('Grid', 'ubcg'), value: 'grid' }
                            ]
                        }),
                        el(ToggleControl, {
                            label: __('Show Featured Image', 'ubcg'),
                            checked: attributes.showImage,
                            onChange: function(value) { setAttributes({ showImage: value }); }
                        }),
                        el(ToggleControl, {
                            label: __('Show Date', 'ubcg'),
                            checked: attributes.showDate,
                            onChange: function(value) { setAttributes({ showDate: value }); }
                        }),
                        el(ToggleControl, {
                            label: __('Show Excerpt', 'ubcg'),
                            checked: attributes.showExcerpt,
                            onChange: function(value) { setAttributes({ showExcerpt: value }); }
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'ubcg/featured-posts',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null;
        }
    });

    // Category Showcase Block
    registerBlockType('ubcg/category-showcase', {
        title: __('Category Showcase', 'ubcg'),
        description: __('Display categories with counts and descriptions', 'ubcg'),
        icon: 'category',
        category: 'widgets',
        attributes: {
            columns: { type: 'number', default: 3 },
            showCount: { type: 'boolean', default: true },
            showDescription: { type: 'boolean', default: true }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el(
                Fragment,
                {},
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        { title: __('Category Settings', 'ubcg'), initialOpen: true },
                        el(RangeControl, {
                            label: __('Columns', 'ubcg'),
                            value: attributes.columns,
                            onChange: function(value) { setAttributes({ columns: value }); },
                            min: 1,
                            max: 4
                        }),
                        el(ToggleControl, {
                            label: __('Show Post Count', 'ubcg'),
                            checked: attributes.showCount,
                            onChange: function(value) { setAttributes({ showCount: value }); }
                        }),
                        el(ToggleControl, {
                            label: __('Show Description', 'ubcg'),
                            checked: attributes.showDescription,
                            onChange: function(value) { setAttributes({ showDescription: value }); }
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'ubcg/category-showcase',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null;
        }
    });

    // Tag Cloud Gaming Block
    registerBlockType('ubcg/tag-cloud-gaming', {
        title: __('Tag Cloud Gaming', 'ubcg'),
        description: __('Interactive tag cloud with post counts', 'ubcg'),
        icon: 'tag',
        category: 'widgets',
        attributes: {
            number: { type: 'number', default: 30 }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el(
                Fragment,
                {},
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        { title: __('Tag Cloud Settings', 'ubcg'), initialOpen: true },
                        el(RangeControl, {
                            label: __('Number of Tags', 'ubcg'),
                            value: attributes.number,
                            onChange: function(value) { setAttributes({ number: value }); },
                            min: 10,
                            max: 50
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'ubcg/tag-cloud-gaming',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null;
        }
    });

    // Gaming Hero Block
    registerBlockType('ubcg/gaming-hero', {
        title: __('Gaming Hero', 'ubcg'),
        description: __('Eye-catching hero section for homepage', 'ubcg'),
        icon: 'align-center',
        category: 'layout',
        attributes: {
            title: { type: 'string', default: 'Cloud Gaming News & Reviews' },
            subtitle: { type: 'string', default: 'Your ultimate source for cloud gaming content' },
            showButton: { type: 'boolean', default: true },
            buttonText: { type: 'string', default: 'Explore Now' }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var TextControl = wp.components.TextControl;

            return el(
                Fragment,
                {},
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        { title: __('Hero Settings', 'ubcg'), initialOpen: true },
                        el(TextControl, {
                            label: __('Title', 'ubcg'),
                            value: attributes.title,
                            onChange: function(value) { setAttributes({ title: value }); }
                        }),
                        el(TextControl, {
                            label: __('Subtitle', 'ubcg'),
                            value: attributes.subtitle,
                            onChange: function(value) { setAttributes({ subtitle: value }); }
                        }),
                        el(ToggleControl, {
                            label: __('Show Button', 'ubcg'),
                            checked: attributes.showButton,
                            onChange: function(value) { setAttributes({ showButton: value }); }
                        }),
                        attributes.showButton && el(TextControl, {
                            label: __('Button Text', 'ubcg'),
                            value: attributes.buttonText,
                            onChange: function(value) { setAttributes({ buttonText: value }); }
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'ubcg/gaming-hero',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null;
        }
    });

    // Additional simplified blocks
    ['review-card', 'game-specs', 'streaming-platforms', 'performance-stats', 'newsletter-signup'].forEach(function(blockName) {
        registerBlockType('ubcg/' + blockName, {
            title: __(blockName.split('-').map(function(word) {
                return word.charAt(0).toUpperCase() + word.slice(1);
            }).join(' '), 'ubcg'),
            icon: 'admin-generic',
            category: 'widgets',
            edit: function(props) {
                return el(ServerSideRender, {
                    block: 'ubcg/' + blockName,
                    attributes: props.attributes
                });
            },
            save: function() {
                return null;
            }
        });
    });

})(window.wp);
