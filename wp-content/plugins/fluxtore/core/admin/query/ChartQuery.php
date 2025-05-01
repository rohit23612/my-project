<?php
namespace fluXtore\Core\Admin\Query;

use fluXtore\Core\Admin\Query;

class ChartQuery extends Query {

	
	/**
	 * Get chart data based on the current query vars.
	 *
	 * @return array
	 */
	public function get_data() {
		$args = $this->get_query_vars();

		$data_store = \WC_Data_Store::load( 'fluxtore-chart' );
		$results    = $data_store->get_data( $args );
		return $results;
	}
}