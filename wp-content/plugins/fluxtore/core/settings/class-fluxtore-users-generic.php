<?php

namespace fluXtore\Core\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_UsersGeneric' ) ) {
	class fluXtore_UsersGeneric {
		const FLUXTORE_AUTHOR_ONLY = "authors_only";

		/**
		 * @var self $instance
		 */
		private static $instance;

		private function __construct() {}

		public static function init() {
			if ( !is_null( self::$instance ) ) {
				return;
			}

			self::$instance = new self();
			add_action( 'fluxtore_register_options', [ self::$instance, 'add_options' ] );
		}

		public function add_options() {
			fluXtore_Settings::register_option( 'users', self::FLUXTORE_AUTHOR_ONLY, [
				'type' => 'bool',
				'label' => 'Permit only to authors to see/edit their funnels',
				'section' => 'generic',
                'pro' => true
			] );
		}
	}
}

fluXtore_UsersGeneric::init();