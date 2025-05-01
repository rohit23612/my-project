<?php

namespace fluXtore\Core\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_AdminMenu' ) ) {
	class fluXtore_AdminMenu {
		/**
		 * @var self $instance
		 */
		private static $instance;

		/**
		 * @var string $_menu_slug
		 */
		private $_menu_slug;

		/**
		 * @var array $_submenus
		 */
		private $_submenus;

		/**
		 * Private constructor for Singleton
		 */
		private function __construct() {
			$this->_menu_slug = FLUXTORE_SLUG;
			$this->_submenus = [
				'home' => 'Home',
				'funnels' => 'Funnels',
				'settings' => 'Settings'
			];
				// Old settings injection.
				add_filter( 'woocommerce_components_settings', [$this, 'add_api_settings'], 999 );
				// New settings injection.
				add_filter( 'woocommerce_admin_shared_settings', [$this, 'add_api_settings'], 999 );
				add_filter('woocommerce_admin_rest_controllers', [$this, 'add_controllers']);
				add_filter( 'woocommerce_data_stores', [$this, 'add_data_stores' ] );
		}

		/**
		 * Initializes the core
		 */
		public static function init() {
			if ( !is_null( self::$instance ) ) {
				return;
			}

			self::$instance = new self();
			add_action( 'admin_menu', [ self::$instance, '__setup_menu' ] );
		}

		/**
		 * Add submenu to admin menu.
		 *
		 * @since 1.0.0
		 */
		public function __setup_menu() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			global $submenu;

			$capability  = 'manage_options';

			add_menu_page(
				'fluXtore',
				'fluXtore',
				$capability,
				$this->_menu_slug,
				[ $this, '__render' ],
				'data:image/svg+xml;base64,' . base64_encode( file_get_contents( FLUXTORE_PLUGIN_DIR . 'assets/images/fluxtore-icon.svg' ) ),
				40
			);

			foreach ( $this->_submenus as $id => $name ) {
				if ( $id === 'home' ) {
					continue;
				}

				add_submenu_page(
					$this->_menu_slug,
					__( $name, 'fluxtore' ),
					__( $name, 'fluxtore' ),
					$capability,
					'admin.php?page=' . $this->_menu_slug . '#/' . $id
				);
			}

			// Rename to Home menu.
			$submenu[$this->_menu_slug][0][0] = __( 'Home', 'fluxtore' );
		}

		public function __render() {			
			$path = isset( $_GET['path'] ) ? sanitize_text_field( wp_unslash( $_GET['path'] ) ) : 'home';
			$page_action = '';

			if ( isset( $_GET['action'] )  ) {
				$page_action = sanitize_text_field( wp_unslash( $_GET['action'] ) );
				$page_action = str_replace( '_', '-', $page_action );
			}

			$submenus = $this->_submenus;

			include_once FLUXTORE_PLUGIN_DIR . "views/fluxtore-admin-menu.php";
		}

		public function add_api_settings($data) {
			if ( !is_admin() ) {
				return $data;
			}
		
			$data['fluXtoreEndpoints'] = [
				[
					'chart' => 'count',
					'label' => __('Orders'),
					'stat' => 'order/count'
				],
				[
					'chart' => 'avg_total',
					'label' => __('Average order value'),
					'stat' => 'order/avg_total'
				],
				[
					'chart' => 'visits',
					'label' => __('Visits'),
					'stat' => 'visit/count'
				],
				[
					'chart' => 'revenue',
					'label' => __('Revenue'),
					'stat' => 'order/revenue'
				],
				[
					'chart' => 'revenue_visit',
					'label' => __('Revenue per visit'),
					'stat' => 'visit/revenue'
				],

			];
			$data['fluXtoreData'] = [
				'first_order' => $this->get_first_flux_order_date(),
				
			]; 
			return $data;
		}

		public function add_controllers($controllers) {
			// $controllers[] = 'fluXtore\Core\Admin\Controllers\Chart\Controller';
			// $controllers[] = 'fluXtore\Core\Admin\Controllers\LeaderBoard\Controller';
			// $controllers[] = 'fluXtore\Core\Admin\Controllers\Indicators\Controller';
			$controllers[] = 'fluXtore\Core\Admin\Controllers\Indicators\HomeController';
			$controllers[] = 'fluXtore\Core\Admin\Controllers\Chart\HomeController';
			$controllers[] = 'fluXtore\Core\Admin\Controllers\LeaderBoard\HomeController';
			
			return $controllers;
		}

		public function add_data_stores($data_stores) {
			// $data_stores['fluxtore-order'] = 'fluXtore\Core\Admin\Stores\Orders\DataStore';	
			// $data_stores['fluxtore-chart'] = 'fluXtore\Core\Admin\Stores\Charts\DataStore';	
			// $data_stores['fluxtore-leaderboard'] = 'fluXtore\Core\Admin\Stores\LeaderBoard\DataStore';	
			$data_stores['fluxtore-home-order'] = 'fluXtore\Core\Admin\Stores\Orders\HomeDataStore';	
			$data_stores['fluxtore-home-chart'] = 'fluXtore\Core\Admin\Stores\Charts\HomeDataStore';	
			$data_stores['fluxtore-home-leaderboard'] = 'fluXtore\Core\Admin\Stores\LeaderBoard\HomeDataStore';
			return $data_stores;
		}

		public function get_first_flux_order_date() {
			$orders = wc_get_orders([
				'limit' => -1,
				'status' => ['wc-completed','wc-processing','wc-on-hold','wc-pending'],
				'meta_key' => FLUXTORE_PREFIX . 'is_fluxtore_order',
				'orderby' => 'date',
				'order' => 'ASC',
			]);

			if (!empty($orders)) {
				return $orders[0]->get_date_created()->date('Y-m-d');
			}

			return date('Y-m-d', strtotime('now'));
		}
	}
}

// Initializes the admin menu.
fluXtore_AdminMenu::init();
