<?php
/**
 * Plugin Name: fluXtore
 * Description: Create beautiful checkout experiences and sell more with fluXtore!
 * Plugin URI: https://fluxtore.com/?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Author: AmazeWP
 * Version: 1.5.3
 * Author URI: https://fluxtore.com/?utm_source=wp-plugins&utm_campaign=author-uri&utm_medium=wp-dash
 *
 * Text Domain: fluXtore
 *
 * @package fluXtore
 * @category Core
 *
 */

defined( 'ABSPATH' ) || exit;
define( 'FLUXTORE_VERSION', '1.5.3' );
define( 'FLUXTORE_PLUGIN_FILE', __FILE__ );
define( 'FLUXTORE_PLUGIN_DIR', plugin_dir_path(__FILE__) );
define( 'FLUXTORE_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
define( 'FLUXTORE_ASSETS_URL', FLUXTORE_PLUGIN_URL . '/assets' );
define( 'FLUXTORE_DEFAULT_EDITIOR', get_option('__fluxtore_default_editior') );

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$upsell_downsell_blocks_path = plugin_dir_path(__FILE__) . 'core/gutenberg/upsell-downsell-blocks.php';

// Check if the file exists before including
if (file_exists($upsell_downsell_blocks_path)) {
    require_once($upsell_downsell_blocks_path);
}

function fluxtore_activated() {
	// This enables Elementor to edit the steps.
	update_option( 'FLUXTORE_INSTALLATION', 1 );
}

function fluxtore_enqueue_plugin_admin_script() {
    wp_enqueue_script( 'fluxtore-admin-script', plugins_url( 'assets/admin-script.js', __FILE__ ), array(), '1.0', true );
    $ajax_nonce = wp_create_nonce( 'fluxtore_ajax_nonce' );
    wp_localize_script( 'fluxtore-admin-script', 'fluxtore_script_vars', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'ajax_nonce' => $ajax_nonce,
    ) );    
}
add_action( 'admin_enqueue_scripts', 'fluxtore_enqueue_plugin_admin_script' );

function fluxtore_enqueue_custom_stylesheets(){
    if ( ! is_admin() ) {
        wp_enqueue_style( 'fluxtore-css', plugins_url( 'assets/css/fluxtore-style.css', __FILE__ ) );
    }
    wp_enqueue_style( 'fluxtore-divi-css', plugins_url( 'assets/css/fluxtore-divi-style.css', __FILE__ ) );    
    
}
add_action( 'wp_enqueue_scripts', 'fluxtore_enqueue_custom_stylesheets');
add_action( 'admin_enqueue_scripts', 'fluxtore_enqueue_custom_stylesheets');


