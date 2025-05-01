if (!window.registerBlockType) {
    window.registerBlockType = wp.blocks.registerBlockType;
}


const { InspectorControls } = wp.blockEditor;
const { PanelBody, ColorPicker, SelectControl, TextControl } = wp.components;
const { createElement } = wp.element;
const { useState } = wp.element;
const { __ } = wp.i18n;

registerBlockType('blocks/gutenberg-next-step-blocks', {
    title: 'Fluxtore Next Step Button',
    icon: 'forward',
    category: 'common',
    attributes: {
        buttonTextNext: {
            type: 'string',
            default: 'Buy Now',
        },
        buttonBgColorNext: {
            type: 'string',
            default: '#bebebe',
        },
        buttonTextColorNext: {
            type: 'string',
            default: '#100000',
        },
        buttonWidthNext: {
            type: 'number',
            default: 100,
        },
        buttonHeightNext: {
            type: 'number',
            default: 40,
        },
        buttonAlignmentHorizontalNext: {
            type: 'string',
            default: 'center', // Default horizontal alignment
        },
        buttonAlignmentVerticalNext: {
            type: 'string',
            default: 'middle', // Default vertical alignment
        },
        textAlignNext: {
            type: 'string',
            default: 'center', // Default text alignment
        },
        containerHeightNext: {
            type: 'number',
            default: 200, // Default container height
        },
        fontSizeNext: {
            type: 'number',
            default: 16, // Default font size
        },
        fontWeightNext: {
            type: 'number',
            default: 400, // Default font weight
        },
    },
    edit({ attributes, setAttributes }) {
        const { buttonTextNext, buttonBgColorNext, buttonTextColorNext, buttonHeightNext, buttonAlignmentHorizontalNext, buttonAlignmentVerticalNext, textAlignNext, containerHeightNext, fontSizeNext, fontWeightNext } = attributes;

        const onChangeButtonText = (event) => {
            setAttributes({ buttonTextNext: event.target.value });
        };

        const onChangeBgColorNext = (newColor) => {
            setAttributes({ buttonBgColorNext: newColor });
        };

        const onChangeTextColorNext = (newColor) => {
            setAttributes({ buttonTextColorNext: newColor });
        };

        const onChangeWidthNext = (newWidth) => {
            setAttributes({ buttonWidthNext: parseInt(newWidth) });
        };

        const onChangeHeightNext = (newHeight) => {
            setAttributes({ buttonHeightNext: parseInt(newHeight) });
        };

        const onChangeAlignmentHorizontalNext = (newAlignment) => {
            setAttributes({ buttonAlignmentHorizontalNext: newAlignment });
        };

        const onChangeAlignmentVerticalNext = (newAlignment) => {
            setAttributes({ buttonAlignmentVerticalNext: newAlignment });
        };

        const onChangeTextAlignmentNext = (newAlignment) => {
            setAttributes({ textAlignNext: newAlignment });
        };

        const onChangeContainerHeightNext = (newHeight) => {
            setAttributes({ containerHeightNext: parseInt(newHeight) });
        };

        const onChangeFontSizeNext = (newSize) => {
            setAttributes({ fontSizeNext: parseInt(newSize) });
        };

        const onChangeFontWeightNext = (newWeight) => {
            setAttributes({ fontWeightNext: parseInt(newWeight) });
        };

        const buttonWidthNext = buttonTextNext.length * (fontSizeNext * 1.0);

        return (
            createElement('div', {
                style: {
                    height: containerHeightNext + 'px',
                    display: 'flex',
                    justifyContent: buttonAlignmentHorizontalNext,
                    alignItems: buttonAlignmentVerticalNext === 'top' ? 'flex-start' : buttonAlignmentVerticalNext === 'bottom' ? 'flex-end' : 'center',
                }
            },
                createElement(InspectorControls, {},
                    createElement(PanelBody, { title: __('Background Color', 'gutenberg-blocks') },
                        createElement(ColorPicker, {
                            color: buttonBgColorNext,
                            onChangeComplete: (color) => onChangeBgColorNext(color.hex),
                            disableAlpha: true,
                        })
                    ),
                    createElement(PanelBody, { title: __('Text Color', 'gutenberg-blocks') },
                        createElement(ColorPicker, {
                            color: buttonTextColorNext,
                            onChangeComplete: (color) => onChangeTextColorNext(color.hex),
                            disableAlpha: true,
                        })
                    ),
                    createElement(PanelBody, { title: __('Alignment Settings', 'gutenberg-blocks') },
                        createElement(SelectControl, {
                            label: __('Horizontal Alignment', 'gutenberg-blocks'),
                            value: buttonAlignmentHorizontalNext,
                            options: [
                                { label: __('Left', 'gutenberg-blocks'), value: 'flex-start' },
                                { label: __('Center', 'gutenberg-blocks'), value: 'center' },
                                { label: __('Right', 'gutenberg-blocks'), value: 'flex-end' },
                            ],
                            onChange: onChangeAlignmentHorizontalNext,
                        }),
                        createElement(SelectControl, {
                            label: __('Vertical Alignment', 'gutenberg-blocks'),
                            value: buttonAlignmentVerticalNext,
                            options: [
                                { label: __('Top', 'gutenberg-blocks'), value: 'top' },
                                { label: __('Middle', 'gutenberg-blocks'), value: 'middle' },
                                { label: __('Bottom', 'gutenberg-blocks'), value: 'bottom' },
                            ],
                            onChange: onChangeAlignmentVerticalNext,
                        }),
                        createElement(SelectControl, {
                            label: __('Text Alignment', 'gutenberg-blocks'),
                            value: textAlignNext,
                            options: [
                                { label: __('Left', 'gutenberg-blocks'), value: 'left' },
                                { label: __('Center', 'gutenberg-blocks'), value: 'center' },
                                { label: __('Right', 'gutenberg-blocks'), value: 'right' },
                            ],
                            onChange: onChangeTextAlignmentNext,
                        })
                    ),
                    createElement(PanelBody, { title: __('Typography Settings', 'gutenberg-blocks') },
                        createElement(TextControl, {
                            label: __('Font Size (px)', 'gutenberg-blocks'),
                            value: fontSizeNext,
                            onChange: onChangeFontSizeNext,
                            type: 'number',
                            step: '1',
                        }),
                        createElement(TextControl, {
                            label: __('Font Weight', 'gutenberg-blocks'),
                            value: fontWeightNext,
                            onChange: onChangeFontWeightNext,
                            type: 'number',
                            step: '100',
                        })
                    ),
                    createElement(PanelBody, { title: __('Container Height (px)', 'gutenberg-blocks') },
                        createElement(TextControl, {
                            label: __('Container Height (px)', 'gutenberg-blocks'),
                            value: containerHeightNext,
                            onChange: onChangeContainerHeightNext,
                            type: 'number',
                            step: '1',
                        })
                    )
                ),
                createElement('input', {
                    type: 'text',
                    value: buttonTextNext,
                    style: {
                        backgroundColor: buttonBgColorNext,
                        color: buttonTextColorNext,
                        border: 'None',
                        padding: '8px',
                        fontSize: fontSizeNext + 'px',
                        borderRadius: '4px',
                        outline: 'None',
                        height: buttonHeightNext + 'px',
                        textAlign: textAlignNext,
                        fontWeight: fontWeightNext,
                        width: buttonWidthNext + 'px', // Set width dynamically
                        display: 'inline-block', // Set display to inline-block
                    },
                    onChange: onChangeButtonText,
                })
            )
        );
    },
    save({ attributes }) {
        const { buttonTextNext, buttonBgColorNext, buttonTextColorNext, fontSizeNext, fontWeightNext } = attributes;

        return (
            createElement('button', {
                style: {
                    backgroundColor: buttonBgColorNext,
                    color: buttonTextColorNext,
                    padding: '8px 16px',
                    width: '100%', // Set width to 100%
                    fontSize: fontSizeNext + 'px',
                    fontWeight: fontWeightNext,
                },
            }, buttonTextNext)
        );
    },
});
