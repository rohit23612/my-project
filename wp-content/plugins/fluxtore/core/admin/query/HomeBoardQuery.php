<?php
namespace fluXtore\Core\Admin\Query;

use fluXtore\Core\Admin\Query;

class HomeBoardQuery extends Query {

	
	/**
	 * Get leaderboard data based on the current query vars.
	 *
	 * @return array
	 */
	public function get_data() {
		$args = $this->get_query_vars();

		$data_store = \WC_Data_Store::load( 'fluxtore-home-leaderboard' );
		$results    = $data_store->get_data( $args );
		return $results;
	}
}