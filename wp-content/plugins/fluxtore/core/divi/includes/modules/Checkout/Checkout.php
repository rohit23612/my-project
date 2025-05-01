<?php
class FLDT_Checkout extends ET_Builder_Module
{
    protected $module_credits = array(
        'module_uri' => '',
        'author'     => '',
        'author_uri' => '',
    );

    public function init()
    {
        $this->name             = et_builder_i18n('Fluxtore Checkout');        
        $this->slug             = 'fldt_checkout';
        $this->vb_support       = 'on';
        $this->main_css_element = '%%order_class%%';
        $this->wrapper_settings = array(
            'order_class_wrapper' => false,
        );

              // Banning the 'Content' tab
              $this->advanced_fields = array(
                'banned_tabs' => array('content'),
                'borders'         => array(
                    'default' => false,
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
                    'default' => false,
                ),
                'background'      => false,
                'fonts'           => false,
                'max_width'       => false,
                'height'          => false,
                'link_options'    => false,
                'admin_label'    => false,
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
    

    }

    public function render($attrs, $content = null, $render_slug)
    {
   
     
        $shortcode_content = "[woocommerce_checkout]";

     
        $output = do_shortcode($shortcode_content);

        $output = sprintf(
            '<div class="et_pb_button_module_wrapper fluxtore_yes_button %2$s et_pb_module">
                %1$s
            </div>',
            $output,
            esc_attr(ET_Builder_Element::get_module_order_class($this->slug))
        );

        return $output;
    }
}

new FLDT_Checkout;
