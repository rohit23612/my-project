<?php

namespace fluXtore\Core\Utilities;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Avoid defining the class twice.
if (!class_exists('fluXtore_Dependencies')) {
	/**
	 * fluXtore Dependencies Management.
	 *
	 * @package fluXtore
	 */
	class fluXtore_Dependencies {
		/**
		 * @var fluXtore_Dependencies $instance
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
			if (!is_null(self::$instance)) {
				return;
			}

			self::$instance = new self();

			add_action( 'admin_notices', [ self::$instance, 'generate_woocommerce_notice' ], 10, 0);
			//add_action( 'admin_notices', [ self::$instance, 'generate_elementor_notice' ], 10, 0);
		}

		public function generate_woocommerce_notice() {
			$this->generate_notice( 'woocommerce' );
		}

		public function generate_elementor_notice() {
			$this->generate_notice( 'elementor' );
		}

		private function generate_notice( $plugin ) {
			$plugin_file_name = "$plugin/$plugin.php";

			if ( is_plugin_active( $plugin_file_name ) ) {
				return;
			}

			if ( !file_exists( WP_PLUGIN_DIR . '/' . $plugin_file_name ) ) {
				$action_name = "Install";
				$url = $this->plugin_not_installed_notice( $plugin );
			} else {
				$action_name = "Activate";
				$url = $this->plugin_not_active_but_installed_notice( $plugin_file_name );
			}

			$message = '<h3>' . esc_html__( $action_name . ' the ' . ucfirst( $plugin ) . ' Plugin', 'fluXtore' ) . '</h3>';
			$message .= '<p>' . esc_html__( 'Before you can use all the features of fluXtore, you need to ' . strtolower( $action_name ) .  ' the ' . ucfirst( $plugin ) . ' plugin first.', 'fluXtore' ) . '</p>';
			$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $url, esc_html__( $action_name . ' Now', 'fluXtore' ) ) . '</p>';
			echo "<div class='error'>$message</div>";
		}

		private function plugin_not_active_but_installed_notice( $plugin ) {
			return wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
		}

		private function plugin_not_installed_notice( $plugin ) {
			return wp_nonce_url(
				add_query_arg(
					[ 'action' => "install-plugin", 'plugin' => $plugin ],
					admin_url( 'update.php' )
				),
				"install-plugin_$plugin"
			);
		}
	}
}

fluXtore_Dependencies::init();