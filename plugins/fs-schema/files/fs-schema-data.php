<?php
/*/////////////////////////////////////////////////////////////////

	FS SCHEMA, PHP Class - DATA

	Copyright (C) 2013 Klas Ehnemark (http://klasehnemark.com)
	This program is not free software.

//////////////////////////////////////////////////////////////////*/


class fs_schema_data {


	// private variables
	private $settings_array = array();
	
	
	// public variables
	public $brp;
	public $profit;
	public $last_cache_status = 'Använder inte cache';
	public $last_http_code = '';
	public $debug = '';
	
	

	////////////////////////////////////////////////////////////////////////////////
	//
	// INITIALIZE OBJECT
	//
	////////////////////////////////////////////////////////////////////////////////

	public function __construct () {
	
		$this->brp 	= new fs_schema_brp;
		
		$this->profit 	= new fs_schema_profit;
	
	}
		
		
		
	//////////////////////////////////////////////////////////////////////////////
	//
	// GET SCHEMA
	//
	//////////////////////////////////////////////////////////////////////////////
	
	public function get_schema ( $args ) {
	
		$settings = $this->settings();
		
		$defaults = array(
			'typ'				=> 'vecka', 		// vecka, pass
			'anlaggning'			=> '',			// id, eller kommaseparerad id
			'datum'				=> '', 			// format: YYYY-MM-DD
			'username'			=> '',
			'password'			=> '',
			'integration'			=> $settings[ 'fs_schema_integration' ]
		);
		
		$r 						= wp_parse_args( $args, $defaults );
		
		$r['error'] 				= '';
		
		$r['message'] 				= '';
		
		$r['schema']				= array();
			

		// fix business units ids
		if ( $r[ 'anlaggning' ] != '' )
		
			$r['businessunitids'] 	= $r[ 'anlaggning' ];
		
		else 
		
			$r['businessunitids'] 	= $settings[ 'fs_booking_bpi_businessunitids' ];
		
		
		// fix start date
		$date_from_string 			= fs_schema::get_date_from_string ( $r['datum'] );
		
		if ( $date_from_string === false ) 
		
			$r['date_stamp'] 		= date('Y-m-d'); // today
		
		else $r['date_stamp'] 		= $r['datum'];
		
		$r['start_date '] 			= fs_schema::get_date_from_string ( $r['date_stamp'] );
		
			
		// fix week dates
		if ( $r['typ'] == 'vecka' ) {
		
			$date_week_start 		= mktime( 0, 0, 0, date( "m", $r['start_date '] ), date( "d", $r['start_date '] ) - date( 'N', $r['start_date '] )+1, date( "Y", $r['start_date '] ));
		
			$r['date_stamp'] 		= date( 'Y-m-d', $date_week_start );
			
			$r['date_stamp_end'] 	= date( 'Y-m-d', mktime( 0, 0, 0, date( "m", $date_week_start ), date( "d", $date_week_start ) + 6, date( "Y", $date_week_start )));
		
			$r['start_date'] 		= fs_schema::get_date_from_string ( $r['date_stamp'] );
		
		} else {
		
			$r['date_stamp_end'] 	= $r['date_stamp'];
			
		}
			
		$r['end_date'] 			= fs_schema::get_date_from_string ( $r['date_stamp_end']  );


		// get info from integration
		switch ( $r['integration'] ) {
		
			case 'BRP':
			
				return $this->brp->get_schema( $r );
				break;
				
				
			case 'PROFIT':
			
				return $this->profit->get_schema( $r );
				break;
				
		}			
	}
	
	

	//////////////////////////////////////////////////////////////////////////////
	//
	// LOGIN
	//
	//////////////////////////////////////////////////////////////////////////////
	
	public function login ( $username, $password ) {	
	
		$settings = $this->settings();
		
		switch ( $settings[ 'fs_schema_integration' ] ) {
		
			case 'BRP':
			
				return $this->brp->login( $username, $password );
				break;
				
				
			case 'PROFIT':
			
				return $this->profit->login( $username, $password );
				break;
		
		}
	
	}
	
		


	////////////////////////////////////////////////////////////////////////////////
	//
	// BOOK ACTIVITY
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function book_activity ( $username, $password, $activity_id ) {

		$settings = $this->settings();
		
		switch ( $settings[ 'fs_schema_integration' ] ) {
		
			case 'BRP':
			
				return $this->brp->book_activity( $username, $password, $activity_id );
				break;
				
				
			case 'PROFIT':
			
				return $this->profit->book_activity( $username, $password, $activity_id );
				break;
		
		}
	}	
	
	




