<?php

/**
 * Elementor Classes.
 *
 * @package fluxtore
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Checkout Fields Widget
 *
 * @since 1.0.0
 */
class fluXtore_Checkout extends \Elementor\Widget_Base
{

    /**
     * Module should load or not.
     *
     * @param string $step_type Current step type.
     *
     * @return bool true|false.
     */
    public static function is_enable($step_type)
    {
        return FLUXTORE_CHECKOUT_TAG === $step_type;
    }

    /**
     * Retrieve the widget name.
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'checkout';
    }

    /**
     * Retrieve the widget title.
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return __('Checkout fields', 'fluXtore');
    }

    /**
     * Retrieve the widget icon.
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'fas fa-table';
    }

    /**
     * Retrieve the list of categories the widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * Note that currently Elementor supports only one category.
     * When multiple categories passed, Elementor uses the first one.
     *
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return ['fluxtore-widgets'];
    }

    /**
     * Retrieve Widget Keywords.
     *
     * @return array Widget keywords.
     */
    public function get_keywords()
    {
        return ['fluxtore', 'checkout fields', 'form'];
    }


    public function modify_billings_fields($fields)
    {
        global $post;

        if ($visible_fields = fluxtore_get_checkout_fields($post->ID, $fields, 'billing')) {

            $fields = $visible_fields;
        };
        return $fields;
    }
    public function modify_shipings_fields($fields)
    {
        global $post;

        if ($visible_fields = fluxtore_get_checkout_fields($post->ID, $fields, 'shipping')) {

            $fields = $visible_fields;
        }


        return $fields;
    }

    public function add_percentages($field, $key, $args, $value)
    {

        $field = preg_replace('/(<p\b[^><]*)>/i', '$1 style="width:' . $args['percentage'] . '%;">', $field);
        return $field;
    }


    /**
     * Render Order Details Form output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $checkout = WC()->checkout();
        // add_filter('woocommerce_checkout_fields', [$this, 'modify_billing_fields']);
        // add_filter('woocommerce_default_address_fields', [$this, 'modify_checkout_fields']);
        add_filter('woocommerce_form_field', [$this, 'add_percentages'], 10, 4);
        add_filter('woocommerce_billing_fields', [$this, 'modify_billings_fields']);
        add_filter('woocommerce_shipping_fields', [$this, 'modify_shipings_fields']);
        echo do_shortcode('[woocommerce_checkout]');
        // wc_get_template('checkout/form-checkout.php', ['settings' => $settings, 'checkout' => $checkout], '', FLUXTORE_PLUGIN_DIR . 'woocommerce/');

        remove_filter('woocommerce_form_field', [$this, 'add_percentages'], 10, 4);
        remove_filter('woocommerce_billing_fields', [$this, 'modify_billings_fields']);
        remove_filter('woocommerce_shipping_fields', [$this, 'modify_shipings_fields']);
    }
}
