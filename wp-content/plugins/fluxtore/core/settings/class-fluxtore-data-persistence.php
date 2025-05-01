<?php

namespace fluXtore\Core\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_DataPersistence' ) ) {
	class fluXtore_DataPersistence {
		const OPTIONS_PERSISTENCE_OPTION = "options_persistence";
		const FLOWS_PERSISTENCE_OPTION = "flows_persistence";

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
			fluXtore_Settings::register_option( 'advanced', self::OPTIONS_PERSISTENCE_OPTION, [
				'type' => 'bool',
				'label' => 'Remove all the fluXtore options when the plugin is uninstalled/deleted',
				'section' => 'data_persistence',
				'modal' => [
					'title' => 'Remove options on uninstall/delete?',
					'description' => 'Make sure to understand what it means before confirming',
					'type' => 'confirmation',
					'style' => 'error'
				]
			] );

			fluXtore_Settings::register_option( 'advanced', self::FLOWS_PERSISTENCE_OPTION, [
				'type' => 'bool',
				'label' => 'Remove all the fluXtore flows and steps when the plugin is uninstalled/deleted',
				'section' => 'data_persistence',
				'modal' => [
					'title' => 'Remove flows and steps on uninstall/delete?',
					'description' => 'Make sure to understand what it means before confirming',
					'type' => 'confirmation',
					'style' => 'error'
				]
			] );
		}
	}
}

fluXtore_DataPersistence::init();