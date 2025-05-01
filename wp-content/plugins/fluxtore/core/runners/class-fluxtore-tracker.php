<?php

namespace fluXtore\Core\Runners;

// Exit if accessed directly.
use fluXtore\Core\Models\fluXtore_Flow;


if (!defined('ABSPATH')) {
	exit;
}

// Avoid defining the class twice.
if (!class_exists('fluXtore_Tracker')) {
	class fluXtore_Tracker {
		/**
		 * @var self $instance
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

			self::$instance = new self();
			add_action('fluxtore_step_before_render', [self::$instance, 'visitor_track'], 10, 1);
		}

		public function visitor_track($step_id) {
			$ip = fluxtore_get_ip();

			$flow_id = get_post_meta($step_id, FLUXTORE_PREFIX . 'flow')[0];
			$transient = FLUXTORE_PREFIX . 'ip=' . $ip . '_fid=' . $flow_id;
			$transient_step = FLUXTORE_PREFIX . 'ip=' . $ip . '_fid=' . $flow_id . '_sid=' . $step_id;
			if (false === get_transient($transient)) {
				set_transient($transient, true, 1 * HOUR_IN_SECONDS);
				$flow_model = new fluXtore_Flow();
				$flow_model->new_visitor($flow_id);
			}
			if (false === get_transient($transient_step)) {
				set_transient($transient_step, true, 1 * HOUR_IN_SECONDS);
				$visitors = get_post_meta($step_id, FLUXTORE_PREFIX . 'step_visitors', true);
				$visitors_arr = $visitors ? $visitors : [];
				array_push($visitors_arr, date("Y-m-d"));
				update_post_meta($step_id, FLUXTORE_PREFIX . 'step_visitors', $visitors_arr);
			}
		}
	}
}

fluXtore_Tracker::init();
