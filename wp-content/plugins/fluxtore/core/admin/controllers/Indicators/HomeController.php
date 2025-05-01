<?php
namespace fluXtore\Core\Admin\Controllers\Indicators;

use fluXtore\Core\Admin\Query\HomeOrderQuery;

class HomeController extends \WC_REST_Reports_Controller {
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
	protected $rest_base = 'home/indicators';

    	/**
	 * Maps query arguments from the REST request.
	 *
	 * @param array $request Request array.
	 * @return array
	 */
	protected function prepare_reports_query( $request ) {
		$args              = array();
        $args['stats']     = $request['stats'];
      
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
		$reports_revenue = new HomeOrderQuery( $query_args );
        
		try {
			$report_data = $reports_revenue->get_data();
		} catch ( \Exception $e ) {
			return new \WP_Error( 'invalid_request', $e->getMessage(), array( 'status' => $e->getCode() ) );
		}

		$data = $this->prepare_data_for_response($report_data);
        $response = rest_ensure_response( $data );
	
		return $response;
    }
    public function prepare_data_for_response($data) {
        $fields = [];
        foreach($data as $key => $value) {
            if($key === 'avg_total') {
                $fields[$key] = [
				'chart' => 'avg_total',
				'label' => __('Average order value'),
				'stat' => 'order/avg_total',
				'format' => 'currency',
				'value' => $value
                ];
            }
            if($key === 'count') {
                $fields[$key] = [
                'chart' => 'count',
                'label' => __('Orders'),
                'stat' => 'order/count',
                'format' => 'number',
                'value' => $value
                ];
            }
			if($key === 'visits') {
                $fields[$key] = [
				'chart' => 'visits',
				'label' => __('Visits'),
				'stat' => 'visit/count',
                'format' => 'number',
                'value' => $value
                ];
            }
			if($key === 'revenue') {
                $fields[$key] = [
				'chart' => 'revenue',
				'label' => __('Revenue'),
				'stat' => 'order/revenue',
                'format' => 'currency',
                'value' => $value
                ];
            }
			if($key === 'revenue_visit') {
                $fields[$key] = [
				'chart' => 'revenue_visit',
				'label' => __('Revenue per visit'),
				'stat' => 'visit/revenue',
                'format' => 'currency',
                'value' => $value
                ];
            }
        }
        return ['data' => $fields];
    }
}