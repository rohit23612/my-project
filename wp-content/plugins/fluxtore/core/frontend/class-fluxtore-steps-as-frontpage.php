<?php

namespace fluXtore\Core\Frontend;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_Steps_As_Frontpage' ) ) {
	class fluXtore_Steps_As_Frontpage {
		/**
		 * @var self $instance
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
			if ( ! is_null( self::$instance ) ) {
				return;
			}

			self::$instance = new self();

			add_action( 'pre_get_posts', [ self::$instance, 'pre_get_posts' ] );
			add_action( 'template_redirect', [ self::$instance, 'template_redirect' ] );

			if ( is_admin() ) {
				add_filter( 'wp_dropdown_pages', [ self::$instance, 'wp_dropdown_pages' ] );
			}
		}

		public function pre_get_posts( $query ) {

			if ( $query->is_main_query() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

				$post_type = $query->get( 'post_type' );

				$page_id = $query->get( 'page_id' );

				if ( empty( $post_type ) && ! empty( $page_id ) ) {
					$query->set( 'post_type', get_post_type( $page_id ) );
				}
			}
		}

		function wp_dropdown_pages( $output ) {
			global $pagenow;

			if ( ( 'options-reading.php' === $pagenow || 'customize.php' === $pagenow ) && preg_match( '#page_on_front#', $output ) ) {
				$args = [
					'post_type'   => FLUXTORE_STEP,
					'numberposts' => -1,
					'meta_query'  => [
						'relation' => 'OR', [
							'key'   => FLUXTORE_PREFIX . 'tag',
							'value' => FLUXTORE_LANDING_TAG,
						]/*, [
							'key'   => FLUXTORE_PREFIX . 'tag',
							'value' => FLUXTORE_CHECKOUT_TAG,
						]*/
					]
				];

				$steps = get_posts( $args );

				if ( is_array( $steps ) && ! empty( $steps ) ) {
					$option = '';

					$front_page_id = get_option( 'page_on_front' );

					foreach ( $steps as $step ) {
						$selected = selected( $front_page_id, $step->ID, false );
						$option .= "<option value=\"{$step->ID}\"{$selected}>{$step->post_title} ( #{$step->ID} - fluXtore )</option>";
					}

					$option .= '</select>';
					$output = str_replace( '</select>', $option, $output );
				}
			}

			return $output;
		}

		public function template_redirect() {
			global $post;

			if ( class_exists( '\Elementor\Plugin' ) ) {
				if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
					return;
				}
			}

			if ( is_singular() && ! is_front_page() && get_option( 'page_on_front' ) == $post->ID && $post->post_type === FLUXTORE_STEP ) {
				wp_safe_redirect( site_url(), 301 );
			}
		}
	}
}

fluXtore_Steps_As_Frontpage::init();