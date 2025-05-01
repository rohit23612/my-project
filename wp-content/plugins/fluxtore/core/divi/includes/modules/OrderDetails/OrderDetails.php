<?php
defined( 'ABSPATH' ) || exit;

class FLDT_OrderDetails extends ET_Builder_Module
{

	public function init()
	{
		$this->name = esc_html__( 'Fluxtore Order Details', 'Fluxtore-Order-Details' );
		$this->slug             = 'fldt_order_details';		
		$this->vb_support  = 'on';
		$this->folder_name = 'et_pb_woo_modules';
		$this->main_css_element = '%%order_class%%';
		// $this->wrapper_settings = array(
		// 	// Flag that indicates that this module's wrapper where order class is declared
		// 	// has another wrapper (mostly for button alignment purpose).
		// 	'order_class_wrapper' => true,
		// );
		// $this->custom_css_fields = array(
		// 	'main_element' => array(
		// 		'label'                    => et_builder_i18n('Main Element'),
		// 		'no_space_before_selector' => true,
		// 	),
		// );
		$this->settings_modal_toggles = array(
			'advanced' => array(
				'toggles' => array(
					'title'        => array(
						'title'    => esc_html__( 'Title Text', 'et_builder' ),
						'priority' => 55,
					),
					'column_label' => array(
						'title'    => esc_html__( 'Column Label', 'et_builder' ),
						'priority' => 60,
					),
					'body'         => array(
						'title'             => esc_html__( 'Body Text', 'et_builder' ),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => array(
							'p' => array(
								'name' => 'P',
								'icon' => 'text-left',
							),
							'a' => array(
								'name' => 'A',
								'icon' => 'text-link',
							),
						),
						'priority'          => 65,
					),
					'table'        => array(
						'title'    => esc_html__( 'Table', 'et_builder' ),
						'priority' => 70,
					),
					'table_row'    => array(
						'title'    => esc_html__( 'Table Row', 'et_builder' ),
						'priority' => 75,
					),
					'table_cell'   => array(
						'title'    => esc_html__( 'Table Cell', 'et_builder' ),
						'priority' => 80,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'title'        => array(
					'label'       => esc_html__( 'Title', 'et_builder' ),
					'css'         => array(
						'main' => "$this->main_css_element .woocommerce-order-details .woocommerce-order-details__title, $this->main_css_element .woocommerce-customer-details .woocommerce-column__title",
					),
					'font_size'   => array(
						'default' => '22px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'column_label' => array(
					'label'       => esc_html__( 'Column Label', 'et_builder' ),
					'css'         => array(
						'main' => "$this->main_css_element table.shop_table thead th, $this->main_css_element table.shop_table tfoot th",
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
				),
				'body'         => array(
					'label'       => esc_html__( 'Body', 'et_builder' ),
					'css'         => array(

						// Accepts only string and not array. Hence using `implode`.
						'main'        => implode(
							', ',
							array(
								"$this->main_css_element table.shop_table tbody tr td",
								"$this->main_css_element table.shop_table tfoot tr td",
								"$this->main_css_element table.shop_table tbody tr td a",
								"$this->main_css_element .woocommerce-customer-details address",
								"$this->main_css_element .woocommerce-customer-details address p",
							)
						),

						// Accepts only string and not array. Hence using `implode`.
						'line_height' => implode(
							', ',
							array(
								"$this->main_css_element table.shop_table th",
								"$this->main_css_element table.shop_table td",
							)
						),
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
					'toggle_slug' => 'body',
					'sub_toggle'  => 'p',
				),
				'link'         => array(
					'label'       => esc_html__( 'Link', 'et_builder' ),
					'css'         => array(
						'main'        => "$this->main_css_element td a",
						'line_height' => "$this->main_css_element td a",
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
					'toggle_slug' => 'body',
					'sub_toggle'  => 'a',
				),
			),
			'text'           => array(
				'css' => array(
					'text_orientation' => "$this->main_css_element h3, table.shop_table th, table.shop_table tr td",
					// Refer ET_Builder_Module_Field_TextShadow::update_styles for selector
					// definition.
					'text_shadow'      => "$this->main_css_element h3, table.shop_table th, table.shop_table tr td",
				),
			),
			'link_options'   => false,
			'form_field'     => array(
				'table'      => array(
					'label'                  => esc_html__( 'Table', 'et_builder' ),
					'css'                    => array(
						'main' => "$this->main_css_element table.shop_table thead, $this->main_css_element table.shop_table tfoot, $this->main_css_element table.shop_table tbody, $this->main_css_element .woocommerce-customer-details address",
					),
					'background_color'       => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s table.', 'et_builder' ),
					),
					'font_field'             => false,
					'margin_padding'         => array(
						'css'             => array(
							'main'      => "$this->main_css_element table.shop_table, $this->main_css_element .woocommerce-customer-details address",
							'important' => array( 'custom_margin' ),
						),
						'depends_on'      => array(
							'collapse_table_gutters_borders',
						),
						'depends_show_if' => 'off',
					),
					'text_color'             => false,
					'focus_background_color' => false,
					'focus_text_color'       => false,
					'border_styles'          => array(
						'table' => array(
							'label_prefix'      => esc_html__( 'Table', 'et_builder' ),
							'css'               => array(
								'main' => array(
									'border_styles' => "$this->main_css_element table.shop_table, $this->main_css_element .woocommerce-customer-details address",
									'border_radii'  => "$this->main_css_element table.shop_table, $this->main_css_element .woocommerce-customer-details address",
								),
							),
							'use_focus_borders' => false,
							'defaults'          => array(
								'border_radii'  => 'on|5px|5px|5px|5px',
								'border_styles' => array(
									'width' => '1px',
								),
							),
							'depends_on'        => array(
								'collapse_table_gutters_borders',
							),
							'depends_show_if'   => 'off',
						),
					),
					'box_shadow'             => array(
						'css' => array(
							'main' => "$this->main_css_element table.shop_table, $this->main_css_element .woocommerce-customer-details address",
						),
					),
				),
				'table_row'  => array(
					'label'                  => esc_html__( 'Table Row', 'et_builder' ),
					'css'                    => array(
						'main' => "$this->main_css_element table.shop_table tr",
					),
					'background_color'       => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s table row.', 'et_builder' ),
					),
					'font_field'             => false,
					'margin_padding'         => array(
						'css'         => array(
							'main' => "$this->main_css_element table.shop_table tr th, $this->main_css_element table.shop_table tr td",
						),
						'use_margin'  => false,
						'use_padding' => false,
					),
					'text_color'             => false,
					'focus_background_color' => false,
					'focus_text_color'       => false,
					'border_styles'          => array(
						'table_row' => array(
							'label_prefix'      => esc_html__( 'Table Row', 'et_builder' ),
							'css'               => array(
								'main'      => array(

									// Accepts only string and not array. Hence using `implode`.
									'border_radii'  => implode(
										', ',
										array(
											"$this->main_css_element table.shop_table th",
											"$this->main_css_element table.shop_table td",
										)
									),
									'border_styles' => implode(
										', ',
										array(
											"$this->main_css_element table.shop_table th",
											"$this->main_css_element table.shop_table td",
										)
									),
								),
								'important' => true,
							),
							'use_focus_borders' => false,
							'defaults'          => array(
								'border_radii'  => 'on|0px|0px|0px|0px',
								'border_styles' => array(
									'width' => '1px',
								),
							),
							'depends_on'        => array(
								'collapse_table_gutters_borders',
							),
							'depends_show_if'   => 'on',
							'use_radius'        => false,
						),
					),
					'box_shadow'             => array(
						'css' => array(
							'main' => "$this->main_css_element table.shop_table tr",
						),
					),
				),
				'table_cell' => array(
					'label'                  => esc_html__( 'Table Cell', 'et_builder' ),
					'css'                    => array(
						'main' => "$this->main_css_element table.shop_table tr th, $this->main_css_element table.shop_table tr td",
					),
					'background_color'       => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s table cell.', 'et_builder' ),
					),
					'font_field'             => false,
					'margin_padding'         => array(
						'css'        => array(
							'main' => implode(
								', ',
								array(
									"$this->main_css_element table.shop_table tr th",
									"$this->main_css_element table.shop_table tr td",
								)
							),
						),
						'use_margin' => false,
					),
					'text_color'             => false,
					'focus_background_color' => false,
					'focus_text_color'       => false,
					'border_styles'          => array(
						'table_cell' => array(
							'label_prefix'      => esc_html__( 'Table Cell', 'et_builder' ),
							'css'               => array(
								'main'      => array(
									'border_styles' => "$this->main_css_element table.shop_table tr th,$this->main_css_element table.shop_table tr td",
									'border_radii'  => "$this->main_css_element table.shop_table tr th, $this->main_css_element table.shop_table tr td",
								),
								'important' => array( 'border-color' ),
							),
							'use_focus_borders' => false,
							'defaults'          => array(
								'border_radii'  => 'on|0px|0px|0px|0px',
								'border_styles' => array(
									'width' => '0px',
									'style' => 'solid',
								),
								'composite'     => array(
									'border_top' => array(
										'border_width_top' => '1px',
										'border_style_top' => 'solid',
										'border_color_top' => '#eeeeee',
									),
								),
							),
							'depends_on'        => array(
								'collapse_table_gutters_borders',
							),
							'depends_show_if'   => 'off',
						),
					),
					'box_shadow'             => array(
						'css' => array(
							'main' => "$this->main_css_element table.shop_table tr th, $this->main_css_element table.shop_table td",
						),
					),
				),
			),

			// Use !important in Spacing OG — Margin values.
			'margin_padding' => array(
				'css' => array(
					'important' => array( 'custom_margin' ),
				),
			),
		);

		$this->custom_css_fields = array(
			'title_text' => array(
				'label'    => esc_html__( 'Title Text', 'et_builder' ),
				'selector' => "$this->main_css_element h1, $this->main_css_element h2, $this->main_css_element h3, $this->main_css_element h4, $this->main_css_element h5, $this->main_css_element h6",
			),
		);

		
		
	}

	public function get_fields()
	{
		$fields = array();		

		return $fields;
	}
	

	public function render( $unprocessed_props, $content, $render_slug ) {	
		
		if (!isset($_GET['key'])) {
			return;
		}
		$output = "";	
		$count = 0;				
		foreach ($_GET['key'] as $k) {
			$order_id = wc_get_order_id_by_order_key(sanitize_text_field($k));			
		
		
		$order = wc_get_order( $order_id );
		$country = $order->get_billing_country();
		$state = $order->get_billing_state();
		$output .= '<section class="woocommerce-order-details">';
		$output .= '<h2 class="woocommerce-order-details__title">Order details</h2>';		
		$output .= '<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">';
		$output .= '<thead>';
		$output .= '<tr>';
		$output .= '<th class="woocommerce-table__product-name product-name">Product</th>';
        $output .= '<th class="woocommerce-table__product-table product-total">Total</th>';
        $output .= '</tr>';
        $output .= '</thead>';
        $output .= '<tbody>';
		$subtotal = 0;
		foreach ($order->get_items() as $item_id => $item ) {
		$subtotal += $item->get_subtotal();		
		$output .= '<tr class="woocommerce-table__line-item order_item">';
        $output .= '<td class="woocommerce-table__product-name product-name">';
        $output .= '<a href="'.get_permalink( $item->get_product_id() ).'">'.$item->get_name().'</a>';
        $output .= '<strong class="product-quantity">×&nbsp;'.$item->get_quantity().'</strong>';	
        $output .= '</td>';
        $output .= '<td class="woocommerce-table__product-total product-total">';
        $output .= '<span class="woocommerce-Price-amount amount"><bdi>'.number_format($item->get_subtotal(), 2, ",", "").'&nbsp;<span class="woocommerce-Price-currencySymbol">'.get_woocommerce_currency_symbol().'</span></bdi></span>';
        $output .= '</td>';
        $output .= '</tr>';
		}	
		$output .= '</tbody>';
		$output .= '<tfoot>';
		$output .= '<tr>';
		$output .= '<th scope="row">Subtotal:</th>';
		$output .= '<td>';
		$output .= '<span class="woocommerce-Price-amount amount">'.number_format($subtotal, 2, ",", "") .'&nbsp;<span class="woocommerce-Price-currencySymbol">'.get_woocommerce_currency_symbol().'</span></span>';
		$output .= '</td>';
		$output .= '</tr>';
		if($count == 0){
		$output .= '<tr>';		
		$output .= '<th scope="row">Payment method:</th>';
		$output .= '<td>'.$order->data['payment_method_title'].'</td>';
		$output .= '</tr>';
		}
		$output .= '<tr>';
		$output .= '<th scope="row">Total:</th>';
		$output .= '<td><span class="woocommerce-Price-amount amount">'.number_format($order->data['total'], 2, ",", "").'&nbsp;<span class="woocommerce-Price-currencySymbol">'.get_woocommerce_currency_symbol().'</span></span></td>';
		$output .= '</tr>';
		$output .= '</tfoot>';
		$output .= '</table> ' ;
	  	$output .= '</section>';
		$output .= '<section class="woocommerce-customer-details">';
		$output .= '<h2 class="woocommerce-column__title">Billing address</h2>';
		$output .= '<address>'. $order->data['billing']['first_name'] . ' ' .$order->data['billing']['last_name'] .'<br />'. $order->data['billing']['address_1'] .'<br />'. $order->data['billing']['address_2'].'<br />'. $order->data['billing']['city'] . ' ' .$order->data['billing']['postcode'].'<br />'. WC()->countries->get_states( $country )[$state] . ', ' .WC()->countries->countries[$order->data['billing']['country']] .' <p class="woocommerce-customer-details--phone">'. $order->data['billing']['phone'] .'</p><p class="woocommerce-customer-details--email">'. $order->data['billing']['email'] .'</p></address>';
		$output .= '</section>';
		$count++;	
		}		
		
		return $output;
		
	}

}

new FLDT_OrderDetails;
