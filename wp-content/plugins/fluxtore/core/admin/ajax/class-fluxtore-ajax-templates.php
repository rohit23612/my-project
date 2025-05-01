<?php

namespace fluXtore\Core\Admin\Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_AjaxTemplates' ) ) {
	class fluXtore_AjaxTemplates extends fluXtore_AjaxBase {
		/**
		 * @var fluXtore_AjaxTemplates $instance
		 */
		private static $instance;

		public static function init() {
			if ( !is_null( self::$instance ) ) {
				return;
			}

			self::$instance = new fluXtore_AjaxTemplates();
			self::$instance->register_ajax_events();
		}

		function register_ajax_events() {
			$this->init_ajax_events([
				'get_templates',
				'get_steps_templates'
			]);
		}

		function get_templates() {
			wp_send_json_success( [ "elementor_templates" => fluxtore_get_templates(), "elementor-pro-templates" => fluxtore_get_pro_templates() , "gutenberg_templates" => fluxtore_get_gutenburg_templates() , "gutenberg-pro-templates" => array()] );
		}

		function get_steps_templates() {
			$steps = [];

			foreach ( fluxtore_get_steps_templates() as $step ) {
				if ( isset( $step[ 'hide' ] ) && $step[ 'hide' ] ) {
					continue;
				}

				$steps[] = $step;
			}

			wp_send_json_success( [ "templates" => $steps, "pro-templates" => fluxtore_get_pro_steps_templates() ] );
		}
	}
}

fluXtore_AjaxTemplates::init();