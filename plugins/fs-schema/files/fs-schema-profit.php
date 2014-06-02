<?php
/*/////////////////////////////////////////////////////////////////

	FS SCHEMA, PHP Class - PROFIT

	Copyright (C) 2013-2014 Klas Ehnemark (http://klasehnemark.com)
	This program is not free software.
	
	
	In all xml for sprint is:
	
	%1$s	= session key
	
	XOR-encryption is not implemented
	
//////////////////////////////////////////////////////////////////*/


class fs_schema_profit {

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
	
		global $fs_schema;
		
		$defaults = array(
			'type'				=> 'week', 		// week, pass
			'facility'			=> '',			// id, eller kommaseparerad id
			'date'				=> '', 			// format: YYYY-MM-DD
			'username'			=> '',
			'password'			=> ''
		);
		
		$r 						= wp_parse_args( $args, $defaults );
		
		$sub_cache_name			= 'schema_' . $r['type'] . '_' . $r['facility'] . '_' . $r['date'];

		//return $this->update_schema( $r );

		// if username and password, don't cache this schema
		if ( $r['username'] != '' || $r['password'] != '' ) {
		
			 $schema = $this->update_schema( $r );
		
		} else {
		
			// get a cached or refreshed version of schema object
			$schema					= $fs_schema->data->cached ( 'fs_schema_objects', array ( $this, 'update_schema' ), $r, false, $sub_cache_name );
			
		
			// if this version contains an error, force a refresh on cache
			if ( $schema['error'] != ''|| $schema === false ) 
			
				$schema				= $fs_schema->data->cached ( 'fs_schema_objects', array ( $this, 'update_schema' ), $r, true, $sub_cache_name );
				
		}
		
