<?php
/*/////////////////////////////////////////////////////////////////

	FS SCHEMA, PHP Class - PROFIT

	Copyright (C) 2013 Klas Ehnemark (http://klasehnemark.com)
	This program is not free software.
	
	
	In all xml for sprint is:
	
	%1$s	= session key
	
	username: klas@klas.se
	password: 20898
	
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
			'typ'				=> 'vecka', 		// vecka, pass
			'anlaggning'			=> '',			// id, eller kommaseparerad id
			'datum'				=> '', 			// format: YYYY-MM-DD
			'username'			=> '',
			'password'			=> ''
		);
		
		$r 						= wp_parse_args( $args, $defaults );
		
		$sub_cache_name			= 'schema_' . $r['typ'] . '_' . $r['anlaggning'] . '_' . $r['datum'];


		//$r['username'] 			= 'klas@klas.se';
		//$r['password']				= '20898';

		return $this->update_schema( $r );
		 
		

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
	

	////////////////////////////////////////////////////////////////////////////////
	//
	// BOOK ACTIVITY
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function book_activity ( $username, $password, $activity_id, $session_key ) {

		global $fs_schema;
		
		$settings = $fs_schema->data->settings();

		$xmls = '<ProfitAndroid command="FetchBookings">
			<GUID>%1$s</GUID>
			<BOID>' . $activity_id . '</BOID>
			<User>' . $username . '</User>
			<Password>' . $password . '</Password>
		</ProfitAndroid>';
		
		$this->debug 				.= '<br>Påbörjar bokning av aktivitet hos Profit som inloggad användare ' . $username;
		
		$result 					= $this->make_soap_call ( $xmls, $session_key, $username, $password );
		
		if ( $result['error'] 		== '' && !isset ( $result['xml']['status'] )) {
		
			$result['error'] 		= 'YES';
				
			$result['message'] 		= 'Det gick inte att boka. Profit Bokningssystem svarade inte.';		
		}
		
		if ( $result['error'] 		== '' && $result['xml']['status'] != 'OK' ) {
		
			$result['error'] 		= 'YES';
			
			$result['message'] 		= 'Det gick inte att boka. ' . $result['xml']['status'];	
		}
		
		if ( $result['error'] 		== '' ) {
			
			$result['bookingid'] 	= '';
		
			$result['message'] 		= 'Bokningen är genomförd. Tyvärr kan inte Profit bokningssystem visa bokningar i veckovy.';
		
		}
		
		$result['debug'] 			= $this->debug . print_r($result, true);
		
		return $result;

	}	
		
		
	////////////////////////////////////////////////////////////////////////////////
	//
	// UNBOOK ACTIVITY
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function unbook_activity ( $username, $password, $bookingid ) {

		global $fs_schema;
		
		$result = array( 'error' => '', 'message' => '' );
		
		$result['error'] 		= 'YES';
		
		$result['message'] 		= 'PROFIt ÄR INtE INtEGRERAT ÄNNU';				

		$result['debug'] = $this->debug;
		
		return $result;

	}		
	
	//////////////////////////////////////////////////////////////////////////////
	//
	// LOGIN
	// 20898
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
		
		}
		
		$result['debug'] 				= $this->debug . print_r($result, true);
		
		return $result;
	}
	
	
	//////////////////////////////////////////////////////////////////////////////
	//
	// UPDATE SCHEMA INTO CACHE, return a serialized object
	//
	// %1$s in xml is session key
	//
	//////////////////////////////////////////////////////////////////////////////
	
	
	public function update_schema ( $r ) {
			
		// if no username, get schema as guest
		if ( $r['username'] == '' || $r['password'] == '' || $r['session_key'] == '' ) {
		
			$this->debug 		.= '<br>Påbörjar anrop till Profit som Gäst';
			
			$xmls = '<ProfitAndroid command="FetchBookableObjectsFiltered">
				<GUID>%1$s</GUID>
				<START>' . $r['date_stamp'] . '</START>
				<END>' . $r['date_stamp_end'] . '</END>
				<GLOBALUNITID>-1</GLOBALUNITID>
				<ACTIVITYID>-1</ACTIVITYID>
				<ACTIVITYCATEGORYID>-1</ACTIVITYCATEGORYID>
				<LEADERFREETEXT></LEADERFREETEXT>
			</ProfitAndroid>';
			
			$result 			= $this->make_soap_call( $xmls );
		
		} else {
		
			$this->debug 		.= '<br>Påbörjar anrop till Profit som autentierad anvädnare, session_key ' . $r['session_key'];
			
			$xmls = '<ProfitAndroid command="FetchBookableObjects">
				<GUID>%1$s</GUID>
				<Date>' . $r['date_stamp'] . '</Date>
				<User>' . $r['username'] . '</User>
				<Password>' . $r['password'] . '</Password>
			</ProfitAndroid>';
		
			$result 			= $this->make_soap_call( $xmls, $r['session_key'], $r['username'], $r['password'] );
		}

		if ( $result['error'] 	== '' && !isset ( $result['xml']->AndroidBookableObjects->bo )) {
			
			$r['error'] 		= 'YES';
			
			$r['message'] 		= 'Schemat för aktuell period är tomt.';							
			
		} else if ( $result['error'] != '' ) {
		
			$r = $result;
		
		} else {

			// loop thru xml and store data into array
			//$schema = array();
			
			foreach ( $result['xml']->AndroidBookableObjects->bo as $activity ){

				
				// fix dates
				$start_time_stamp			= strtotime ( (string) $activity->start );
				
				$startdate 				= date( 'Y-m-d', $start_time_stamp );
				
				$starttime 				= date( 'G:i', $start_time_stamp );
				
				$end_time_stamp			= strtotime ( (string) $activity->e );
				
				$enddate 					= date( 'Y-m-d', $start_time_stamp );
				
				$endtime 					= date( 'G:i', $start_time_stamp );
				
				
				// put it together
				array_push( $r['schema'],
				
					array(
					
						'id'				=> (string) $activity->aid,
						
						'products'		=> (string) $activity->desc,
						
						'resources'		=> '',
						
						'staff'			=> (string) $activity->l,
						
						'room'			=> (string) $activity->r,
						
						'businessuniidt'	=> '',
						
						'businessunit'		=> '',
						
						'startdate'		=> $startdate,
						
						'starttime'		=> $starttime,
						
						'startdatetime'	=> (string) $activity->start,
						
						'enddate'			=> $enddate,
						
						'endtime'			=> $endtime,
						
						'enddatetime'		=> (string) $activity->e,
						
						'freeslots'		=> (string) $activity->sl,
						
						'bookableslots'	=> (string) $activity->rsl,
						
						'bookingid'		=> '',
						
						'cancelled'		=> (string) $activity->ca == '1' ? true : false
					)
				);
			}
		}
		
		return $r;
	}
	

	

	
	////////////////////////////////////////////////////////////////////////////////
	//
	// MAKE A SOAP CALL
	// Make GUID in xml_command look like %1$s
	//
	////////////////////////////////////////////////////////////////////////////////
	
	private function make_soap_call ( $xml_command, $session_key = '', $username = '', $password = '', $num_retry = 0 ) {
	
		$this->debug .= '<br>Make soap call, session_key: ' . $session_key . ', username: ' . $username . ', password:' . $password;
	
		$output = array( 'error' => '', 'message' => '', 'xml' => false, 'new_session_key' => '' );
	
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
			
				$user_login =  $this->login ( $username, $password );
				
				if ( $user_login['error'] != '' ) {
				
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
		$r['url']				= 'http://bookapi.pastell16.pastelldata.se/v4186/MobileServices.asmx?wsdl';	
		
		
		// Create the client instance
		$client = new soapclient( $r['url'] );

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
		
		$xml_doc = simplexml_load_string( iconv( "UTF-8", "UTF-8//IGNORE",  $result->processUnsafeResult ) );

		//if ( $xml_doc == '' ) return new WP_Error('exec_soap_call_error', 'Det gick inte att ladda xml-data.' );
		
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
	// ENCRYPT AND DECRYPT XOR
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public static function encrypt_decrypt_xor ( $text_to_encrypt ) {
	
		$output = '';
		
		$key = 129;
		
		for ( $i = 0; $i < strlen($text_to_encrypt); $i++ ) {
		
			$char = $text_to_encrypt[ $i ];
		
			$char = chr(ord($char) ^ $key);
			
			$output .= $char;
		}
	
		return $output;
	
	}


} //End Class

?>