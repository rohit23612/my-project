<?php
/** Glutenberg block upsell/downsell **/

use fluXtore\Core\Models\fluXtore_Step;

//Register Blocks

function register_checkout_block() {
    register_block_type( 'woocommerce-checkout/checkout-block', array(
        'editor_script' => 'checkout-block-editor',
        'render_callback' => 'render_checkout_block',
    ) );  
    
    register_block_type( 'blocks/gutenberg-blocks', array(
        'editor_script' => 'gutenberg-block-script',
        'render_callback' => 'render_gutenberg_block',
    ) );

    register_block_type('blocks/gutenberg-no-blocks', array(
        'editor_script' => 'gutenberg-no-block-script',
        'render_callback' => 'render_gutenberg_no_block',
    ));

    register_block_type('blocks/gutenberg-orderdetails-blocks', array(
        'editor_script' => 'gutenberg-orderdetails-script',
        'render_callback' => 'render_gutenberg_orderdetails_block',
    ));

    register_block_type('blocks/gutenberg-next-step-blocks', array(
        'editor_script' => 'gutenberg-next-step-block-script',
        'render_callback' => 'render_gutenberg_next_step_block',
    ));
}
add_action( 'init', 'register_checkout_block' );


//Enqueue all scripts

function enqueue_block_editor_script() {
    global $post;
    $post_meta = get_post_meta($post->ID);   
   if($post->post_type == "fluxtore_step"){
       if (isset($post_meta['__fluxtore__tag'])) {

            if ($post_meta['__fluxtore__tag'][0]== "Checkout") {
                wp_enqueue_script(
                    'checkout-block-editor',
                    plugins_url( 'js/checkout-block.js', __FILE__ ),
                    array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components' ),
                    filemtime( plugin_dir_path( __FILE__ ) . 'js/checkout-block.js' )
                );

           } elseif($post_meta['__fluxtore__tag'][0]== "Downsell" ||  $post_meta['__fluxtore__tag'][0]== "Upsell") {
                wp_enqueue_script(
                    'gutenberg-block-script',
                    plugins_url('/js/gutenberg-block.js', __FILE__),
                    array('wp-blocks', 'wp-components', 'wp-element','wp-editor'),
                    filemtime(plugin_dir_path(__FILE__) . 'js/gutenberg-block.js')
                );  
                wp_enqueue_script(
                    'gutenberg-no-block-script',
                    plugins_url('/js/gutenberg-no-block.js', __FILE__),
                    array('wp-blocks', 'wp-element', 'wp-components','wp-editor'),
                    filemtime(plugin_dir_path(__FILE__) . 'js/gutenberg-no-block.js')
                );
           } elseif($post_meta['__fluxtore__tag'][0] == "Thank You" || $post_meta['__fluxtore__tag'][0] == "Thank"){
            wp_enqueue_script(
                'gutenberg-orderdetails-script',
                plugins_url('/js/gutenberg-orderdetails-block.js', __FILE__),
                array('wp-blocks', 'wp-element', 'wp-components','wp-editor'),
                filemtime(plugin_dir_path(__FILE__) . 'js/gutenberg-orderdetails-block.js')
            );
       }
        }
    }   
    wp_enqueue_script(
        'gutenberg-next-step-block-script',
        plugins_url('/js/gutenberg-next-step-block.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-components','wp-editor'),
        filemtime(plugin_dir_path(__FILE__) . 'js/gutenberg-next-step-block.js')
    );
   
}
add_action( 'enqueue_block_editor_assets', 'enqueue_block_editor_script' );



