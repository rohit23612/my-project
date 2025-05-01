if (!window.registerBlockType) {
    window.registerBlockType = wp.blocks.registerBlockType;
}

if (!window.InspectorControls) {
    const { InspectorControls } = wp.blockEditor;
}

if (!window.PanelBody || !window.ColorPicker) {
    const { PanelBody, ColorPicker } = wp.components;
}

if (!window.createElement) {
    const { createElement } = wp.element;
}

if (!window.useState) {
    const { useState } = wp.element;
}

if (!window.__) {
    const { __ } = wp.i18n;
}



registerBlockType('blocks/gutenberg-no-blocks', {
    title: 'Upsell/Downsell No Button',
    icon: 'no',
    category: 'common',
    attributes: {
        buttonTextNo: {
            type: 'string',
            default: 'No, I let go of this fantastic opportunity',
        },
        buttonBgColorNo: {
            type: 'string',
            default: '#bebebe',
        },
        buttonTextColorNo: {
            type: 'string',
            default: '#100000',
        },
        buttonWidthNo: {
            type: 'number',
            default: 100,
        },
        buttonHeightNo: {
            type: 'number',
            default: 40,
        },
        buttonAlignmentHorizontalNo: {
            type: 'string',
            default: 'center', // Default horizontal alignment
        },
        buttonAlignmentVerticalNo: {
            type: 'string',
            default: 'middle', // Default vertical alignment
        },
        textAlignNo: {
            type: 'string',
            default: 'center', // Default text alignment
        },
        containerHeightNo: {
            type: 'number',
            default: 200, // Default container height
        },
        fontSizeNo: {
            type: 'number',
            default: 16, // Default font size
        },
        fontWeightNo: {
            type: 'number',
            default: 400, // Default font weight
        },
    },
    edit({ attributes, setAttributes }) {
        const { buttonTextNo, buttonBgColorNo, buttonTextColorNo, buttonHeightNo, buttonAlignmentHorizontalNo, buttonAlignmentVerticalNo, textAlignNo, containerHeightNo, fontSizeNo, fontWeightNo } = attributes;

        const onChangeButtonText = (event) => {
            setAttributes({ buttonTextNo: event.target.value });
        };

        const onChangeBgColorNo = (newColor) => {
            setAttributes({ buttonBgColorNo: newColor });
        };

        const onChangeTextColorNo = (newColor) => {
            setAttributes({ buttonTextColorNo: newColor });
        };

        const onChangeWidthNo = (newWidth) => {
            setAttributes({ buttonWidthNo: parseInt(newWidth) });
        };

        const onChangeHeightNo = (newHeight) => {
            setAttributes({ buttonHeightNo: parseInt(newHeight) });
        };

        const onChangeAlignmentHorizontalNo = (newAlignment) => {
            setAttributes({ buttonAlignmentHorizontalNo: newAlignment });
        };

        const onChangeAlignmentVerticalNo = (newAlignment) => {
            setAttributes({ buttonAlignmentVerticalNo: newAlignment });
        };

        const onChangeTextAlignmentNo = (newAlignment) => {
            setAttributes({ textAlignNo: newAlignment });
        };

        const onChangeContainerHeightNo = (newHeight) => {
            setAttributes({ containerHeightNo: parseInt(newHeight) });
        };

        const onChangeFontSizeNo = (newSize) => {
            setAttributes({ fontSizeNo: parseInt(newSize) });
        };

        const onChangeFontWeightNo = (newWeight) => {
            setAttributes({ fontWeightNo: parseInt(newWeight) });
        };

        const buttonWidthNo = buttonTextNo.length * (fontSizeNo * 0.6);

        return (
            createElement('div', {
                style: {
                    height: containerHeightNo + 'px',
                    display: 'flex',
                    justifyContent: buttonAlignmentHorizontalNo,
                    alignItems: buttonAlignmentVerticalNo === 'top' ? 'flex-start' : buttonAlignmentVerticalNo === 'bottom' ? 'flex-end' : 'center',
                }
            },
                createElement(InspectorControls, {},
                    createElement(PanelBody, { title: __('Background Color', 'gutenberg-blocks') },
                        createElement(ColorPicker, {
                            color: buttonBgColorNo,
                            onChangeComplete: (color) => onChangeBgColorNo(color.hex),
                            disableAlpha: true,
                        })
                    ),
                    createElement(PanelBody, { title: __('Text Color', 'gutenberg-blocks') },
                        createElement(ColorPicker, {
                            color: buttonTextColorNo,
                            onChangeComplete: (color) => onChangeTextColorNo(color.hex),
                            disableAlpha: true,
                        })
                    ),
                    createElement(PanelBody, { title: __('Alignment Settings', 'gutenberg-blocks') },
                        createElement(SelectControl, {
                            label: __('Horizontal Alignment', 'gutenberg-blocks'),
                            value: buttonAlignmentHorizontalNo,
                            options: [
                                { label: __('Left', 'gutenberg-blocks'), value: 'flex-start' },
                                { label: __('Center', 'gutenberg-blocks'), value: 'center' },
                                { label: __('Right', 'gutenberg-blocks'), value: 'flex-end' },
                            ],
                            onChange: onChangeAlignmentHorizontalNo,
                        }),
                        createElement(SelectControl, {
                            label: __('Vertical Alignment', 'gutenberg-blocks'),
                            value: buttonAlignmentVerticalNo,
                            options: [
                                { label: __('Top', 'gutenberg-blocks'), value: 'top' },
                                { label: __('Middle', 'gutenberg-blocks'), value: 'middle' },
                                { label: __('Bottom', 'gutenberg-blocks'), value: 'bottom' },
                            ],
                            onChange: onChangeAlignmentVerticalNo,
                        }),
                        createElement(SelectControl, {
                            label: __('Text Alignment', 'gutenberg-blocks'),
                            value: textAlignNo,
                            options: [
                                { label: __('Left', 'gutenberg-blocks'), value: 'left' },
                                { label: __('Center', 'gutenberg-blocks'), value: 'center' },
                                { label: __('Right', 'gutenberg-blocks'), value: 'right' },
                            ],
                            onChange: onChangeTextAlignmentNo,
                        })
                    ),
                    createElement(PanelBody, { title: __('Typography Settings', 'gutenberg-blocks') },
                        createElement(TextControl, {
                            label: __('Font Size (px)', 'gutenberg-blocks'),
                            value: fontSizeNo,
                            onChange: onChangeFontSizeNo,
                            type: 'number',
                            step: '1',
                        }),
                        createElement(TextControl, {
                            label: __('Font Weight', 'gutenberg-blocks'),
                            value: fontWeightNo,
                            onChange: onChangeFontWeightNo,
                            type: 'number',
                            step: '100',
                        })
                    ),
                    createElement(PanelBody, { title: __('Container Height (px)', 'gutenberg-blocks') },
                        createElement(TextControl, {
                            label: __('Container Height (px)', 'gutenberg-blocks'),
                            value: containerHeightNo,
                            onChange: onChangeContainerHeightNo,
                            type: 'number',
                            step: '1',
                        })
                    )
                ),
                createElement('input', {
                    type: 'text',
                    value: buttonTextNo,
                    style: {
                        backgroundColor: buttonBgColorNo,
                        color: buttonTextColorNo,
                        border: 'none',
                        padding: '8px',
                        fontSize: fontSizeNo + 'px',
                        borderRadius: '4px',
                        outline: 'none',
                        height: buttonHeightNo + 'px',
                        textAlign: textAlignNo,
                        fontWeight: fontWeightNo,
                        width: buttonWidthNo + 'px', // Set width dynamically
                        display: 'inline-block', // Set display to inline-block
                    },
                    onChange: onChangeButtonText,
                })
            )
        );
    },
    save({ attributes }) {
        const { buttonTextNo, buttonBgColorNo, buttonTextColorNo, fontSizeNo, fontWeightNo } = attributes;

        return (
            createElement('button', {
                style: {
                    backgroundColor: buttonBgColorNo,
                    color: buttonTextColorNo,
                    padding: '8px 16px',
                    width: '100%', // Set width to 100%
                    fontSize: fontSizeNo + 'px',
                    fontWeight: fontWeightNo,
                },
            }, buttonTextNo)
        );
    },
});
