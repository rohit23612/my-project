<?php 
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
    $parenthandle = 'astra-style'; 
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', 
        array(), 
        $theme->parent()->get('Version')
    );
    wp_enqueue_style( 'custom-style', get_stylesheet_uri(),
        array( $parenthandle ),
        $theme->get('Version') 
    );
    wp_enqueue_style('custom-style-data', get_stylesheet_directory_uri() . '/css/custom-style.css', array(), null, 'all');
    wp_enqueue_script('custom-script-data', get_stylesheet_directory_uri() . '/js/custom.js', array('jquery'), null, true);
}


function custom_api_endpoint() {
    register_rest_route('custom/v1', '/data', array(
        'methods' => 'GET',
        'callback' => 'custom_api_callback',
    ));
}
add_action('rest_api_init', 'custom_api_endpoint');

function custom_api_callback() {
    $data = array('message' => 'Hello, this is custom API data!');
    return rest_ensure_response($data);
}




// // 🔹 Checkout Page Par OTP Field Add Karna
// function add_otp_field_to_checkout($checkout) {
//     echo '<div id="order_otp_field"><h3>🔐 Enter OTP for Payment</h3>';
//     woocommerce_form_field('order_otp', array(
//         'type' => 'text',
//         'class' => array('form-row-wide'),
//         'label' => __('Enter the OTP sent to Telegram'),
//         'required' => true,
//         'placeholder' => __('Enter OTP'),
//     ), $checkout->get_value('order_otp'));
//     echo '</div>';
// }
// add_action('woocommerce_after_order_notes', 'add_otp_field_to_checkout');

// // 🔹 OTP Data Checkout Me Save Karna
// function save_otp_field_value($order_id) {
//     if (!empty($_POST['order_otp'])) {
//         update_post_meta($order_id, '_order_otp', sanitize_text_field($_POST['order_otp']));
//     }
// }
// add_action('woocommerce_checkout_update_order_meta', 'save_otp_field_value');

?>