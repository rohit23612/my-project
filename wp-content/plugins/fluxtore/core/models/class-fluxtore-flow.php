<?php

namespace fluXtore\Core\Models;

// Exit if accessed directly.
use fluXtore\Core\Admin\fluXtore_GoPro;
use fluXtore\Core\Runners\fluXtore_ImportTemplate;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_Flow' ) ) {
	class fluXtore_Flow extends fluXtore_ModelBase {
		function create( $params ) {
			$step_model = new fluXtore_Step();
			$steps = [];

			$limits = fluXtore_GoPro::get_flows_limits();

			if ( $limits > -1 && $limits <= wp_count_posts( FLUXTORE_FLOW ) ) {
				throw new \Exception( 'Cannot create new flows. Limit of ' . $limits . ' reached.<br />If you need more please <a className="font-bold text-perfect-blue" href="https://fluxtore.com/pricing?email=' . wp_get_current_user()->user_email . '" target="_blank"/>upgrade to Pro</a>' );
			}

			$data = [
				'post_title' => $params[ 'title' ],
				'post_type' => FLUXTORE_FLOW,
				'comment_status' => 'close',
				'post_status' => 'publish',
				'post_author' => get_current_user_id()
			];

			$flow_id = wp_insert_post( $data );

			$template_steps = fluxtore_get_steps_from_template( $params[ 'template' ] );
			$pro = empty( $template_steps );
			$template_steps = $pro ? ( is_fluxtore_pro() ? fluxtore_get_pro_steps_from_template( $params[ 'template' ] ) : [] ) : $template_steps;

			foreach ( $template_steps as $step ) {
				$step[ 'flow_id' ] = $flow_id;
				$step_id = $step_model->create( $step );

				fluXtore_ImportTemplate::maybe_import_template_in_post( $step_id, $step['file'] ?? "", $pro);

				$steps []= $step_id;
			}

			update_post_meta( $flow_id, FLUXTORE_PREFIX . 'steps', $steps );
			update_post_meta( $flow_id, FLUXTORE_PREFIX . 'visitors', [] );

			return $flow_id;
		}

		function delete( $id ) {
			$step_model = new fluXtore_Step();

			foreach ( get_post_meta( $id, FLUXTORE_PREFIX . 'steps' )  as $step ) {
				foreach ( $step as $s ) {
					$step_model->delete( $s );
				}
			}

			return wp_delete_post( $id, true );
		}

		function update( $id, $params ) {
			$flow = $this->get( $id );

			if ( isset( $params[ 'post_name' ] ) && $flow->post_name !== $params[ 'post_name' ] ) {
				$params[ 'post_name' ] = wp_unique_post_slug( $params[ 'post_name' ], $id, $flow->post_status, $flow->post_type, -1 );
			}

			$result = wp_update_post( array_merge( [ 'ID' => $id ], $params ) );

			if ( is_wp_error( $result ) ) {
				throw new \Exception( $result->get_error_message() );
			}

			return $this->get( $id );
		}

        function update_meta( $id, $params ) {
            foreach ( $params as $key => $value ) {
                update_post_meta( $id, FLUXTORE_PREFIX . $key, $value );
            }
        }

		function get( $id ) {
			$flow = get_post( $id );
			return ( object )array_merge( ( array )$flow, ( array )get_post_meta( $id ) );
		}

		function get_all( $query = '' ) {
            $options = [
                'post_type' => FLUXTORE_FLOW,
                'numberposts' => -1,
                'post_status' => [ 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash' ]
            ];

            $options = apply_filters( 'fluxtore_get_all_flows', $options );

			$flows = get_posts( $options );

			foreach ( $flows as $index => $flow ) {
				$flows[ $index ] = ( object )array_merge( ( array )$flow, ( array )get_post_meta( $flow->ID ) );
			}

			return $flows;
		}

		function new_visitor( $id ) {
			// Flow visitors
			$visitors = maybe_unserialize( get_post_meta( $id, FLUXTORE_PREFIX . 'visitors' )[ 0 ] );
			$visitors[] = date("Y-m-d");
			update_post_meta( $id, FLUXTORE_PREFIX . 'visitors', $visitors );

			// Global visitors
			$global_visitors = maybe_unserialize( get_option( FLUXTORE_PREFIX . 'visitors', [ [] ] ) );
			$global_visitors[] = date("Y-m-d");
			update_option( FLUXTORE_PREFIX . 'visitors', $global_visitors );

			return true;
		}
	}
}