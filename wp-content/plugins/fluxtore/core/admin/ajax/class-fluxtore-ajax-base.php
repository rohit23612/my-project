<?php

namespace fluXtore\Core\Admin\Ajax;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_AjaxBase' ) ) {
	abstract class fluXtore_AjaxBase {
		/**
		 * Ajax action prefix.
		 *
		 * @var string $_prefix
		 */
		private $_prefix;

		/**
		 * Private constructor for Singleton
		 */
		protected function __construct() {
			$this->_prefix = 'fluxtore';
		}

		abstract function register_ajax_events();

		/**
		 * Register ajax events.
		 *
		 * @param array $ajax_events Ajax events.
		 */
		public function init_ajax_events( $ajax_events ) {
			if ( ! empty( $ajax_events ) ) {
				foreach ( $ajax_events as $ajax_event ) {
					add_action( 'wp_ajax_' . $this->_prefix . '_' . $ajax_event, [ $this, $ajax_event ] );
				}
			}
		}
	}
}
