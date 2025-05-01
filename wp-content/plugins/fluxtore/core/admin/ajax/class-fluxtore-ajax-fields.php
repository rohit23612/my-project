<?php

namespace fluXtore\Core\Admin\Ajax;

// Exit if accessed directly.
use fluXtore\Core\Models\fluXtore_Step;
use fluXtore\Core\Runners\fluXtore_ImportTemplate;

if (!defined('ABSPATH')) {
    exit;
}

// Avoid defining the class twice.
if (!class_exists('fluXtore_AjaxFields')) {
    class fluXtore_AjaxFields extends fluXtore_AjaxBase {
        /**
         * @var self $instance
         */
        private static $instance;

        public static function init() {
            if (!is_null(self::$instance)) {
                return;
            }

            self::$instance = new self();
            self::$instance->register_ajax_events();
        }

        function register_ajax_events() {
            $this->init_ajax_events([
                'save_checkout_fields'
            ]);
        }

        function save_checkout_fields() {
            // $step_id = absint($_GET['id']);
            $step_id = absint($_POST['id']);
            $billing_fields = wc_clean(json_decode(wp_unslash($_POST['billing_fields']), true));
            $shipping_fields = wc_clean(json_decode(wp_unslash($_POST['shipping_fields']), true));
			$hideAdditionalNotes = wc_clean(json_decode(wp_unslash($_POST['hide_additional_notes']), true));

            update_post_meta($step_id, FLUXTORE_PREFIX . 'billing_fields', $billing_fields);
            update_post_meta($step_id, FLUXTORE_PREFIX . 'shipping_fields', $shipping_fields);
            update_post_meta($step_id, FLUXTORE_PREFIX . 'hide_additional_notes', $hideAdditionalNotes);
            wp_send_json_success([
                'id' =>  $step_id,
                'post' => $_POST,
                'billing_fields' => $billing_fields,
                'shipping_fields' => $shipping_fields
            ]);
        }
    }
}

fluXtore_AjaxFields::init();
