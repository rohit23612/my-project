const { registerBlockType } = wp.blocks;


registerBlockType( 'woocommerce-checkout/checkout-block', {
    title :'Fluxtore Checkout',
    icon :'cart',
    category: 'common',
    keywords: ['checkout', 'woocommerce', 'form'],
    edit :function() {
        return wp.element.createElement(
            'div',
            { className: 'woocommerce-checkout-block' },
            '[woocommerce_checkout]'
        );
    },

    save: function() {
        return wp.element.createElement(
            'div',
            { className: 'woocommerce-checkout-block' },
            '[woocommerce_checkout]'
        );
    },
} );