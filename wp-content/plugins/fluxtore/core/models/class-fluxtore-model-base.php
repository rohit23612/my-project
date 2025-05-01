<?php

namespace fluXtore\Core\Models;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtore_ModelBase' ) ) {
	abstract class fluXtore_ModelBase {
		abstract function create( $params );
		abstract function delete( $id );
		abstract function update( $id, $params );
		abstract function get( $id );
		abstract function get_all( $query = '' );
	}
}