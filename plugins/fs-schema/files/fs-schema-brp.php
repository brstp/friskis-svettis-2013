<?php
/*/////////////////////////////////////////////////////////////////

	FS SCHEMA, PHP Class - BRP

	Copyright (C) 2013-2014 Klas Ehnemark (http://klasehnemark.com)
	This program is not free software.

//////////////////////////////////////////////////////////////////*/


class fs_schema_brp {
	
	// public variables
	public $debug = '';
	
	

	////////////////////////////////////////////////////////////////////////////////
	//
	// INITIALIZE OBJECT
	//
	////////////////////////////////////////////////////////////////////////////////

	public function __construct () {}
	
	

	//////////////////////////////////////////////////////////////////////////////
	//
	// GET SCHEMA
	//
	//////////////////////////////////////////////////////////////////////////////
	
	public function get_schema ( $args ) {
	
		$defaults = array(
			'type'				=> 'week', 		// week, day
			'facility'			=> '',			// id, eller kommaseparerad id
			'date'				=> '', 			// format: YYYY-MM-DD
			'username'			=> '',
			'password'			=> ''
		);
		
		$r 						= wp_parse_args( $args, $defaults );
		
		global $fs_schema;
		
		$sub_cache_name			= 'schema_' . $r['type'] . '_' . $r['facility'] . '_' . $r['date'];


		// if username and password, don't cache this schema
		if ( $r['username'] != '' || $r['password'] != '' ) {
		
			 $schema = $this->update_schema( $r );
		
		} else {

			// get a cached or refreshed version of schema object
			$schema					= $fs_schema->data->cached ( 'fs_schema_objects', array ( $this, 'update_schema' ), $r, false, $sub_cache_name );
			
			
			// if this version contains an error, force a refresh on cache
			if ( $schema['error'] != '' ) 
			
				$schema				= $fs_schema->data->cached ( 'fs_schema_objects', array ( $this, 'update_schema' ), $r, true, $sub_cache_name );
				
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
		if ( $r[ 'facility' ] != '' )
		
			$r['businessunitids'] 	= $r[ 'facility' ];
		
		else 
		
			$r['businessunitids'] 	= $settings[ 'fs_booking_bpi_businessunitids' ];

		// 
		$r['url']	 			= $settings[ 'fs_schema_brp_server_url' ] . 'activities.xml?apikey=' . $settings[ 'fs_booking_bpi_api_key' ] . '&businessunitids=' . $r['businessunitids'] . '&startdate=' . $r['date_stamp'] . '&enddate=' . $r['date_stamp_end']; //&product=12,23
		
		if ( $r['username'] != '' && $r['password'] != '' ) {
			
			$r['url']			.= '&includebooking=true';
			
			$result 			= $this->get_xml_file ( $r['url'], false, $r['username'], $r['password'] );
		
		} else {
		
			$result			= $this->get_xml_file ( $r['url'] );
		
		}
		
		
		$result				= $this->check_brp_xml_errors ( $result );
		
		if ( $result['error'] 	== '' && !isset ( $result['xml'] )) {  // ->activity
			
			$r['error'] 		= 'YES';
			
			$r['message'] 		= 'Schemat för aktuell period är tomt.';							
			
		} else if ( $result['error'] != '' ) {
		
			$r = $result;
		
		} else {
			
			
			// loop thru xml and store data into array
			
			$r['schema'] = array_merge ( $r['schema'], $this->add_schema_xml ( $result['xml']->activity, 'schema' ));
	
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
		
		$settings 				= $fs_schema->data->settings();
		
		$result 					= array( 'error' => '', 'message' => '' );
		
		$server_url				= $settings[ 'fs_schema_brp_server_url' ];
		
		$api_key					= $settings[ 'fs_booking_bpi_api_key' ];
		
		$url 					= $settings[ 'fs_schema_brp_server_url' ] . 'activitybookings.xml';
		
		$post_data				= array (
		
			'apikey'				=> $settings[ 'fs_booking_bpi_api_key' ],
			
			'type'				=> 'ordinary',
			
			'activityid'			=> $activity_id
		
		);
		
		$result					= $this->get_xml_file( $url, $post_data, $username, $password );
		
		$result					= $this->check_brp_xml_errors ( $result );
		
		$activity_booking			= $result['xml']->xpath('/activitybooking' );
		
		if ( $result['error'] 	== '' && !isset ( $activity_booking[0] )) {
			
			$result['error'] 		= 'YES';
			
			$result['message'] 		= 'Bokningen har skickats iväg, men det gick inte att få bekräftat från bokningssystemet att bokningen lyckats.';
			
		} else if ( $result['error'] 	== '' ) {
		
			$result['message'] 		= 'Bokningen är genomförd och har fått id ' . $activity_booking[0]->id . ' i bokningssystemet.';
			
			$result['bookingid']	= (string)$activity_booking[0]->id;

		}
				
		$result['debug'] 			= $this->debug;
		
		return $result;

	}	

	
	

	////////////////////////////////////////////////////////////////////////////////
	//
	// UNBOOK ACTIVITY
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function unbook_activity ( $username, $password, $bookingid, $type ) {

		global $fs_schema;
		
		$settings 			= $fs_schema->data->settings();
		
		$result 				= array( 'error' => '', 'message' => '' );

		$server_url			= $settings[ 'fs_schema_brp_server_url' ];
		
		$api_key				= $settings[ 'fs_booking_bpi_api_key' ];
		
		$unbook_type			= $type != 'waitinglist' ? 'ordinary' : $type;
		
		$url 				= $settings[ 'fs_schema_brp_server_url' ] . 'activitybookings/' . $bookingid . '.xml?apikey=' . $api_key . '&type=' . $unbook_type;

		$result				= $this->get_xml_file( $url, false, $username, $password, true );
		
		$result				= $this->check_brp_xml_errors ( $result );
		
		if ( $this->last_http_code == '204' ) {
		
			$result['error'] 		= '';
		
			$result['message'] 		= 'Avbokningen är genomförd i bokningssystemet.';
		
		} else {
		
			if ( $result['error'] 	== '' ) {
			
				$result['error'] 	= 'YES';
				
				$result['message'] 	= 'Avbokningen har skickats iväg men det gick inte att avgöra om den lyckades eller inte. Vi laddar om schemat för att se om aktiviteten fortfarande är bokad på dig.';							
				
			} 
		}
		
		$result['debug'] = $this->debug;
		
		return $result;

	}	
		

	


	////////////////////////////////////////////////////////////////////////////////
	//
	// BOOK WAITINGLIST
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function book_waitinglist ( $username, $password, $activity_id ) {

		global $fs_schema;
		
		$settings 						= $fs_schema->data->settings();
			
		$result 							= array( 'error' => '', 'message' => '' );
		
		$server_url						= $settings[ 'fs_schema_brp_server_url' ];
		
		$api_key							= $settings[ 'fs_booking_bpi_api_key' ];
		
		$url 							= $settings[ 'fs_schema_brp_server_url' ] . 'activitybookings.xml';
		
		$post_data						= array (
		
			'apikey'						=> $settings[ 'fs_booking_bpi_api_key' ],
			
			'type'						=> 'waitinglist',
			
			'activityid'					=> $activity_id
		
		);
		
		$result							= $this->get_xml_file( $url, $post_data, $username, $password );
		
		$result							= $this->check_brp_xml_errors ( $result );
		
		$activity_booking					= $result['xml']->xpath('/activitybooking' );
		
		if ( $result['error'] 	== '' && !isset ( $activity_booking[0] )) {
			
			$result['error'] 				= 'YES';
			
			$result['message'] 				= 'Bokningen av reservplats har skickats iväg, men det gick inte att få bekräftat från bokningssystemet att bokningen lyckats.';
			
		} else if ( $result['error'] 	== '' ) {
		
			$result['message'] 				= 'Bokningen av reservplats är genomförd och har fått id ' . $activity_booking[0]->id . ' i bokningssystemet.';
			
			$result['bookingid']			= (string)$activity_booking[0]->id;
			
			$result['waitinglistposition']	= (string)$activity_booking[0]->waitinglistposition;
			
			$result['waitinglistsize']		= (string)$activity_booking[0]->waitinglistsize;

		}
				
		$result['debug'] 			= $this->debug;
		
		return $result;

	}	
		
	
	////////////////////////////////////////////////////////////////////////////////
	//
	// UNBOOK WATINGLIST
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function unbook_waitinglist ( $username, $password, $bookingid ) {

		return $this->unbook_activity ( $username, $password, $bookingid, 'waitinglist' );

	}
	
		
			

	////////////////////////////////////////////////////////////////////////////////
	//
	// GET XML FILE
	//
	////////////////////////////////////////////////////////////////////////////////
	
	public function get_xml_file ( $url, $post_data=null, $username = '', $password = '', $send_delete = false, $headers = false ) {
		
		global $fs_schema;
		
		$output 					= array ( 'error' => '', 'message' => '', 'xml' => null );
		
		// get content via curl
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		
		if ( $send_delete === true ) curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
		
		if ( $post_data ) {
		
			curl_setopt( $ch, CURLOPT_POST, 1);
			
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data ); 
			
			$this->debug .= 'POST DATA: ' . print_r ($post_data, true ) . '<br>'; 
		
		}
		
		if (  $headers !== false ) {
		
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			stereotype::debug ( $headers );
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
				
				$output['message'] 	= 'Ett okänt fel uppstod. Var vänlig försök igen senare. ';
				
				break;				

			case '-1':
			
				$output['error'] 	= 'YES';
				
				$output['message'] 	= 'Det gick inte att få kontakt med bokningssystemet. Var vänlig försök igen senare. ';
				
				break;
				
			case '401':
			
				$output['error'] 	= 'YES';
				
				$output['message'] 	= 'Inloggning misslyckades. Användarnamn och/eller lösenord är felaktigt. ';
				
				break;
				
			case '403':
			
				$output['error'] 	= 'YES';
				
				$output['message'] 	= 'Inloggning lyckades, men du har inte behörighet att boka denna aktivitet. ';
				
				$output['xml']		= @simplexml_load_string( iconv( "UTF-8", "UTF-8//IGNORE",  $curl_output ) ); // windows-1252
				
				break;
				
			case '400':
			
				$output['error'] 	= 'YES';
				
				$output['message'] 	= 'Bad Request. ';
				
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
	// LOGIN
	//
	//////////////////////////////////////////////////////////////////////////////
	
	public function login ( $username, $password ) {	
	
		global $fs_schema;
		
		$settings = $fs_schema->data->settings();
			
		$url = $settings[ 'fs_schema_brp_server_url' ] . 'persons.xml?apikey=' . $settings[ 'fs_booking_bpi_api_key' ];
		
		$result		= $this->get_xml_file( $url, false, $username, $password );
		
		$result		= $this->check_brp_xml_errors ( $result );
		
		if ( $result['error'] == '' && !isset ( $result['xml']->person )) {
				
			$result['error'] 		= 'YES';
			
			$result['message'] 		= 'Inloggningen verkade lyckas, men vi fick inte tillbaks korrekt information från bokningssystemet.';							
		
		} else if ( $result['error'] == '' ) {
		
			$result['personid'] = $result['xml']->person->id;
			
			$result['name'] 	= $result['xml']->person->firstname . ' ' . $result['xml']->person->lastname;
		
		} 
		
		$result['debug'] = $this->debug . print_r($result, true);
		
		return $result;
	
	}
	

	
	
	//////////////////////////////////////////////////////////////////////////////
	//
	// GET BOOKINGS
	//
	//////////////////////////////////////////////////////////////////////////////
	
	public function get_bookings ( $args ) {
	
		$defaults = array(
			'date'			=> '', 			// format: YYYY-MM-DD
			'username'		=> '',
			'password'		=> ''
		);
		
		$r 					= wp_parse_args( $args, $defaults );
		
		global $fs_schema;

		global $fs_schema;
		
		$settings = $fs_schema->data->settings();
		
		$r['error'] 			= '';
		
		$r['message'] 			= '';
		
		$r['schema']			= array();
		
		
		// create url for ordinary bookings
		$r['url']	 			= $settings[ 'fs_schema_brp_server_url' ] . 'activitybookings.xml?type=ordinary&apikey=' . $settings[ 'fs_booking_bpi_api_key' ]  . '&startdate=' . $r['date_stamp'] . '&enddate=' . date('Y-m-d', mktime(0,0,0,date("m")+1,date("d"),date("Y")));; // add one month from now
		
		$result 				= $this->get_xml_file ( $r['url'], false, $r['username'], $r['password'], 'bookings' );
		
		$result				= $this->check_brp_xml_errors ( $result );
		
		if ( $result['error'] != '' ) {
		
			$r = $result;
		
		} else { 
		
			// loop thru xml and store data into array
			$r['schema'] = array_merge ( $r['schema'], $this->add_schema_xml ( $result['xml']->activitybooking, 'bookings' ));	
			
			
			// create url for waitinglist bookings
			$r['url']	 			= $settings[ 'fs_schema_brp_server_url' ] . 'activitybookings.xml?type=waitinglist&apikey=' . $settings[ 'fs_booking_bpi_api_key' ]  . '&startdate=' . $r['date_stamp'] . '&enddate=' . date('Y-m-d', mktime(0,0,0,date("m")+1,date("d"),date("Y")));; // add one month from now
			
			$result 				= $this->get_xml_file ( $r['url'], false, $r['username'], $r['password'] );
			
			$result				= $this->check_brp_xml_errors ( $result );
			
			if ( $result['error'] != '' ) {
			
				$r = $result;
			
			} else {
				
				// loop thru xml and store data into array
				$r['schema'] = array_merge ( $r['schema'], $this->add_schema_xml ( $result['xml']->activitybooking, 'bookings' ));	
				
			}				
		}
		
		if ( $result['error'] 	== '' && count($r['schema']) == 0  ) { 
			
			$r['error'] 		= 'YES';
			
			$r['message'] 		= 'Du verkar inte ha några bokningar.';	
			
		}
		
		return $r;
	}



	//////////////////////////////////////////////////////////////////////////////
	//
	// PRIVATE FUNCTINO ADD SCHEMA XML
	//
	//////////////////////////////////////////////////////////////////////////////
	
	private function add_schema_xml ( $activites_nodes, $xml_type ) {
	
		$schema = array();
		
		foreach ( $activites_nodes as $activity ){


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
						
					case 'Trningssalar':
					case 'Lokal':
					case 'Träningssalar': 	$room 	.= (string) $resource->name; break;
				
				}
			
				array_push( $resources, array ( 'id' =>  (string) $resource->id, 'name' => (string) $resource->name, 'type' => (string) $resource->type  ));
				
			}	
			
			
			// for schemas
			
			if ( $xml_type == 'schema' ) {
			
			
				// get time for booking possibilites
			
				$bookableearliest 				= (string) $activity->bookableearliest->timepoint->timestamp;
				
				$bookablelatest 				= (string) $activity->bookablelatest->timepoint->timestamp;
				
				
				// build the status
			
				$entry_status 					= 'BOOK';  										// default: booking is possible
	
				
				if ( (string) $activity->freeslots == '0' ) {										// entry has no free slots (no drop in either)
				
					$entry_status 				= 'reserve';											// only reserve booking is possible
					
																								// there is no way saying ONLY DROPIN
				
				} else if ( (string) $activity->cancelled == 'true' ) {								// entry is cancelled
				
					$entry_status 				= 'cancelled';
				
				} else if ( $bookableearliest != '' && strtotime('now') < Date( $bookableearliest ) ) {		// booking has not opened yet
				
					$entry_status 				= 'not_opened_yet';
				
				} else if ( $bookablelatest != '' && strtotime('now') >  Date(  $bookablelatest ) ) {		// booking has been closed
				
					$entry_status 				= 'closed';
				}
				
				
				// build the booking type and id
				
				$booking_type					= (string) $activity->bookingtype == '' ? 'ordinary' : (string) $activity->bookingtype;
				
				$booking_id					= (string) $activity->bookingid;
				
			
			// this is for my bookings list
			
			} else {
			
				$entry_status = '';
				
				$booking_type					= (string) $activity->type == '' ? 'ordinary' : (string) $activity->type;
				
				$booking_id					= (string) $activity->id;
				
			}

			// put it together
			array_push( $schema,
			
				array(
				
					'id'					=> $xml_type == 'schema' ? (string) $activity->id : (string) $activity->activityid,
					
					'products'			=> $products,
					
					'resources'			=> $resources,
					
					'staff'				=> $staff,
					
					'room'				=> $room,
					
					'businessuniidt'		=> (string) $activity->businessunit->id,
					
					'businessunit'			=> (string) $activity->businessunit->name,
					
					'startdate'			=> (string) $activity->start->timepoint->date,
					
					'starttime'			=> (string) $activity->start->timepoint->time,
					
					'startdatetime'		=> (string) $activity->start->timepoint->datetime,
					
					'enddate'				=> (string) $activity->end->timepoint->date,
					
					'endtime'				=> (string) $activity->end->timepoint->time,
					
					'enddatetime'			=> (string) $activity->end->timepoint->datetime,
					
					'totalslots'			=> isset ( $activity->totalslots ) ? (string) $activity->totalslots : '-1',
					
					'freeslots'			=> isset ( $activity->freeslots ) ? (string) $activity->freeslots : '-1',
					
					'bookableslots'		=> (string) $activity->bookableslots,
					
					'dropinslots'			=> -1, //(int) $activity->bookableslots - (int) $activity->freeslots,
					
					'waitinglistsize'		=> (string) $activity->waitinglistsize,
					
					'waitinglistposition'	=> (string) $activity->waitinglistposition,
					
					'bookingid'			=> $booking_id,
					
					'bookingtype'			=> $booking_type,
					
					'status'				=> strtolower( $entry_status )
				)
			);
		}	
		return $schema;
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	// CHECK BRP ERRORS
	//
	////////////////////////////////////////////////////////////////////////////////
			
	private function check_brp_xml_errors ( $obj ) {
			
		if ( isset ( $obj['xml']->error )) {
		
			foreach ( $obj['xml']->error as $err ) {
			
				switch ( $err->code ) {
				
					case '1007':
						$obj['message'] .= 'Bokningen krockar med tidigare bokningar. ';
						break;
						
					case '1102':
						$obj['message'] .= 'Bokningssystemet förväntar sig ett anrop med angivna anläggninar, vilket administratören för webbplatsen inte har fyllt i. ';
						break;	
						
					default:
						$obj['message'] .= $err->message . ' Kod: ' . $err->code . ' Debuginfo: ' . $err->debuginfo;
						break;
				
				}
			}
			
			$obj['error']	= 'YES';
			
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
		
	public function get_businessunits ( $server_url = null, $api_key = null ) {
	
		global $fs_schema;
		
		$settings = $fs_schema->data->settings();

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
		
			if ( is_array ( $settings[ 'fs_booking_bpi_businessunitids' ] )) $stored_units = $settings[ 'fs_booking_bpi_businessunitids' ];
		
			else $stored_units			= explode ( ',', $settings[ 'fs_booking_bpi_businessunitids' ]);
			
			$count					= 0;
		
			foreach ( $xml->businessunit as $businessunit ) {
			
				$output .= '<label for="fs_booking_bpi_businessunitids' . $businessunit->id . '">
						  <input type="checkbox" name="fs_booking_bpi_businessunitids[' . ( $count++) . ']" id="fs_booking_bpi_businessunitids' . $businessunit->id . '" 
						  value="' . $businessunit->id . '"' . ( in_array( $businessunit->id, $stored_units ) ? ' checked="checked"' : '' ) . '> ' .  $businessunit->name . '<div class="buid">id: ' . $businessunit->id . '</div></label><br/>';
	
			}
			
			if ( $output == '' ) $output = 'Det gick inte att hitta några anläggningar med angivet API';
			
		}
		
		return $output;
	
		break;
	}
	
	

	
} //End Class

?>