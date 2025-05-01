<?php

namespace fluXtore\Core\Admin\Ajax;

// Exit if accessed directly.
use fluXtore\Core\Settings\fluXtore_Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_AjaxSettings' ) ) {
	class fluXtore_AjaxSettings extends fluXtore_AjaxBase {
		/**
		 * @var self $instance
		 */
		private static $instance;

		public static function init() {
			if ( ! is_null( self::$instance ) ) {
				return;
			}

			self::$instance = new self();
			self::$instance->register_ajax_events();
		}

		function register_ajax_events() {
			$this->init_ajax_events( [
				'get_settings',
				'save_settings',
				'get_settings_values'
			] );
		}

		public function get_settings() {
			$result = fluXtore_Settings::get_settings();
			wp_send_json_success( [ "settings" => $result ] );
		}

		public function get_settings_values() {
			$settings = [];

			foreach ( fluXtore_Settings::get_settings() as $section ) {
				foreach ( $section as $setting_name => $setting_params ) {
					$settings[ $setting_name ] = fluXtore_Settings::get( $setting_name );
				}
			}

			wp_send_json_success( [ "settings" => $settings ] );
		}

		public function save_settings() {
			if ( !isset( $_GET[ 'fluxtore_settings' ] ) ) {
				wp_send_json_success( [] );
			}

			$settings = wc_clean( json_decode( wp_unslash( $_GET[ 'fluxtore_settings' ] ), true ) );
			$result = fluXtore_Settings::save( $settings );
			wp_send_json_success( $result );
		}
	}
}

fluXtore_AjaxSettings::init();
