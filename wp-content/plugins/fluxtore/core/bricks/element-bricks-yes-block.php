<?php 
// element-test.php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Prefix_Element_Yes_Block extends \Bricks\Element {
  // Element properties
  public $category     = 'woocommerce'; // Use predefined element category 'general'  
  public $icon         = 'ti-bolt-alt'; // Themify icon font class
  public $scripts      = ['prefixElementTest']; // Script(s) run when element is rendered on frontend or updated in builder
  public $block    = 'core/button';	
  public $name     = 'prefix-test-yes';	
	public $tag      = 'span';

  // Return localised element label
  public function get_label() {
    return esc_html__( 'Upsell/Downsell Yes Button Block', 'bricks' );
  }

  // Set builder control groups
  public function set_control_groups() {
    $this->control_groups['text'] = [ // Unique group identifier (lowercase, no spaces)
      'title' => esc_html__( 'Text', 'bricks' ), // Localized control group title
      'tab' => 'content', // Set to either "content" or "style"
    ];

    $this->control_groups['settings'] = [
      'title' => esc_html__( 'Settings', 'bricks' ),
      'tab' => 'content',
    ];
  }
 
  // Set builder controls
  public function set_controls() {
		$this->controls['text'] = [
			'type'        => 'text',
			'default'     => esc_html__( 'Yes, I want to take this offer', 'bricks' ),
			'placeholder' => esc_html__( 'Yes, I want to take this offer', 'bricks' ),
		];

		$this->controls['tag'] = [
			'label'          => esc_html__( 'HTML tag', 'bricks' ),
			'type'           => 'text',
			'hasDynamicData' => false,
			'inline'         => true,
			'placeholder'    => 'span',
			'required'       => [ 'link', '=', '' ],
		];

		$this->controls['size'] = [
			'label'       => esc_html__( 'Size', 'bricks' ),
			'type'        => 'select',
			'options'     => $this->control_options['buttonSizes'],
			'inline'      => true,
			'reset'       => true,
			'placeholder' => esc_html__( 'Default', 'bricks' ),
		];

		$this->controls['style'] = [
			'label'       => esc_html__( 'Style', 'bricks' ),
			'type'        => 'select',
			'options'     => $this->control_options['styles'],
			'inline'      => true,
			'reset'       => true,
			'default'     => 'primary',
			'placeholder' => esc_html__( 'None', 'bricks' ),
		];

		$this->controls['circle'] = [
			'label' => esc_html__( 'Circle', 'bricks' ),
			'type'  => 'checkbox',
			'reset' => true,
		];

		$this->controls['outline'] = [
			'label' => esc_html__( 'Outline', 'bricks' ),
			'type'  => 'checkbox',
			'reset' => true,
		];

		// Link
		$this->controls['linkSeparator'] = [
			'label' => esc_html__( 'Link', 'bricks' ),
			'type'  => 'separator',
		];

		$this->controls['link'] = [
			'label' => esc_html__( 'Link type', 'bricks' ),
			'type'  => 'link',
		];

		// Icon
		$this->controls['iconSeparator'] = [
			'label' => esc_html__( 'Icon', 'bricks' ),
			'type'  => 'separator',
		];

		$this->controls['icon'] = [
			'label' => esc_html__( 'Icon', 'bricks' ),
			'type'  => 'icon',
		];

		$this->controls['iconTypography'] = [
			'label'    => esc_html__( 'Typography', 'bricks' ),
			'type'     => 'typography',
			'css'      => [
				[
					'property' => 'font',
					'selector' => 'i',
				],
			],
			'required' => [ 'icon.icon', '!=', '' ],
		];

		$this->controls['iconPosition'] = [
			'label'       => esc_html__( 'Position', 'bricks' ),
			'type'        => 'select',
			'options'     => $this->control_options['iconPosition'],
			'inline'      => true,
			'placeholder' => esc_html__( 'Right', 'bricks' ),
			'required'    => [ 'icon', '!=', '' ],
		];

		$this->controls['iconGap'] = [
			'label'    => esc_html__( 'Gap', 'bricks' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => [
				[
					'property' => 'gap',
				],
			],
			'required' => [ 'icon', '!=', '' ],
		];

		$this->controls['iconSpace'] = [
			'label'    => esc_html__( 'Space between', 'bricks' ),
			'type'     => 'checkbox',
			'css'      => [
				[
					'property' => 'justify-content',
					'value'    => 'space-between',
				],
			],
			'required' => [ 'icon', '!=', '' ],
		];
	}

  // Enqueue element styles and scripts
  public function enqueue_scripts() {
    wp_enqueue_script( 'prefix-test-script' );
  }
  
// Render element HTML
public function render() {
  global $wp, $post;
  $next = get_post_meta($post->ID, '__fluxtore__yes_step', true);
  $url = $next ? get_permalink($next) : '';

  if(isset($_GET['key'])){      
      if (!is_array($_GET['key'])) {
          $_GET['key'] = [sanitize_text_field($_GET['key'])];
      }                       
      foreach ($_GET['key'] as $k) {
          $k = sanitize_text_field($k);
          if (parse_url($url, PHP_URL_QUERY)) {
              $url .= '&key[]=' . $k;
          } else {
              $url .= '?key[]=' . $k;
          }
      }  
      $url .= "&fluxtore_offer_accepted=yes&step_id=" . $post->ID; 
  } else {
      $url .= "?fluxtore_offer_accepted=yes&step_id=" . $post->ID; 
  }  

  $settings = $this->settings;

  $this->set_attribute( '_root', 'class', 'bricks-button' );

  if ( ! empty( $settings['size'] ) ) {
    $this->set_attribute( '_root', 'class', $settings['size'] );
  }   

  if ( ! empty( $settings['style'] ) ) {
    // Outline
    if ( isset( $settings['outline'] ) ) {
      $this->set_attribute( '_root', 'class', 'outline' );
      $this->set_attribute( '_root', 'class', "bricks-color-{$settings['style']}" );
    }

    // Fill (= default)
    else {
      $this->set_attribute( '_root', 'class', "bricks-background-{$settings['style']}" );
    }
  }

  // Button circle
  if ( isset( $settings['circle'] ) ) {
    $this->set_attribute( '_root', 'class', 'circle' );
  }

  if ( isset( $settings['block'] ) ) {
    $this->set_attribute( '_root', 'class', 'block' );
  }

  // Link
  if ( empty( $settings['link'] ) ) {
    $this->tag = 'a';
    $this->set_attribute( '_root', 'href', $url ); // Set the href attribute to $url
  }

  $output = "<{$this->tag} {$this->render_attributes( '_root' )}>"; // Opening tag

  // Add button text or icon if available
  if ( isset( $settings['text'] ) ) {
    $output .= trim( $settings['text'] );
  }

  // Close the tag
  $output .= "</{$this->tag}>"; 

  echo $output; // Output the button
}

public static function render_builder() { ?>
  <script type="text/x-template" id="tmpl-bricks-element-button">
    <component
      :is="settings.link ? 'a' : settings.tag ? settings.tag : 'span'"
      :class="[
        'bricks-button',
        settings.size ? settings.size : null,
        settings.style ? settings.outline ? `outline bricks-color-${settings.style}` : `bricks-background-${settings.style}` : null,
        settings.circle ? 'circle' : null,
        settings.block ? 'block' : null,
        settings.icon && settings.iconPosition ? `icon-${settings.iconPosition}` : null,
        settings.icon && !settings.iconPosition ? 'icon-right' : null
      ]">
      <icon-svg v-if="settings.iconPosition === 'left' && (settings?.icon?.icon || settings?.icon?.svg)" :iconSettings="settings.icon"/>
      <contenteditable tag="span" :name="name" controlKey="text" toolbar="style" :settings="settings"/>
      <icon-svg v-if="settings.iconPosition !== 'left' && (settings?.icon?.icon || settings?.icon?.svg)" :iconSettings="settings.icon"/>
    </component>
  </script>
  <?php
}

