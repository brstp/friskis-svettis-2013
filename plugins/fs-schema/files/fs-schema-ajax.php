<?php
/*/////////////////////////////////////////////////////////////////

	FS SCHEMA, PHP Class - AJAX

	Copyright (C) 2013 Klas Ehnemark (http://klasehnemark.com)
	This program is not free software.

//////////////////////////////////////////////////////////////////*/


class fs_schema_ajax {


	// private variables
	private $data;



	////////////////////////////////////////////////////////////////////////////////
	//
	// INITIALIZE OBJECT
	//
	////////////////////////////////////////////////////////////////////////////////

	public function __construct() {

		$this->data = new fs_schema_data ();
		
		// add ajax functions for admin
		add_action('wp_ajax_get_businessunits', 		array ( $this, 'get_businessunits' ));
		
		
		// add ajax functions for public
		add_action('wp_ajax_get_bookings', 			array ( $this, 'get_bookings' ));
		add_action('wp_ajax_nopriv_get_bookings', 		array ( $this, 'get_bookings' ));
		
		add_action('wp_ajax_book_activity', 			array ( $this, 'book_activity' ));
		add_action('wp_ajax_nopriv_book_activity', 		array ( $this, 'book_activity' ));
		
		add_action('wp_ajax_unbook_activity', 			array ( $this, 'unbook_activity' ));
		add_action('wp_ajax_nopriv_unbook_activity', 	array ( $this, 'unbook_activity' ));

		add_action('wp_ajax_walk_schema', 				array ( $this, 'walk_schema' ));
		add_action('wp_ajax_nopriv_walk_schema', 		array ( $this, 'walk_schema' ));

		add_action('wp_ajax_login', 					array ( $this, 'login' ));
		add_action('wp_ajax_nopriv_login', 			array ( $this, 'login' ));
		
		
		add_action('wp_ajax_test_post', 				array ( $this, 'test_post' ));
		add_action('wp_ajax_nopriv_test_post', 			array ( $this, 'test_post' ));

	}


	////////////////////////////////////////////////////////////////////////////////
	//
	// GET BOOKINGS
	//
	////////////////////////////////////////////////////////////////////////////////

	function get_booking () { 
	
		global $fs_schema;
		
		echo $fs_schema->data->get_bookings();
		
		die();
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	// GET BUSINESS IDS (FOR ADMIN)
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function get_businessunits () {
	
		global $fs_schema;
		
		$api_key 			= isset( $_POST[ 'api_key' ] )? $_POST[ 'api_key' ] : 0;
		
		$server_url 		= isset( $_POST[ 'server_url' ] )? $_POST[ 'server_url' ] : 0;
		
		echo $fs_schema->data->brp->get_businessunits( $server_url, $api_key );
		
		die();
		
	}
	
	
		
	////////////////////////////////////////////////////////////////////////////////
	//
	// BOOK ACTIViTY
	//
	////////////////////////////////////////////////////////////////////////////////

	function book_activity () { 
	
		global $fs_schema;
		
		$username 		= isset( $_POST[ 'username' ] )? $_POST[ 'username' ] : '';
		
		$password 		= isset( $_POST[ 'password' ] )? $_POST[ 'password' ] : '';
		
		$activity_id 		= isset( $_POST[ 'activityid' ] )? $_POST[ 'activityid' ] : '';
		
		echo json_encode( $fs_schema->public->book_activity ( $username, $password, $activity_id ));
		
		die();
	}
	
	

	////////////////////////////////////////////////////////////////////////////////
	//
	// UNBOOK ACTIViTY
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function unbook_activity () {
	
		global $fs_schema;
		
		$username 		= isset( $_POST[ 'username' ] )? $_POST[ 'username' ] : '';
		
		$password 		= isset( $_POST[ 'password' ] )? $_POST[ 'password' ] : '';
		
		$bookingid 		= isset( $_POST[ 'bookingid' ] )? $_POST[ 'bookingid' ] : '';
		
		echo json_encode( $fs_schema->public->unbook_activity ( $username, $password, $bookingid ));
		
		//echo fs_schema::debug ( $fs_schema->public->unbook_activity ( $username, $password, $bookingid ));
		
		die();
	}	



	////////////////////////////////////////////////////////////////////////////////
	//
	// WALK SCHEMA
	//
	////////////////////////////////////////////////////////////////////////////////
	
	
	function walk_schema () {
	
		global $fs_schema;
		
		$date_info 		= isset( $_POST[ 'date_info' ] )	? $_POST[ 'date_info' ] 	: '';
		
		$step			= isset( $_POST[ 'step' ] )		? $_POST[ 'step' ] 		: '';
		
		$username 		= isset( $_POST[ 'username' ] )	? $_POST[ 'username' ] 	: '';
		
		$password 		= isset( $_POST[ 'password' ] )	? $_POST[ 'password' ] 	: '';

		echo $fs_schema->public->walk_schema ( $date_info, $step, $username, $password );
		
		die();
	}



	////////////////////////////////////////////////////////////////////////////////
	//
	// LOGIN
	//
	////////////////////////////////////////////////////////////////////////////////
	
	
	function login () {
	
		global $fs_schema;
		
		$username 		= isset( $_POST[ 'username' ] )? $_POST[ 'username' ] : '';
		
		$password 		= isset( $_POST[ 'password' ] )? $_POST[ 'password' ] : '';

		echo json_encode( $fs_schema->public->login ( $username, $password ));
		
		die();
	}	
	
	
	
	
	
	function test_post () {
	
		$apikey 		= isset( $_POST[ 'apikey' ] )? $_POST[ 'apikey' ] : '';
		
		$type 		= isset( $_POST[ 'type' ] )? $_POST[ 'type' ] : '';
		
		$activityid 	= isset( $_POST[ 'activityid' ] )? $_POST[ 'activityid' ] : '';
		
		echo 'apikey=' . $apikey . ' type=' . $type . ' activityid=' . $activityid;
		
		die();
	
	}
	

 	
} //End Class
?>