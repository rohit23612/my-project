<?php

namespace fluXtore\Core\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_UrlRewriter' ) ) {
	class fluXtore_UrlRewriter {
		const REWRITE_OPTION = "rewrite_url";
		const REWRITE_OPTION_PREVIEW = "rewrite_url_preview";

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
			$rewrite_enabled = fluXtore_Settings::get( self::REWRITE_OPTION );
			$site_url = get_site_url();
			$permalink = $rewrite_enabled ? $site_url . '/{step_slug}' : $site_url . '/fluxtore_step/{step_slug}';

			fluXtore_Settings::register_option( 'advanced', self::REWRITE_OPTION, [
				'type' => 'bool',
				'label' => 'Remove fluxtore_step form Steps URL',
				'pro' => true,
				'section' => 'rewrite'
			] );

			fluXtore_Settings::register_option( 'advanced', self::REWRITE_OPTION_PREVIEW, [
				'type' => 'text',
				'label' => 'Permalink preview',
				'parent' => self::REWRITE_OPTION,
				'formula' => '
					let site_url = "' . $site_url . '";
					
					if (parent.checked) {
						self.value = "' . $site_url . '/{step_slug}";
					} else {
						self.value = "' . $site_url . '/fluxtore_step/{step_slug}";
					}
				',
				'default' => $permalink,
				'section' => 'rewrite',
				'disabled' => true
			] );
		}
	}
}

fluXtore_UrlRewriter::init();