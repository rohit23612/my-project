<?php

namespace fluXtore\Core\Frontend;

// Exit if accessed directly.
use fluXtore\Core\Models\fluXtore_Flow;
use fluXtore\Core\Models\fluXtore_Step;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_LoadTemplate' ) ) {
	class fluXtore_LoadTemplate {
		/**
		 * @var fluXtore_LoadTemplate $instance
		 */
		private static $instance;

		/**
		 * Private constructor for Singleton
		 */
		private function __construct() {}

		/**
		 * Initializes the core
		 */
		public static function init() {
			if ( !is_null( self::$instance ) ) {
				return;
			}

			self::$instance = new fluXtore_LoadTemplate();

			add_filter( 'template_include', [ self::$instance, 'load_page_template' ], 90 );
		}

		function load_page_template( $template ) {
			global $post;

			if ( 'string' == gettype( $template ) && is_object( $post ) && FLUXTORE_STEP === $post->post_type ) {
                $step = new fluXtore_Step();
                $flow_id = $step->get_flow( $post->ID )[0];
				$template_file = FLUXTORE_PLUGIN_DIR . 'templates/default.php';

				do_action( 'fluxtore_step_before_render', $post->ID );

				if ( file_exists( $template_file ) ) {
                    $GLOBALS['fluxtore_show_header'] = get_post_meta( $flow_id, FLUXTORE_PREFIX . 'show_header', true ) === "true";
                    $GLOBALS['fluxtore_show_footer'] = get_post_meta( $flow_id, FLUXTORE_PREFIX . 'show_footer', true ) === "true";
                    return $template_file;
				}
			}

			return $template;
		}
	}
}

fluXtore_LoadTemplate::init();