<?php

namespace fluXtore\Core\Admin\Ajax;

// Exit if accessed directly.
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_AjaxFlows' ) ) {
	class fluXtore_AjaxProducts extends fluXtore_AjaxBase {
		/**
		 * @var fluXtore_AjaxProducts $instance
		 */
		private static $instance;

		public static function init() {
			if ( !is_null( self::$instance ) ) {
				return;
			}

			self::$instance = new fluXtore_AjaxProducts();
			self::$instance->register_ajax_events();
		}

		function register_ajax_events() {
			$this->init_ajax_events([
				'get_products'
			]);
		}

		private function sanitize_products_fields( $product ) {
			foreach ( $product as $key => $value ) {
				if ( is_string( $value ) ) {
					$value = maybe_unserialize( $value );
				} else if ( is_array( $value ) ) {
					foreach ( $value as $i => $v ) {
						if ( is_string( $v ) ) {
							$value[ $i ] = maybe_unserialize( $v );
						}
					}
				}

				if ( string_starts_with( $key, 'post_' ) && $key !== 'post_password' ) {
					$k = explode( 'post_', $key )[ 1 ];
					$product->$k = $value;
					unset( $product->$key );
				}
			}

			return $product;
		}

		function get_products() {
			$args = [
				'post_type' => 'product',
				'posts_per_page' => -1
			];

			$loop = new WP_Query( $args );
			$result = [];

			if ( $loop->have_posts() ) {
				while ( $loop->have_posts() ) {
					$loop->the_post();
					global $product;

					$result[] = $this->sanitize_products_fields( $product->get_data() );
				}
			}

			wp_reset_postdata();
			wp_send_json_success( $result );
		}
	}
}

fluXtore_AjaxProducts::init();