//Render Blocks
function render_checkout_block( $attributes ) { 
ob_start();
?>
<main class="wp-block-group has-global-padding is-layout-constrained wp-block-group-is-layout-constrained" id="wp--skip-link--target">
	<div class="entry-content alignwide wp-block-post-content is-layout-flow wp-block-post-content-is-layout-flow" bis_skin_checked="1">
	<div data-block-name="woocommerce-checkout/checkout-block" class="woocommerce-checkout-block" bis_skin_checked="1">   
    <div class="woocommerce-checkout-block">        
        <?php echo do_shortcode('[woocommerce_checkout]'); ?>
    </div>
    </div>
</div>
</main>
<style>
	.woocommerce-page table.shop_table  { width:100%!important}
</style>
<?php 
return ob_get_clean();
}


function render_gutenberg_block($attributes){
    global $wp, $post;

    // Get button text from attributes
    $buttonTextYes = isset($attributes['buttonTextYes']) ? $attributes['buttonTextYes'] : 'Yes, I want to take this offer now ...';
    $buttonBgColor = isset($attributes['buttonBgColor']) ? $attributes['buttonBgColor'] : '#000';
    $buttonTextColor = isset($attributes['buttonTextColor']) ? $attributes['buttonTextColor'] : '#fff';
    $buttonHeight = isset($attributes['buttonHeight']) ? $attributes['buttonHeight'] . 'px' : 'auto';
    $buttonAlignmentHorizontal = isset($attributes['buttonAlignmentHorizontal']) ? $attributes['buttonAlignmentHorizontal'] : 'center';
    $buttonAlignmentVertical = isset($attributes['buttonAlignmentVertical']) ? $attributes['buttonAlignmentVertical'] : 'middle';
    $textAlign = isset($attributes['textAlign']) ? $attributes['textAlign'] : 'center';
    $containerHeight = isset($attributes['containerHeight']) ? $attributes['containerHeight'] . 'px' : '200px';
    $fontSize = isset($attributes['fontSize']) ? $attributes['fontSize'] : '16';
    $fontWeight = isset($attributes['fontWeight']) ? $attributes['fontWeight'] : '400';

    // Calculate the width of the button based on the text length and font size
    $buttonWidth = strlen($buttonTextYes) * ($fontSize * 0.6); // Adjust the factor (0.6) based on your font and design

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

    // Construct button HTML
    $button_html = "<a href='" . esc_url($url) ."'>";
    $button_html .= "<button class='upsell-downsell-yes-button' style='";
    $button_html .= "background-color: " . esc_attr($buttonBgColor) . "; ";
    $button_html .= "color: " . esc_attr($buttonTextColor) . "; ";
    $button_html .= "width: " . esc_attr($buttonWidth) . "px; "; // Set width dynamically
    $button_html .= "height: " . esc_attr($buttonHeight) . "; ";
    $button_html .= "text-align: " . esc_attr($textAlign) . "; ";
    $button_html .= "font-size: " . esc_attr($fontSize) . "px; "; // Set font size
    $button_html .= "font-weight: " . esc_attr($fontWeight) . "; "; // Set font weight
    $button_html .= "border: none";
    $button_html .= "'>";
    $button_html .= esc_html($buttonTextYes);
    $button_html .= "</button></a>";

    // Wrap button in container if height is specified
    if ($containerHeight) {
        // Determine the align-items property based on the buttonAlignmentVertical attribute
        $align_items = 'center'; // Default to center if buttonAlignmentVertical is not set or invalid
        if ($buttonAlignmentVertical === 'top') {
            $align_items = 'flex-start';
        } elseif ($buttonAlignmentVertical === 'bottom') {
            $align_items = 'flex-end';
        }

        $button_html = "<div style='height: " . esc_attr($containerHeight) . "; display: flex; align-items: " . esc_attr($align_items) . "; justify-content: " . esc_attr($buttonAlignmentHorizontal) . ";'>" . $button_html . "</div>";
    }

    return $button_html;
}
   
  
function render_gutenberg_no_block($attributes) {
    global $post;

    $next = get_post_meta($post->ID, '__fluxtore__no_step', true);
    $url = $next ? get_permalink($next) : '';
   
    if (!empty($_GET['key'])) {
        $keys = is_array($_GET['key']) ? $_GET['key'] : [sanitize_text_field($_GET['key'])];
        foreach ($keys as $k) {
            $k = sanitize_text_field($k);
            $url = add_query_arg('key[]', $k, $url);
        }
    }

    // Append additional query parameters
    $url = add_query_arg([
        'fluxtore_offer_accepted' => 'no',
        'step_id' => $post->ID,
    ], $url);

    // Get button text from attributes
    $buttonTextNo = isset($attributes['buttonTextNo']) ? $attributes['buttonTextNo'] : 'No, I let go of this fantastic opportunity';

    // Get button background color from attributes
    $buttonBgColorNo = isset($attributes['buttonBgColorNo']) ? $attributes['buttonBgColorNo'] : '#bebebe';

    // Get button text color from attributes
    $buttonTextColorNo = isset($attributes['buttonTextColorNo']) ? $attributes['buttonTextColorNo'] : '#100000';

    // Get button width from attributes
    $buttonWidthNo = isset($attributes['buttonWidthNo']) ? $attributes['buttonWidthNo'] . 'px' : 'auto';

    // Get button height from attributes
    $buttonHeightNo = isset($attributes['buttonHeightNo']) ? $attributes['buttonHeightNo'] . 'px' : 'auto';

    // Get button horizontal alignment from attributes
    $buttonAlignmentHorizontalNo = isset($attributes['buttonAlignmentHorizontalNo']) ? $attributes['buttonAlignmentHorizontalNo'] : 'center';

    // Get button vertical alignment from attributes
    $buttonAlignmentVerticalNo = isset($attributes['buttonAlignmentVerticalNo']) ? $attributes['buttonAlignmentVerticalNo'] : 'middle';

    // Get text alignment from attributes
    $textAlignNo = isset($attributes['textAlignNo']) ? $attributes['textAlignNo'] : 'center';

    // Get container height from attributes
    $containerHeightNo = isset($attributes['containerHeightNo']) ? $attributes['containerHeightNo'] . 'px' : '200px';

    // Get font size from attributes
    $fontSizeNo = isset($attributes['fontSizeNo']) ? $attributes['fontSizeNo'] : '16';

    // Get font weight from attributes
    $fontWeightNo = isset($attributes['fontWeightNo']) ? $attributes['fontWeightNo'] : '400';

    // Calculate the width of the button based on the text length and font size
    $buttonWidthNo = strlen($buttonTextNo) * ($fontSizeNo * 0.6); // Adjust the factor (0.6) based on your font and design

    // Construct the button HTML
    $button_html = "<a href='" . esc_url($url) . "'><button class='upsell-downsell-no-button' style='";
    $button_html .= "background-color: " . esc_attr($buttonBgColorNo) . "; ";
    $button_html .= "color: " . esc_attr($buttonTextColorNo) . "; ";
    $button_html .= "width: " . esc_attr($buttonWidthNo) . "px; "; // Set width dynamically
    $button_html .= "height: " . esc_attr($buttonHeightNo) . "; ";
    $button_html .= "text-align: " . esc_attr($textAlignNo) . "; ";
    $button_html .= "font-size: " . esc_attr($fontSizeNo) . "px; "; // Set font size
    $button_html .= "font-weight: " . esc_attr($fontWeightNo) . "; "; // Set font weight
    $button_html .= "border: none";
    $button_html .= "'>";
    $button_html .= esc_html($buttonTextNo);
    $button_html .= "</button></a>";

    // Wrap button in container if height is specified
    if ($containerHeightNo) {
        // Determine the align-items property based on the buttonAlignmentVertical attribute
        $align_items_no = 'center'; // Default to center if buttonAlignmentVertical is not set or invalid
        if ($buttonAlignmentVerticalNo === 'top') {
            $align_items_no = 'flex-start';
        } elseif ($buttonAlignmentVerticalNo === 'bottom') {
            $align_items_no = 'flex-end';
        }

        $button_html = "<div style='height: " . esc_attr($containerHeightNo) . "; display: flex; align-items: " . esc_attr($align_items_no) . "; justify-content: " . esc_attr($buttonAlignmentHorizontalNo) . ";'>" . $button_html . "</div>";
    }

    return $button_html;
}