		return $schema;
			
	}


	
	//////////////////////////////////////////////////////////////////////////////
	//
	// UPDATE SCHEMA INTO CACHE, return a serialized object
	//
	// %1$s in xml is session key
	//
	//////////////////////////////////////////////////////////////////////////////
	
	
	public function update_schema ( $r ) {
	
		$user_bookings = $user_reserve_bookings = array();
	
		// if no username, get schema as guest
		if ( $r['username'] == '' || $r['password'] == '' || $r['session_key'] == '' ) {
		
			$this->debug 	 .= '<br>Påbörjar anrop till Profit som Gäst';
			
			$xmls  		  = '<ProfitAndroid command="FetchBookableObjectsFiltered">
								<GUID>%1$s</GUID>
								<START>' . $r['date_stamp'] . '</START>
								<END>' . $r['date_stamp_end'] . '</END>
								<GLOBALUNITID>-1</GLOBALUNITID>
								<ACTIVITYID>-1</ACTIVITYID>
								<ACTIVITYCATEGORYID>-1</ACTIVITYCATEGORYID>
								<LEADERFREETEXT></LEADERFREETEXT>
							</ProfitAndroid>';
			
			$result 		  = $this->make_soap_call( $xmls );
		
		} else {

			$this->debug 	 .= '<br>Påbörjar anrop till Profit som autentierad anvädnare, session_key ' . $r['session_key'];
					
			$xmls  		  = '<ProfitAndroid command="FetchBookableObjects">
								<GUID>%1$s</GUID>
								<Date>' . $r['date_stamp'] . '</Date>
								<User>' . $r['username'] . '</User>
								<Password>' . $r['password'] . '</Password>
							</ProfitAndroid>';
		
			$result 		  = $this->make_soap_call( $xmls, $r['session_key'], $r['username'], $r['password'] );
			
			$this->debug 	 .= '<br>Resultat från anropet: ' . print_r( $result, true) ;
			
			
			
			// get user bookings so we can mark what events this user is booked at
			
			$this->debug 	 .= '<br>Fortsätter anrop till Profit som autentierad anvädnare, session_key ' . $r['session_key'] . ' för att hämta bokningarna';

			$xmls  		  = '<ProfitAndroid command="FetchBookings">
								<GUID>%1$s</GUID>
								<Date>' . $r['date_stamp'] . '</Date>
								<User>' . $r['username'] . '</User>
								<Password>' . $r['password'] . '</Password>
							</ProfitAndroid>';
			
			$book_result 	  = $this->make_soap_call( $xmls, $r['session_key'], $r['username'], $r['password'] );
			
			$this->debug 	 .= '<br>Resultat från anropet: ' . print_r( $book_result, true) ;
			
			if ( $book_result['error'] != '' ) {
			
				return array_merge($r, $book_result);
			
			} else {
			
				if ( isset ( $book_result['xml']->AndroidBookingObjects->Booking )) {
			
					foreach ( $book_result['xml']->AndroidBookingObjects->Booking as $booking ){
					
						$user_bookings[ (string) $booking->bookableobjectid ] = (string) $booking->BOOKINGID;
					}
				}
			}
			
			
			// get user waitinglist bookings so we can mark what events this user is on waiting list
			
			$this->debug 	 .= '<br>Fortsätter anrop till Profit som autentierad anvädnare, session_key ' . $r['session_key'] . ' för att hämta reservlistbokningarna';

			$xmls  		  = '<ProfitAndroid command="FetchReserveBookings">
								<GUID>%1$s</GUID>
								<Date>' . $r['date_stamp'] . '</Date>
								<User>' . $r['username'] . '</User>
								<Password>' . $r['password'] . '</Password>
							</ProfitAndroid>';
			
			$book_result 	  = $this->make_soap_call( $xmls, $r['session_key'], $r['username'], $r['password'] );
			
			$this->debug 	 .= '<br>Resultat från anropet: ' . print_r( $book_result, true) ;
			
			if ( $book_result['error'] != '' ) {
			
				return array_merge($r, $book_result);
			
			} else {
			
				if ( isset ( $book_result['xml']->AndroidBookingObjects->Booking )) {
			
					foreach ( $book_result['xml']->AndroidBookingObjects->Booking as $booking ){
					
						$user_reserve_bookings[ (string) $booking->bookableobjectid ] = array ( 
						
							'bookingid' 	=> (string) $booking->RESERVEBOOKINGID,
							
							'position' 	=> (string) $booking->POSITION
						);
					}
				}
			}
			
			$this->debug 	 .= '<br>user_bookings: ' . print_r( $user_bookings, true) ;
			
			$this->debug 	 .= '<br>user_reserve_bookings: ' . print_r( $user_reserve_bookings, true) ;
			
		}


		if ( $result['error'] == '' && !isset ( $result['xml']->AndroidBookableObjects )) {  // ->bo
			
			$r['error'] 		= 'YES';
			
			$r['message'] 		= 'Schemat för aktuell period är tomt.';							
			
		} else if ( $result['error'] != '' ) {
		
			$r = array_merge($r, $result);
		
		} else {

			$r['schema'] 		= $this->add_schema_xml ( $result['xml']->AndroidBookableObjects->bo, 'schema', $user_bookings, $user_reserve_bookings );
		}
		//var_dump($r);
		return $r;
	}
	
	

	////////////////////////////////////////////////////////////////////////////////
	//
	// BOOK ACTIVITY
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function book_activity ( $username, $password, $activity_id, $session_key ) {

		global $fs_schema;
		
		$settings = $fs_schema->data->settings();

		$xmls = '<ProfitAndroid command="Book">
			<GUID>%1$s</GUID>
			<BOID>' . $activity_id . '</BOID>
			<User>' . $username . '</User>
			<Password>' . $password . '</Password>
		</ProfitAndroid>';
		
		$this->debug 				.= '<br>Påbörjar bokning av aktivitet hos Profit som inloggad användare ' . $username;
		
		$result 					= $this->make_soap_call ( $xmls, $session_key, $username, $password );
		
		if ( $result['error'] 		== '' && isset ( $result['xml']->status )) {
		
			$this->debug .= '<br>We have a response with status ' . $result['xml']->status;
		
			if ( $result['xml']->status != 'OK' ) {
		
				$result['error'] 		= 'YES';
			
				$result['message'] 		= 'Det gick inte att boka. ' . $result['xml']->status . '.';
			
			} else {
			
				// PROFIT DONT RETURN ANY BOOKING ID. What kind of booking system is this???
				$result['bookingid']	= 'x';
			}
		
		} else {
		
			$this->debug .= '<br>Profit didnt give us any status in the response.';
		
			$result['error'] 		= 'YES';
				
			$result['message'] 		= 'Det gick inte att boka. Profit Bokningssystem svarade inte.';		
		}
		
		$result['debug'] 			= $this->debug . '<br>' . print_r($result, true);
		
		return $result;

	}	
		
		
	////////////////////////////////////////////////////////////////////////////////
	//
	// UNBOOK ACTIVITY
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function unbook_activity ( $username, $password, $bookingid, $session_key ) {

		global $fs_schema;
		
		$settings = $fs_schema->data->settings();

		// try to make a "debooking"
		$xmls = '<ProfitAndroid command="DeBook">
			<GUID>%1$s</GUID>
			<BOOKINGID>' . $bookingid . '</BOOKINGID>
			<User>' . $username . '</User>
			<Password>' . $password . '</Password>
		</ProfitAndroid>';
		
		$this->debug 				.= '<br>Påbörjar avbokning av bokning ' . $bookingid . ' hos Profit som inloggad användare ' . $username;
		
		$result 					= $this->make_soap_call ( $xmls, $session_key, $username, $password );
		
		if ( $result['error'] 		== '' && isset ( $result['xml']->status )) {
		
			$this->debug .= '<br>We have a response with status ' . $result['xml']->status;
		
			if ( strpos ( (string) $result['xml']->status, 'registrerad' ) === false ) {
			
				$this->debug .= '<br>Cannot find registrerad in the response';
		
				$result['error'] 		= 'YES';
			
				$result['message'] 		= $result['xml']->status . '.';
			}
		
		} else {
		
			$this->debug .= '<br>Profit didnt give us any status in the response.';
		
			$result['error'] 		= 'YES';
				
			$result['message'] 		= 'Det gick inte att avboka. Profit Bokningssystem svarade inte.';		
		}
		
		$result['debug'] 			= $this->debug . '<br>' . print_r($result, true);
		
		return $result;

	}		


	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	// BOOK WAITINGLIST
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function book_waitinglist ( $username, $password, $activity_id, $session_key ) {

		global $fs_schema;
		
		$settings = $fs_schema->data->settings();

		$xmls = '<ProfitAndroid command="BookReserv">
			<GUID>%1$s</GUID>
			<BOID>' . $activity_id . '</BOID>
			<User>' . $username . '</User>
			<Password>' . $password . '</Password>
		</ProfitAndroid>';
		
		$this->debug 				.= '<br>Påbörjar bokning av reservplats på aktivitet hos Profit som inloggad användare ' . $username;
		
		$result 					= $this->make_soap_call ( $xmls, $session_key, $username, $password );
		
		if ( $result['error'] 		== '' && isset ( $result['xml']->status )) {
		
			$this->debug .= '<br>We have a response with status ' . $result['xml']->status;
		
			if ( $result['xml']->status != 'OK' ) {
		
				$result['error'] 		= 'YES';
			
				$result['message'] 		= 'Det gick inte att boka. ' . $result['xml']->status . '.';
			
			} else {
			
				// PROFIT DONT RETURN ANY BOOKING ID. What kind of booking system is this???
				$result['bookingid']	= '';
			}
		
		} else {
		
			$this->debug .= '<br>Profit didnt give us any status in the response.';
		
			$result['error'] 		= 'YES';
				
			$result['message'] 		= 'Det gick inte att boka. Profit Bokningssystem svarade inte.';		
		}
		
		$result['debug'] 			= $this->debug . '<br>' . print_r($result, true);
		
		return $result;	
	}



	////////////////////////////////////////////////////////////////////////////////
	//
	// UNBOOK WAITINGLIST
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function unbook_waitinglist ( $username, $password, $bookingid, $session_key ) {

		global $fs_schema;
		
		$settings 		   = $fs_schema->data->settings();
	

		$xmls			   = '<ProfitAndroid command="DeBookReserve">
								<GUID>%1$s</GUID>
								<RESERVEBOOKINGID>' . $bookingid. '</RESERVEBOOKINGID>
								<User>' . $username . '</User>
								<Password>' . $password . '</Password>
							</ProfitAndroid>';
		
		$this->debug 		  .= '<br>Påbörjar förfrågan om att avboka reservbokningen ' . $bookingid . ' hos Profit som inloggad användare ' . $username;
		
		$result 		   	  = $this->make_soap_call( $xmls, $session_key, $username, $password );
		
		$this->debug 		  .= '<br>Resultat från bokningen: ' . print_r( $result, true) ;
		
		if ( $result['xml']->status != 'OK' ) {
	
			$result['error']   = 'YES';
		
			$result['message'] = 'Det gick inte att avboka. ' . $result['xml']->status . '.';
		
		} 
		
		$result['debug'] 	   = $this->debug . '<br>' . print_r($result, true);
	
		return $result;
	}



	//////////////////////////////////////////////////////////////////////////////
	//
	// LOGIN
	// 
	//////////////////////////////////////////////////////////////////////////////
	
	public function login ( $username, $password ) {	
	
		global $fs_schema;
		
		$settings = $fs_schema->data->settings();

		$xmls = '<ProfitAndroid command="UsualAuthenticate">
			<globalunit>' . $settings['fs_booking_profit_organization_unit'] . '</globalunit>
			<type>ANDROID</type>
			<User>' .  $username . '</User>
			<Password>' . $password . '</Password>
			<PartyName>KlasEhnemark</PartyName>
			<Licenskey>' . $settings['fs_booking_profit_3part_licence_key'] . '</Licenskey>
		</ProfitAndroid>';
		
		$this->debug 			.= '<br>Påbörjar anrop till Profit som inloggad användare ' . $username;
		
		$result 				= $this->make_soap_call( $xmls );
		
		if ( $result['error'] 	== '' ) {
		
			if ( isset ( $result['xml']['status'] ) && $result['xml']['status'] == 'EpicFail' ) {
			
				$result['error'] 		= 'YES';
				
				$result['message'] 		= 'Det gick inte att logga in. Är användarnamnet och lösenordet korrekt? Försök igen.';					
			
			} 
		}
		
		if ( $result['error'] 			== '' ) {
		
			$result['personid'] 		= $result['xml']->person->id;
			
			$result['session_key'] 		= (string) $result['xml']->GUID;
			
			$result['name'] 			= $result['xml']->user->firstname . ' ' . $result['xml']->user->lastname;
			
			$result['forceday']			= true;
		
		}
		
		$result['debug'] 				= $this->debug . print_r($result, true);
		
		return $result;
	}
	



	//////////////////////////////////////////////////////////////////////////////
	//
	// GET BOOKINGS
	//
	//////////////////////////////////////////////////////////////////////////////
	
	public function get_bookings ( $r ) {

		$user_bookings = array();
	
		// get user bookings so we can mark what events this user is booked at
		
		$this->debug 		.= '<br>Fortsätter anrop till Profit som autentierad anvädnare, session_key ' . $r['session_key'] . ' för att hämta bokningarna';

		$xmls = '<ProfitAndroid command="FetchBookings">
			<GUID>%1$s</GUID>
			<Date>' . $r['date_stamp'] . '</Date>
			<User>' . $r['username'] . '</User>
			<Password>' . $r['password'] . '</Password>
		</ProfitAndroid>';
		
		$book_result 			= $this->make_soap_call( $xmls, $r['session_key'], $r['username'], $r['password'] );
		
		$this->debug 		.= '<br>Resultat från bokningen: ' . print_r( $book_result, true) ;
		
		if ( $book_result['error'] != '' ) {
		
			return $book_result;
		
		} else {
		
			$r['schema'] = $this->add_schema_xml ( $book_result['xml']->AndroidBookingObjects->Booking, 'bookings' );
		}
		
		return $r;
		
	}
	
	

	//////////////////////////////////////////////////////////////////////////////
	//
	// PRIVATE FUNCTINO ADD SCHEMA XML
	//
	//////////////////////////////////////////////////////////////////////////////
	
	private function add_schema_xml ( $activites_nodes, $xml_type, $user_bookings = array(), $user_reserve_bookings = array() ) {

		$schema = array();
		
		foreach ( $activites_nodes as $activity ){

			// fix dates
			$start_time_stamp			= strtotime ( (string) $activity->start );
			
			$startdate 				= date( 'Y-m-d', $start_time_stamp );
			
			$starttime 				= date( 'G:i', $start_time_stamp );
			
			$end_time_stamp			= strtotime ( (string) $activity->e );
			
			$enddate 					= date( 'Y-m-d', $end_time_stamp );
			
			$endtime 					= date( 'G:i', $end_time_stamp );
			
			
			// fix booking id and bookingtype
			
			$booking_id 		= array_key_exists ( (string) $activity->BOID, $user_bookings) ? $user_bookings [ (string) $activity->BOID ] : '';
			
			$reserve_booking_id = array_key_exists ( (string) $activity->BOID, $user_reserve_bookings) ? $user_reserve_bookings [ (string) $activity->BOID ]['bookingid'] : '';
			
			$reserve_position	= array_key_exists ( (string) $activity->BOID, $user_reserve_bookings) ? $user_reserve_bookings [ (string) $activity->BOID ]['position'] : '';
			
			$booking_type		= '';
			
			if (  $booking_id != '' ) {
			
				$booking_type 	= 'ordinary';
			
			} else if ( $reserve_booking_id != '' ) {
			
				$booking_type 	= 'waitinglist';
				
				$booking_id	= $reserve_booking_id;
			
			}

			// put it together
			array_push( $schema,
			
				array(
				
					'id'					=> (string) $activity->BOID,
					
					'products'			=> (string) $activity->desc,
					
					'resources'			=> '',
					
					'staff'				=> (string) $activity->l,
					
					'room'				=> (string) $activity->r,
					
					'businessuniidt'		=> '',
					
					'businessunit'			=> '',
					
					'startdate'			=> $startdate,
					
					'starttime'			=> $starttime,
					
					'startdatetime'		=> (string) $activity->start,
					
					'enddate'				=> $enddate,
					
					'endtime'				=> $endtime,
					
					'enddatetime'			=> (string) $activity->e,
					
					'totalslots'			=> (string) $activity->s,
					
					'freeslots'			=> (string) $activity->sl,
					
					'bookableslots'		=> (string) $activity->sl,
					
					'dropinslots'			=>  (string) $activity->dsl, // '-1', // unknown
					
					'waitinglistsize'		=> '-1', // unknown
					
					'waitinglistposition'	=> $reserve_position,
					
					'bookingid'			=> $booking_id, 
					
					'bookingtype'			=> $booking_type,
					
					'status'				=> strtolower( (string) $activity->bookbuttonstatus ) // BOOK, DROPIN (endast dropin), RESERVE, NOTBOOKABLE, CANCELLED, FULL, CLOSED
				)
			);
		}
		
		return $schema;
	}

	
	////////////////////////////////////////////////////////////////////////////////
	//
	// MAKE A SOAP CALL
	// Make GUID in xml_command look like %1$s
	//
	////////////////////////////////////////////////////////////////////////////////
	
	private function make_soap_call ( $xml_command, $session_key = '', $username = '', $password = '', $num_retry = 0 ) {
	
		$this->debug .= '<br>Make soap call, session_key: ' . $session_key . ', username: ' . $username . ', password:' . $password;
	
		$output = array( 'error' => '', 'message' => '', 'xml' => false, 'new_session_key' => '', 'debug' => '' );
	
		// login as guest, get current session_guid from database
		if ( $username == '' && $password == '' ) {
		
			$session_key = get_option('profit_session_key');
			
			$this->debug .= '<br>Get key from option: ' . $session_key;

			// if no session guid has been stored, login and get a new one and store it in the database
			if ( $session_key == '' ) {
			
				$session_key  = $this->soap_guest_login ();
				
				if ( is_wp_error ( $session_key )) {
					
					$output['error'] 	= 'YES';
				
					$output['message'] 	= 'Det gick inte att logga in på Profit bokningssystem.';
					
					return $output;
					
				} else {
				
					$this->debug .= '<br>Getting new key from login: ' . $session_key;
					
					update_option( 'profit_session_key', $session_key );
					
					update_option( 'profit_session_key_timestamp', time() );
					
					$output['new_session_key'] = $session_key;
				}
			}
		
		// login as user
		} else {
		
			if ( $session_key == '' ) {
			
				$this->debug .= '<br>Session key is empty, trying to get a new one from : ' . $username;
			
				$user_login =  $this->login ( $username, $password );
				
				if ( $user_login['error'] != '' ) {
				
					$this->debug .= '<br>Error while trying to login again.';
				
					$output['error'] 	= 'YES';
			
					$output['message'] 	= 'Det gick inte att logga in på Profit bokningssystem. ' . $user_login['message'];
					
					return $output;				
				
				} else {
				
					$session_key = $user_login['session_key'];
				
					$this->debug .= '<br>Getting new key from login: ' . $session_key;
				
					$output['new_session_key'] = $session_key;				
				}
			}
		}
		
		$final_xml_command = sprintf ( $xml_command, $session_key );

		$result = $this->exec_soap_call( $final_xml_command );
		
		if ( is_wp_error ( $result )) {
		
			if ( $result->get_error_message() == 'session maybe has expired' && $num_retry == 0 ) {
			
				if ( $username == '' && $password == '' ) update_option( 'profit_session_key', '' ); // empty guest session key
				
				$this->debug .= '<br>Session maybe has expired, going back in and try again';
				
				$output = $this->make_soap_call (  $xml_command, '', $username, $password, 1 );
			
			} else {
		
				$this->debug .= '<br>Felmeddelande från profit: ' . $result->get_error_message();
			
				$output['error'] 	= 'YES';
			
				$output['message'] 	= 'Detta är felmeddelandet från Profit Bokningssystem: ' . $result->get_error_message();
				
			}
		
		} else {
			
			$output['xml']		= $result;
		
		}
		
		return $output;
	}
	


	////////////////////////////////////////////////////////////////////////////////
	//
	// EXECUTE SOAP CALL
	//
	////////////////////////////////////////////////////////////////////////////////
	
	private function exec_soap_call ( $xml_command ) {

		$this->debug .= '<br>exec_soap_call: ' . $xml_command;
		
		global $fs_schema;
		
		$settings = $fs_schema->data->settings();

		$r['url']	 			= $settings[ 'fs_schema_profit_server_url' ];
		//$r['url']				= 'http://bookapi.pastell16.pastelldata.se/v4186/MobileServices.asmx?wsdl';	
		
		
		// Create the client instance
		try  { 
		
			$client = @new soapclient( $r['url'] );
		
		} 
		
		catch (SoapFault $exception) {
		
			$this->debug .= '<br>Det måste vara en felaktig adress i Bokningssystemets URL.';
		
			return new WP_Error('exec_soap_call_error', 'Det gick inte att få kontakt med bokningssystemets server. Servern verkar inte finnas.' );		

		}

		try  { 
		
			$result = $client->processUnsafe( array('xml' => $xml_command )); 
		} 
		
		catch (SoapFault $exception) { 
		
			//$this->debug .= '<br>Soap Exception: ' . print_r( $exception, true);
			
			switch ( $exception->getMessage() ) {
			
				case 'Server was unable to process request. ---> Object reference not set to an instance of an object.':
				
					return new WP_Error('exec_soap_call_error', 'session maybe has expired' );
					break;
			
				default:
					
					$this->debug .= '<br>Felet orsakades av anropet: ' . $client->__getLastRequest();
					return new WP_Error('exec_soap_call_error', $exception->getMessage() );
					break;
					
			}
		} 
		
		$this->debug .= '<br>Resultat från servern: ' . $result->processUnsafeResult;
		
		$xml_doc = @simplexml_load_string( iconv( "UTF-8", "UTF-8//IGNORE",  $result->processUnsafeResult ) );
			
		if ( $xml_doc === false ) {
		
			$this->debug .= '<br>Det gick inte att ladda följande text som ett xml-dokument: ' . $result->processUnsafeResult ;
		
			return new WP_Error('exec_soap_call_error', 'Det gick inte att ladda xml-data.' );
			
		}
		
		return $xml_doc;	
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	// LOGIN AS GUEST
	// Return av valid session_key, and set login properties
	//
	////////////////////////////////////////////////////////////////////////////////
		
	private function soap_guest_login () {

		// login as guest
		
		global $fs_schema;
		
		$settings = $fs_schema->data->settings();

		$this->debug .= '<br>Soap_Login as guest';
	
		$xml_command = '<ProfitAndroid command="UsualAuthenticate">
						<globalunit>' . $settings['fs_booking_profit_organization_unit'] . '</globalunit>
						<type>GUEST</type>
						<PartyName>KlasEhnemark</PartyName>
						<Licenskey>' . $settings['fs_booking_profit_3part_licence_key'] . '</Licenskey>
					 </ProfitAndroid>';

		$result = $this->exec_soap_call( $xml_command );
	
		if ( is_wp_error ( $result )) {
		
			$this->debug .= '<br>Det går inte att logga in på Profit bokingssystem: ' .  $result->get_error_message();
			
			return new WP_Error('soap_login_error', 'Det går inte att logga in på Profit bokingssystem: ' .  $result->get_error_message() );
		
		} else {
		
			$guid = (string) $result->GUID;
			
			return $guid;
		}
	}
	


	////////////////////////////////////////////////////////////////////////////////
	//
	// ENCRYPT AND DECRYPT XOR, not implemented
	//
	////////////////////////////////////////////////////////////////////////////////

	private function encrypt_decrypt_XOR ($text_to_encrypt) {

		$key = 129;
		
		$outText = ''; 

		for ( $i = 0; $i < strlen($text_to_encrypt); $i++ ) {
	
			$outText .= chr($text_to_encrypt{$i} ^ $key);
		}
		
		return $outText;
	}


} //End Class

?>