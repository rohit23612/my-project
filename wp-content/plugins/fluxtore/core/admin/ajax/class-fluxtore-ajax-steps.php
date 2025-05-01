<?php

namespace fluXtore\Core\Admin\Ajax;

// Exit if accessed directly.
use fluXtore\Core\Models\fluXtore_Step;
use fluXtore\Core\Runners\fluXtore_ImportTemplate;


if (!defined('ABSPATH')) {
	exit;
}

// Avoid defining the class twice.
if (!class_exists('fluXtore_AjaxSteps')) {
	class fluXtore_AjaxSteps extends fluXtore_AjaxBase {
		/**
		 * @var self $instance
		 */
		private static $instance;

		public static function init() {
			if (!is_null(self::$instance)) {
				return;
			}

			self::$instance = new self();
			self::$instance->register_ajax_events();
		}

		function register_ajax_events() {
			$this->init_ajax_events([
				'create_step',
				'get_step',
				'get_steps_by_flow_id',
				'get_steps_by_step_id',
				'save_step_props',
				'save_step_product',
				'get_step_price_type',
				'get_step_price',
				'delete_step',
				'reorder_steps'
			]);
		}

		function create_step() {
			$flow_id = intval($_GET['flow_id']);
			$file_name = $_GET['file'];
			$templates = fluxtore_get_steps_templates();

			$index = array_search($file_name, array_column($templates, 'file'));
			$pro = false;

			if ($index === false) {
				$templates = fluxtore_get_pro_steps_templates();
				$index = array_search($file_name, array_column($templates, 'file'));
				$pro = true;
			}

			$step = new fluXtore_Step();

			$step_id = $step->create([
				'title' => sanitize_text_field($_GET['name']),
				'flow_id' => $flow_id,
				'tag' => $templates[$index]['tag']
			]);

			fluXtore_ImportTemplate::maybe_import_template_in_post($step_id, $file_name, $pro);

			$steps = get_post_meta($flow_id, FLUXTORE_PREFIX . 'steps')[0];
			$steps[] = $step_id;

			update_post_meta($flow_id, FLUXTORE_PREFIX . 'steps', $steps);

			wp_send_json_success(['id' => $step_id]);
		}

		function get_step() {
			$id = intval($_GET['id']);

			$step = new fluXtore_Step();
			$result = $step->get($id);

			wp_send_json_success($this->sanitize_steps_fields($result));
		}

	private function sanitize_steps_fields($step) {
    foreach ($step as $key => $value) {
        if (is_string($value) && unserialize($value) !== false) {
            $value = unserialize($value);
        } else if (is_array($value)) {
            foreach ($value as $i => $v) {
                if (is_string($v) && unserialize($v) !== false) {
                    $value[$i] = unserialize($v);
                }
            }
        }

        if (string_starts_with($key, 'post_')) {
            $k = explode('post_', $key)[1];
            $step->$k = $value;
            unset($step->$key);
        } elseif (string_starts_with($key, FLUXTORE_PREFIX)) {
            $k = explode(FLUXTORE_PREFIX, $key)[1];
            if ($k === 'billing_fields' || $k === 'shipping_fields') {
                $step->$k = $value[0];
            } else {
                $step->$k = $value;
            }
            unset($step->$key);
        }
    }

    // Generate the appropriate link for Bricks
    if (get_option('page_on_front') == $step->ID) {
        // If this step is the homepage, use the home URL
        $step->bricks_link = get_home_url() . '?bricks=run';
    } else {
        // Otherwise, use the original GUID
        $step->bricks_link = $step->guid . '?bricks=run';
    }

    $step->preview_link = get_preview_post_link($step->ID);
    $step->edit_link = get_site_url() . '/wp-admin/post.php?post=' . $step->ID . '&action=elementor';
    $step->gutenberg_link = get_site_url() . '/wp-admin/post.php?post=' . $step->ID . '&action=edit';
    $step->divi_link = $step->guid . '?et_fb=1&PageSpeed=off';
    $step->settings_link = '/' . $step->flow[0] . '/' . $step->ID . '/editstep';
   
    return $step;
}


		function get_steps_by_flow_id() {
			$flow_id = intval($_GET['flow_id']);
			$simple = boolval($_GET['simple']) ?? false;

			$step = new fluXtore_Step();
			$result = [];

			foreach ($step->get_all(compact('flow_id')) as $step) {
				$sanitized = $this->sanitize_steps_fields($step);

				if ($simple) {
					$push_step = [
						'ID' => $sanitized->ID,
						'tag' => $sanitized->tag,
						'title' => $sanitized->title,
						'edit_link' => $sanitized->edit_link,
						'override_edit_link' => $sanitized->settings_link,
						'preview_link' => $sanitized->preview_link
					];

					switch (strtolower($sanitized->tag[0])) {
						case 'checkout':
						case 'upsell':
						case 'downsell':
							if (!isset($sanitized->product) || $sanitized->product[0] <= 0) {
								$push_step['error'] = "No product assigned";
							} else {
								$product = wc_get_product($sanitized->product[0]);

								if ($product === false) {
									$push_step['error'] = "No product assigned";
								} else {
									$push_step['success'] = "Product " . $product->get_name() . " assigned";
								}
							}

							break;
					}
					/**
					 * abtest admin steps
					 * @since 1.14
					 */
					$is_abtest = get_post_meta($sanitized->ID, FLUXTORE_PREFIX . 'abtest', true);
					if ($is_abtest) {
						$is_abtest_active = get_post_meta($sanitized->ID, FLUXTORE_PREFIX . 'ab_test_active', true);
						$push_step['abtest_data'] = $is_abtest;
						$push_step['abtest_active'] = (int) $is_abtest_active;
						$abtest_variations = get_post_meta($sanitized->ID, FLUXTORE_PREFIX . 'abtest_variations', true);
						if ($abtest_variations && !empty($abtest_variations)) {
							foreach ($abtest_variations as $variation_key => $variation) {

								$step = fluXtore_Step::get_step($variation);
								$sanitized_child = $this->sanitize_steps_fields($step);
								$abtest_data = get_post_meta($variation, FLUXTORE_PREFIX . 'abtest', true);
								$push_step['abtest_variations'][$variation_key] = [
									'ID' => $sanitized_child->ID,
									'tag' => $sanitized_child->tag,
									'title' => $sanitized_child->title,
									'edit_link' => $sanitized_child->edit_link,
									'override_edit_link' => $sanitized_child->settings_link,
									'preview_link' => $sanitized_child->preview_link,
									'abtest_data' => $abtest_data
								];

								switch (strtolower($sanitized_child->tag[0])) {
									case 'checkout':
									case 'upsell':
									case 'downsell':
										if (!isset($sanitized_child->product) || $sanitized_child->product[0] <= 0) {
											$push_step['abtest_variations'][$variation_key]['error'] = "No product assigned";
										} else {
											$product = wc_get_product($sanitized_child->product[0]);

											if ($product === false) {
												$push_step['abtest_variations'][$variation_key]['error'] = "No product assigned";
											} else {
												$push_step['abtest_variations'][$variation_key]['success'] = "Product " . $product->get_name() . " assigned";
											}
										}

										break;
								}
							}
						}
					}
					$result[] = $push_step;

					continue;
				}

				$result[] = $sanitized;
			}

			wp_send_json_success($result);
		}

		function get_steps_by_step_id() {
			$step_id = intval($_GET['step_id']);
			$step = new fluXtore_Step();
			$flow = $step->get_flow($step_id);

			$_GET['flow_id'] = $flow[0];

			$this->get_steps_by_flow_id();
		}

		function save_step_props() {
			try {
				$id = intval($_GET['id']);

				$step = new fluXtore_Step();
				$params = [];

				foreach ($_GET as $key => $value) {
					$value = sanitize_text_field($value);
					if (string_starts_with($key, 'step_')) {
						$params[str_replace('step_', '', $key)] = $value;
					}

					if (string_starts_with($key, 'post_')) {
						$params[$key] = $value;
					}
				}

				$result = $step->update($id, $params);
				wp_send_json_success($result);
			} catch (\Exception $ex) {
				wp_send_json_error(["message" => $ex->errorMessage()]);
			}
		}

		function save_step_product() {
			$id = intval($_GET['id']);
			$pid = intval($_GET['product']);
			$price_type = sanitize_text_field($_GET['type']);
			$price = floatval($_GET['price']);

			$step = new fluXtore_Step();

			$result = $step->update_product($id, $pid, $price_type, $price);

			wp_send_json_success($result);
		}

		function get_step_price_type() {
			$id = intval($_GET['id']);
			$step = new fluXtore_Step();
			$result = $step->get_price_type($id);

			wp_send_json_success($result);
		}

		function get_step_price() {
			$id = intval($_GET['id']);
			$step = new fluXtore_Step();
			$result = $step->get_price($id);

			wp_send_json_success($result);
		}

		function delete_step() {
			$id = intval($_GET['id']);

			$step = new fluXtore_Step();

			$step_obj = fluXtore_Step::get_step($id);

			$parent_variation_id = $step_obj->post_parent > 0 ? $step_obj->post_parent : $step_obj->ID;
			$variations = get_post_meta($parent_variation_id, FLUXTORE_PREFIX . 'abtest_variations', true);


			if ($variations) {


				if ($step_obj->post_parent > 0) {
					$updated_value = array_values(array_filter($variations, function ($v) use ($id) {
						return ((int) $v !== $id);
					}));

					if (count($updated_value) < 1) {
						// wp_send_json_error([
						// 	'1' => $updated_value
						// ]);
						delete_post_meta($parent_variation_id, FLUXTORE_PREFIX . 'abtest_variations');
						delete_post_meta($parent_variation_id, FLUXTORE_PREFIX . 'abtest');
					} else {
						// wp_send_json_error([
						// 	'2' => $updated_value
						// ]);
						update_post_meta($parent_variation_id, FLUXTORE_PREFIX . 'abtest_variations', $updated_value);
					}
				} else {
					$flow_id = get_post_meta($id, FLUXTORE_PREFIX . 'flow', true);
					$steps = get_post_meta($flow_id, FLUXTORE_PREFIX . 'steps', true);
					// array_unshift($variations, $parent_variation_id);
					// $updated_value_children = array_values(array_filter($variations, function ($v) use ($id) {
					// 	return (int) $v !== $id;
					// }));
					$new_king = $variations[0];

					if (count($variations) === 1) {
						// wp_send_json_error([
						// 	'var' => $variations,
						// 	'king' => $new_king
						// ]);
						wp_update_post([
							'ID' => $new_king,
							'post_parent' => 0
						]);
						delete_post_meta($new_king, FLUXTORE_PREFIX . 'abtest');
						delete_post_meta($new_king, FLUXTORE_PREFIX . 'abtest_variations');
						delete_post_meta($new_king, FLUXTORE_PREFIX . 'ab_test_active');
					} else {
						// wp_send_json_error([
						// 	'var' => $variations,
						// 	'king' => $new_king
						// ]);
						foreach ($variations as $var_key => $var_id) {


							if ($var_key === 0) {
								wp_update_post(
									[
										'ID' => $var_id,
										'post_parent' => 0
									]
								);
							} else {
								wp_update_post(
									[
										'ID' => $var_id,
										'post_parent' => $new_king
									]
								);
							}
						}

						update_post_meta($new_king, FLUXTORE_PREFIX . 'ab_test_active', 0);
						unset($variations[0]);
						update_post_meta($new_king, FLUXTORE_PREFIX . 'abtest_variations', array_values($variations));
					}
					$new_king_in_flow = array_push($steps, $new_king);
					update_post_meta($flow_id, FLUXTORE_PREFIX . 'steps', $steps);
				}
			}
			$result = $step->delete($id);
			wp_send_json_success($result);
		}

		function reorder_steps() {
			$step = new fluXtore_Step();

			foreach ($_GET['items'] as $order => $ID) {
				$step->update_order(intval($ID), intval($order));
			}
		}

	}
}

fluXtore_AjaxSteps::init();
