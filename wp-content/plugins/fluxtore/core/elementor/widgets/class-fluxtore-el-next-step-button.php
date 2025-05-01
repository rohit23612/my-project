<?php

use fluXtore\Core\Models\fluXtore_Step;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

class fluXtore_Next_Step_Button extends \Elementor\Widget_Button {

	private $excluded_controls = [
		'link'
	];

	/**
	 * Module should load or not.
	 *
	 * @param string $step_type Current step type.
	 *
	 * @return bool true|false.
	 */
	public static function is_enable( $step_type ) {
		return FLUXTORE_LANDING_TAG === $step_type;
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'next-step-button';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Next Step Button', 'fluxtore' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fas fa-angle-double-right';
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
	public function get_categories() {
		return [ 'fluxtore-widgets' ];
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'fluxtore', 'next step', 'button' ];
	}

	public function add_control( $id, array $args, $options = [] ) {
		global $post;

		if ( in_array( $id, $this->excluded_controls ) ) {
			$step_model = new fluXtore_Step();
			$next = $step_model->next_step( $post->ID );
			$url = '#';

			if ( $next ) {
				$url = get_permalink( $next->ID );
			}

			$args[ 'type' ] = 'hidden';
			$args[ 'default' ][ 'url' ] = $url;
		}

		return parent::add_control( $id, $args, $options );
	}
}