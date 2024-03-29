<?php
/*/////////////////////////////////////////////////////////////////

	FS SCHEMA, PHP Class - AJAX

	Copyright (C) 2013-2014 Klas Ehnemark (http://klasehnemark.com)
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
		
		add_action('wp_ajax_clear_cache', 				array ( $this, 'clear_cache' ));
		
		
		// add ajax functions for public
		
		add_action('wp_ajax_get_bookings', 			array ( $this, 'get_bookings' ));
		
		add_action('wp_ajax_nopriv_get_bookings', 		array ( $this, 'get_bookings' ));
		
		
		add_action('wp_ajax_book_activity', 			array ( $this, 'book_activity' ));
		
		add_action('wp_ajax_nopriv_book_activity', 		array ( $this, 'book_activity' ));
		
		
		add_action('wp_ajax_unbook_activity', 			array ( $this, 'unbook_activity' ));
		
		add_action('wp_ajax_nopriv_unbook_activity', 	array ( $this, 'unbook_activity' ));
		
		
		add_action('wp_ajax_book_waitinglist', 			array ( $this, 'book_waitinglist' ));
		
		add_action('wp_ajax_nopriv_book_waitinglist', 	array ( $this, 'book_waitinglist' ));
		
		
		add_action('wp_ajax_unbook_waitinglist', 		array ( $this, 'unbook_waitinglist' ));
		
		add_action('wp_ajax_nopriv_unbook_waitinglist', 	array ( $this, 'unbook_waitinglist' ));
		
	
		add_action('wp_ajax_walk_schema', 				array ( $this, 'walk_schema' ));
		
		add_action('wp_ajax_nopriv_walk_schema', 		array ( $this, 'walk_schema' ));


		add_action('wp_ajax_login', 					array ( $this, 'login' ));
		
		add_action('wp_ajax_nopriv_login', 			array ( $this, 'login' ));
		
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
	// CLEAR CACHE (FOR ADMIN)
	//
	////////////////////////////////////////////////////////////////////////////////
		
	function clear_cache () {
	
		global $fs_schema;
		
		$fs_schema->data->clear_cache ();
	
		echo 'Mellanlagrad cache har rensats.';
		
		die();
	
	}

		
	////////////////////////////////////////////////////////////////////////////////
	//
	// BOOK ACTIViTY
	//
	////////////////////////////////////////////////////////////////////////////////

	function book_activity () { 
	
		global $fs_schema;
		
		$username 		= isset( $_POST[ 'username' ] )? $_POST[ 'username' ] 		: '';
		
		$password 		= isset( $_POST[ 'password' ] )? $_POST[ 'password' ] 		: '';
		
		$activity_id 		= isset( $_POST[ 'activityid' ] )? $_POST[ 'activityid' ] 	: '';
		
		$session_key 		= isset( $_POST[ 'session_key' ] )	? $_POST[ 'session_key' ] : '';
		
		echo json_encode( $fs_schema->public->book_activity ( $username, $password, $activity_id, $session_key ));
		
		die();
	}




	////////////////////////////////////////////////////////////////////////////////
	//
	// UNBOOK ACTIViTY
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function unbook_activity () {
	
		global $fs_schema;
		
		$username 		= isset( $_POST[ 'username' ] )? $_POST[ 'username' ] 		: '';
		
		$password 		= isset( $_POST[ 'password' ] )? $_POST[ 'password' ] 		: '';
		
		$bookingid 		= isset( $_POST[ 'bookingid' ] )? $_POST[ 'bookingid' ] 	: '';
		
		$session_key 		= isset( $_POST[ 'session_key' ] )	? $_POST[ 'session_key' ] : '';
		
		echo json_encode( $fs_schema->public->unbook_activity ( $username, $password, $bookingid, $session_key ));
		
		die();
	}	



	////////////////////////////////////////////////////////////////////////////////
	//
	// BOOK WAITINGLIST
	//
	////////////////////////////////////////////////////////////////////////////////

	function book_waitinglist () { 
	
		global $fs_schema;
		
		$username 		= isset( $_POST[ 'username' ] )? $_POST[ 'username' ] 		: '';
		
		$password 		= isset( $_POST[ 'password' ] )? $_POST[ 'password' ] 		: '';
		
		$activity_id 		= isset( $_POST[ 'activityid' ] )? $_POST[ 'activityid' ] 	: '';
		
		$session_key 		= isset( $_POST[ 'session_key' ] )	? $_POST[ 'session_key' ] : '';
		
		echo json_encode( $fs_schema->public->book_waitinglist ( $username, $password, $activity_id, $session_key ));
		
		die();
	}
		
	
	////////////////////////////////////////////////////////////////////////////////
	//
	// UNBOOK WAITINGLIST
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function unbook_waitinglist () {
	
		global $fs_schema;
		
		$username 		= isset( $_POST[ 'username' ] )? $_POST[ 'username' ] 		: '';
		
		$password 		= isset( $_POST[ 'password' ] )? $_POST[ 'password' ] 		: '';
		
		$bookingid 		= isset( $_POST[ 'bookingid' ] )? $_POST[ 'bookingid' ] 	: '';
		
		$session_key 		= isset( $_POST[ 'session_key' ] )	? $_POST[ 'session_key' ] : '';
		
		echo json_encode( $fs_schema->public->unbook_waitinglist ( $username, $password, $bookingid, $session_key));
		
		die();
	}
	


	////////////////////////////////////////////////////////////////////////////////
	//
	// WALK SCHEMA
	//
	////////////////////////////////////////////////////////////////////////////////
	
	
	function walk_schema () {
	
		global $fs_schema;
		
		$num_days			= isset( $_POST[ 'num_days' ] )	? $_POST[ 'num_days' ] 	: '7';
		
		$date_info 		= isset( $_POST[ 'date_info' ] )	? $_POST[ 'date_info' ] 	: '';
		
		$step			= isset( $_POST[ 'step' ] )		? $_POST[ 'step' ] 		: '';
		
		$username 		= isset( $_POST[ 'username' ] )	? $_POST[ 'username' ] 	: '';
		
		$password 		= isset( $_POST[ 'password' ] )	? $_POST[ 'password' ] 	: '';
		
		$session_key 		= isset( $_POST[ 'session_key' ] )	? $_POST[ 'session_key' ] : '';

		echo $fs_schema->public->walk_schema ( $num_days, $date_info, $step, $username, $password, $session_key );
		
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
		
		//echo fs_schema::debug( $fs_schema->public->login ( $username, $password ));
		
		die();
	}	
	
 	
} //End Class
?>