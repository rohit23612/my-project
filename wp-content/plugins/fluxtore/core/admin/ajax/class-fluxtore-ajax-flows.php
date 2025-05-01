<?php

namespace fluXtore\Core\Admin\Ajax;

// Exit if accessed directly.
use fluXtore\Core\Models\fluXtore_Flow;
use fluXtore\Core\Models\fluXtore_Step;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_AjaxFlows' ) ) {
	class fluXtore_AjaxFlows extends fluXtore_AjaxBase {
		/**
		 * @var fluXtore_AjaxFlows $instance
		 */
		private static $instance;

		public static function init() {
			if ( !is_null( self::$instance ) ) {
				return;
			}

			self::$instance = new fluXtore_AjaxFlows();
			self::$instance->register_ajax_events();
		}

		function register_ajax_events() {
			$this->init_ajax_events([
				'create_flow',
				'get_flow',
				'get_all_flows',
				'save_flow_props',
				'delete_flow'
			]);
		}

		function create_flow() {
			$flow = new fluXtore_Flow();

			try {
				$flow_id = $flow->create( [
					'title' => sanitize_text_field( $_GET[ 'name' ] ),
					'template' => sanitize_text_field( $_GET[ 'template' ] )
				] );
			} catch (Exception $ex) {
				wp_send_json_error( [ 'error' => $ex->getMessage(), 401 ] );
				exit;
			}

			wp_send_json_success( [ 'id' => $flow_id ] );
		}

		private function sanitize_flow_fields( $flow ) {
			foreach ( $flow as $key => $value ) {
				if ( is_string( $value ) ) {
					$value = maybe_unserialize( $value );
				} else if ( is_array( $value ) ) {
					foreach ( $value as $i => $v ) {
						if ( is_string( $v ) ) {
							$value[ $i ] = maybe_unserialize( $v );
						}
					}
				}

				if ( string_starts_with( $key, 'post_' ) ) {
					$k = explode( 'post_', $key )[ 1 ];
					$flow->$k = $value;
					unset( $flow->$key );
				} elseif ( string_starts_with( $key, FLUXTORE_PREFIX ) ) {
					$k = explode( FLUXTORE_PREFIX, $key )[ 1 ];
					$flow->$k = $value;
					unset( $flow->$key );
				}
			}

			foreach ( $flow->steps as $step_index => $step ) {
				foreach ( $step as $id_index => $step_id ) {
					$flow->steps[ 0 ][ $id_index ] = ( new fluXtore_Step() )->get( $step_id );
					$preview_link = get_preview_post_link( $step_id );
					$flow->steps[ 0 ][ $id_index ]->preview_link = $preview_link;

					if ( "" . $flow->steps[ 0 ][ $id_index ]->{FLUXTORE_PREFIX . 'order'}[ 0 ] === '0' ) {
						$flow->preview_link = $preview_link;
					}
				}
			}

			$flow->edit_link = '/' . $flow->ID . '/steps';

			return $flow;
		}

		function get_flow() {
			$id = intval( $_GET[ 'id' ] );

			$flow = new fluXtore_Flow();
			$result = $flow->get( $id );

			wp_send_json_success( $this->sanitize_flow_fields( $result ) );
		}

		function get_all_flows() {
			$flow = new fluXtore_Flow();
			$result = [];

			foreach ( $flow->get_all() as $f ) {
				$result[] = $this->sanitize_flow_fields( $f );
			}

			wp_send_json_success( $result );
		}

		function save_flow_props() {
			try {
				$id = intval( $_GET[ 'id' ] );

				$flow = new fluXtore_Flow();
				$params = [];
                $metas = [];

				foreach ( $_GET as $key => $value ) {
					$value = sanitize_text_field( $value );

                    if ( string_starts_with( $key, 'meta_' ) ) {
                        $metas[ str_replace( 'meta_', '', $key ) ] = $value;
                        continue;
                    }

					if ( string_starts_with( $key, 'flow_' ) ) {
						$params[ str_replace( 'flow_', '', $key ) ] = $value;
					}

					if ( string_starts_with( $key, 'post_' ) ) {
						$params[ $key ] = $value;
					}
				}

                $flow->update_meta( $id, $metas );
				$result = $flow->update( $id, $params );
				wp_send_json_success( $result );
			} catch (\Exception $ex) {
				wp_send_json_error([ "message" => $ex->errorMessage() ]);
			}
		}

		function delete_flow() {
			$id = intval( $_GET[ 'id' ] );

			$flow = new fluXtore_Flow();
			$result = $flow->delete( $id );

			wp_send_json_success( $result );
		}
	}
}

fluXtore_AjaxFlows::init();