public function convert_element_settings_to_block( $settings ) {
  $text = isset( $settings['text'] ) ? trim( $settings['text'] ) : false;

  if ( ! $text ) {
    return;
  }

  $text = str_replace( 'draggable="false"', '', $text );
  $text = str_replace( 'draggable="true"', '', $text );

  $block = [ 'blockName' => 'core/buttons' ];

  $attributes = [];

  $html = '<div class="wp-block-buttons"><!-- wp:button -->';

  if ( isset( $settings['outline'] ) ) {
    $attributes['className'] = 'is-style-outline';
    $html                   .= '<div class="wp-block-button is-style-outline">';
  } else {
    $html .= '<div class="wp-block-button"><a class="wp-block-button__link">';
  }

  if ( isset( $settings['_border']['radius']['top'] ) ) {
    $attributes['borderRadius'] = intval( $settings['_border']['radius']['top'] );
  }

  $html .= $text . '</a></div><!-- /wp:button --></div>';

  $block['attrs']        = $attributes;
  $block['innerContent'] = [ $html ];

  return $block;
}

public function convert_block_to_element_settings( $block, $attributes ) {
  $text = strip_tags( $block['innerHTML'] );

  $element_settings = [
    'text'  => $text,
    'style' => 'dark',
  ];

  $border_radius = isset( $attributes['borderRadius'] ) ? intval( $attributes['borderRadius'] ) : false;

  // Outline
  if ( isset( $attributes['className'] ) && strpos( $attributes['className'], 'is-style-outline' ) !== false ) {
    $element_settings['outline'] = true;
  }

  if ( $border_radius ) {
    $element_settings['_border'] = [
      'radius' => [
        'top'    => $border_radius,
        'right'  => $border_radius,
        'bottom' => $border_radius,
        'left'   => $border_radius,
      ],
    ];
  }

  return $element_settings;
}

}