<?php

namespace fluXtore\Core\Admin\Stores;

use DateTime;

abstract class DataStore {
	const DATETIME_FORMAT = 'Y-m-d H:i:s';
	const DATE_FORMAT = 'Y-m-d';

    public function get_data($args) { }

    public function loop_bumps($orders) {
        $result = [];

        foreach ( $orders as $order_id ) {
            if ( fluxtore_order_has_bump( $order_id ) ) {
                $result []= $order_id;
            }
        }

        return $result;
    }

    public function loop_upsells($orders) {
        $result = [];

        foreach ( $orders as $order_id ) {
			if (  fluxtore_order_is_upsell( $order_id ) ) {
                $result []= $order_id;
            }
        }

        return $result;
    }

    public function loop_downsells($orders) {
        $result = [];

        foreach ( $orders as $order_id ) {
            if ( fluxtore_order_is_downsell( $order_id ) ) {
                $result []= $order_id;
            }
        }

        return $result;
    }

	/**
	 * @param \WP_Post $step
	 * @param $args
	 *
	 * @return int[]|\WP_Post[]
	 * @throws \Exception
	 */
    public function get_orders_by_step($step, $args) {
        $date_after = new DateTime($args['after']);
		$date_before = new DateTime($args['before']);

		$args = [
            'numberposts' => -1,
            'post_type' => 'shop_order',
            'post_status'    => ['wc-completed', 'wc-processing', 'wc-on-hold', 'wc-pending'],
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => FLUXTORE_PREFIX . 'order_flow',
                    'value' => absint($args['flow_id']),
                    'type' => 'NUMERIC',
                    'compare' => '='
                ],
                [
                    'key' =>  FLUXTORE_PREFIX . 'order_step',
                    'value' => absint($step->ID),
                    'type' => 'NUMERIC',
                    'compare' => '='
                ]
            ],
            'date_query' => [
                [
                    'before' =>  $date_before->format(self::DATETIME_FORMAT),
                    'after' => $date_after->format(self::DATE_FORMAT),
                    'inclusive' => true,
                ]
            ],
            'fields' => 'ids'
		];
            
      $order_ids = wc_get_orders( $args );

			if ( ! empty( $order_ids ) ) {
				// Do something with the order IDs
				foreach ( $order_ids as $order_id ) {
					//echo $order_id . '<br>';
				}
			} else {
				// No orders found
				//echo 'No orders found.';
			}

			return $order_ids; 
			//   return get_posts($args);
    }

	private function get_order_args_by_flow_id($flow_id = null) {
		if ($flow_id) {
			$args = [
				'meta_key' => FLUXTORE_PREFIX . 'order_flow',
				'meta_value' => $flow_id,
			];
		} else {
			$args = ['meta_key' => FLUXTORE_PREFIX . 'is_fluxtore_order'];
		}

		return $args;
	}

	private function get_orders_by_date($date, $flow_id = null) {
		$args = $this->get_order_args_by_flow_id($flow_id);

		$defaults = [
			'limit' => -1,
			'status' => ['wc-completed', 'wc-processing', 'wc-on-hold', 'wc-pending'],
			'return' => 'ids',
			'date_created' => $date
		];

		$args = wp_parse_args( $args, $defaults );

		return wc_get_orders($args);
	}

    public function get_orders_by_flow($args) {
		$date_after = new DateTime($args['after']);
		$date_before = new DateTime($args['before']);

		$date_between = $date_after->format(self::DATETIME_FORMAT) .
		                '...' .
		                $date_before->format(self::DATE_FORMAT);

		return $this->get_orders_by_date($date_between, $args['flow_id']);
	}

    public function get_order_per_flow_for_checkout(array $order_ids) {
		return array_filter($order_ids, function($id) {
			$step_id = get_post_meta($id, FLUXTORE_PREFIX . 'order_step', true);
			return fluxtore_get_step_tag($step_id) === 'Checkout';
		});
    }

    public function get_orders_by_day($day, $flow_id) {
	    return $this->get_orders_by_date($day, $flow_id);
    }

    public function get_orders_by_week($week_start, $week_end, $flow_id) {
        $date_between = $week_start->format(self::DATETIME_FORMAT) .
                        '...' .
                        $week_end->modify('-1 day')->format(self::DATE_FORMAT);

        return $this->get_orders_by_date($date_between, $flow_id);
    }

	public function get_orders_by_month($month_start, $month_end, $flow_id) {
		$date_between = $month_start->format(self::DATETIME_FORMAT) .
                        '...' .
                        $month_end->format(self::DATE_FORMAT);

		return $this->get_orders_by_date($date_between, $flow_id);
    }

    public function get_orders_by_quarter($quarter_start, $quarter_end, $flow_id) {
		$date_between = $quarter_start->format(self::DATETIME_FORMAT) .
		                '...' .
		                $quarter_end->format(self::DATE_FORMAT);

		return $this->get_orders_by_date($date_between, $flow_id);
    }

    public function get_orders_by_year($year_start, $year_end) {
		$date_between = $year_start->format(self::DATE_FORMAT) .
                        '...' .
                        $year_end->format(self::DATE_FORMAT);

		return $this->get_orders_by_date($date_between);
	}

	public function get_orders_total( $args) {
		$orders = $this->get_orders_by_flow($args);
		$total = 0;

		foreach ( $orders as $order_id ) {
			$total += floatval( get_post_meta( $order_id, '_order_total', true ) );
		}

		return $total;
		
	}

    //visits
	private function get_visitors_array($args) {
		if ($args['flow_id']) {
			$visitors = get_post_meta($args['flow_id'], FLUXTORE_PREFIX . 'step_visitors', true);
		} else if ($args['step_id']) {
			$visitors = get_post_meta($args['step_id'], FLUXTORE_PREFIX . 'visitors', true);
		} else {
			$visitors = get_option('__fluxtore__visitors');
		}

		return $visitors;
	}

	protected function get_visits($args) {
		if (is_a($args['after'], DateTime::class)) {
			$dateAfter = $args['after'];
		} else {
			$dateAfter = new DateTime($args['after']);
		}
		if (is_a($args['before'], DateTime::class)) {
			$dateBefore = $args['before'];
		} else {
			$dateBefore = new DateTime($args['before']);
		}

		$visitors = $this->get_visitors_array($args);
		$total = 0;

		if (empty($visitors)) {
			return $total;
		}

		foreach ($visitors as $visitor_date) {
			if (is_array($visitor_date) && empty($visitor_date)) {
				continue;
			}

			$date = new DateTime($visitor_date);

			if (is_null($dateBefore)) {
				if ($date->format(self::DATE_FORMAT) === $dateAfter->format(self::DATE_FORMAT)) {
					$total++;
				}
			} else if ($date >= $dateAfter && $date <= $dateBefore) {
				$total++;
			}
		}

		return $total;
	}

    public function get_flow_visits($args) {
        return $this->get_visits($args);
    }

    public function get_step_visits($args, $step_id) {
		$args['step_id'] = $step_id;
        return $this->get_visits($args);
    }

    public function get_flow_visits_for_chart($date_after, $date_before, $flow_id) {
		$args = [
			'after' => $date_after,
			'before' => $date_before,
			'flow_id' => $flow_id
		];

        return $this->get_visits($args);
    }

    public function get_flow_revenue($args) {
        $orders = $this->get_orders_by_flow($args);
        $total = 0;

		foreach ( $orders as $order_id ) {
            $order = wc_get_order($order_id);
			$total += (float) $order->get_subtotal();
		}

		return $total;
    }

    public function get_orders_revenue_by_orders($orders) {
        $total = 0;

		foreach ( $orders as $order_id ) {
            $order = wc_get_order($order_id);
			$total += (float) $order->get_subtotal();
		}

		return $total;
    }

    public function get_revenue_from_visits($args) {
        $total_revenue = $this->get_flow_revenue($args);
        $total_visits = $this->get_flow_visits($args);
        return ( (float) $total_revenue / ($total_visits === 0 ? 1 : (int) $total_visits ));
    }

    public function get_avg_order_value($args) {
        $total = $this->get_flow_revenue($args);
        $orders = $this->get_orders_by_flow($args);
        if(count($orders) > 0) {
            return ($total / count($orders));
        }
        return $total;
    }
}
