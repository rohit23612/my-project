<?php
namespace fluXtore\Core\Admin\Stores\Charts;

use DateTime, DateInterval, DatePeriod;
use fluXtore\Core\Admin\Stores\DataStore as FluxDataStore;

class DataStore extends FluxDataStore {
    public function get_data( $query_args ) {
        return $this->get_chart_data($query_args);
    }

    public function create_data_for_bar_chart($orders,$visits, $date,$chart)
    {
       /**
        *  $orders orders made throught website
        *  $filtered_orders orders made on checkout page
       */
        $bumps = $this->loop_bumps( $orders );
        $upsells = $this->loop_upsells( $orders );
        $filtered_orders = $this->get_order_per_flow_for_checkout($orders);
        $total_revenue = $this->get_orders_revenue_by_orders($orders);
        $total_orders = count($orders) > 0 ?  count($orders) : 1;
        
        $data =[];
        switch($chart) {
      
            case 'count':
                $data['date'] = $date->format('Y-m-d');
                $data['orders'] = count($orders);
                $data['orders_raw'] = count($filtered_orders);
                $data['bumps'] = count($bumps);
                $data['upsells'] = count($upsells);
                break;
            case 'avg_total':
                $data['date'] = $date->format('Y-m-d');
                $data['avg_total'] = $total_revenue / $total_orders;
                break;  
            case 'visits':
                $data['date'] = $date->format('Y-m-d');
                $data['visits'] = $visits;
                break;
            case 'revenue':
                $revenue = $this->get_orders_revenue_by_orders($orders);
                $data['date'] = $date->format('Y-m-d');
                $data['revenue'] = $revenue;
                break;   
            case 'revenue_visit':
                $revenue = $this->get_orders_revenue_by_orders($orders);
                $revenue_visit = ( (float) $revenue / ($visits === 0 ? 1 : (int) $visits ));
                $data['date'] = $date->format('Y-m-d');
                $data['revenue_visit'] = $revenue_visit;
                break; 
        }
        return $data;
    }
    public function create_data_for_line_chart($orders,$visits, $date, $chart)
    {
      /**
        *  $orders orders made throught website
        *  $filtered_orders orders made on checkout page
       */
        $bumps = $this->loop_bumps( $orders );
        $upsells = $this->loop_upsells( $orders );
      
        $filtered_orders = $this->get_order_per_flow_for_checkout($orders);
        $total_revenue = $this->get_orders_revenue_by_orders($orders);
     
        $total_orders = count($orders) > 0 ?  count($orders) : 1;
        $data =[];
        switch($chart) {
       
            case 'count':
                $data['x'] = $date->format('Y-m-d');
                $data['y'] = count($filtered_orders) + count($upsells) + count($bumps); 
                
                break;
            case 'avg_total':
                $data['x'] = $date->format('Y-m-d');
                $data['y'] = $total_revenue / $total_orders;
                break; 
            case 'visits':
                $data['x'] = $date->format('Y-m-d');
                $data['y'] = $visits;
                break;  
                
            case 'revenue':
                $revenue = $this->get_orders_revenue_by_orders($orders);
                $data['x'] = $date->format('Y-m-d');
                $data['y'] = $revenue;
                break; 

            case 'revenue_visit':
                $revenue = $this->get_orders_revenue_by_orders($orders);
                $revenue_visit = ( (float) $revenue / ($visits === 0 ? 1 : (int) $visits ));
                $data['x'] = $date->format('Y-m-d');
                $data['y'] = $revenue_visit;
                break; 
        }
        return $data;
    }
    public function create_data_for_chart_type( $orders, $date, $chart, $chart_type, $visits)
    {
        
     
        $data =[];
        switch($chart_type) {
            
            case 'bar':
           
                $data  = $this->create_data_for_bar_chart($orders,$visits, $date, $chart);
                break;
                
            case 'line' :
               
                $data  = $this->create_data_for_line_chart($orders,$visits, $date, $chart);
                break;
        }
        return $data;
    }
    public function create_data_from_interval($date_after,$interval, $date_before, $flow_id, $chart, $chart_type)
    {
        $data = [];
        switch($interval) {
            case 'day':
            $int =  DateInterval::createFromDateString('1 day');
            $daterange = new DatePeriod($date_after, $int , $date_before);
            
            foreach($daterange as $key => $date) {
                $orders = $this->get_orders_by_day($date->format('Y-m-d'), $flow_id );
                $visits = $this->get_flow_visits_for_chart($date, null, $flow_id);
             
                $data_for_type = $this->create_data_for_chart_type( $orders, $date, $chart, $chart_type, $visits);
                $data[$key] = $data_for_type;
         
               
            }
            break;
            case 'week':
                $int =   DateInterval::createFromDateString('1 week');
      
                $daterange = new DatePeriod($date_after, $int , $date_before);
                foreach($daterange as $key => $date) {
                    $next_day_obj = clone $date;
                    $next_day = $next_day_obj->modify('+ 1 week');
                $orders = $this->get_orders_by_week($date, $next_day , $flow_id ); 
                $visits = $this->get_flow_visits_for_chart($date, $next_day, $flow_id);
              
                $data_for_type = $this->create_data_for_chart_type( $orders, $date, $chart, $chart_type, $visits);
                $data[$key] = $data_for_type;
                // $data[$key] = $orders;
          
                }
                break;
            case 'month': 
                $int =  DateInterval::createFromDateString('1 month');
               
                $daterange = new DatePeriod($date_after, $int , $date_before);
            foreach($daterange as $key => $date) {
                $next_day_obj = clone $date;
                $next_day = $next_day_obj->modify('+ 1 month');
                $orders = $this->get_orders_by_month($date, $next_day , $flow_id ); 
                $visits = $this->get_flow_visits_for_chart($date, $next_day, $flow_id);
                $data_for_type = $this->create_data_for_chart_type( $orders, $date, $chart, $chart_type, $visits);
                $data[$key] = $data_for_type;
            }
            break;
            case 'quarter':
                $int = DateInterval::createFromDateString('3 months');
               
                $daterange = new DatePeriod($date_after, $int , $date_before);
            foreach($daterange as $key => $date) {
                $next_day_obj = clone $date;
                $next_day = $next_day_obj->modify('+3 months');
                $orders = $this->get_orders_by_quarter($date, $next_day , $flow_id );
                $visits = $this->get_flow_visits_for_chart($date, $next_day, $flow_id);
              
          
                $data_for_type = $this->create_data_for_chart_type( $orders, $date, $chart, $chart_type, $visits);
                $data[$key] = $data_for_type;
            }
            break;
        }
        return $data;
    }
    public function get_chart_data_overview($query_args)
    {
        $flow_id = $query_args['flow_id'];
        $date_after = new DateTime($query_args['after']);
		$date_before = new DateTime($query_args['before']);
        
        $data = $this->create_data_from_interval($date_after, $query_args['interval'],$date_before,$flow_id, $query_args['chart'], $query_args['type']);
       
        return $data;
    }
    public function get_chart_data_count($query_args)
    {
        $flow_id = $query_args['flow_id'];
        $date_after = new DateTime($query_args['after']);
		$date_before = new DateTime($query_args['before']);
        
       

        $data = $this->create_data_from_interval($date_after, $query_args['interval'],$date_before,$flow_id, $query_args['chart'], $query_args['type']);
       
        // return $data;
        return [ 'id' => $date_after->format('M Y'), 'color' => "hsl(352, 70%, 50%)", 'data' => $data];
    }
    public function get_chart_data($query_args)
    {
        $flow_id = $query_args['flow_id'];
        $date_after = new DateTime($query_args['after']);
		$date_before = new DateTime($query_args['before']);
        $data = $this->create_data_from_interval($date_after, $query_args['interval'],$date_before,$flow_id, $query_args['chart'], $query_args['type']);
       if($query_args['type'] === 'bar') {
	    return [
			'data' => $data,
		];
       }
       else {
           return [ 'id' => $date_after->format('Y-m-d'), 'color' => "hsl(352, 70%, 50%)", 'data' => $data];
       }
    }
}