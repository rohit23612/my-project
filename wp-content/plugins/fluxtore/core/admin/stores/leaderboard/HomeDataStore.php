<?php
namespace fluXtore\Core\Admin\Stores\LeaderBoard;

use fluXtore\Core\Admin\Stores\DataStore as FluxDataStore;

class HomeDataStore extends FluxDataStore {
    protected $visitors = 0;

    public function get_data( $query_args ) {
       
		return $this->get_leaderboard_data($query_args);
		
    }
	public function get_leaderboard_data($args) {

    
    $fluxtore_tags = ['Landing','Checkout', 'Upsell', 'Downsell', 'Thank You'];
    $data = [];
        foreach($fluxtore_tags as $tag) {
            $steps_visitors = $this->get_steps_visits($tag);
            $this->visitors = $steps_visitors;
          
            $data[] = [
                'title' => $tag,
                'revenue' => $this->get_revenue_for_steps($tag),
                'visits' =>  $steps_visitors,
                'conversion' => $this->get_conversion_for_steps($tag),
                'conversion_rate' => $this->get_conversion_rate_for_steps($tag),

            ];
            if($tag === 'Checkout') {
                $data[] = [
                    
                    'title' => __('Order Bump', 'fluXtore'),
                    'revenue' => $this->get_revenue_for_bumps($tag),
                    'visits' =>  $steps_visitors,
                    'conversion' => $this->get_conversion_for_bumps($tag),
                    'conversion_rate' => $this->get_conversion_rate_for_bumps($tag),
                ]; 
            }
        }
        return $data;
	}

    public function get_revenue_for_bumps($tag) {
      
        $orders = $this->get_home_orders_by_steps($tag);
		//print_r($tag);
        $total = 0;
        foreach ( $orders as $order_id ) {
           
          $is_bump_accepted = get_post_meta($order_id, FLUXTORE_PREFIX . 'order_bump_accepted', true);
          if(!$is_bump_accepted) {
              continue;
          }
          $order_bump_price = get_post_meta($order_id, FLUXTORE_PREFIX . 'order_bump_price', true);
          $total += (float) $order_bump_price;
        }
        return $total;
      }
      public function get_conversion_for_bumps($tag) {
        $orders = $this->get_home_orders_by_steps($tag);
          $count = 0;
          foreach ( $orders as $order_id ) {
          
              $is_bump_accepted = get_post_meta($order_id, FLUXTORE_PREFIX . 'order_bump_accepted', true);
              if($is_bump_accepted) {
                  $count++;
              }
            }
            return $count === 0 ? '-' : $count;
      }
      public function get_conversion_rate_for_bumps($tag) {
        $steps_visitors = $this->visitors;
          $conversion = $this->get_conversion_for_bumps($tag);
          if(is_numeric($conversion)) {
              
              if($steps_visitors > 0) {
                   return ( (int) $conversion / $steps_visitors );
              }
   
          }
          return '-';  
      }

    public function get_revenue_for_steps($tag) {
        if(in_array($tag, ['Landing', 'Thank You'])) {
            return '-';
        }
        
      $orders = $this->get_home_orders_by_steps($tag);
      $total = 0;
      foreach ( $orders as $order_id ) {
        $order = wc_get_order($order_id);
        $total += $order->get_subtotal();
      }
      return $total;
    }
    
    public function get_conversion_for_steps($tag) {
        $orders = $this->get_home_orders_by_steps($tag);
         return count($orders) > 0 ? count($orders) : '-';
    }

    public function get_conversion_rate_for_steps($tag) {
        $steps_visitors = $this->visitors;
       $conversion = $this->get_conversion_for_steps($tag);
       if(is_numeric($conversion)) {
           
           if($steps_visitors > 0) {
                return ( (int) $conversion / $steps_visitors );
           }

       }
       return '-';
    }

    public function get_steps_visits($tag) {
       $posts = get_posts([
           'numberposts' => -1,
           'post_type' => 'fluxtore_step',
           'fields' => 'ids',
           'meta_query' => [
               [
                   'key' => FLUXTORE_PREFIX . 'tag',
                   'value' => $tag
               ]
           ]
       ]);
       if(!empty($posts)) {
        $total = 0;
           foreach($posts as $step_id) {
            $visitors = get_post_meta($step_id, FLUXTORE_PREFIX . 'step_visitors', true);
            if(!empty($visitors)) {
            
                foreach($visitors as $visitor_date) {
                        $total++;
                }   
            }
           }
           return $total;
       }
        
        return 0;
    }

	public function get_home_orders_by_steps($tag) {
	
		$post_ids = get_posts([
			'numberposts' => -1,
			'post_type' => 'fluxtore_step',
			'fields' => 'ids',
			'meta_query' => [
				[
					'key' => FLUXTORE_PREFIX . 'tag',
					'value' => $tag
				]
			]
		]);
		//print_r($post_ids);
		// $date_between = $date_after->format('Y-m-d') . '...' . $date_before->format('Y-m-d');
		$args = [
			'numberposts' => -1,
			'post_type' => 'shop_order',
			'post_status'    => ['wc-completed','wc-processing','wc-on-hold','wc-pending'],
			'meta_query' => [
				[
					'key' => FLUXTORE_PREFIX.'order_step',
					'value' => $post_ids,
					'compare' => 'IN'
				]
			],
			'return' => 'ids',
		];

		$order_ids = wc_get_orders( $args );

			if ( ! empty( $order_ids ) ) {
				// Do something with the order IDs
				foreach ( $order_ids as $order_id ) {
					//echo $order_id . '<br>';
				}
			} else {
				// No orders found
				//echo 'No orders found.';
			}

			return $order_ids; 
			// return get_posts($args);
	}
}