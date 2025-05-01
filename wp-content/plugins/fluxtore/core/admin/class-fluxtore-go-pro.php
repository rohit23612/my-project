<?php

namespace fluXtore\Core\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_GoPro' ) ) {
	class fluXtore_GoPro {
		/**
		 * @var self $instance
		 */
		private static $instance;

		private static $FLOWS_LIMITS = 5;

		private function __construct() {}

		public static function init() {
			if ( !is_null( self::$instance ) ) {
				return;
			}

			self::$instance = new self();

			add_filter( 'plugin_action_links_' . FLUXTORE_BASE, [ self::$instance, 'add_action_links' ] );
		}

		public function add_action_links( $links ) {
			$default_url = add_query_arg(
				[
					'page' => FLUXTORE_SLUG,
					'path' => '#/settings',
				],
				admin_url()
			);

			$mylinks = [
				'<a href="' . $default_url . '">' . __( 'Settings', 'fluxtore' ) . '</a>',
				'<a target="_blank" href="' . esc_url( 'https://fluxtore.com/docs' ) . '">' . __( 'Docs', 'fluxtore' ) . '</a>',
			];

			if ( ! is_fluxtore_pro() ) {
				array_push( $mylinks, '<a style="color: #5f9bca; font-weight: bold;" target="_blank" href="' . esc_url( 'https://fluxtore.com/pricing?email=' . wp_get_current_user()->user_email ) . '"> Go Pro </a>' );
			}

			return array_merge( $links, $mylinks );
		}

		public static function get_flows_limits() {
			return self::$instance->_get_flows_limits();
		}

		private function _get_flows_limits() {
			return apply_filters( 'fluxtore_calculate_flows_limits', self::$FLOWS_LIMITS );
		}
	}
}

fluXtore_GoPro::init();