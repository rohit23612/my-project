<?php

namespace fluXtore\Core\Elementor;

use fluXtore\Core\Models\fluXtore_Step;

defined('ABSPATH') || exit;

/**
 * Set up Widgets Loader class
 */
class fluXtore_Widgets_Loader
{
	/**
	 * Member Variable
	 *
	 * @var self instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function init()
	{
		if (!is_null(self::$instance)) {
			return;
		}

		self::$instance = new self();
	}

	/**
	 * Setup actions and filters.
	 */
	private function __construct()
	{
		// Register category.
		add_action('elementor/elements/categories_registered', [$this, 'register_widget_category']);

		// Register widgets.
		add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
	}

	/**
	 * Returns Script array.
	 *
	 */
	public static function get_widget_list()
	{
		$widget_list = [
			'next-step-button',
			'order-details',
			// 'checkout'
		];

		$widget_list = apply_filters('fluxtore_elementor_widgets', $widget_list);

		return $widget_list;
	}

	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 */
	public function include_widgets_files()
	{
		$widget_list = $this->get_widget_list();

		if (!empty($widget_list)) {
			foreach ($widget_list as $handle => $data) {
				$file_path = FLUXTORE_PLUGIN_DIR . 'core/elementor/widgets/class-fluxtore-el-' . $data . '.php';
				if (file_exists($file_path)) {
					require_once $file_path;
					continue;
				}

				if (is_fluxtore_pro()) {
					$file_path = FLUXTORE_PRO_PLUGIN_DIR . 'core/elementor/widgets/class-fluxtore-pro-el-' . $data . '.php';
					if (file_exists($file_path)) {
						require_once $file_path;
					}
				}
			}
		}
	}

	/**
	 * Register Category
	 */
	public function register_widget_category($this_cat)
	{
		$category = __('fluXtore', 'fluxtore');

		$this_cat->add_category(
			'fluxtore-widgets',
			[
				'title' => $category,
				'icon'  => 'eicon-font',
			]
		);

		return $this_cat;
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 */
	public function register_widgets()
	{
		global $post;

		if (!isset($post)) {
			return;
		}

		$step_model = new fluXtore_Step();
		$step = $step_model->get($post->ID);

		add_filter('fluxtore_enable_widget_for_post', [$this, 'enable_for_post'], 1, 2);
		$can_post = apply_filters('fluxtore_enable_widget_for_post', false, $step->post_type);

		if ($can_post && class_exists('\Elementor\Plugin')) {

			$widget_manager = \Elementor\Plugin::$instance->widgets_manager;

			$widget_list = $this->get_widget_list();

			// Its is now safe to include Widgets files.
			$this->include_widgets_files();

			foreach ($widget_list as $widget) {
				$widget_name = str_replace('-', ' ', $widget);

				$class_name = 'fluXtore_' . str_replace(' ', '_', ucwords($widget_name));

				add_filter('fluxtore_enable_widget', [$this, 'enable'], 1, 3);
				$enabled = apply_filters('fluxtore_enable_widget', false, $class_name, $step);

				if ($enabled) {
					$widget_manager->register_widget_type(new $class_name());
				}
			}
		}
	}

	public function enable_for_post($can_post, $post_type)
	{
		return $can_post || FLUXTORE_STEP === $post_type;
	}

	public function enable($enabled, $class_name, $post)
	{
		$tag_param = FLUXTORE_PREFIX . 'tag';

		return $enabled || ($this->enable_for_post(false, $post->post_type) && $class_name::is_enable($post->$tag_param[0]));
	}
}

/**
 * Initiate the class.
 */
fluXtore_Widgets_Loader::init();
