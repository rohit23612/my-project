<?php

namespace fluXtore\Core\Models;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Avoid defining the class twice.
if (!class_exists('fluXtore_Step')) {
	class fluXtore_Step extends fluXtore_ModelBase {
		function create($params) {
			global $wpdb;

			$title = $params['title'];

			// WooCommerce reserved word
			if (strtolower($title) === "checkout") {
				$title .= "-page";
			}

			$data = [
				'post_title' => $title,
				'post_type' => FLUXTORE_STEP,
				'comment_status' => 'close',
				'post_status' => 'publish',
				'post_author' => get_current_user_id()
			];

			$flow_id = $params['flow_id'];

			$step_id = wp_insert_post($data);

			$count = $wpdb->get_row("SELECT COUNT(*) AS THE_COUNT FROM $wpdb->postmeta WHERE (meta_key = '" . FLUXTORE_PREFIX . "flow' AND meta_value = '$flow_id')")->THE_COUNT;

			update_post_meta($step_id, FLUXTORE_PREFIX . 'tag', $params['tag']);
			update_post_meta($step_id, FLUXTORE_PREFIX . 'flow', $flow_id);

			$this->update_product($step_id, 0, 'original', 0);

			$this->update_order_bump($step_id, false, 0, 0, 0, 0, "ADD THIS PRODUCT NOW AND GET A DISCOUNT!!", "Add now and pay");

			$this->update_order($step_id, $count);

			return $step_id;
		}

		function delete($id) {
			$flow_id = get_post_meta($id, FLUXTORE_PREFIX . 'flow')[0];

			$steps = get_post_meta($flow_id, FLUXTORE_PREFIX . 'steps');

			if (($key = array_search($id, $steps[0])) !== false) {
				unset($steps[0][$key]);
			}

			update_post_meta($flow_id, FLUXTORE_PREFIX . 'steps', array_values($steps[0]));

			return wp_delete_post($id, true);
		}

		function update($id, $params) {
			$step = $this->get($id);

			if (isset($params['post_name']) && $step->post_name !== $params['post_name']) {
				$params['post_name'] = wp_unique_post_slug($params['post_name'], $id, $step->post_status, $step->post_type, -1);
			}

			$result = wp_update_post(array_merge(['ID' => $id], $params));

			if (is_wp_error($result)) {
				throw new \Exception($result->get_error_message());
			}

			return $this->get($id);
		}

		function update_order($id, $order) {
			update_post_meta($id, FLUXTORE_PREFIX . 'order', $order);
		}

		function get($id) {
			$step = get_post($id);
			return (object)array_merge((array)$step, (array)get_post_meta($id));
		}
		public static function get_step($id) {
			$step = get_post($id);
			return (object)array_merge((array)$step, (array)get_post_meta($id));
		}
		function get_tag($id) {
			return get_post_meta($id, FLUXTORE_PREFIX . 'tag', true);
		}

		function get_all($query = '') {
			if (!is_array($query)) {
				$query = [
					'id' => $query
				];
			}

			$steps = [];

			if (isset($query['flow_id'])) {
				foreach (get_post_meta($query['flow_id'], FLUXTORE_PREFIX . 'steps') as $step) {
					foreach ($step as $s) {
						$steps[] = $this->get($s);
					}
				}
			}

			usort($steps, function ($a, $b) {
				return $a->{FLUXTORE_PREFIX . 'order'}[0] > $b->{FLUXTORE_PREFIX . 'order'}[0];
			});

			return $steps;
		}

		function update_product($id, $product, $price_type, $price) {
			update_post_meta($id, FLUXTORE_PREFIX . 'product', $product);
			update_post_meta($id, FLUXTORE_PREFIX . 'price_type', $price_type);
			update_post_meta($id, FLUXTORE_PREFIX . 'price', $price);

			return true;
		}

		function get_product($id) {
			return get_post_meta($id, FLUXTORE_PREFIX . 'product', true);
		}

		function update_yes_no_steps($id, $yes, $no) {
			update_post_meta($id, FLUXTORE_PREFIX . 'yes_step', $yes);
			update_post_meta($id, FLUXTORE_PREFIX . 'no_step', $no);

			return true;
		}

		function update_order_bump($id, $enabled, $default_checked, $product, $price, $discount, $text, $toggle) {
			update_post_meta($id, FLUXTORE_PREFIX . 'order_bump_enabled', $enabled);
			update_post_meta($id, FLUXTORE_PREFIX . 'order_bump_default_checked', $default_checked);
			update_post_meta($id, FLUXTORE_PREFIX . 'order_bump_product', $product);
			update_post_meta($id, FLUXTORE_PREFIX . 'order_bump_price', $price);
			update_post_meta($id, FLUXTORE_PREFIX . 'order_bump_discount', $discount);
			update_post_meta($id, FLUXTORE_PREFIX . 'order_bump_text', $text);
			update_post_meta($id, FLUXTORE_PREFIX . 'order_bump_toggle', $toggle);

			return true;
		}

		function get_yes_step($id) {
			
			$step = $this->get($id);
			$yes = get_post_meta($id, FLUXTORE_PREFIX . 'yes_step', true);

			if (empty($yes)) {
				$next = $this->next_step($id);

				if ($next) {
					$step = $next;
					$next = $this->next_step($next->ID);

					if ($next) {
						$step = $next;
					}
				}
			} else {
				$step = $this->get($yes);
			}

			return $step;
		}

		function get_no_step($id) {
		 	 
			$step = $this->get($id);
			$no = get_post_meta($id, FLUXTORE_PREFIX . 'no_step', true);

			if (empty($no)) {
				$next = $this->next_step($id);

				if ($next) {
					$step = $next;
				}
			} else {
				$step = $this->get($no);
			}

			return $step;
		}

		function get_flow($id) {
			return get_post_meta($id, FLUXTORE_PREFIX . 'flow');
		}

		function is_bump_enabled($id) {
			return get_post_meta($id, FLUXTORE_PREFIX . 'order_bump_enabled');
		}

		function get_price_type($id) {
			return get_post_meta($id, FLUXTORE_PREFIX . 'price_type');
		}

		function get_price($id) {
			return get_post_meta($id, FLUXTORE_PREFIX . 'price');
		}

		function get_bump_prices($id) {
			return [
				'price' => get_post_meta($id, FLUXTORE_PREFIX . 'order_bump_price'),
				'discount' => get_post_meta($id, FLUXTORE_PREFIX . 'order_bump_discount')
			];
		}

		function next_step($id) {			
			//$step = $this->get($id);
			$step = $this->get($id);
			$flow_id = get_post_meta($id, FLUXTORE_PREFIX . 'flow')[0];
			$flow_model = new fluXtore_Flow();
			$flow = $flow_model->get($flow_id);

			$steps_param = FLUXTORE_PREFIX . 'steps';
			$order_param = FLUXTORE_PREFIX . 'order';

			$steps = maybe_unserialize($flow->$steps_param[0]);
         
			foreach ($steps as $i => $s) {
				if ($s === $step->ID) {
					$steps[$i] = $step;
					continue;
				}

				$steps[$i] = $this->get($s);
			}
			
			if ($steps!=null){
                usort($steps, function ($a, $b) use ($order_param) {
                    return $a->$order_param[0] > $b->$order_param[0];
                });
			}

			$result = $step;
			$index = 0;

			foreach ($steps as $key => $value) {
				if ($value->ID == $result->ID) {
					$index = $key;
					break;
				}
			}

			 if (count($steps) > $index + 1) {
				$result = $steps[$index + 1];
			} else {
				$result = null;
			} 

			return $result;
		}
	}
}
  