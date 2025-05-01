<?php

namespace fluXtore\Core;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Avoid defining the class twice.
if (!class_exists('fluXtore_Loader')) {
	/**
	 * fluXtore Loader.
	 *
	 * @package fluXtore
	 */
	class fluXtore_Loader {
		/**
		 * @var fluXtore_Loader $instance
		 */
		private static $instance;

		/**
		 * Private constructor for Singleton
		 */
		private function __construct() {
		}

		/**
		 * Initializes the core
		 */
		public static function init() {
			if (!is_null(self::$instance)) {
				return;
			}

			self::$instance = new fluXtore_Loader();

			self::define_constants();
			self::load_classes();

			add_action('init', [self::$instance, '__register_post_types'], 10, 2);
			add_action('admin_enqueue_scripts', [self::$instance, '__admin_enqueue_scripts'], 10, 2);
			add_action('wp_enqueue_scripts', [self::$instance, '__enqueue_scripts'], 10, 2);

			/**
			 * fluXtore Init.
			 *
			 * Fires when fluXtore is instantiated.
			 *
			 * @since 1.0.0
			 */
			do_action('fluxtore_init');
		}

		/**
		 * Defines the required constants
		 */
		private static function define_constants() {
			self::$instance->__define_constants();
		}

		private function __define_constants() {
			define('FLUXTORE_BASE', plugin_basename(FLUXTORE_PLUGIN_FILE));
			define('FLUXTORE_SLUG', 'fluxtore');
			define('FLUXTORE_PREFIX', '__fluxtore__');
			define('FLUXTORE_FLOW', 'fluxtore_flow');
			define('FLUXTORE_STEP', 'fluxtore_step');

			define('FLUXTORE_LANDING_TAG', 'Landing');
			define('FLUXTORE_CHECKOUT_TAG', 'Checkout');
			define('FLUXTORE_UPSELL_TAG', 'Upsell');
			define('FLUXTORE_DOWNSELL_TAG', 'Downsell');
			define('FLUXTORE_THANK_YOU_TAG', 'Thank You');

			define('FLUXTORE_SERVER_URL', 'https://fluxtore.com/');
			define('FLUXTORE_API_URL', FLUXTORE_SERVER_URL . 'wp-json/fluxtore/v1/');

			define('FREE_TEMPLATE_API_KEY', 'AKIAV632FVM3PYP7J4YQ');
			define('FREE_TEMPLATE_API_SECRET', '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');
			define('FREE_TEMPLATE_BUCKET', 'fluxtore-template-free');
			define('FREE_TEMPLATE_AWS_REGION', 'eu-central-1');

			define('PRO_TEMPLATE_API_KEY', 'AKIAV632FVM3DD7SXQDZ');
			define('PRO_TEMPLATE_API_SECRET', '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');
			define('PRO_TEMPLATE_BUCKET', 'fluxtore-template-premium');
			define('PRO_TEMPLATE_AWS_REGION', 'eu-central-1');
		}

		public function __register_post_types() {
			register_post_type(FLUXTORE_FLOW);
			register_post_type(FLUXTORE_STEP, [
				'labels' => [
					'name' => esc_html_x('Steps', 'flow step general name', 'fluxtore'),
					'singular_name' => esc_html_x('Step', 'flow step singular name', 'fluxtore'),
					'search_items' => esc_html__('Search Steps', 'fluxtore'),
					'all_items' => esc_html__('All Steps', 'fluxtore'),
					'edit_item' => esc_html__('Edit Step', 'fluxtore'),
					'view_item' => esc_html__('View Step', 'fluxtore'),
					'add_new' => esc_html__('Add New', 'fluxtore'),
					'update_item' => esc_html__('Update Step', 'fluxtore'),
					'add_new_item' => esc_html__('Add New', 'fluxtore'),
					'new_item_name' => esc_html__('New Step Name', 'fluxtore'),
				],
				'public' => true,
				'query_var' => true,
				'can_export' => true,
				'exclude_from_search' => true,
				'show_ui' => true,
				'show_in_menu' => false,
				'show_in_admin_bar' => true,
				'show_in_rest' => true,
				'supports' => [
                    'title',
                    'editor',
                    'elementor',
                    'revisions',
                    'custom-fields'
                ],
				'capability_type' => 'post',
				'capabilities' => [
					'create_posts' => 'do_not_allow'
				],
				'map_meta_cap' => true,
			]);

            register_meta(
                'post',
                FLUXTORE_PREFIX . 'tag',
                [
                    'show_in_rest' => true,
                    'single' => true,
                    'type' => 'string',
                    'default' => ''
                ]
            );

			if ("" . get_option('FLUXTORE_INSTALLATION', 0) === "1") {
				flush_rewrite_rules();
				update_option('FLUXTORE_INSTALLATION', 0);
			}
		}

		private static function load_classes() {
			self::$instance->__load_classes();
		}

		private function __load_classes() {
			// Utilities
			require_once "utilities/templates.php";
			require_once "utilities/utilities.php";

			// Admin
			require_once "admin/class-fluxtore-admin-menu.php";
			require_once "admin/class-fluxtore-go-pro.php";

			// Models
			require_once "models/class-fluxtore-model-base.php";
			require_once "models/class-fluxtore-flow.php";
			require_once "models/class-fluxtore-step.php";

			// Ajax
			require_once "admin/ajax/class-fluxtore-ajax-base.php";
			require_once "admin/ajax/class-fluxtore-ajax-flows.php";
			require_once "admin/ajax/class-fluxtore-ajax-orders.php";
			require_once "admin/ajax/class-fluxtore-ajax-plan.php";
			require_once "admin/ajax/class-fluxtore-ajax-products.php";
			require_once "admin/ajax/class-fluxtore-ajax-settings.php";
			require_once "admin/ajax/class-fluxtore-ajax-steps.php";
			require_once "admin/ajax/class-fluxtore-ajax-fields.php";
			require_once "admin/ajax/class-fluxtore-ajax-templates.php";

			// Frontend
			require_once "frontend/class-fluxtore-load-template.php";
			require_once "frontend/class-fluxtore-override-woocommerce.php";
			require_once "frontend/class-fluxtore-steps-as-frontpage.php";

			// Runners
			require_once "runners/class-fluxtore-import-template.php";
			require_once "runners/class-fluxtore-tracker.php";

			// Settings
			require_once "settings/class-fluxtore-settings.php";
			require_once "settings/class-fluxtore-data-persistence.php";
			require_once "settings/class-fluxtore-url-rewriter.php";
			require_once "settings/class-fluxtore-users-generic.php";

			// Elementor
			require_once "elementor/class-fluxtore-el-widgets-loader.php";

			//Analytics
			require_once "admin/class-fluxtore-query.php";
			require_once "admin/stores/DataStore.php";
			require_once "admin/query/HomeOrderQuery.php";
			require_once "admin/query/HomeChartQuery.php";
			require_once "admin/query/HomeBoardQuery.php";
			require_once "admin/stores/orders/HomeDataStore.php";
			require_once "admin/stores/charts/HomeDataStore.php";
			require_once "admin/stores/leaderboard/HomeDataStore.php";
			require_once "admin/controllers/LeaderBoard/HomeController.php";
			require_once "admin/controllers/Indicators/HomeController.php";
			require_once "admin/controllers/Chart/HomeController.php";

			// WooCommerce Admin
			require_once "admin/woocommerce/class-fluxtore-order-details.php";

			// Providers
			require_once "admin/providers/class-fluxtore-s3-provider.php";
			require_once "admin/providers/class-fluxtore-s3-free-template-provider.php";
			require_once "admin/providers/class-fluxtore-s3-pro-template-provider.php";
		}

		/**
		 * Enqueue the admin scripts
		 */
		public function __admin_enqueue_scripts() {
			if (!(is_admin() && isset($_GET['page']) && $_GET['page'] === 'fluxtore')) {
				return;
			}

			wp_enqueue_script('fluxtore-empty-js', FLUXTORE_ASSETS_URL . '/empty.js');
			wp_add_inline_script('fluxtore-empty-js', 'let fluxtore_site_url = "' . get_site_url() . '";');

			$script_asset_path = FLUXTORE_PLUGIN_DIR . 'assets/bundle.asset.php';
			$script_asset = file_exists($script_asset_path)
				? require($script_asset_path)
				: array('dependencies' => array(), 'version' => filemtime($script_asset_path));
			wp_enqueue_script('fluxtore-bundle-js', FLUXTORE_ASSETS_URL . '/bundle.js', $script_asset['dependencies'], $script_asset['version'], true);
			wp_localize_script('fluxtore-bundle-js', 'FLUXTORE_DATA', [
				'billing_fields' => WC()->checkout->get_checkout_fields()['billing'],
				'shipping_fields' => WC()->checkout->get_checkout_fields()['shipping'],
				'fields' => WC()->checkout->get_checkout_fields()['billing'],
				'wp_site_url' => get_site_url()
			]);

			wp_enqueue_style('fluxtore-style', FLUXTORE_ASSETS_URL . '/style.css', ['wp-components', 'wc-components']);
		}

		/**
		 * Enqueue the scripts
		 */
		public function __enqueue_scripts() {
			//wp_enqueue_script( 'fluxtore-checkout-js', FLUXTORE_ASSETS_URL . '/js/checkout/index.js', [ 'jquery' ] );
		}
	}
}

if(FLUXTORE_DEFAULT_EDITIOR == "Elementor"){
	if (is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active('elementor/elementor.php')) {
		// Initializes fluXtore.
		add_action('elementor/init', [fluXtore_Loader::class, 'init']);
	} else {
		require_once "utilities/class-fluxtore-dependencies.php";
	}
} else {
	if (is_plugin_active('woocommerce/woocommerce.php')) {
		// Initializes fluXtore.
		add_action('plugins_loaded', [fluXtore_Loader::class, 'init']);
	} else {
		require_once "utilities/class-fluxtore-dependencies.php";
	}
	
}