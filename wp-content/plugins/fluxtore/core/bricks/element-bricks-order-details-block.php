<?php

namespace Bricks;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Prefix_Element_Order_Details_Block extends Element {
	public $category        = 'woocommerce';
	public $name            = 'fluxtore-order-Review';
	public $icon            = 'ti-view-list-alt';	
	public $scripts      = ['prefixElementOrderBlock']; 

	public function get_label() {
		return esc_html__( 'Fluxtore Order Review', 'bricks' );
	}

	public function render() {
		Woocommerce_Helpers::maybe_populate_cart_contents();

		$settings = $this->settings;
		
		echo '<div ' . $this->render_attributes( '_root' ) . '>';
		echo '<div id="fluxtore-order-details">';
		if(isset($_GET['key'])){
			echo do_shortcode('[' . FLUXTORE_PREFIX . 'render_order_details]'); 				
		} else {
			echo '<div>Order Details Block</div>';
		}
		echo '</div>';
		echo '</div>';		
		
	}
}
