const { registerBlockType } = wp.blocks;
const { InspectorControls, BlockControls, AlignmentToolbar ,ColorPalette } = wp.blockEditor;
const { PanelBody, TextControl } = wp.components;
const { createElement } = wp.element;
const { __ } = wp.i18n;

registerBlockType('blocks/gutenberg-orderdetails-blocks', {
    title: __('Order Details', 'gutenberg-orderdetails-blocks'),
    icon: 'list-view',
    category: 'common',
    keywords: [__('order', 'gutenberg-orderdetails-blocks'), __('woocommerce', 'gutenberg-orderdetails-blocks'), __('form', 'gutenberg-orderdetails-blocks')],
    attributes: {
        orderDetailsMaxWidth: {
            type: 'string',
            default: '1100',
        },
        orderDetailsMarginTop: {
            type: 'string',
            default: '',
        },
        orderDetailsMarginRight: {
            type: 'string',
            default: '',
        },
        orderDetailsMarginBottom: {
            type: 'string',
            default: '',
        },
        orderDetailsMarginLeft: {
            type: 'string',
            default: '',
        },
        orderDetailsPaddingTop: {
            type: 'string',
            default: '10',
        },
        orderDetailsPaddingRight: {
            type: 'string',
            default: '10',
        },
        orderDetailsPaddingBottom: {
            type: 'string',
            default: '10',
        },
        orderDetailsPaddingLeft: {
            type: 'string',
            default: '10',
        },
        textAlign: {
            type: 'string',
            default: 'left',
        },
        backgroundColor: {
            type: 'string',
            default: '#ffffff', // Default to white
        },
        textColor: {
            type: 'string',
            default: '#000000', // Default to black
        },
    },
    edit: function({ attributes, setAttributes }) {
        const {
            orderDetailsMaxWidth,
            orderDetailsMarginTop,
            orderDetailsMarginRight,
            orderDetailsMarginBottom,
            orderDetailsMarginLeft,
            orderDetailsPaddingTop,
            orderDetailsPaddingRight,
            orderDetailsPaddingBottom,
            orderDetailsPaddingLeft,
            textAlign,
            backgroundColor,
            textColor,
        } = attributes;


        const onChangeBackgroundColor = (newColor) => {
            setAttributes({ backgroundColor: newColor });
        };

        const onChangeTextColor = (newColor) => {
            setAttributes({ textColor: newColor });
        };

        return (
            createElement('div', { className: 'fluxtore-orderdetails-block' },
                createElement(BlockControls, {},
                    createElement(AlignmentToolbar, {
                        value: textAlign,
                        onChange: (newAlign) => setAttributes({ textAlign: newAlign })
                    })
                ),
                createElement(InspectorControls, {},
                    createElement(PanelBody, { title: __('Settings', 'gutenberg-orderdetails-blocks') },
                        createElement(TextControl, {
                            label: __('Max Width (px)', 'gutenberg-orderdetails-blocks'),
                            value: orderDetailsMaxWidth,
                            onChange: (newValue) => setAttributes({ orderDetailsMaxWidth: newValue }),
                            type: 'number',
                            step: '1',
                        }),
                        createElement(TextControl, {
                            label: __('Margin Top (px)', 'gutenberg-orderdetails-blocks'),
                            value: orderDetailsMarginTop,
                            onChange: (newValue) => setAttributes({ orderDetailsMarginTop: newValue }),
                            type: 'number',
                            step: '1',
                        }),
                        createElement(TextControl, {
                            label: __('Margin Right (px)', 'gutenberg-orderdetails-blocks'),
                            value: orderDetailsMarginRight,
                            onChange: (newValue) => setAttributes({ orderDetailsMarginRight: newValue }),
                            type: 'number',
                            step: '1',
                        }),
                        createElement(TextControl, {
                            label: __('Margin Bottom (px)', 'gutenberg-orderdetails-blocks'),
                            value: orderDetailsMarginBottom,
                            onChange: (newValue) => setAttributes({ orderDetailsMarginBottom: newValue }),
                            type: 'number',
                            step: '1',
                        }),
                        createElement(TextControl, {
                            label: __('Margin Left (px)', 'gutenberg-orderdetails-blocks'),
                            value: orderDetailsMarginLeft,
                            onChange: (newValue) => setAttributes({ orderDetailsMarginLeft: newValue }),
                            type: 'number',
                            step: '1',
                        }),
                        createElement(TextControl, {
                            label: __('Padding Top (px)', 'gutenberg-orderdetails-blocks'),
                            value: orderDetailsPaddingTop,
                            onChange: (newValue) => setAttributes({ orderDetailsPaddingTop: newValue }),
                            type: 'number',
                            step: '1',
                        }),
                        createElement(TextControl, {
                            label: __('Padding Right (px)', 'gutenberg-orderdetails-blocks'),
                            value: orderDetailsPaddingRight,
                            onChange: (newValue) => setAttributes({ orderDetailsPaddingRight: newValue }),
                            type: 'number',
                            step: '1',
                        }),
                        createElement(TextControl, {
                            label: __('Padding Bottom (px)', 'gutenberg-orderdetails-blocks'),
                            value: orderDetailsPaddingBottom,
                            onChange: (newValue) => setAttributes({ orderDetailsPaddingBottom: newValue }),
                            type: 'number',
                            step: '1',
                        }),
                        createElement(TextControl, {
                            label: __('Padding Left (px)', 'gutenberg-orderdetails-blocks'),
                            value: orderDetailsPaddingLeft,
                            onChange: (newValue) => setAttributes({ orderDetailsPaddingLeft: newValue }),
                            type: 'number',
                            step: '1',
                        }),
                        createElement(ColorPalette, {
                            label: __('Background Color', 'gutenberg-orderdetails-blocks'),
                            colors: [
                                { name: 'White', color: '#ffffff' },
                                { name: 'Black', color: '#000000' },
                                { name: 'Red', color: '#ff0000' },
                                // Add more colors as needed
                            ],
                            value: backgroundColor,
                            onChange: onChangeBackgroundColor,
                        }),
                        createElement(ColorPalette, {
                            label: __('Text Color', 'gutenberg-orderdetails-blocks'),
                            colors: [
                                { name: 'Black', color: '#000000' },
                                { name: 'White', color: '#ffffff' },
                                { name: 'Blue', color: '#0000ff' },
                                // Add more colors as needed
                            ],
                            value: textColor,
                            onChange: onChangeTextColor,
                        }),
                    ),
                ),
                createElement('div', {
                    className: 'fluxtore-orderdetails-block',
                    style: {
                        maxWidth: `${orderDetailsMaxWidth}px`,
                        margin: `${orderDetailsMarginTop}px ${orderDetailsMarginRight}px ${orderDetailsMarginBottom}px ${orderDetailsMarginLeft}px`,
                        paddingTop: `${orderDetailsPaddingTop}px`,
                        paddingRight: `${orderDetailsPaddingRight}px`,
                        paddingBottom: `${orderDetailsPaddingBottom}px`,
                        paddingLeft: `${orderDetailsPaddingLeft}px`,
                        textAlign: textAlign,
                        border: '1px solid black',
                        backgroundColor:backgroundColor,
                        color:textColor
                    },
                },
                    'Order Details'
                )
            )
        );
    },
    save: function() {
        return null; // Server-side rendering
    },
});
