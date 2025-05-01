<?php
namespace fluXtore\Core\Admin\Controllers\LeaderBoard;

use fluXtore\Core\Admin\Query\LeaderBoardQuery;

class Controller extends \WC_REST_Reports_Controller {

    	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'fluxtore-analytics';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'leaderboard';

    	/**
	 * Maps query arguments from the REST request.
	 *
	 * @param array $request Request array.
	 * @return array
	 */
	protected function prepare_reports_query( $request ) {
		$args              = array();
		$args['before']    = $request['before'];
		$args['after']     = $request['after'];
		$args['chart']     = $request['chart'] ?? 'overview'; 
        $args['flow_id']   = $request['flow_id'];

		return $args;
	}

    /**
	 * Get all reports.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_items( $request ) {
		$query_args      = $this->prepare_reports_query( $request );
		$leaderboard_query = new LeaderBoardQuery( $query_args );
        
		try {
			$rows_data = $leaderboard_query->get_data();
		} catch ( \Exception $e ) {
			return new \WP_Error( $e->getCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
		$data = $this->get_leaderboard($rows_data);
        $response = rest_ensure_response( $data );
	
		return $response;

	}

	public function get_leaderboard( $rows_data ) {


		$rows = [];
		foreach ( $rows_data as $row ) {
	
			$rows[]       = array(
				array(
					'display' => $row['title'],
					'value'   => $row['title'],
				),
				array(
					'display' =>  wc_price( $row['revenue'] ),
					'value'   =>  $row['revenue'],
				),
				array(
					'display' => wc_admin_number_format($row['visits'] ),
					'value'   =>  $row['visits'],
				),
				array(
					'display' => $row['conversion'],
					'value'   =>  $row['conversion'],
				),
				array(
					'display' => $row['conversion_rate'] !== '-' ? ($row['conversion_rate'] * 100) . '%' : '-',
					'value'   =>  $row['conversion_rate'],
				),
			);
		}

		return array(
			'id'      => 'total_board',
			'label'   => __( 'LeaderBoard', 'fluXtore' ),
			'headers' => array(
				array(
					'label' => __( 'Step', 'fluXtore' ),
				),
				array(
					'label' => __( 'Revenue', 'fluXtore' ),
				),
				array(
					'label' => __( 'Visits', 'fluXtore' ),
				),
				array(
					'label' => __( 'Conversion', 'fluXtore' ),
				),
				array(
					'label' => __( 'Conversion Rate', 'fluXtore' ),
				),
			),
			'rows'    => $rows,
		);
	}
}
