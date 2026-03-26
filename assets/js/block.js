/**
 * Cloud Games Availability v2 - Gutenberg Block
 */

const { registerBlockType } = wp.blocks;
const { InspectorControls, BlockControls } = wp.editor;
const { Component } = wp.element;
const { SelectControl, RangeControl, ToggleControl, PanelBody, TextControl } = wp.components;
const { __ } = wp.i18n;

class CloudGamesAvailabilityBlock extends Component {
    constructor() {
        super(...arguments);
    }

    render() {
        const { attributes, setAttributes, isSelected } = this.props;
        const { game_id, group_by_availability, columns } = attributes;

        return [
            isSelected && (
                <InspectorControls key="inspector">
                    <PanelBody title={__('Settings', 'cloud-games-availability-v2')}>
                        <TextControl
                            label={__('Game ID', 'cloud-games-availability-v2')}
                            help={__('Enter the ID of the game to display', 'cloud-games-availability-v2')}
                            value={game_id}
                            onChange={(value) => setAttributes({ game_id: parseInt(value) })}
                            type="number"
                        />

                        <SelectControl
                            label={__('Group by Availability', 'cloud-games-availability-v2')}
                            value={group_by_availability}
                            options={[
                                { label: __('No', 'cloud-games-availability-v2'), value: 'no' },
                                { label: __('Yes', 'cloud-games-availability-v2'), value: 'yes' },
                            ]}
                            onChange={(value) => setAttributes({ group_by_availability: value })}
                        />

                        <SelectControl
                            label={__('Columns', 'cloud-games-availability-v2')}
                            value={columns}
                            options={[
                                { label: __('1 Column', 'cloud-games-availability-v2'), value: 1 },
                                { label: __('2 Columns', 'cloud-games-availability-v2'), value: 2 },
                                { label: __('3 Columns', 'cloud-games-availability-v2'), value: 3 },
                                { label: __('4 Columns', 'cloud-games-availability-v2'), value: 4 },
                                { label: __('6 Columns', 'cloud-games-availability-v2'), value: 6 },
                            ]}
                            onChange={(value) => setAttributes({ columns: parseInt(value) })}
                        />
                    </PanelBody>
                </InspectorControls>
            ),

            <div key="preview" className="cga-block-editor-wrapper">
                <div className="cga-block-controls">
                    <div>
                        <label>{__('Game ID:', 'cloud-games-availability-v2')}</label>
                        <input
                            type="number"
                            value={game_id}
                            onChange={(e) => setAttributes({ game_id: parseInt(e.target.value) })}
                        />
                    </div>
                    <div>
                        <label>{__('Columns:', 'cloud-games-availability-v2')}</label>
                        <select
                            value={columns}
                            onChange={(e) => setAttributes({ columns: parseInt(e.target.value) })}
                        >
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="6">6</option>
                        </select>
                    </div>
                </div>
                <div className="cga-block-preview">
                    {game_id > 0 ? (
                        <div dangerouslySetInnerHTML={{
                            __html: cgaBlockPreview
                                .replace('game_id="0"', `game_id="${game_id}"`)
                                .replace('columns="3"', `columns="${columns}"`)
                        }} />
                    ) : (
                        <div className="cga-block-empty-state">
                            <span className="dashicons dashicons-games"></span>
                            <p>{__('Enter a Game ID to preview the availability cards.', 'cloud-games-availability-v2')}</p>
                        </div>
                    )}
                </div>
            </div>
        ];
    }
}

// Register the block
registerBlockType('cloud-games-availability/availability-cards', {
    title: __('Cloud Games Availability', 'cloud-games-availability-v2'),
    description: __('Display cloud gaming platform availability cards', 'cloud-games-availability-v2'),
    icon: 'cloud',
    category: 'widgets',
    keywords: [
        __('cloud gaming', 'cloud-games-availability-v2'),
        __('games', 'cloud-games-availability-v2'),
        __('availability', 'cloud-games-availability-v2'),
    ],
    attributes: {
        game_id: {
            type: 'number',
            default: 0,
        },
        group_by_availability: {
            type: 'string',
            default: 'no',
        },
        columns: {
            type: 'number',
            default: 3,
        },
    },
    edit: CloudGamesAvailabilityBlock,
    save: function(props) {
        return null; // Render callback handles this
    },
});
