<?php
namespace Bricks;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Prefix_Element_Checkout extends Element {
    public $category = 'woocommerce';
    public $name     = 'Fluxtore Checkout';
    public $icon     = 'ti-shopping-cart';
    public $css_selector = '.checkout-wrapper';

    public function get_label() {
        return esc_html__( 'Fluxtore Checkout', 'bricks' );
    }

    public function set_controls() {
        // Define controls if any required
    }

    public function render() {
        global $post;
		$post_id = $post->ID;
		$post_meta = get_post_meta($post_id);
        $product =  $post_meta['__fluxtore__product'];

        $this->set_attribute( '_root', 'class', 'checkout-wrapper' );

        // Output the HTML with the WooCommerce Checkout shortcode
        echo '<div ' . $this->render_attributes( '_root' ) . '>';
        if (isset($post_meta['__fluxtore__product']) && !empty($post_meta['__fluxtore__product'][0])) {
            echo do_shortcode( '[woocommerce_checkout]' );
        } else {
            echo '<p style="text-align:center">Product is empty or not set.</p>';
        }
        echo '</div>';
    }

    public static function render_builder() {
        ?>
        <script type="text/x-template" id="tmpl-bricks-element-checkout">
            <component is="div" class="checkout-wrapper">
                <div>
                <?php 
                global $post;
                $post_id = $post->ID;
                $post_meta = get_post_meta($post_id);
                if (isset($post_meta['__fluxtore__product']) && !empty($post_meta['__fluxtore__product'][0])) {
                            echo do_shortcode( '[woocommerce_checkout]' );
                        } else {
                            echo '<p style="text-align:center">Product is empty or not set.</p>';
                        }
                ?>
            </div>
            </component>
        </script>
        <?php
    }
}

// Register the custom element with Bricks Builder
add_action( 'bricks/init', function() {
    if ( class_exists( 'Bricks\\Element' ) && class_exists( 'Bricks\\Prefix_Element_Checkout' ) ) {
        bricks_register_element( 'Bricks\\Prefix_Element_Checkout' );
    }
});

// Add custom styles for the checkout form
add_action( 'wp_head', function() {
    ?>
    <style>
        div#customer_details {
            display: flex;
            grid-template-columns: repeat(2 , 1fr);
            gap: 20px;
            width: 100% !important;
        }

        .wcf-col-1.col-1, .wcf-col-2.col-2 {
            width: 50% !important;
            flex: unset !important;
        }

        .woocommerce-checkout #order_review {
            border: 1px solid var(--bricks-border-color);
            float: right;
            width: 100%;
        }
    </style>
    <?php
} );
