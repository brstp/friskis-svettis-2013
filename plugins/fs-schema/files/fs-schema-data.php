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
	public $last_cache_status = 'Använder inte cache';
	public $last_http_code = '';
	public $debug = '';
	
	

	////////////////////////////////////////////////////////////////////////////////
	//
	// INITIALIZE OBJECT
	//
	////////////////////////////////////////////////////////////////////////////////

	public function __construct () {}
		

	////////////////////////////////////////////////////////////////////////////////
	//
	// GET XML FILE
	//
	////////////////////////////////////////////////////////////////////////////////
	
	public function get_xml_file ( $url, $post_data=null, $username = '', $password = '', $send_delete = false ) {
		
		global $fs_schema;
		
		$output 					= array ( 'error' => '', 'message' => '', 'xml' => null );
		
		// get content via curl
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		if ( $send_delete === true ) curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
		
		if ( $post_data ) {
		
			curl_setopt( $ch, CURLOPT_POST, 1);
			
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data ); 
		
		}
		
		if ( $username != '' && $password != ''	) {
		
			curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
			
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			
		}

		$curl_output = curl_exec($ch);
		
		$curl_info = curl_getinfo($ch);
		
		$this->debug .= 'CURL INFO: ' . print_r ($curl_info, true ) . '<br>CURL OUTPUT: ' . print_r ($curl_output, true ) . '<br>'; 
		
		curl_close($ch);
		
		
		// analyze content and return the correct response
		$http_code				= '0';
		
		if ( isset ( $curl_info ['http_code'] )) $http_code = $curl_info ['http_code'];
		
		if ( $curl_output == '' && $http_code != '204' ) $http_code = '-1';
		
		$this->last_http_code = $http_code;
			
		switch ( $http_code ) {
		
			case '0':
			
				$output['error'] 	= 'YES';
				
				$output['message'] 	= 'Ett okänt fel uppstod. Var vänlig försök igen senare.';
				
				break;				

			case '-1':
			
				$output['error'] 	= 'YES';
				
				$output['message'] 	= 'Ett okänt fel uppstod, bokningssystemet returnerade ingen information. Var vänlig försök igen senare.';
				
				break;
				
			case '401':
			
				$output['error'] 	= 'YES';
				
				$output['message'] 	= 'Inloggning misslyckades. Användarnamn och/eller lösenord är felaktigt.';
				
				break;
				
			case '403':
			
				$output['error'] 	= 'YES';
				
				$output['message'] 	= 'Inloggning lyckades, men du har inte behörighet att boka denna aktivitet.';
				
				$output['xml']		= @simplexml_load_string( iconv( "UTF-8", "UTF-8//IGNORE",  $curl_output ) );
				
				break;
				
			case '400':
			
				$output['error'] 	= 'YES';
				
				$output['message'] 	= 'Bad Request';
				
				$output['xml']		= @simplexml_load_string( iconv( "UTF-8", "UTF-8//IGNORE",  $curl_output ) );
				
				break;
		
			case '200':
		
				$output['xml']		= @simplexml_load_string( iconv( "UTF-8", "UTF-8//IGNORE",  $curl_output ) );
				
				break;
				
		
		}
		
		$this->debug .= 'XML FILE OBJECT: ' . print_r ($output, true ) . '<br>'; 

		return $output;
	
	}
	

	////////////////////////////////////////////////////////////////////////////////
	//
	// GET XML FROM FILE AND OPTION
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function get_xml_from_file_and_option ( $url, $option_name, $force_update = false ) {
	
		$xml_string				= '';
		
		$xml_string_cached			= get_option( $option_name );
		
		$xml						= false;
		
		
			
		// get forced update
		if ( $force_update === true ) {
		
			$file_object			= $this->get_xml_file ( $url );
			
			if ( $file_object['error'] != 'YES' ) $xml = $file_object['xml'];
		
		// or try to get a cached version
		} else {
		
			if ( $xml_string_cached != '' ) 
				
				$xml 			= @simplexml_load_string ( $xml_string_cached );
				
			
			// if cached version don't exist or is invalid, get a new version
			if ( $xml === false ) {
			
				$file_object			= $this->get_xml_file ( $url );
				
				if ( $file_object['error'] != 'YES' ) $xml = $file_object['xml'];			
			
			}
		}
		
		return $xml;
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
	// LOGIN
	//
	//////////////////////////////////////////////////////////////////////////////
	
	public function login ( $username, $password ) {	
	
		global $fs_schema;
		
		$settings = $fs_schema->data->settings();
			
		switch ( $settings[ 'fs_schema_integration' ] ) {
		
			case 'BRP':
			
				$url = $settings[ 'fs_schema_brp_server_url' ] . 'persons.xml?apikey=' . $settings[ 'fs_booking_bpi_api_key' ];
				
				$result		= $this->get_xml_file( $url, false, $username, $password );
				
				$result		= $this->check_brp_xml_errors ( $result );
				
				if ( $result['error'] == '' && !isset ( $result['xml']->person )) {
						
					$result['error'] 		= 'YES';
					
					$result['message'] 		= 'Inloggningen verkade lyckas, men vi fick inte tillbaks korrekt information från bokningssystemet.';							
				
				} else {
				
					$result['personid'] = $result['xml']->person->id;
					
					$result['name'] 	= $result['xml']->person->firstname . ' ' . $result['xml']->person->lastname;
				
				}
				break;
			
			case 'PROFIT':
			
				break;		
		}
		
		$result['debug'] = $this->debug . print_r($result, true);
		
		return $result;
	
	}
	
	
	
	//////////////////////////////////////////////////////////////////////////////
	//
	// GET SCHEMA
	//
	//////////////////////////////////////////////////////////////////////////////
	
	public function get_schema ( $args ) {
	
		$defaults = array(
			'typ'				=> 'vecka', 		// vecka, pass
			'anlaggning'			=> '',			// id, eller kommaseparerad id
			'datum'				=> '', 			// format: YYYY-MM-DD
			'username'			=> '',
			'password'			=> ''
		);
		
		$r 						= wp_parse_args( $args, $defaults );
		
		$sub_cache_name			= 'schema_' . $r['typ'] . '_' . $r['anlaggning'] . '_' . $r['datum'];


		// if username and password, don't cache this schema
		if ( $r['username'] != '' || $r['password'] != '' ) {
		
			 $schema = $this->update_schema( $r );
		
		} else {

			// get a cached or refreshed version of schema object
			$schema					= $this->cached ( 'fs_schema_objects', array ( $this, 'update_schema' ), $r, false, $sub_cache_name );
			
			
			// if this version contains an error, force a refresh on cache
			if ( $schema['error'] != '' ) 
			
				$schema				= $this->cached ( 'fs_schema_objects', array ( $this, 'update_schema' ), $r, true, $sub_cache_name );
				
		}
		
		return $schema;
			
	}
	
	

	//////////////////////////////////////////////////////////////////////////////
	//
	// UPDATE SCHEMA INTO CACHE, return a serialized object
	//
	//////////////////////////////////////////////////////////////////////////////
	
	
	public function update_schema ( $r ) {
	
		global $fs_schema;
		
		$settings = $fs_schema->data->settings();
		
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

			
		
		// 
		switch ( $settings[ 'fs_schema_integration' ] ) {
		
			case 'BRP':
				
				$r['url']	 			= $settings[ 'fs_schema_brp_server_url' ] . 'activities.xml?apikey=' . $settings[ 'fs_booking_bpi_api_key' ] . '&businessunitids=' . $r['businessunitids'] . '&startdate=' . $r['date_stamp'] . '&enddate=' . $r['date_stamp_end']; //&product=12,23
				
				if ( $r['username'] != '' && $r['password'] != '' ) {
				
					$r['url']			.= '&includebooking=true';
					
					$result 			= $this->get_xml_file ( $r['url'], false, $r['username'], $r['password'] );
				
				} else {
				
					$result			= $this->get_xml_file ( $r['url'] );
				
				}
				
				
				$result				= $this->check_brp_xml_errors ( $result );
				
				if ( $result['error'] 	== '' && !isset ( $result['xml']->activity )) {
					
					$r['error'] 		= 'YES';
					
					$r['message'] 		= 'Schemat för aktuell period är tomt.';							
					
				} else if ( $result['error'] != '' ) {
				
					$r = $result;
				
				} else {
					
					
					// loop thru xml and store data into array
					$schema = array();
					
					foreach ( $result['xml']->activity as $activity ){
	
	
						// build the product
						$products = array();
						
						foreach ( $activity->product as $product ) {
						
							array_push( $products, array ( 'id' =>  (string) $product->id, 'name' => (string) $product->name ));
							
						}
						
	
						// build the resources
						$resources 	= array();
						$staff		= '';
						$room		= '';
						
						foreach ( $activity->resources->resource as $resource ) {
						
							switch ( (string) $resource->type ) {
							
								case 'Personal': 		$staff 	.= (string) $resource->name; break;
									
								case 'Trningssalar':	$room 	.= (string) $resource->name; break;
							
							}
						
							array_push( $resources, array ( 'id' =>  (string) $resource->id, 'name' => (string) $resource->name, 'type' => (string) $resource->type  ));
							
						}				
						
						
						
						// put it together
						array_push( $r['schema'],
						
							array(
							
								'id'				=> (string) $activity->id,
								
								'products'		=> $products,
								
								'resources'		=> $resources,
								
								'staff'			=> $staff,
								
								'room'			=> $room,
								
								'businessuniidt'	=> (string) $activity->businessunit->id,
								
								'businessunit'		=> (string) $activity->businessunit->name,
								
								'startdate'		=> (string) $activity->start->timepoint->date,
								
								'starttime'		=> (string) $activity->start->timepoint->time,
								
								'startdatetime'	=> (string) $activity->start->timepoint->datetime,
								
								'enddate'			=> (string) $activity->end->timepoint->date,
								
								'endtime'			=> (string) $activity->end->timepoint->time,
								
								'enddatetime'		=> (string) $activity->end->timepoint->datetime,
								
								'freeslots'		=> (string) $activity->freeslots,
								
								'bookableslots'	=> (string) $activity->bookableslots,
								
								'bookingid'		=> (string) $activity->bookingid
							)
						);
					}
				}
			
				break;
				
			case 'PROFIT':
			
				return $this->profit->get_schema_xml();
			
				break;
		
			default:
			
				return false;
				
				break;
		
		}
		
		return $r;
	}
	



	////////////////////////////////////////////////////////////////////////////////
	//
	// BOOK ACTIVITY
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function book_activity ( $username, $password, $activity_id ) {

		global $fs_schema;
		
		$settings = $this->settings();
		
		$result = array( 'error' => '', 'message' => '' );
		
		switch ( $settings[ 'fs_schema_integration' ] ) {
		
			case 'BRP':
		
				$server_url			= $settings[ 'fs_schema_brp_server_url' ];
				
				$api_key				= $settings[ 'fs_booking_bpi_api_key' ];
				
				$url 				= $settings[ 'fs_schema_brp_server_url' ] . 'activitybookings.xml';
				
				$post_data			= array (
				
					'apikey'			=> $settings[ 'fs_booking_bpi_api_key' ],
					
					'type'			=> 'ordinary',
					
					'activityid'		=> $activity_id
				
				);
				
				$result				= $this->get_xml_file( $url, $post_data, $username, $password );
				
				$result				= $this->check_brp_xml_errors ( $result );
				
				$activity_booking		= $result['xml']->xpath('/activitybooking' );
				
				if ( $result['error'] 	== '' && !isset ( $activity_booking[0] )) {
					
					$result['error'] 	= 'YES';
					
					$result['message'] 	= 'Bokningen har skickats iväg, men det gick inte att få bekräftat från bokningssystemet att bokningen lyckats.';							
					
				} else {
				
					$result['message'] 		= 'Bokningen är genomförd och har fått id ' . $activity_booking[0]->id . ' i bokningssystemet.';
					
					$result['bookingid']	= (string)$activity_booking[0]->id;

				}
				break;
				
			case 'PROFIT':
			
				$result['error'] 		= 'YES';
				
				$result['message'] 		= 'PROFIt ÄR INtE INtEGRERAT ÄNNU';				
			
				break;
				
		}
		
		$result['debug'] = $this->debug;
		
		return $result;

	}	
	
	




	////////////////////////////////////////////////////////////////////////////////
	//
	// UNBOOK ACTIVITY
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function unbook_activity ( $username, $password, $bookingid ) {

		global $fs_schema;
		
		$settings = $this->settings();
		
		$result = array( 'error' => '', 'message' => '' );
		
		switch ( $settings[ 'fs_schema_integration' ] ) {
		
			case 'BRP':
		
				$server_url			= $settings[ 'fs_schema_brp_server_url' ];
				
				$api_key				= $settings[ 'fs_booking_bpi_api_key' ];
				
				$url 				= $settings[ 'fs_schema_brp_server_url' ] . 'activitybookings/' . $bookingid . '.xml?apikey=' . $api_key . '&type=ordinary';

				$result				= $this->get_xml_file( $url, false, $username, $password, true );
				
				$result				= $this->check_brp_xml_errors ( $result );
				
				if ( $this->last_http_code == '204' ) {
				
					$result['error'] 		= '';
				
					$result['message'] 		= 'Avbokningen är genomförd i bokningssystemet.';
				
				} else {
				
					if ( $result['error'] 	== '' ) {
					
						$result['error'] 	= 'YES';
						
						$result['message'] 	= 'Avbokningen har skickats iväg men det gick inte att avgöra om den lyckades eller inte. Ladda om schemat för att se om aktiviteten fortfarande är bokad på dig.';							
						
					} 
				}
				break;
				
			case 'PROFIT':
			
				$result['error'] 		= 'YES';
				
				$result['message'] 		= 'PROFIt ÄR INtE INtEGRERAT ÄNNU';				
			
				break;
				
		}
		
		$result['debug'] = $this->debug;
		
		return $result;

	}	
		
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	// CHECK BRP ERRORS
	//
	////////////////////////////////////////////////////////////////////////////////
			
	public function check_brp_xml_errors ( $obj ) {
			
		if ( isset ( $obj['xml']->error )) {
		
			foreach ( $obj['xml']->error as $err ) {
			
				switch ( $err->code ) {
				
					case '1007':
						$obj['message'] .= 'Du är redan inbokad på denna aktivitet. ';
						break;
						
					default:
						$obj['message'] .= $err->message . ' Kod: ' . $err->code . ' Debuginfo: ' . $err->debuginfo;
						break;
				
				}
			}
			
			$obj['message'] = 'Bokningssystemet skickade tillbaks följande felmeddelande: ' . $obj['message'];
			
		}
		
		return $obj;
	}
	
	
	
	//////////////////////////////////////////////////////////////////////////////
	//
	// GET BUSINESSUNITS
	// Used in admin
	//
	//////////////////////////////////////////////////////////////////////////////
		
	public function get_businessunits ( $integration = null, $server_url = null, $api_key = null ) {
	
		$settings = $this->settings();
		
		if ( !$integration ) $integration =  $settings[ 'fs_schema_integration' ];
		
		switch ( $integration ) {
		
			case 'BRP':
			
				
				$output 					= '';
				
				$force_update				= false;
				
		
				// get parameters from argument 
				if ( $server_url && $api_key )  $force_update = true;
					
				else {
				
					$server_url			= $settings[ 'fs_schema_brp_server_url' ];
					$api_key				= $settings[ 'fs_booking_bpi_api_key' ];		
				}
				
				// get the xml
				$xml = $this->get_xml_from_file_and_option ( $server_url . 'businessunits.xml?apikey=' . $api_key, '_brp_business_units_xml_string', $force_update );
				
		
				if ( $xml === false || !isset( $xml->businessunit )) {
				
					 $output = 'Det gick inte att hämta information. Antagligen är API-url eller API-nyckel felaktig. <a href="javascript: fs_schema_admin.brp_update_businessunitids();">Försök igen.</a>.';
					
				} else {
				
					$stored_units				= explode ( ',', $settings[ 'fs_booking_bpi_businessunitids' ]);
					
					$count					= 0;
				
					foreach ( $xml->businessunit as $businessunit ) {
					
						$output .= '<label for="fs_booking_bpi_businessunitids' . $businessunit->id . '">
								  <input type="checkbox" name="fs_booking_bpi_businessunitids[' . ( $count++) . ']" id="fs_booking_bpi_businessunitids' . $businessunit->id . '" 
								  value="' . $businessunit->id . '"' . ( in_array( $businessunit->id, $stored_units ) ? 'checked="checked"' : '' ) . '> ' .  $businessunit->name . '<div class="buid">id: ' . $businessunit->id . '</div></label><br/>';
			
					}
					
					if ( $output == '' ) $output = 'Det gick inte att hitta några anläggningar med angivet API';
					
				}
				
				return $output;
			
				break;
				
			case 'PROFIT':
			
				return $this->profit->get_businessunits ( $server_url, $api_key );
			
				break;
		
			default:
			
				return false;
				
				break;
		
		}	
	
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
			
			$this->settings_array[ 'fs_booking_profit_contract_name' ] 		= get_option( 'fs_booking_profit_contract_name' );
			
			$this->settings_array[ 'fs_booking_profit_license_key' ] 		= get_option( 'fs_booking_profit_license_key' );
			
			$this->settings_array[ 'fs_schema_extra_column' ]				= get_option( 'fs_schema_extra_column' );
			
			$this->settings_array[ 'fs_schema_update_inteval' ]			= get_option( 'fs_schema_update_inteval' ) 	== '' ? '60' 	:  get_option( 'fs_schema_update_inteval' );
			
			if ( substr( $this->settings_array[ 'fs_schema_brp_server_url' ] , -1 ) != '/' ) $this->settings_array[ 'fs_schema_brp_server_url' ] .= '/';
		
		}
		
		return $this->settings_array;
	
	}



	
} //End Class

?>