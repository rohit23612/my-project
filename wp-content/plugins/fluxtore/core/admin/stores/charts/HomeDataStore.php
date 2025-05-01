<?php
namespace fluXtore\Core\Admin\Stores\Charts;

use DateTime, DateInterval, DatePeriod;
use fluXtore\Core\Admin\Stores\DataStore as FluxDataStore;

class HomeDataStore extends FluxDataStore {
    public function get_data( $query_args ) {
        return $this->get_chart_data($query_args);
    }

	public function get_home_visits_for_chart($date_after, $date_before) {
		return $this->get_visits([
			'after' => $date_after,
			'before' => $date_before
		]);
	}

    public function create_data_for_bar_chart($orders, $visits, $date, $chart) {
       /**
        *  $orders orders made throught website
        *  $filtered_orders orders made on checkout page
       */
        $bumps = $this->loop_bumps( $orders );
        $upsells = $this->loop_upsells( $orders );
        $downsells = $this->loop_downsells($orders);
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
                $data['downsells'] = count($downsells);
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
    public function create_data_for_line_chart($orders,$visits, $date, $chart) {
      /**
        *  $orders orders made throught website
        *  $filtered_orders orders made on checkout page
       */
        $bumps = $this->loop_bumps( $orders );
        $upsells = $this->loop_upsells( $orders );
        $downsells = $this->loop_downsells($orders);
        $filtered_orders = $this->get_order_per_flow_for_checkout($orders);
        $total_revenue = $this->get_orders_revenue_by_orders($orders);
     
        $total_orders = count($orders) > 0 ?  count($orders) : 1;
        $data =[];
        switch($chart) {
       
            case 'count':
                $data['x'] = $date->format('Y-m-d');
                $data['y'] = count($filtered_orders) + count($upsells) + count($bumps) + count($downsells);
                $data['orders'] = count($filtered_orders); 
                $data['bumps'] = count($bumps);
                $data['upsells'] = count($upsells);
                $data['downsells'] = count($downsells); 
                
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
    public function create_data_for_chart_type( $orders, $date, $chart, $chart_type, $visits) {
        
     
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

    public function create_home_data($interval, $chart, $chart_type) {
        $data = [];
        $first_order_date = $this->get_first_flux_order_date();
        // $first_order_date = new DateTime('2022-02-01');
        $now = new DateTime('now');
        // return ['now' => $now->format('Y-m-d'), 'then' => $first_order_date->format('Y-m-d')];
        if($first_order_date === $now) {
            return $data;
        }
        switch($interval) {
            case 'day':
            $int =  DateInterval::createFromDateString('1 day');
            $daterange = new DatePeriod( $first_order_date, $int , $now);
            
            foreach($daterange as $key => $date) {
                $orders = $this->get_orders_by_day($date->format('Y-m-d'), false);
                $visits = $this->get_home_visits_for_chart($date, null);
             
                $data_for_type = $this->create_data_for_chart_type( $orders, $date, $chart, $chart_type, $visits);
                $data[$key] = $data_for_type;
         
               
            }
            break;
            case 'week':
                $int =   DateInterval::createFromDateString('1 week');
      
                $daterange = new DatePeriod( $first_order_date, $int , $now);
                foreach($daterange as $key => $date) {
                    $next_day_obj = clone $date;
                    $next_day = $next_day_obj->modify('+ 1 week');
                $orders = $this->get_orders_by_week($date, $next_day , false ); 
                $visits = $this->get_home_visits_for_chart($date, $next_day);
              
                $data_for_type = $this->create_data_for_chart_type( $orders, $date, $chart, $chart_type, $visits);
                $data[$key] = $data_for_type;
                // $data[$key] = $orders;
          
                }
                break;
            case 'month': 
                $int =  DateInterval::createFromDateString('1 month');
               
                $daterange = new DatePeriod( $first_order_date, $int , $now);
            foreach($daterange as $key => $date) {
                $next_day_obj = clone $date;
                $next_day = $next_day_obj->modify('+ 1 month');
                $orders = $this->get_orders_by_month($date, $next_day , false ); 
                $visits = $this->get_home_visits_for_chart($date, $next_day);
                $data_for_type = $this->create_data_for_chart_type( $orders, $date, $chart, $chart_type, $visits);
                $data[$key] = $data_for_type;
            }
            break;
            case 'quarter':
                $int = DateInterval::createFromDateString('3 months');
               
                $daterange = new DatePeriod( $first_order_date, $int , $now);
            foreach($daterange as $key => $date) {
                $next_day_obj = clone $date;
                $next_day = $next_day_obj->modify('+3 months');
                $orders = $this->get_orders_by_quarter($date, $next_day , false );
                $visits = $this->get_home_visits_for_chart($date, $next_day);
              
          
                $data_for_type = $this->create_data_for_chart_type( $orders, $date, $chart, $chart_type, $visits);
                $data[$key] = $data_for_type;
            }
            break;
            case 'year':
                $int = DateInterval::createFromDateString('1 year');
               
                $daterange = new DatePeriod( $first_order_date, $int , $now);
            foreach($daterange as $key => $date) {
                $next_day_obj = clone $date;
                $next_day = $next_day_obj->modify('+1 year');
                $orders = $this->get_orders_by_year($date, $next_day ); 
                $visits = $this->get_home_visits_for_chart($date, $next_day);
              
          
                $data_for_type = $this->create_data_for_chart_type( $orders, $date, $chart, $chart_type, $visits);
                $data[$key] = $data_for_type;
            }
            break;
                
        }
        return $data;
    }
    public function get_line_orders_chart($interval,$type) {
        $data = ['id' => $type, 'color' => "hsl(169, 70%, 50%)", 'data' => []];
        $first_order_date = $this->get_first_flux_order_date();
        
        $now = new DateTime('now');
        
        if($first_order_date === $now) {
            return $data;
        }

        switch($interval) {
            case 'day':
            $int =  DateInterval::createFromDateString('1 day');
            $daterange = new DatePeriod( $first_order_date, $int , $now);
            
            foreach($daterange as $key => $date) {
                $orders = $this->get_orders_by_day($date->format('Y-m-d'), false);
                $data_for_line = $this->create_data_for_line_orders( $orders, $date, $type);
                $data['data'][$key] = $data_for_line;
         
            }
            break;
            
                
        }
        return $data;
    }
    public function create_data_for_line_orders($orders, $date, $type) {
        $bumps = $this->loop_bumps( $orders );
        $upsells = $this->loop_upsells( $orders );
        $downsells = $this->loop_downsells($orders);
        $filtered_orders = $this->get_order_per_flow_for_checkout($orders);
        switch($type) {
        case 'orders':
            return ['x' => $date->format('Y-m-d'), 'y' => count($filtered_orders)];
        case 'bumps':
            return ['x' => $date->format('Y-m-d'), 'y' => count($bumps)];
        case 'upsells':
            return ['x' => $date->format('Y-m-d'), 'y' => count($upsells)];
        case 'downsells':
            return ['x' => $date->format('Y-m-d'), 'y' => count($downsells)];

        }
    }
    public function get_chart_data($query_args) {
        // if($query_args['chart'] === 'count' && $query_args['type'] === 'line') {
        //     $line_data = [];
        //     foreach(['orders', 'bumps', 'upsells', 'downsells'] as $type) {
        //        $line_data[] =  $this->get_line_orders_chart($query_args['interval'], $type);
        //     }
        //     return $line_data;
        // }
        $data = $this->create_home_data( $query_args['interval'], $query_args['chart'], $query_args['type']);
       if($query_args['type'] === 'bar') {
	    return [
			'data' => $data,
		];
       }
       else {
           return [ 'id' => date('Y-m-d', strtotime('now')), 'color' => "hsl(352, 70%, 50%)", 'data' => $data];
       }
    }

	public function get_first_flux_order_date() : DateTime {
		$orders = wc_get_orders([
			'limit' => -1,
			'status' => ['wc-completed','wc-processing','wc-on-hold','wc-pending'],
			'meta_key' => FLUXTORE_PREFIX . 'is_fluxtore_order',
			'orderby' => 'date',
			'order' => 'ASC',


		]);
		if(!empty($orders)) {
			return new DateTime($orders[0]->get_date_created()->date('Y-m-d')) ;
		}
		return new DateTime('now');
	}
}