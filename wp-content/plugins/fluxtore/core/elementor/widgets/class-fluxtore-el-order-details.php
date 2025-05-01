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
 * Order Details Form Widget
 *
 * @since 1.0.0
 */
class fluXtore_Order_Details extends \Elementor\Widget_Base
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
		return FLUXTORE_THANK_YOU_TAG === $step_type;
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name()
	{
		return 'order-details';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title()
	{
		return __('Order Details', 'fluXtore');
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
		return ['fluxtore', 'order details', 'form'];
	}

	/**
	 * Render Order Details Form output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render()
	{
		do_shortcode('[' . FLUXTORE_PREFIX . 'render_order_details]');
	}
}
