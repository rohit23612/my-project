<?php
namespace fluXtore\Core\Admin\Stores\Orders;
use DateTime;
use fluXtore\Core\Admin\Stores\DataStore as FluxDataStore;

class HomeDataStore extends FluxDataStore {
	public function get_home_orders() {
		return wc_get_orders([
			'limit' => -1,
			'status' => ['wc-completed','wc-processing','wc-on-hold','wc-pending'],
			'meta_key' => FLUXTORE_PREFIX . 'is_fluxtore_order',
			'return' => 'ids',
		]);
	}

	public function get_total_revenue() {
		$orders = $this->get_home_orders();
		$total = 0;

		foreach ( $orders as $order_id ) {
			$order = wc_get_order($order_id);
			$total += (float) $order->get_subtotal();
		}

		return $total;
	}

	public function get_avg_home_order_value() {
		$total = $this->get_total_revenue();
		$orders = $this->get_home_orders();
		if(count($orders) > 0) {
			return ($total / count($orders));
		}
		return $total;
	}

	public function get_home_visits() {
		$visitors = get_option('__fluxtore__visitors');
		return count($visitors);
	}

	public function get_home_revenue_from_visits() {
		$total_revenue = $this->get_total_revenue();
		$total_visits = $this->get_home_visits();
		return ( (float) $total_revenue / ($total_visits === 0 ? 1 : (int) $total_visits ));
	}

    public function get_data( $query_args ) {
		return [
			'count' => count($this->get_home_orders()),
			'avg_total' => $this->get_avg_home_order_value(),
			'visits' => $this->get_home_visits(),
			'revenue' => $this->get_total_revenue(),
			'revenue_visit' => $this->get_home_revenue_from_visits(),
		];
    }
}