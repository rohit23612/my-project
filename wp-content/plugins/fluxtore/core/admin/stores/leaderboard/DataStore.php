<?php
namespace fluXtore\Core\Admin\Stores\LeaderBoard;
use fluXtore\Core\Models\fluXtore_Step;
use DateTime;
use fluXtore\Core\Admin\Stores\DataStore as FluxDataStore;

class DataStore extends FluxDataStore {

    protected $visitors = [];

    public function get_data( $query_args ) {
       
		return $this->get_leaderboard_data($query_args);
		
    }


	public function get_leaderboard_data($args) {
	$flow_id = 	$args['flow_id'];
    $flow_step_ids = get_post_meta($flow_id, FLUXTORE_PREFIX . 'steps', true);
    
    $data = [];
        foreach($flow_step_ids as $step_id) {
            $step_visitors = $this->get_step_visits($args, $step_id);
            $this->visitors[$step_id] = $step_visitors;
            $step = new fluXtore_Step();
		    $step_data = $step->get($step_id);
            $data[] = [
                'id' => $step->id,
                'title' => $step_data->post_title,
                'post_type' => $step_data->post_type,
                'revenue' => $this->get_revenue_for_step($step_data, $args),
                'visits' =>  $step_visitors,
                'conversion' => $this->get_conversion_for_step($step_data, $args),
                'conversion_rate' => $this->get_conversion_rate_for_step($step_data, $args),

            ];
        }
        return $data;
	}
    public function get_revenue_for_step($step, $query_args)
    {
        if(in_array($step->__fluxtore__tag, ['Landing', 'Thank You'])) {
            return '-';
        }
        
      $orders = $this->get_orders_by_step($step, $query_args);
      $total = 0;
      foreach ( $orders as $order_id ) {
        $order = wc_get_order($order_id);
        $total += $order->get_subtotal();
      }
      return $total;
    }
    public function get_conversion_for_step($step, $query_args)
    {
        $orders = $this->get_orders_by_step($step, $query_args);
         return count($orders) > 0 ? count($orders) : '-';
    }
    public function get_conversion_rate_for_step($step_data, $args)
    {
        $step_visitors = isset($this->visitors[$step_data->ID]) ? $this->visitors[$step_data->ID] : $this->get_step_visits($args, $step_data->ID);
       $conversion = $this->get_conversion_for_step($step_data, $args);
       if(is_numeric($conversion)) {
           
           if($step_visitors > 0) {
                return ( (int) $conversion / $step_visitors );
           }

       }
       return '-';
    }
}