
const { Button } = wp.components;
const { createElement } = wp.element;
const { TextControl } = wp.components;
registerBlockType('woocommerce-checkout/gutenberg-blocks', {
    title: 'Custom Block with Button',
    icon: 'warning',
    category: 'common',
    attributes: {
        buttonTextYes: {
            type: 'string',
            default: 'Yes, I want to take this offer now ...',
        },   
    },
    edit({ attributes, setAttributes }) {
        const { buttonTextYes } = attributes;
       
        const onChangeButtonText = (newButtonText) => {
            setAttributes({ buttonTextYes: newButtonText });
        };

        return (
            createElement('div', {},
                createElement(TextControl, {
                    tagName: 'button',
                    value: buttonTextYes,
                    onChange: onChangeButtonText,
                    placeholder: 'Button Text',
                })
            )
        );
    },
    save({ attributes }) {
        const { buttonTextYes } = attributes;

        return createElement('button', {}, buttonTextYes);
    },
});