	////////////////////////////////////////////////////////////////////////////////
	//
	// UNBOOK ACTIVITY
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function unbook_activity ( $username, $password, $bookingid ) {

		$settings = $this->settings();
		
		switch ( $settings[ 'fs_schema_integration' ] ) {
		
			case 'BRP':
			
				return $this->brp->unbook_activity( $username, $password, $bookingid );
				break;
				
				
			case 'PROFIT':
			
				return $this->profit->unbook_activity( $username, $password, $bookingid );
				break;
		
		}
	}	
	
	
	
	
	//////////////////////////////////////////////////////////////////////////////
	//
	// CACHE FUNCTION
	// Stores a cached version of value in a wp option
	//
	//////////////////////////////////////////////////////////////////////////////
	
	public function cached ( $cache_name, $update_function_name, $args = null, $force_update = false, $sub_cache_name = null ) {
	
		global $fs_schema;
		
		// get settings
		$settings = $fs_schema->data->settings();
//$force_update = true;	
		$number_second_cache = $settings[ 'fs_schema_update_inteval' ];
//$number_second_cache = 600;
		$do_update = $force_update;
		
		$cached_value = '';

		if ( $do_update == false ) {
		
			$cache_datestamp = get_option( $cache_name . '_datestamp' );
			
			if ( $cache_datestamp == '' ) $do_update = true;
			
			else {
			
				$cached_value = get_option( $cache_name . '_value' );
				
				if ( $cached_value == '' ) $do_update = true;
				
				if ( $cached_value != '' ) {
				
					$timeDiff = (strtotime('now') - $cache_datestamp); // check if timestamp differs enough to update it - get seconds of time diff since last update
					
					if ( $timeDiff > $number_second_cache) $do_update = true;
				}
			}
		}
		
		if ( $do_update == false && $sub_cache_name && is_array( $cached_value ) && !isset ( $cached_value[ $sub_cache_name ]) ) $do_update = true;

		if ( $do_update == true ) {  // update the latest version from function
		
			$save_results = true;
		
			$cached_value = call_user_func( $update_function_name, &$args );
			
			if ( isset ( $cached_value['error'] ) && isset ( $cached_value['xml'] )) {
			
				if ( $cached_value['error'] != '' ) $save_results = false;
				
				$cached_value = $cached_value['xml'];
			
			}
			
			$cached_value = serialize ( $cached_value );
			
			if ( $sub_cache_name ) $cached_value = array ( $sub_cache_name => $cached_value );
	
			$cache_datestamp = strtotime('now');  // set new timestamp and if this is the first time option is set, add option otherwise update it
			
			if ( get_option( $cache_name . '_datestamp' ) === false ) add_option( $cache_name . '_datestamp' , $cache_datestamp, '', 'yes');
			
			else update_option ( $cache_name . '_datestamp', $cache_datestamp );
			
			// if this is the option is set, add option otherwise update it
			if ( get_option( $cache_name . '_value' ) === false ) add_option( $cache_name . '_value', $cached_value, '', 'yes');
			
			else update_option ( $cache_name . '_value', $cached_value );
			
			$this->last_cache_status = 'Uppdaterar cache ' . $sub_cache_name;
		
		} else {
			
			$this->last_cache_status = 'Använder cache ' . $sub_cache_name;
		
		}
		
		if ( $sub_cache_name ) return unserialize ( $cached_value [$sub_cache_name ] );
		
		else return unserialize ( $cached_value );		

	}

	
	//////////////////////////////////////////////////////////////////////////////
	//
	// GET SCHEMA SETTINGS
	//
	//////////////////////////////////////////////////////////////////////////////
	
	public function settings () {
	
		if ( count ( $this->settings_array ) == 0 ) {

			$this->settings_array[ 'fs_schema_integration' ]				= get_option( 'fs_schema_integration' );
			
			$this->settings_array[ 'fs_schema_brp_server_url' ] 			= get_option( 'fs_schema_brp_server_url' );
			
			$this->settings_array[ 'fs_schema_profit_server_url' ] 		= get_option( 'fs_schema_profit_server_url' );
			
			$this->settings_array[ 'fs_booking_bpi_api_key' ]				= get_option( 'fs_booking_bpi_api_key' );
			
			$this->settings_array[ 'fs_booking_bpi_businessunitids' ]		= get_option( 'fs_booking_bpi_businessunitids' );
			
			$this->settings_array[ 'fs_booking_profit_3part_licence_key' ] 	= get_option( 'fs_booking_profit_3part_licence_key' );
			
			$this->settings_array[ 'fs_booking_profit_organization_unit' ] 	= get_option( 'fs_booking_profit_organization_unit' );
			
			$this->settings_array[ 'fs_schema_extra_column' ]				= get_option( 'fs_schema_extra_column' );
			
			$this->settings_array[ 'fs_schema_update_inteval' ]			= get_option( 'fs_schema_update_inteval' ) 	== '' ? '60' 	:  get_option( 'fs_schema_update_inteval' );
			
			if ( substr( $this->settings_array[ 'fs_schema_brp_server_url' ] , -1 ) != '/' ) $this->settings_array[ 'fs_schema_brp_server_url' ] .= '/';
		
		}
		
		return $this->settings_array;
	
	}



	
} //End Class

?>