function render_gutenberg_orderdetails_block($attributes) {
    ob_start();
    
    $orderDetailsMaxWidth = isset($attributes['orderDetailsMaxWidth']) ? $attributes['orderDetailsMaxWidth'] . 'px' : '1100px';
    $orderDetailsMargin = (isset($attributes['orderDetailsMarginTop']) ? $attributes['orderDetailsMarginTop'] . 'px' : 'auto') . ' ' .
                          (isset($attributes['orderDetailsMarginRight']) ? $attributes['orderDetailsMarginRight'] . 'px' : 'auto') . ' ' .
                          (isset($attributes['orderDetailsMarginBottom']) ? $attributes['orderDetailsMarginBottom'] . 'px' : 'auto') . ' ' .
                          (isset($attributes['orderDetailsMarginLeft']) ? $attributes['orderDetailsMarginLeft'] . 'px' : 'auto');
    $orderDetailsPaddingTop = isset($attributes['orderDetailsPaddingTop']) ? $attributes['orderDetailsPaddingTop'] . 'px' : '10px';
    $orderDetailsPaddingRight = isset($attributes['orderDetailsPaddingRight']) ? $attributes['orderDetailsPaddingRight'] . 'px' : '10px';
    $orderDetailsPaddingBottom = isset($attributes['orderDetailsPaddingBottom']) ? $attributes['orderDetailsPaddingBottom'] . 'px' : '10px';
    $orderDetailsPaddingLeft = isset($attributes['orderDetailsPaddingLeft']) ? $attributes['orderDetailsPaddingLeft'] . 'px' : '10px';
    $textAlign = isset($attributes['textAlign']) ? $attributes['textAlign'] : 'left';
    $backgroundColor = isset($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '#ffffff';
    $textColor = isset($attributes['textColor']) ? $attributes['textColor'] : '#000000';
    ?>
    <style>
        .fluxtore-order-details section table tr  {
                text-align: <?php echo esc_attr($textAlign); ?>;
            }
        .woocommerce-js .fluxtore-container .woocommerce-customer-details address {
                text-align: <?php echo esc_attr($textAlign); ?>;
                }
    </style>
    <div class="fluxtore-order-gutenberg-container" style="max-width: <?php echo esc_attr($orderDetailsMaxWidth); ?>; margin: <?php echo esc_attr($orderDetailsMargin); ?>; padding-top: <?php echo esc_attr($orderDetailsPaddingTop); ?>; padding-right: <?php echo esc_attr($orderDetailsPaddingRight); ?>; padding-bottom: <?php echo esc_attr($orderDetailsPaddingBottom); ?>; padding-left: <?php echo esc_attr($orderDetailsPaddingLeft); ?>; text-align: <?php echo esc_attr($textAlign); ?>; background: <?php echo esc_attr($backgroundColor); ?>; color:  <?php echo esc_attr($textColor); ?>;">
        <div class="fluxtore-order-details">
        <?php echo do_shortcode('[' . FLUXTORE_PREFIX . 'render_order_details]'); ?>
        </div>
    </div>
    <?php

    return ob_get_clean();
}

function render_gutenberg_next_step_block($attributes){
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

    $next_step_url= isset($next_step_url) ? $next_step_url :  '#';

    // Get button text from attributes
    $buttonTextNext = isset($attributes['buttonTextNext']) ? $attributes['buttonTextNext'] : 'Buy Now';

    // Get button background color from attributes
    $buttonBgColorNext = isset($attributes['buttonBgColorNext']) ? $attributes['buttonBgColorNext'] : '#bebebe';

    // Get button text color from attributes
    $buttonTextColorNext = isset($attributes['buttonTextColorNext']) ? $attributes['buttonTextColorNext'] : '#100000';

    // Get button width from attributes
    $buttonWidthNext = isset($attributes['buttonWidthNext']) ? $attributes['buttonWidthNext'] . 'px' : 'auto';

    // Get button height from attributes
    $buttonHeightNext = isset($attributes['buttonHeightNext']) ? $attributes['buttonHeightNext'] . 'px' : 'auto';

    // Get button horizontal alignment from attributes
    $buttonAlignmentHorizontalNext = isset($attributes['buttonAlignmentHorizontalNext']) ? $attributes['buttonAlignmentHorizontalNext'] : 'center';

    // Get button vertical alignment from attributes
    $buttonAlignmentVerticalNext = isset($attributes['buttonAlignmentVerticalNext']) ? $attributes['buttonAlignmentVerticalNext'] : 'middle';

    // Get text alignment from attributes
    $textAlignNext = isset($attributes['textAlignNext']) ? $attributes['textAlignNext'] : 'center';

    // Get container height from attributes
    $containerHeightNext = isset($attributes['containerHeightNext']) ? $attributes['containerHeightNext'] . 'px' : '200px';

    // Get font size from attributes
    $fontSizeNext = isset($attributes['fontSizeNext']) ? $attributes['fontSizeNext'] : '16';

    // Get font weight from attributes
    $fontWeightNext = isset($attributes['fontWeightNext']) ? $attributes['fontWeightNext'] : '400';

    $PaddingNext = isset($attributes['PaddingNext']) ? $attributes['PaddingNext'] : '12px';

    $BorderRadius = isset($attributes['BorderRadius']) ? $attributes['BorderRadius'] : '5px';


    // Calculate the width of the button based on the text length and font size
    $buttonWidthNext = strlen($buttonTextNext) * ($fontSizeNext * 1.0); // Adjust the factor (0.6) based on your font and design

    // Construct the button HTML
    $button_html = "<a href='" . esc_url($next_step_url) . "'><button class='upsell-downsell-Next-button' style='";
    $button_html .= "background-color: " . esc_attr($buttonBgColorNext) . "; ";
    $button_html .= "color: " . esc_attr($buttonTextColorNext) . "; ";
    $button_html .= "width: " . esc_attr($buttonWidthNext) . "px; "; // Set width dynamically
    $button_html .= "height: " . esc_attr($buttonHeightNext) . "; ";
    $button_html .= "text-align: " . esc_attr($textAlignNext) . "; ";
    $button_html .= "font-size: " . esc_attr($fontSizeNext) . "px; "; // Set font size
    $button_html .= "font-weight: " . esc_attr($fontWeightNext) . "; "; // Set font weight
    $button_html .= "padding: " . esc_attr($PaddingNext) . "; "; // Set Padding
    $button_html .= "border: none ;";
    $button_html .= "border-radius: $BorderRadius";
    $button_html .= "'>";
    $button_html .= esc_html($buttonTextNext);
    $button_html .= "</button></a>";

    // Wrap button in container if height is specified
    if ($containerHeightNext) {
        // Determine the align-items property based on the buttonAlignmentVertical attribute
        $align_items_Next = 'center'; // Default to center if buttonAlignmentVertical is not set or invalid
        if ($buttonAlignmentVerticalNext === 'top') {
            $align_items_Next = 'flex-start';
        } elseif ($buttonAlignmentVerticalNext === 'bottom') {
            $align_items_Next = 'flex-end';
        }

        $button_html = "<div style='height: " . esc_attr($containerHeightNext) . "; display: flex; align-items: " . esc_attr($align_items_Next) . "; justify-content: " . esc_attr($buttonAlignmentHorizontalNext) . ";'>" . $button_html . "</div>";
    }

    return $button_html;
}