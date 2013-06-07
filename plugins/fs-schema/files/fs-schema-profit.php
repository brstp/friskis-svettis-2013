<?php
/*/////////////////////////////////////////////////////////////////

	FS SCHEMA, PHP Class - PROFIT

	Copyright (C) 2013 Klas Ehnemark (http://klasehnemark.com)
	This program is not free software.

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
	// LOGIN
	//
	//////////////////////////////////////////////////////////////////////////////
	
	public function login ( $username, $password ) {	
	
		global $fs_schema;
		
		$result['debug'] = $this->debug . print_r($result, true);
		
		return $result;
	
	}
	
	
	//////////////////////////////////////////////////////////////////////////////
	//
	// UPDATE SCHEMA INTO CACHE, return a serialized object
	//
	//////////////////////////////////////////////////////////////////////////////
	
	
	public function update_schema ( $r ) {
	
		global $fs_schema;
		
		$settings = $fs_schema->data->settings();
		
		
		$r['url']	 			= $settings[ 'fs_schema_profit_server_url' ];
		$r['url']				= 'http://bookapi.pastell16.pastelldata.se/v4186/MobileServices.asmx?wsdl';		
		
		
		$xmls = '<ProfitAndroid command="UsualAuthenticate"><globalunit>1437</globalunit><type>GUEST</type><PartyName>KlasEhnemark</PartyName><Licenskey>Txx3453HgbPWW132</Licenskey></ProfitAndroid>';				

		// Create the client instance
		$client = new soapclient( $r['url'] );

		$result = $client->processUnsafe( array('xml' => $xmls ));
		
		echo '<h2>Result</h2><pre>' .  print_r ( $result, true ) . '</pre>';

		die();
	
		
		// 

			
				$post_xdata			= '<ProfitAndroid command="UsualAuthenticate"><globalunit>1437</globalunit><type>GUEST</type><PartyName>KlasEhnemark</PartyName><Licenskey>Txx3453HgbPWW132</Licenskey></ProfitAndroid>';
			
				//$post_xdata			= '<ProfitAndroid command="UsualAuthenticate"><type>GUEST</type></ProfitAndroid>';
			
				$post_data			= '<?xml version="1.0" encoding="utf-8"?>
										<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
										  <soap:Body>
										    <processUnsafe xmlns="http://tempuri.org/">
											 <xml>' . $post_xdata . '</xml>
										    </processUnsafe>
										  </soap:Body>
										</soap:Envelope>';
			
				$r['url']	 			= $settings[ 'fs_schema_profit_server_url' ];
				$r['url']				= 'http://bookapi.pastell16.pastelldata.se/v4186/MobileServices.asmx?op=version';
				//$post_data			= false;
				
				
				/*if ( $r['username'] != '' && $r['password'] != '' ) {
				
					//$r['url']			.= '&includebooking=true';
					
					//$result 			= $this->get_xml_file ( $r['url'], $post_data, $r['username'], $r['password'] );
				
				} else {
				
					$result			= $this->get_xml_file ( $r['url'], $post_data );
				
				}
				
				$headers = array(
				    "Content-type: text/xml;charset=utf-8",
				    "Cache-Control: no-cache",
				    "Pragma: no-cache",
				    "SOAPAction: http://tempuri.org/processUnsafe",
				    "Content-length: ".strlen($post_data),
				);*/
				
		
		
				//$result			= $this->get_xml_file ( $r['url'], $post_data, '', '', false, $headers );
				
				
				

        // xml post structure

       /* $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
                            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                              <soap:Body>
                                <version xmlns="http://tempuri.org/">
                                  <xml>string test</xml> 
                                </version>
                              </soap:Body>
                            </soap:Envelope>';
 		$xml_post_string = $post_data;*/
          
          
          
          
		// den här ger fel 500: soap:ServerServer was unable to process request. ---> Root element is missing.
		$post_data_500			= '<?xml version="1.0" encoding="utf-8"?>
								<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
								  <soap:Body>
								    <processUnsafe xmlns="http://tempuri.org/">
									 <xml></xml>
								    </processUnsafe>
								  </soap:Body>
								</soap:Envelope>';
								
								
		$xmls = '<ProfitAndroid command="UsualAuthenticate"><globalunit>1437</globalunit><type>GUEST</type><PartyName>KlasEhnemark</PartyName><Licenskey>Txx3453HgbPWW132</Licenskey></ProfitAndroid>';				
						
		// den här ger fel http code 400 Bad Request
		$post_data_400			= '<?xml version="1.0" encoding="utf-8"?>';
		$post_data_400			.= '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">';
		$post_data_400			.= '<soap:Body>';
		$post_data_400			.= '<processUnsafe xmlns="http://tempuri.org/">';
		$post_data_400			.= '<xml><ProfitAndroid command="UsualAuthenticate"><globalunit>1437</globalunit><type>GUEST</type><PartyName>KlasEhnemark</PartyName><Licenskey>Txx3453HgbPWW132</Licenskey></ProfitAndroid></xml>';
		$post_data_400			.= '</processUnsafe>';
		$post_data_400			.= '</soap:Body>';
		$post_data_400			.= '</soap:Envelope>';
			
		$current_post	= $post_data_400;

		$headers = array(
		
			"Content-type: text/xml;charset=\"utf-8\"",	
			
			"Accept: text/xml",
			
			"Cache-Control: no-cache",
			
			"Pragma: no-cache",
			
			"SOAPAction: http://tempuri.org/processUnsafe", 
			
			"Content-length: " . strlen($current_post)
		);

		$url = 'http://bookapi.pastell16.pastelldata.se/v4186/MobileServices.asmx?op=processUnsafe';
		$url = 'http://bookapi.pastell16.pastelldata.se/v4186/MobileServices.asmx?wsdl';
		
		

		



		
		
		
		echo '<h2>Headers</h2><pre>' . print_r ( $headers, true ) . '</pre>';
		
		echo '<h2>Post data</h2><pre>' . print_r ( htmlspecialchars ($current_post), true ) . '</pre>';
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		
		$cookiePath = tempnam('/tmp', 'cookie');
		
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiePath);
		
		curl_setopt($ch, CURLOPT_HEADER, 0);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
	  	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		
		curl_setopt($ch, CURLOPT_POST, true);
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $current_post);
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$curl_output 	= curl_exec($ch); 
		
		$curl_info 	= curl_getinfo($ch);

		echo '<h2>Error</h2><pre>' . print_r ( curl_error($ch), true ) . '</pre>';

		curl_close($ch);
		
		echo '<h2>Output</h2><pre>' . print_r ( $curl_output, true ) . '</pre>';
		
		echo '<h2>Curl info</h2><pre>' . print_r ( $curl_info, true ) . '</pre>';
		
		die();
		
		
		
		
		// converting
		$response1 = str_replace("<soap:Body>","",$response);
		$response2 = str_replace("</soap:Body>","",$response1);
		
		// convertingc to XML
		$parser = simplexml_load_string($response2);
		// user $parser to get your data out of XML response and to display it.
		
		stereotype::debug ( $parser, true);
				
				
				
				
				
				
				
				//stereotype::debug( $result );
				
				
				/*$result				= $this->check_brp_xml_errors ( $result );
				
				if ( $result['error'] 	== '' && !isset ( $result['xml']->activity )) {
					
					$r['error'] 		= 'YES';
					
					$r['message'] 		= 'Schemat för aktuell period är tomt.';							
					
				} else if ( $result['error'] != '' ) {
				
					$r = $result;
				
				} else {

				}*/

		return $r;
	}
	



	////////////////////////////////////////////////////////////////////////////////
	//
	// BOOK ACTIVITY
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function book_activity ( $username, $password, $activity_id ) {
		
		$result = array( 'error' => '', 'message' => '' );
		
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
		
		$result = array( 'error' => '', 'message' => '' );
		
		$result['error'] 		= 'YES';
		
		$result['message'] 		= 'PROFIt ÄR INtE INtEGRERAT ÄNNU';				

		$result['debug'] = $this->debug;
		
		return $result;

	}		
} //End Class

?>