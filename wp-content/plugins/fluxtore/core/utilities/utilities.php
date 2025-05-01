<?php

function is_fluxtore_pro() {
	return is_plugin_active('fluxtore-pro/fluxtore-pro.php') && fluXtorePro\Core\Admin\fluXtorePro_Licenser::is_license_valid();
}

function fluxtore_get_ip() {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}

function fluxtore_get_admin_order_fields($step_id, $order, $fields, $type) {
	if (!in_array($type, ['billing', 'shipping'], true)) {
		return false;
	}

	$step_fields = get_post_meta($step_id, FLUXTORE_PREFIX . $type . '_fields', true);

	if ($step_fields) {
		foreach ($step_fields as $field_id => $field) {
			$stripped_field_id = str_replace("{$type}_", "", $field_id);

			if (!array_key_exists($stripped_field_id, $fields)) {
				$fields[$stripped_field_id] = [
					"label" => $field['label'],
					"show" => $field['visible'] == 1,
					"value" => $order->get_meta($field_id)
				];
			}
		}

		return $fields;
	}

	return [];
}

function fluxtore_get_checkout_fields($post_id, $fields, $type) {
	if (!in_array($type, ['billing', 'shipping'], true)) {
		return false;
	}

	$step_fields = get_post_meta($post_id, FLUXTORE_PREFIX . $type . '_fields', true);

	if ($step_fields) {
		$visible_fields  = array_diff_key($step_fields, $fields);

		foreach ($step_fields as $field_id => $field) {
			foreach ($fields as $f_id => $f_v) {
				if ($field_id === $f_id) {
					$visible_fields[$field_id] = $f_v;
					//$visible_fields[$field_id]['percentage'] = $field['percentage'];
					$visible_fields[$field_id]['class'] = $field['class'];
					$visible_fields[$field_id]['priority'] = $field['priority'];
					$visible_fields[$field_id]['visible'] = $field['visible'];

					if (!$field['required'] || empty($field['required'])) {
						unset($visible_fields[$field_id]['required']);
					}
				}
			}
		}

		$visible_fields = array_filter($visible_fields, function ($field) {
			return $field['visible'];
		});

		uasort($visible_fields, 'wc_checkout_fields_uasort_comparison');

		return $visible_fields;
	}

	return false;
}

function fluxtore_default_checkout_fields() {
	$default = [
		'billing_last_name',
		'billing_company',
		'billing_country',
		'billing_address_2',
		'billing_address_1',
		'billing_city',
		'billing_state',
		'billing_phone',
		'billing_email',
		'billing_postcode',
		'billing_first_name',
		'shipping_first_name',
		'shipping_company',
		'shipping_state',
		'shipping_country',
		'shipping_postcode',
		'shipping_address_2',
		'shipping_address_1',
		'shipping_last_name',
		'shipping_city',
	];
	return $default;
}

function fluxtore_addslashes_to_strings_only($value) {
	return is_string($value) ? addslashes($value) : $value;
}

function fluxtore_recursively_slash_strings($value) {
	return map_deep($value,  'fluxtore_addslashes_to_strings_only');
}

function fluxtore_order_is_upsell($order_id) {
	return get_post_meta( $order_id, FLUXTORE_PREFIX . 'is_upsell', true)
		&& fluxtore_get_step_tag(fluxtore_get_order_step_id($order_id)) === FLUXTORE_UPSELL_TAG;
}

function fluxtore_order_is_downsell($order_id) {
	return get_post_meta( $order_id, FLUXTORE_PREFIX . 'is_upsell', true)
	       && fluxtore_get_step_tag(fluxtore_get_order_step_id($order_id)) === FLUXTORE_DOWNSELL_TAG;
}

function fluxtore_get_order_step_id($order_id) {
	return get_post_meta( $order_id, FLUXTORE_PREFIX . 'order_step', true);
}

function fluxtore_order_has_bump($order_id) {
	return count( get_post_meta( $order_id, FLUXTORE_PREFIX . 'order_bump_price' ) ) > 0;
}

function fluxtore_get_step_tag($step_id) {
	return get_post_meta( $step_id, FLUXTORE_PREFIX . 'tag', true);
}