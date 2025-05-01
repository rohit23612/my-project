<?php

namespace fluXtore\Core\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_OrderDetails' ) ) {
	class fluXtore_OrderDetails {
		/**
		 * @var self $instance
		 */
		private static $instance;

		/**
		 * Initializes the core
		 */
		public static function init() {
			if ( !is_null( self::$instance ) ) {
				return;
			}

			self::$instance = new self();
			add_filter( 'woocommerce_admin_billing_fields', [self::$instance, 'admin_billing_fields'] );
			add_filter( 'woocommerce_admin_shipping_fields', [self::$instance, 'admin_shipping_fields'] );
		}

		public function admin_billing_fields($fields) {
			global $post;

			$order = wc_get_order($post);

            if (!$order)
                return $fields;

			$is_fluxtore_order = $order->get_meta(FLUXTORE_PREFIX . 'is_fluxtore_order');

			if ($is_fluxtore_order) {
				$step_id = $order->get_meta(FLUXTORE_PREFIX . 'order_step');
				$fields = fluxtore_get_admin_order_fields($step_id, $order, $fields, 'billing');
			}

			return $fields;
		}

		public function admin_shipping_fields($fields) {
			global $post;

			$order = wc_get_order($post);

            if (!$order)
                return $fields;

			$is_fluxtore_order = $order->get_meta(FLUXTORE_PREFIX . 'is_fluxtore_order');

			if ($is_fluxtore_order) {
				$step_id = $order->get_meta(FLUXTORE_PREFIX . 'order_step');
				$fields = fluxtore_get_admin_order_fields($step_id, $order, $fields, 'shipping');
			}

			return $fields;
		}
	}
}

fluXtore_OrderDetails::init();