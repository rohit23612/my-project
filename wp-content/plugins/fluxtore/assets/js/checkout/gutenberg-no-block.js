

registerBlockType('woocommerce-checkout/gutenberg-no-blocks', {
    title: 'Custom No Block with Button',
    icon: 'warning',
    category: 'common',
    attributes: {
        buttonTextNo: {
            type: 'string',
            default: 'No, I let go of this fantastic opportunity', 
        },
    },
    edit({ attributes, setAttributes }) {
        const { buttonTextNo } = attributes;

        // Handle text input change
        const onChangeButtonText = (newButtonText) => {
            setAttributes({ buttonTextNo: newButtonText });
        };

        return (
            createElement('div', {},
                createElement(TextControl, {
                    tagName: 'button',
                    value: buttonTextNo,
                    onChange: onChangeButtonText,
                    placeholder: 'Button Text',
                })
            )
        );
    },
    save({ attributes }) {
        const { buttonTextNo } = attributes;

        return createElement('button', {}, buttonTextNo);
    },
});