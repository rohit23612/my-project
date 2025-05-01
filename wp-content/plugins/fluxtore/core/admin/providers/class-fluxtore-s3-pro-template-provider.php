<?php

namespace fluXtore\Core\Admin\Providers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtoreS3ProTemplateProvider' ) ) {
	class fluXtoreS3ProTemplateProvider extends fluXtoreS3Provider {
		/**
		 * @var self $instance
		 */
		private static $instance;

		/**
		 * Initializes the core
		 */
		public static function init() {
			if ( !is_null( self::$instance ) ) {
				return;
			}

			self::$instance = new self();
		}

		private function __construct() {
			// SINGLETON
			$this->bucketName = PRO_TEMPLATE_BUCKET;
			parent::__construct([
				'region' => PRO_TEMPLATE_AWS_REGION,
				'accessKeyId' => PRO_TEMPLATE_API_KEY,
				'accessKeySecret' => PRO_TEMPLATE_API_SECRET
			]);
		}

		public static function __callStatic($name, $arguments) {
			return self::$instance->{"_" . $name}(...$arguments);
		}
	}
}

fluXtoreS3ProTemplateProvider::init();


