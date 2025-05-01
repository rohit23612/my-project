<?php

class FLDT_NextStepButton extends ET_Builder_Module
{

	protected $module_credits = array(
		'module_uri' => '',
		'author'     => '',
		'author_uri' => '',
	);


	function init()
	{
		$this->name             = et_builder_i18n('Next Step Button');
		$this->plural           = esc_html__('Next Steps Buttons', 'fluxtore-divi-buttons');
		$this->slug             = 'fldt_next_step_button';
		$this->vb_support       = 'on';
		$this->main_css_element = '%%order_class%%';
		$this->wrapper_settings = array(
			// Flag that indicates that this module's wrapper where order class is declared
			// has another wrapper (mostly for button alignment purpose).
			'order_class_wrapper' => true,
		);



		$this->custom_css_fields = array(
			'main_element' => array(
				'label'                    => et_builder_i18n('Main Element'),
				'no_space_before_selector' => true,
			),
		);

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n('Text'),
					'link'         => et_builder_i18n('Link'),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'alignment' => esc_html__('Alignment', 'et_builder'),
					'text'      => array(
						'title'    => et_builder_i18n('Text'),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'         => array(
				'default' => false,
			),
			'button'          => array(
				'button' => array(
					'label'          => et_builder_i18n('Button'),
					'css'            => array(
						'main'         => $this->main_css_element,
						'limited_main' => "{$this->main_css_element}.et_pb_button",
					),
					'box_shadow'     => false,
					'margin_padding' => false,
				),
			),
			'margin_padding'  => array(
				'css' => array(
					'padding'   => "{$this->main_css_element}_wrapper {$this->main_css_element}, {$this->main_css_element}_wrapper {$this->main_css_element}:hover",
					'margin'    => "{$this->main_css_element}_wrapper",
					'important' => 'all',
				),
			),
			'text'            => array(
				'use_text_orientation'  => false,
				'use_background_layout' => true,
				'options'               => array(
					'background_layout' => array(
						'default_on_front' => 'light',
						'hover'            => 'tabs',
					),
				),
			),
			'text_shadow'     => array(
				// Text Shadow settings are already included on button's advanced style
				'default' => false,
			),
			'background'      => false,
			'fonts'           => false,
			'max_width'       => false,
			'height'          => false,
			'link_options'    => false,
			'position_fields' => array(
				'css' => array(
					'main' => "{$this->main_css_element}_wrapper",
				),
			),
			'transform'       => array(
				'css' => array(
					'main' => "{$this->main_css_element}_wrapper",
				),
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'XpM2G7tQQIE',
				'name' => esc_html__('An introduction to the Button module', 'et_builder'),
			),
		);
	}

	public function get_fields()
	{
		$fields = array(
		
			'url_new_window'   => array(
				'label'            => esc_html__('Button Link Target', 'et_builder'),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__('In The Same Window', 'et_builder'),
					'on'  => esc_html__('In The New Tab', 'et_builder'),
				),
				'toggle_slug'      => 'link',
				'description'      => esc_html__('Here you can choose whether or not your link opens in a new window', 'et_builder'),
				'default_on_front' => 'off',
			),
			'button_text'      => array(
				'label'           => et_builder_i18n('Button'),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__('Input your desired button text.', 'et_builder'),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
				'default'         => 'Click Here'
			),
			'button_alignment' => array(
				'label'           => esc_html__('Button Alignment', 'et_builder'),
				'description'     => esc_html__('Align your button to the left, right or center of the module.', 'et_builder'),
				'type'            => 'text_align',
				'option_category' => 'configuration',
				'options'         => et_builder_get_text_orientation_options(array('justified')),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'alignment',
				'description'     => esc_html__('Here you can define the alignment of Button', 'et_builder'),
				'mobile_options'  => true,
			),
		);
		return $fields;
	}

	public function get_button_alignment($device = 'desktop')
	{
		$suffix           = 'desktop' !== $device ? "_{$device}" : '';
		$text_orientation = isset($this->props["button_alignment{$suffix}"]) ? $this->props["button_alignment{$suffix}"] : '';

		return et_pb_get_alignment($text_orientation);
	}



	public function render($attrs, $content = null, $render_slug)
{
	global $post;
	$post_id = $post->ID;
	$post_meta = get_post_meta($post_id);
	$page_id = isset($post_meta['__fluxtore__flow'][0]) ? $post_meta['__fluxtore__flow'][0] : null;

	if ($page_id) {
		$page_meta = get_post_meta($page_id);
		if (isset($page_meta['__fluxtore__steps'][0])) {
			$steps = unserialize($page_meta['__fluxtore__steps'][0]);
			$current_step_index = array_search($post_id, $steps);

			if ($current_step_index !== false && isset($steps[$current_step_index + 1])) {
				$next_step_id = $steps[$current_step_index + 1];
				$next_step_url = get_permalink($next_step_id);
			}
		} 
	}

	$next_step_url = isset($next_step_url) ? $next_step_url : '#';
	$multi_view     = et_pb_multi_view_options($this);

	$button_rel     = $this->props['button_rel'];
	$button_text    = $this->_esc_attr('button_text', 'limited');
	$url_new_window = $this->props['url_new_window'];
	$button_custom  = $this->props['custom_button'];
	$filter_hue_rotate = $this->props['filter_hue_rotate'];
	$filter_saturate = $this->props['filter_saturate'];
	$filter_brightness = $this->props['filter_brightness'];
	$filter_contrast = $this->props['filter_contrast'];
	$filter_invert = $this->props['filter_invert'];
	$filter_sepia = $this->props['filter_sepia'];
	$filter_opacity = $this->props['filter_opacity'];
	$filter_blur = $this->props['filter_blur'];

	$button_alignment = $this->get_button_alignment();
	$is_button_alignment_responsive = et_pb_responsive_options()->is_responsive_enabled($this->props, 'button_alignment');
	$button_alignment_tablet = $is_button_alignment_responsive ? $this->get_button_alignment('tablet') : '';
	$button_alignment_phone = $is_button_alignment_responsive ? $this->get_button_alignment('phone') : '';

	$custom_icon_values = et_pb_responsive_options()->get_property_values($this->props, 'button_icon');
	$custom_icon = isset($custom_icon_values['desktop']) ? $custom_icon_values['desktop'] : '';
	$custom_icon_tablet = isset($custom_icon_values['tablet']) ? $custom_icon_values['tablet'] : '';
	$custom_icon_phone = isset($custom_icon_values['phone']) ? $custom_icon_values['phone'] : '';

	$button_alignments = array();
	if (!empty($button_alignment)) {
		array_push($button_alignments, sprintf('et_pb_button_alignment_%1$s', esc_attr($button_alignment)));
	}

	if (!empty($button_alignment_tablet)) {
		array_push($button_alignments, sprintf('et_pb_button_alignment_tablet_%1$s', esc_attr($button_alignment_tablet)));
	}

	if (!empty($button_alignment_phone)) {
		array_push($button_alignments, sprintf('et_pb_button_alignment_phone_%1$s', esc_attr($button_alignment_phone)));
	}

	$button_alignment_classes = join(' ', $button_alignments);

	if ('' === $button_text) {
		return '';
	}

	$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs($this->props);


	$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class($this->props);
	$this->add_classname($background_layout_class_names);

	$this->remove_classname('et_pb_module');

	$button = $this->render_button(
		array(
			'button_id' => $this->module_id(false),
			'button_classname' => explode(' ', $this->module_classname($render_slug)),
			'button_custom' => $button_custom,
			'button_rel' => $button_rel,
			'button_text' => $button_text,
			'button_text_escaped' => true,
			'button_url' => $next_step_url,
			'custom_icon' => $custom_icon,
			'custom_icon_tablet' => $custom_icon_tablet,
			'custom_icon_phone' => $custom_icon_phone,
			'has_wrapper' => false,
			'url_new_window' => $url_new_window,
			'multi_view_data' => $multi_view->render_attrs(
				array(
					'content' => '{{button_text}}',
					'hover_selector' => '%%order_class%%.et_pb_button',
					'visibility' => array(
						'button_text' => '__not_empty',
					),
				)
			),
		)
	);

	$output = sprintf(
		'<div class="et_pb_button_module_wrapper fluxtore_next_step_button" style="text-align:' . $button_alignment . ';">
			%1$s
		</div>',
		et_core_esc_previously($button)
	);

	$transition_style = $this->get_transition_style(array('all'));
	self::set_style(
		$render_slug,
		array(
			'selector' => '%%order_class%%, %%order_class%%:after',
			'declaration' => esc_html($transition_style),
		)
	);

	$transition_style_tablet = $this->get_transition_style(array('all'), 'tablet');
	if ($transition_style_tablet !== $transition_style) {
		self::set_style(
			$render_slug,
			array(
				'selector' => '%%order_class%%, %%order_class%%:after',
				'declaration' => esc_html($transition_style_tablet),
				'media_query' => ET_Builder_Element::get_media_query('max_width_980'),
			)
		);
	}

	$transition_style_phone = $this->get_transition_style(array('all'), 'phone');
	if ($transition_style_phone !== $transition_style || $transition_style_phone !== $transition_style_tablet) {
		$el_style = array(
			'selector' => '%%order_class%%, %%order_class%%:after',
			'declaration' => esc_html($transition_style_phone),
			'media_query' => ET_Builder_Element::get_media_query('max_width_767'),
		);
		self::set_style($render_slug, $el_style);
	}

	return $output;
}


	public function multi_view_filter_value($raw_value, $args, $multi_view)
	{
		$name    = isset($args['name']) ? $args['name'] : '';
		$mode    = isset($args['mode']) ? $args['mode'] : '';
		$context = isset($args['context']) ? $args['context'] : '';

		$fields_need_escape = array(
			'title',
		);

		if ($raw_value && 'content' === $context && in_array($name, $fields_need_escape, true)) {
			return $this->_esc_attr($multi_view->get_name_by_mode($name, $mode), 'none', $raw_value);
		}

		return $raw_value;
	}
}

new FLDT_NextStepButton;
