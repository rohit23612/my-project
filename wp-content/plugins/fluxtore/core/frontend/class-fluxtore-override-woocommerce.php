<?php

namespace fluXtore\Core\Frontend;

use fluXtore\Core\Models\fluXtore_Step;
use WC_Order;

if (!defined('ABSPATH')) {
	exit;
}

// Avoid defining the class twice.
if (!class_exists('fluXtore_OverrideWoocommerce')) {
	class fluXtore_OverrideWoocommerce
	{
		/**
		 * @var self $instance
		 */
		private static $instance;

		private $product;
		private $bump_product;
		private $bump_discount;

		private $woo_init;

		/**
		 * Private constructor for Singleton
		 */
		private function __construct() {
			$this->woo_init = false;
		}

		/**
		 * Initializes the core
		 */
		public static function init() {
			if (!is_null(self::$instance) || is_admin()) {
				return;
			}

			self::$instance = new self();

			// Initialization
			add_action('the_post', [self::$instance, 'init_core'], 1);
			add_action('woocommerce_init', [self::$instance, 'woocommerce_init'], 1);

			add_filter('woocommerce_locate_template', [self::$instance, 'override_woo_template'], 20, 3);
			add_action('wp', [self::$instance, 'preconfigured_cart_data'], 1);

			// Checkout management
			add_filter('woocommerce_is_checkout', [self::$instance, 'woo_is_checkout'], 99);

			// Price management
			add_action('woocommerce_before_calculate_totals', [self::$instance, 'woocommerce_custom_price_to_cart_item'], 99);

			// Thank you page
			add_action('woocommerce_checkout_create_order', [self::$instance, 'before_checkout_create_order'], 20, 2);
			add_action('template_redirect', [self::$instance,  'woo_custom_redirect_after_purchase'], 1);

			add_shortcode(FLUXTORE_PREFIX . 'render_order_details', [self::$instance, 'render_order_details']);
			//Checkout fields
			// add_filter('woocommerce_form_field', [self::$instance, 'add_percentages'], 10, 4);
			add_filter('woocommerce_billing_fields', [self::$instance, 'modify_billings_fields']);
			add_filter('woocommerce_shipping_fields', [self::$instance, 'modify_shipings_fields']);
			add_filter('woocommerce_get_country_locale_default', [self::$instance, 'modify_default_locale'], 999);
			//Custom checkout fields
			add_action('woocommerce_checkout_update_order_meta', [self::$instance, 'save_custom_meta']);
			//validate custom fields, which are not in WC()->checkout->get_checkout_fields()
			// add_action('woocommerce_checkout_process', [self::$instance, 'validate_required_fields']);
			//###### debug checkout ########//
			// add_action('woocommerce_before_checkout_form', [self::$instance, 'debug_checkout']);
			// add_action('woocommerce_after_checkout_validation', [self::$instance, 'debug_posted_data'], 10, 2);
			// add_filter('woocommerce_checkout_posted_data', [self::$instance, 'debug_posted_data2']);
		}

		//public function modify_default_locale($fields) {
			//global $post;
			//if (FLUXTORE_STEP === $post->post_type && $post->{FLUXTORE_PREFIX . 'tag'} === FLUXTORE_CHECKOUT_TAG) {
				//foreach ($fields as $field_id => $field) {
					//unset($fields[$field_id]['required']);
					//unset($fields[$field_id]['priority']);
					//unset($fields[$field_id]['class']);
			//	}
			//}

			//return $fields;
		//}

	public function modify_default_locale($fields) {
    global $post;

    // Check if $post is not null before accessing its properties
    if ($post && FLUXTORE_STEP === $post->post_type && isset($post->{FLUXTORE_PREFIX . 'tag'}) && $post->{FLUXTORE_PREFIX . 'tag'} === FLUXTORE_CHECKOUT_TAG) {
        foreach ($fields as $field_id => $field) {
            if (is_array($fields[$field_id])) {
                unset($fields[$field_id]['required']);
                unset($fields[$field_id]['priority']);
                unset($fields[$field_id]['class']);
            }
        }
    }

    return $fields;
}

		public function modify_billings_fields($fields) {
			if (wp_doing_ajax() && isset($_POST['fluxtore_step'])) {
				$post = get_post(absint($_POST['fluxtore_step']));
			} else {
				global $post;
			}
			if (FLUXTORE_STEP === $post->post_type && $post->{FLUXTORE_PREFIX . 'tag'} === FLUXTORE_CHECKOUT_TAG) {
				if ($visible_fields = fluxtore_get_checkout_fields($post->ID, $fields, 'billing')) {

					$fields = $visible_fields;
				};
			}
			// var_dump($fields);
			return $fields;
		}
		public function modify_shipings_fields($fields) {
			if (wp_doing_ajax() && isset($_POST['fluxtore_step'])) {
				$post = get_post(absint($_POST['fluxtore_step']));
			} else {
				global $post;
			}
			if (FLUXTORE_STEP === $post->post_type && $post->{FLUXTORE_PREFIX . 'tag'} === FLUXTORE_CHECKOUT_TAG) {
				if ($visible_fields = fluxtore_get_checkout_fields($post->ID, $fields, 'shipping')) {

					$fields = $visible_fields;
				}
			}
			return $fields;
		}

		public function add_percentages($field, $key, $args, $value) {
			global $post;
			if (FLUXTORE_STEP === $post->post_type && $post->{FLUXTORE_PREFIX . 'tag'} === FLUXTORE_CHECKOUT_TAG) {
				if (isset($args['percentage'])) {
					$field = preg_replace('/(<p\b[^><]*)>/i', '$1 style="width:' . $args['percentage'] . '%;">', $field);
				}
			}
			return $field;
		}
		/**
		 * Check if required on place order
		 */
		public function validate_required_fields() {
			if (wp_doing_ajax() && isset($_POST['fluxtore_step'])) {
				$post = get_post(absint($_POST['fluxtore_step']));
				if (!$post instanceof \WP_Post) {
					return;
				}
				$billing_fields = get_post_meta($post->ID, FLUXTORE_PREFIX . 'billing_fields', true);
				$shipping_fields = get_post_meta($post->ID, FLUXTORE_PREFIX . 'shipping_fields', true);
				if (!$billing_fields || !$shipping_fields) {
					return;
				}
				$default_fields = fluxtore_default_checkout_fields();
				$merged_fields = array_filter(array_merge($billing_fields, $shipping_fields), function ($field_id) use ($default_fields) {
					return !in_array($field_id, $default_fields);
				}, ARRAY_FILTER_USE_KEY);
				if (!empty($merged_fields)) {
					foreach ($merged_fields as $field_id => $field) {

						if (isset($_POST[$field_id]) && empty($_POST[$field_id]) && isset($field['required']) && $field['required']) {
							wc_add_notice(sprintf(__('%s field is required', 'fluXtore'), $field['label']), 'error');
						}
					}
				}
			}
		}
		/**
		 * Save custom field meta on checkout
		 */
		public function save_custom_meta($order_id) {
			if (wp_doing_ajax() && isset($_POST['fluxtore_step'])) {
				$post = get_post(absint($_POST['fluxtore_step']));
				if (!$post instanceof \WP_Post) {
					return;
				}
				$billing_fields = get_post_meta($post->ID, FLUXTORE_PREFIX . 'billing_fields', true);
				$shipping_fields = get_post_meta($post->ID, FLUXTORE_PREFIX . 'shipping_fields', true);
				if (!$billing_fields || !$shipping_fields) {
					return;
				}
				$default_fields = fluxtore_default_checkout_fields();
				$merged_fields = array_filter(array_merge($billing_fields, $shipping_fields), function ($field_id) use ($default_fields) {
					return !in_array($field_id, $default_fields);
				}, ARRAY_FILTER_USE_KEY);

				if (!empty($merged_fields)) {
					foreach ($merged_fields as $field_id => $field) {

						if (isset($_POST[$field_id])) {
							update_post_meta($order_id, $field_id, wc_clean($_POST[$field_id]));
						}
					}
				}
			}
		}
		/**
		 * Debug checkout page
		 */
		public function debug_checkout($checkout) {
			global $post;
			$billing_fields = get_post_meta($post->ID, FLUXTORE_PREFIX . 'billing_fields', true);
			$shipping_fields = get_post_meta($post->ID, FLUXTORE_PREFIX . 'shipping_fields', true);
			if (!$billing_fields || !$shipping_fields) {
				return;
			}
			$default_fields = fluxtore_default_checkout_fields();
			$merged_fields = array_filter(array_merge($billing_fields, $shipping_fields), function ($field_id) use ($default_fields) {
				return !in_array($field_id, $default_fields);
			}, ARRAY_FILTER_USE_KEY);

			echo "<pre>";
			print_r($merged_fields);
			echo "</pre>";
		}

		public function debug_posted_data($data, $errors) {
			$upload_dir = wp_get_upload_dir();
			file_put_contents(trailingslashit($upload_dir['basedir']) . 'posted_data.txt', "<pre>" . print_r($data, true) . "</pre>");
		}

		public function debug_posted_data2($data) {
			$upload_dir = wp_get_upload_dir();
			$fields = WC()->checkout->get_checkout_fields();
			file_put_contents(trailingslashit($upload_dir['basedir']) . 'posted_data2.txt', "<pre>" . print_r($fields, true) . "</pre>");
			return $data;
		}

		public function preconfigured_cart_data() {
			if (is_admin() || wp_doing_ajax()) {
				return;
			}

			global $post;

			if (is_null($post)) {
				return;
			}

			if ($post->post_type === FLUXTORE_STEP && $post->{FLUXTORE_PREFIX . 'tag'} === FLUXTORE_CHECKOUT_TAG) {
				/* Empty the current cart */
				WC()->cart->empty_cart();

				/* Set customer session if not set */
				if (!is_user_logged_in() && WC()->cart->is_empty()) {
					WC()->session->set_customer_session_cookie(true);
				}
			}
		}

		function woo_is_checkout($is_checkout) {
			global $post;

			if (is_null($post)) {
				return false;
			}

			return $is_checkout || ($post->post_type === FLUXTORE_STEP && $post->{FLUXTORE_PREFIX . 'tag'} === FLUXTORE_CHECKOUT_TAG);;
		}

		function before_checkout_create_order($order, $data) {
			if (isset($_POST['fluxtore_checkout'])) {
				$step_id = intval($_POST['fluxtore_step']);
				$step = new fluXtore_Step();
				$flow_id = $step->get_flow($step_id)[0];

				/** @var WC_Order $order */
				$order->update_meta_data(FLUXTORE_PREFIX . 'is_fluxtore_order', true);
				$order->update_meta_data(FLUXTORE_PREFIX . 'order_step', $step_id);
				$order->update_meta_data(FLUXTORE_PREFIX . 'order_flow', intval($flow_id));

				if (isset($_POST['fluxtore_add_to_cart']) && $_POST['fluxtore_add_to_cart'] === 'on') {
					$order->update_meta_data(FLUXTORE_PREFIX . 'order_bump_accepted', true);
					$order->update_meta_data(FLUXTORE_PREFIX . 'order_bump_price', floatval($_POST['fluxtore_bump_price']));
				}
			}
		}

		function woo_custom_redirect_after_purchase() {
           
			global $wp;
			
			if (is_checkout() && !empty($wp->query_vars['order-received'])) {
				
				$order = new WC_Order($wp->query_vars['order-received']);		
				
				if ($order->meta_exists(FLUXTORE_PREFIX . 'order_step')) {
			
					$step = new fluXtore_Step();
					$step_id = $order->get_meta(FLUXTORE_PREFIX . 'order_step');
                     print_r($step_id);  
					if ($order->get_parent_id() > 0) {
						$new_step = $step->get_yes_step($step_id);						
					} else {						
						$new_step = $step->next_step($step_id);						
					}

					if ($new_step) {
						$url = get_permalink($new_step->ID);										
						if (!is_array($_GET['key'])) {
							$_GET['key'] = [sanitize_text_field($_GET['key'])];							
						}						
						foreach ($_GET['key'] as $k) {							
							$k = sanitize_text_field($k);
							if (parse_url($url, PHP_URL_QUERY)) {
								$url .= '&key[]=' . $k;
							} else {
								$url .= '?key[]=' . $k;
							}
						}						
						wp_redirect($url);
						exit;
					}
				}
			}
		}

		public function woocommerce_init() {
			$this->woo_init = true;
		}

		public function init_core() {
			global $post;

			if ($post->post_type !== FLUXTORE_STEP) {
				return;
			}

			$step = new fluXtore_Step();
			$full_step = $step->get($post->ID);

			$tag_param = FLUXTORE_PREFIX . 'tag';
			$product_param = FLUXTORE_PREFIX . 'product';
			$bump_product_param = FLUXTORE_PREFIX . 'order_bump_product';
			$bump_discount_param = FLUXTORE_PREFIX . 'order_bump_discount';
            $bump_default_checked_param = FLUXTORE_PREFIX . 'order_bump_default_checked';

			if (strtolower($full_step->$tag_param[0]) !== "checkout" || !isset($full_step->$product_param)) {
				return;
			}

			if (isset($post->{FLUXTORE_PREFIX . 'hide_additional_notes'}) && $post->{FLUXTORE_PREFIX . 'hide_additional_notes'}) {
				add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );
			}

			$this->product = $full_step->$product_param[0];

			if (isset($_GET['bump_offer']) && "" . $_GET['bump_offer'] === "1") {
				$this->bump_product = $full_step->$bump_product_param[0];
				$this->bump_discount = $full_step->$bump_discount_param[0];
			} else if (!isset($_GET['bump_offer']) && property_exists($full_step, $bump_default_checked_param) && $full_step->$bump_default_checked_param[0] === "1") {
                $this->bump_product = $full_step->$bump_product_param[0];
                $this->bump_discount = $full_step->$bump_discount_param[0];
            }

			if ($this->woo_init) {
				$this->init_woocommerce_cart(true);
			} else {
				add_action('woocommerce_init', [self::$instance, 'init_woocommerce_cart'], 20);
			}
		}

		public function init_woocommerce_cart($init_cart = false) {
			global $post;

			if ($init_cart) {
				WC()->initialize_session();
				WC()->initialize_cart();
			}

			WC()->cart->empty_cart();

			$step = new fluXtore_Step();
			$full_step = $step->get($post->ID);

			$cart_item_data = [];

			if ($full_step->{FLUXTORE_PREFIX . 'price_type'}[0] !== 'original') {
				$cart_item_data = ['custom_price' => $full_step->{FLUXTORE_PREFIX . 'price'}[0]];
			}

			WC()->cart->add_to_cart(self::$instance->product, 1, 0, [], $cart_item_data);

			if ($this->bump_product) {
				$cart_item_data = ['custom_price' => $this->bump_discount];
				WC()->cart->add_to_cart(self::$instance->bump_product, 1, 0, [], $cart_item_data);
			}

			WC()->cart->calculate_totals();
			WC()->cart->set_session();
			WC()->cart->maybe_set_cart_cookies();
		}

		public function woocommerce_custom_price_to_cart_item($cart_object) {
			if (!WC()->session->__isset("reload_checkout")) {
				foreach ($cart_object->cart_contents as $key => $value) {
					if (isset($value["custom_price"])) {
						$value['data']->set_price($value["custom_price"]);
					}
				}
			}
		}

		public function render_order_details() {
			if (!isset($_GET['key'])) {
				return;
			}

			foreach ($_GET['key'] as $k) {
				$order_id = wc_get_order_id_by_order_key(sanitize_text_field($k));

				woocommerce_order_details_table($order_id);
			}
		}

		public function override_woo_template($template, $template_name, $template_path) {
			global $post;

			if (is_null($post))
				return $template;

			if ($post->post_type !== FLUXTORE_STEP) {
				return $template;
			}

			$_template = $template;

			$plugin_path = FLUXTORE_PLUGIN_DIR . 'woocommerce/';

			if (file_exists($plugin_path . $template_name)) {
				$template = $plugin_path . $template_name;
			}

			if (!$template) {
				$template = $_template;
			}

			return $template;
		}
	}
}

fluXtore_OverrideWoocommerce::init();
