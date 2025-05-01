if (!window.registerBlockType) {
    window.registerBlockType = wp.blocks.registerBlockType;
}

const { InspectorControls } = wp.blockEditor;
const { PanelBody, ColorPicker, SelectControl, TextControl } = wp.components;
const { createElement } = wp.element;
const { useState } = wp.element;
const { __ } = wp.i18n;

registerBlockType('blocks/gutenberg-blocks', {
    title: 'Upsell/Downsell Yes Button',
    icon: 'yes',
    category: 'common',
    attributes: {
        buttonTextYes: {
            type: 'string',
            default: 'Yes, I want to take this offer now ...',
        },
        buttonBgColor: {
            type: 'string',
            default: '#000',
        },
        buttonTextColor: {
            type: 'string',
            default: '#fff',
        },
        buttonHeight: {
            type: 'number',
            default: 40,
        },
        buttonAlignmentHorizontal: {
            type: 'string',
            default: 'center', // Default horizontal alignment
        },
        buttonAlignmentVertical: {
            type: 'string',
            default: 'middle', // Default vertical alignment
        },
        textAlign: {
            type: 'string',
            default: 'center', // Default text alignment
        },
        containerHeight: {
            type: 'number',
            default: 200, // Default container height
        },
        fontSize: {
            type: 'number',
            default: 16, // Default font size
        },
        fontWeight: {
            type: 'number',
            default: 400, // Default font weight
        },
    },
    edit({ attributes, setAttributes }) {
        const { buttonTextYes, buttonBgColor, buttonTextColor, buttonHeight, buttonAlignmentHorizontal, buttonAlignmentVertical, textAlign, containerHeight, fontSize, fontWeight } = attributes;

        const onChangeButtonText = (event) => {
            setAttributes({ buttonTextYes: event.target.value });
        };

        const onChangeBgColor = (newColor) => {
            setAttributes({ buttonBgColor: newColor });
        };

        const onChangeTextColor = (newColor) => {
            setAttributes({ buttonTextColor: newColor });
        };

        const onChangeHeight = (newHeight) => {
            setAttributes({ buttonHeight: parseInt(newHeight) });
        };

        const onChangeAlignmentHorizontal = (newAlignment) => {
            setAttributes({ buttonAlignmentHorizontal: newAlignment });
        };

        const onChangeAlignmentVertical = (newAlignment) => {
            setAttributes({ buttonAlignmentVertical: newAlignment });
        };

        const onChangeTextAlignment = (newAlignment) => {
            setAttributes({ textAlign: newAlignment });
        };

        const onChangeContainerHeight = (newHeight) => {
            setAttributes({ containerHeight: parseInt(newHeight) });
        };

        const onChangeFontSize = (newSize) => {
            setAttributes({ fontSize: parseInt(newSize) });
        };

        const onChangeFontWeight = (newWeight) => {
            setAttributes({ fontWeight: parseInt(newWeight) });
        };

        const buttonWidth = buttonTextYes.length * (fontSize * 0.6);
        return (
            createElement('div', {
                style: {
                    height: containerHeight + 'px',
                    display: 'flex',
                    justifyContent: buttonAlignmentHorizontal,
                    alignItems: buttonAlignmentVertical === 'top' ? 'flex-start' : buttonAlignmentVertical === 'bottom' ? 'flex-end' : 'center',
                }
            },
                createElement(InspectorControls, {},
                    createElement(PanelBody, { title: __('Background Color', 'gutenberg-blocks') },
                        createElement(ColorPicker, {
                            color: buttonBgColor,
                            onChangeComplete: (color) => onChangeBgColor(color.hex),
                            disableAlpha: true,
                        })
                    ),
                    createElement(PanelBody, { title: __('Text Color', 'gutenberg-blocks') },
                        createElement(ColorPicker, {
                            color: buttonTextColor,
                            onChangeComplete: (color) => onChangeTextColor(color.hex),
                            disableAlpha: true,
                        })
                    ),
                    createElement(PanelBody, { title: __('Alignment Settings', 'gutenberg-blocks') },
                        createElement(SelectControl, {
                            label: __('Horizontal Alignment', 'gutenberg-blocks'),
                            value: buttonAlignmentHorizontal,
                            options: [
                                { label: __('Left', 'gutenberg-blocks'), value: 'flex-start' },
                                { label: __('Center', 'gutenberg-blocks'), value: 'center' },
                                { label: __('Right', 'gutenberg-blocks'), value: 'flex-end' },
                            ],
                            onChange: onChangeAlignmentHorizontal,
                        }),
                        createElement(SelectControl, {
                            label: __('Vertical Alignment', 'gutenberg-blocks'),
                            value: buttonAlignmentVertical,
                            options: [
                                { label: __('Top', 'gutenberg-blocks'), value: 'top' },
                                { label: __('Middle', 'gutenberg-blocks'), value: 'middle' },
                                { label: __('Bottom', 'gutenberg-blocks'), value: 'bottom' },
                            ],
                            onChange: onChangeAlignmentVertical,
                        }),
                        createElement(SelectControl, {
                            label: __('Text Alignment', 'gutenberg-blocks'),
                            value: textAlign,
                            options: [
                                { label: __('Left', 'gutenberg-blocks'), value: 'left' },
                                { label: __('Center', 'gutenberg-blocks'), value: 'center' },
                                { label: __('Right', 'gutenberg-blocks'), value: 'right' },
                            ],
                            onChange: onChangeTextAlignment,
                        })
                    ),
                    createElement(PanelBody, { title: __('Typography Settings', 'gutenberg-blocks') },
                        createElement(TextControl, {
                            label: __('Font Size (px)', 'gutenberg-blocks'),
                            value: fontSize,
                            onChange: onChangeFontSize,
                            type: 'number',
                            step: '1',
                        }),
                        createElement(TextControl, {
                            label: __('Font Weight', 'gutenberg-blocks'),
                            value: fontWeight,
                            onChange: onChangeFontWeight,
                            type: 'number',
                            step: '100',
                        })
                    ),
                    createElement(PanelBody, { title: __('Container Height (px)', 'gutenberg-blocks') },
                        createElement(TextControl, {
                            label: __('Container Height (px)', 'gutenberg-blocks'),
                            value: containerHeight,
                            onChange: onChangeContainerHeight,
                            type: 'number',
                            step: '1',
                        })
                    )
                ),
              createElement('input', {
                type: 'text',
                value: buttonTextYes,
                style: {
                    backgroundColor: buttonBgColor,
                    color: buttonTextColor,
                    border: 'none',
                    padding: '8px',
                    fontSize: fontSize + 'px',
                    borderRadius: '4px',
                    outline: 'none',
                    height: buttonHeight + 'px',
                    textAlign: textAlign,
                    fontWeight: fontWeight,
                    width: buttonWidth + 'px', // Set width dynamically
                    display: 'inline-block' // Set display to inline-block
                },
                onChange: onChangeButtonText,
            })


            )
        );
    },
    save({ attributes }) {
        const { buttonTextYes, buttonBgColor, buttonTextColor, fontSize, fontWeight } = attributes;

        return (
            createElement('button', {
                style: {
                    backgroundColor: buttonBgColor,
                    color: buttonTextColor,
                    padding: '8px 16px',
                    width: '100%', // Set width to 100%
                    fontSize: fontSize + 'px',
                    fontWeight: fontWeight,
                },
            }, buttonTextYes)
        );
    },
});
