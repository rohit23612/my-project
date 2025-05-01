<?php
namespace fluXtore\Core\Admin\Stores\Orders;

use DateTime;
use fluXtore\Core\Admin\Stores\DataStore as FluxDataStore;

class DataStore extends FluxDataStore {
    /**
	 * Table used to get the data.
	 *
	 * @var string
	 */
	protected static $table_name = 'wc_order_stats';

    public function get_data( $query_args ) {
       
		return [
			// 'total_sales' => $this->get_orders_total($query_args),
			'count' => count($this->get_orders_by_flow($query_args)),
			'avg_total' => $this->get_avg_order_value($query_args),
			'visits' => $this->get_flow_visits($query_args),
			'revenue' => $this->get_flow_revenue($query_args),
			'revenue_visit' => $this->get_revenue_from_visits($query_args),
		];
    }


}