<?php

namespace fluXtore\Core\Admin\Ajax;

use fluXtore\Core\Admin\fluXtore_GoPro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_AjaxPlan' ) ) {
	class fluXtore_AjaxPlan extends fluXtore_AjaxBase {
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
			$this->init_ajax_events([
				'can_create_new_flow'
			]);
		}

		function can_create_new_flow() {
			$limits = fluXtore_GoPro::get_flows_limits();

			if ( $limits > -1 && $limits <= intval( wp_count_posts( FLUXTORE_FLOW )->publish ) ) {
				$result = [
					'success' => false,
					'message' => 'Cannot create new flows. Limit of ' . $limits . ' reached.<br />If you need more please <a class="font-bold text-perfect-blue" href="https://fluxtore.com/pricing?email=' . wp_get_current_user()->user_email . '" target="_blank"/>upgrade to Pro</a>'
				];
			} else {
				$result = [
					'success' => true
				];
			}

			wp_send_json_success( $result );
		}
	}
}

fluXtore_AjaxPlan::init();