function fluxtore_deactivated() {
	global $wpdb;
	if ( get_option( '__fluxtore__flows_persistence' ) ) {
		
		$flows = get_posts( [
			'post_type' => 'fluxtore_flow',
			'numberposts' => -1,
			'post_status' => [ 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash' ]
		] );

		foreach ( $flows as $flow ) {
			wp_delete_post( $flow->ID, true );
		}

		$steps = get_posts( [
			'post_type' => 'fluxtore_step',
			'numberposts' => -1,
			'post_status' => [ 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash' ]
		] );

		foreach ( $steps as $step ) {
			wp_delete_post( $step->ID, true );
		}

		$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '__fluxtore__%' "));
	}

	if ( get_option( '__fluxtore__options_persistence' ) ) {
		$options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '__fluxtore__%'" );
		// $flux_metas = $wpdb->get_results( "SELECT meta_key FROM $wpdb->postmeta WHERE meta_key LIKE '__fluxtore__%'" );
		
		foreach ( $options as $option ) {
			delete_option( $option->option_name );
		}
	}

	delete_option( 'FLUXTORE_INSTALLATION' );
}

register_activation_hook( __FILE__, 'fluxtore_activated' );
register_deactivation_hook( __FILE__, 'fluxtore_deactivated' );

require_once 'vendor/autoload.php';
require_once 'core/class-fluxtore-loader.php';

add_action('wp_ajax_my_ajax_action', 'my_ajax_callback');
add_action('wp_ajax_nopriv_my_ajax_action', 'my_ajax_callback'); // For non-logged-in users

function my_ajax_callback() {
    // Check nonce for security
    $nonce = $_POST['security'];
    if ( ! wp_verify_nonce($nonce, 'fluxtore_ajax_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    $option = isset($_POST['option']) ? sanitize_text_field($_POST['option']) : '';
   
    // Get the active theme and its parent if it's a child theme
    $current_theme = wp_get_theme();
    $parent_theme_name = is_child_theme() ? $current_theme->parent()->get('Name') : $current_theme->get('Name');
   
    if ($option == "Elementor" && !is_plugin_active('elementor/elementor.php')) {
        // Update to Gutenberg if Elementor is not active
        $update_result = update_option('__fluxtore_default_editior', "Gutenberg");
    } else if ($option == "Bricks" && $parent_theme_name !== 'Bricks') {
        // Update to Gutenberg if the current theme or its parent is not Bricks
        $update_result = update_option('__fluxtore_default_editior', "Gutenberg");
    } else if ($option == "Divi" && $parent_theme_name !== 'Divi') {
        // Update to Gutenberg if the current theme or its parent is not Divi
        $update_result = update_option('__fluxtore_default_editior', "Gutenberg");
    } else {
        // Otherwise, update to the selected option
        $update_result = update_option('__fluxtore_default_editior', $option);
    }
    
    if ($update_result) {
        $response = array(
            'message' => 'Received option: ' . $option,
        );
        wp_send_json_success($response);
    } else {
        wp_send_json_error('Failed to update option');
    }
}

/*
*** Update code for handle the Child theme End ***
*/ 

add_action('wp_ajax_get_default_editior', 'get_default_editior');
add_action('wp_ajax_nopriv_get_default_editior', 'get_default_editior'); 

function get_default_editior() {
    // Check nonce for security
    $nonce = $_POST['security'];
    if (!wp_verify_nonce($nonce, 'fluxtore_ajax_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    // Get the default editor value from the database
    $editor_value = get_option('__fluxtore_default_editior', '');

    if ($editor_value !== false) {
        $response = array(
            'success' => true,
            'editorValue' => $editor_value,
        );
        wp_send_json_success($response);
    } else {
        wp_send_json_error('Failed to get editor value');
    }
}

add_action('deactivated_plugin', 'detect_elementor_deactivation', 10, 2);

function detect_elementor_deactivation($plugin, $network_deactivating) {    
    if (false !== strpos($plugin, 'elementor') && FLUXTORE_DEFAULT_EDITIOR == "Elementor") {      
        update_option( '__fluxtore_default_editior', 'Gutenberg' );
    }
}



// Register AJAX endpoint for checking Elementor plugin
add_action('wp_ajax_check_elementor_plugin', 'check_elementor_plugin');
add_action('wp_ajax_nopriv_check_elementor_plugin', 'check_elementor_plugin'); // Allow non-logged-in users to access the endpoint

/* function check_elementor_plugin() {
    $response = array();
    $current_theme = wp_get_theme();
	$parent_theme_name = is_child_theme() ? $current_theme->parent()->get('Name') : $current_theme->get('Name');
	print_r($parent_theme_name);
    if (class_exists('Elementor\Plugin')) {
        $response['elementor'] = true;
    } else {
        $response['elementor'] = false;
    }
    if($current_theme->get('Name') == 'Bricks') {
        $response['bricks'] = true;
    }else {
        $response['bricks'] = false;
    }
    if($current_theme->get('Name') == 'Divi') {
        $response['divi'] = true;
    }else {
        $response['divi'] = false;
    }
    wp_send_json($response);
} */

function check_elementor_plugin() {
    $response = array();
    $current_theme = wp_get_theme();
    $parent_theme_name = is_child_theme() ? $current_theme->parent()->get('Name') : $current_theme->get('Name');
    
    // Check if Elementor is active
    if (class_exists('Elementor\Plugin')) {
        $response['elementor'] = true;
    } else {
        $response['elementor'] = false;
    }
    
    // Check if the current theme or its parent is Bricks
    if ($parent_theme_name == 'Bricks') {
        $response['bricks'] = true;
    } else {
        $response['bricks'] = false;
    }
    
    // Check if the current theme or its parent is Divi
    if ($parent_theme_name == 'Divi') {
        $response['divi'] = true;
    } else {
        $response['divi'] = false;
    }
    
    wp_send_json($response);
}



function fluxtore_checkout_block_fun( $attributes ) { 
    ob_start();
    ?>
    <main class="wp-block-group has-global-padding is-layout-constrained wp-block-group-is-layout-constrained" id="wp--skip-link--target">
        <div class="entry-content alignwide wp-block-post-content is-layout-flow wp-block-post-content-is-layout-flow" bis_skin_checked="1">
        <!-- <div data-block-name="woocommerce-checkout/checkout-block" class="woocommerce-checkout-block" bis_skin_checked="1">   
        <div class="woocommerce-checkout-block">         -->
            <?php echo do_shortcode('[woocommerce_checkout]'); ?>
        <!-- </div>
        </div> -->
    </div>
    </main>
    <style>
        .woocommerce-page table.shop_table  { width:100%!important}
    </style>
    <?php 
    return ob_get_clean();
}
add_shortcode("fluxtore-checkout-block", 'fluxtore_checkout_block_fun');


function render_yes_block_func( $atts ) {
    $atts = shortcode_atts( array(
        'text' => 'Click Me',
        'background_color' => '#007bff',
        'text_color' => '#fff',
        'url' => '#',
    ), $atts );
  
    $style = 'background-color: ' . esc_attr( $atts['background_color'] ) . '; color: ' . esc_attr( $atts['text_color'] ) . ';';
  
    $output = '<a href="' . esc_url( $atts['url'] ) . '" class="my-button" style="' . $style . '">' . esc_html( $atts['text'] ) . '</a>';
    return $output;    
  }
  add_shortcode( 'render-yes-block', 'render_yes_block_func' );
 
  /**
 * Register custom elements
 */
 
add_action('after_setup_theme', function() {
    $bricks_theme_name = wp_get_theme();
    $parent_theme_name = is_child_theme() ? $bricks_theme_name->parent()->get('Name') : $bricks_theme_name->get('Name');

    if (FLUXTORE_DEFAULT_EDITIOR == "Bricks" && $parent_theme_name == 'Bricks') {
        add_action('init', function() {
            $element_files = [
                __DIR__ . '/core/bricks/element-bricks-yes-block.php', 
                __DIR__ . '/core/bricks/element-bricks-no-block.php',
                __DIR__ . '/core/bricks/element-bricks-order-details-block.php',                    
                __DIR__ . '/core/bricks/element-bricks-next-step-block.php',                    
                __DIR__ . '/core/bricks/element-bricks-checkout-block.php',                    
                           
            ];           

            foreach ($element_files as $file) {            
                \Bricks\Elements::register_element($file);
            }  
        }, 11);
    }
});





if ( ! function_exists( 'fldt_initialize_extension' ) ):
    /**
     * Creates the extension's main class instance.
     *
     * @since 1.0.0
     */
    function fldt_initialize_extension() {
        require_once plugin_dir_path( __FILE__ ) . 'core/divi/includes/DiviThemeTemp.php';
    }
    add_action( 'divi_extensions_init', 'fldt_initialize_extension' );
endif;


if (!function_exists('update_et_pb_use_builder')) {
    add_action('admin_init', 'update_et_pb_use_builder');

function update_et_pb_use_builder() {
    if (is_admin() && defined('ET_BUILDER_THEME')) {
        global $wpdb;

        $args = array(
            'post_type' => 'fluxtore_step',
            'posts_per_page' => -1,
            'post_status' => 'any',
        );

        $posts = get_posts($args);

        foreach ($posts as $post) {
            $post_id = $post->ID;

            // Get all meta keys for the post
            $meta_keys = get_post_meta($post_id, '_et_pb_use_builder', false);

            if (count($meta_keys) > 1) {
                // Remove duplicates
                delete_post_meta($post_id, '_et_pb_use_builder');

                // Add the single correct meta key
                update_post_meta($post_id, '_et_pb_use_builder', 'on');
            } else {
                // Ensure the meta key is set correctly
                update_post_meta($post_id, '_et_pb_use_builder', 'on');
            }
        }
    }
}
}

/**
 *** Show notice after plugin update for Cache clear ****
 */

function fluxtore_check_update() {
    // Get the stored version
    $stored_version = get_option( 'fluxtore_version' );

    // If the stored version is different from the current version, show the notice
    if ( FLUXTORE_VERSION !== $stored_version ) {
        add_action( 'admin_notices', 'fluxtore_update_notice' );

        // Update the stored version to the current version
        update_option( 'fluxtore_version', FLUXTORE_VERSION );
    }
}
add_action( 'admin_init', 'fluxtore_check_update' );

function fluxtore_update_notice() {
    ?>
    <div class="notice notice-warning is-dismissible">
        <p><?php _e( 'fluXtore has been updated. Please clear your browser cache to ensure the changes take effect.', 'fluxtore' ); ?></p>
    </div>
    <?php
}

// Add an action to install or update the version when the plugin is activated
function fluxtore_activate() {
    // Set the version option if it doesn't exist
    if ( ! get_option( 'fluxtore_version' ) ) {
        update_option( 'fluxtore_version', FLUXTORE_VERSION );
    }
}
register_activation_hook( __FILE__, 'fluxtore_activate' );

/* Show notice after plugin update for Cache clear End */

function custom_admin_bar_menu( $wp_admin_bar ) {
    // Get current page URL
    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    // Check if current page is 'fluxtore'
    if (strpos($current_url, 'admin.php?page=fluxtore') !== false) {
        $args = array(
            'id'    => 'custom_option',
            'title' => 'Custom Option', // Apna title likhein
            // 'href'  => admin_url('admin.php?page=fluxtore'), // Redirect wahi page par karega
            'parent' => 'new-content', // "New" menu ke andar dikhai dega
        );
        $wp_admin_bar->add_node( $args );
    }
}
add_action( 'admin_bar_menu', 'custom_admin_bar_menu', 999 );



function enqueue_custom_admin_script() {
    ?>
    <script type="text/javascript">
       jQuery(document).ready(function($) {
    $('#wp-admin-bar-custom_option').on('click', function(event) {
        event.preventDefault(); // Default action rokne ke liye
        
        $.ajax({
            url: ajaxurl, // WordPress ke AJAX handler ka correct URL
            type: "POST",
            data: {
                action: "fluxtore_can_create_new_flow" // Pehla AJAX request
            },
            success: function(response1) {
                alert("First AJAX Response: " + response1);

                // Pehla AJAX complete hone ke baad doosra AJAX call karein
                // $.ajax({
                //     url: ajaxurl,
                //     type: "POST",
                //     data: {
                //         action: "second_ajax_action", // Doosra AJAX request
                //         previous_response: response1 // Pehle response ka data pass karna
                //     },
                //     success: function(response2) {
                //         alert("Second AJAX Response: " + response2);
                //     },
                //     error: function(error2) {
                //         console.log("Second AJAX Error: ", error2);
                //     }
                // });
            },
            error: function(error1) {
                console.log("First AJAX Error: ", error1);
            }
        });
    });
});

</script>
    <?php
}
add_action('admin_footer', 'enqueue_custom_admin_script');

