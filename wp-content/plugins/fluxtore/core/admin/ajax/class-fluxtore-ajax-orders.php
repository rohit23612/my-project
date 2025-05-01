<?php

namespace fluXtore\Core\Admin\Ajax;

// Exit if accessed directly.
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_AjaxOrders' ) ) {
	class fluXtore_AjaxOrders extends fluXtore_AjaxBase {
		/**
		 * @var self $instance
		 */
		private static $instance;

		public static function init() {
			if ( !is_null( self::$instance ) ) {
				return;
			}

			self::$instance = new self();
			self::$instance->register_ajax_events();
		}

		function register_ajax_events() {
			$this->init_ajax_events( [
				'get_orders_today',
				'get_orders_yesterday',
				'get_orders_last_week',
				'get_orders_last_month',
				'get_orders_always',
			] );
		}

		private function send_orders( $args ) {
			$loop = new WP_Query( $args );
			$result = [];

			if ( $loop->have_posts() ) {
				while ( $loop->have_posts() ) {
					$loop->the_post();
					global $post;

					$metas = [];
					$admitted_metas = [
						'_order_total'
					];

					foreach ( get_post_meta( $post->ID ) as $k => $v ) {
						if ( string_starts_with( $k, FLUXTORE_PREFIX ) ) {
							$metas[ str_replace(FLUXTORE_PREFIX, '', $k) ] = array_shift( $v );
						} else if ( in_array( $k, $admitted_metas ) ) {
							$metas[ substr( $k, 1 ) ] = array_shift( $v );
						}
					}

					$result []= ( object )array_merge( ( array )$post, ( array )$metas );
				}
			}

			wp_reset_postdata();
			wp_send_json_success( [ 'orders' => $result ] );
		}

		private function get_basic_args() {
			$keys = wc_get_order_statuses();
			unset( $keys[ 'wc-cancelled' ] );
			unset( $keys[ 'wc-refunded' ] );
			unset( $keys[ 'wc-failed' ] );
			$keys = array_keys( $keys );

			return [
				'meta_key' => FLUXTORE_PREFIX . 'is_fluxtore_order',
				'post_type' => 'shop_order',
				'post_status' => $keys,
				'posts_per_page' => -1
			];
		}

		function get_orders_today() {
			$today = getdate();

			$args = $this->get_basic_args();

			$args[ 'date_query' ] = [ [
				'year' => $today[ 'year' ],
				'month' => $today[ 'mon' ],
				'day' => $today[ 'mday' ],
			] ];

			$this->send_orders( $args );
		}

		function get_orders_yesterday() {
			$args = $this->get_basic_args();

			$args[ 'date_query' ] = [
				'column' => 'post_date',
				'after' => '- 2 days',
				'before' => '- 0 days'
			];

			$this->send_orders( $args );
		}

		function get_orders_last_week() {
			$args = $this->get_basic_args();

			$args[ 'date_query' ] = [
				'column' => 'post_date',
				'after' => '- 8 days',
				'before' => '- 0 days'
			];

			$this->send_orders( $args );
		}

		function get_orders_last_month() {
			$args = $this->get_basic_args();

			$args[ 'date_query' ] = [
				'column' => 'post_date',
				'after' => '- 31 days',
				'before' => '- 0 days',
			];

			$this->send_orders( $args );
		}

		function get_orders_always() {
			$args = $this->get_basic_args();

			$this->send_orders( $args );
		}
	}
}

fluXtore_AjaxOrders::init();