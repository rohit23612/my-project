<?php
namespace fluXtore\Core\Admin\Controllers\Chart;

use fluXtore\Core\Admin\Query\ChartQuery;

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
	protected $rest_base = 'charts';

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
		$args['chart']     = $request['chart'] ?? 'count';
		$args['interval']  = $request['interval'] ?? 'month';
		$args['type']  	   = $request['type'] ?? 'bar';    
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
		$chart_query = new ChartQuery( $query_args );
        
		try {
			$report_data = $chart_query->get_data();
		} catch ( \Exception $e ) {
			return new \WP_Error( 'invalid_request', $e->getMessage(), array( 'status' => $e->getCode() ) );
		}

		$data = $this->prepare_data_for_response($report_data);
        $response = rest_ensure_response( $data );
	
		return $response;
    }
    public function prepare_data_for_response($data)
    {
        return $data;
    }
}