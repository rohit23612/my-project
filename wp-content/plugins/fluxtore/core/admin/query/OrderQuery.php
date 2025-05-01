<?php
namespace fluXtore\Core\Admin\Query;

use fluXtore\Core\Admin\Query;

class OrderQuery extends Query {

	
	/**
	 * Get order data based on the current query vars.
	 *
	 * @return array
	 */
	public function get_data() {
		$args = $this->get_query_vars();

		$data_store = \WC_Data_Store::load( 'fluxtore-order' );
		$results    = $data_store->get_data( $args );
		return $results;
	}
}