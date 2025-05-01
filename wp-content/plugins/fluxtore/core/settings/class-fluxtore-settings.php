<?php

namespace fluXtore\Core\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_Settings' ) ) {
	class fluXtore_Settings {
		/**
		 * @var self $instance
		 */
		private static $instance;
		/**
		 * @var array $options
		 */
		private $options;
		/**
		 * @var array $cache
		 */
		private $cache;

		private function __construct() {
			$this->options = [];
			$this->cache = [];
		}

		public static function init() {
			if ( !is_null( self::$instance ) ) {
				return;
			}

			self::$instance = new self();
		}

		public static function update( $name, $value ) {
			update_option( FLUXTORE_PREFIX . $name, $value );
			self::$instance->cache[ $name ] = $value;
		}

		public static function get( $name, $default = false ) {
			if ( isset( self::$instance->cache[ $name ] ) ) {
				$result = self::$instance->cache[ $name ];
			} else {
				$result = get_option( FLUXTORE_PREFIX . $name, $default );
				self::$instance->cache[ $name ] = $result;
			}

			return $result;
		}

		public static function delete( $name ) {
			delete_option( $name );
		}

		public static function register_option( $section, $option, $args = [] ) {
			if ( !isset( self::$instance->options[ $section ]) ) {
				self::$instance->options[ $section ] = [];
			}

			$defaults = [
				'type' => 'text',
				'label' => $option,
				'default' => self::get( $option, '' ),
				'pro' => false
			];

			self::$instance->options[ $section ][ $option ] = wp_parse_args( $args, $defaults );
		}

		public static function get_settings() {
			do_action( 'fluxtore_register_options' );
			return self::$instance->options;
		}

		public static function save( $settings ) {
			foreach ( $settings as $key => $value ) {
				$value = apply_filters( 'fluxtore_settings_' . $key . '_save', $value );
				fluXtore_Settings::update( $key, $value );
			}

			return true;
		}
	}
}

fluXtore_Settings::init();