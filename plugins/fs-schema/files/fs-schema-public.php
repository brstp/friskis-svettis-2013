<?php
/*/////////////////////////////////////////////////////////////////

	FS SCHEMA, PHP Class - PUBLIC

	Copyright (C) 2013 Klas Ehnemark (http://klasehnemark.com)
	This program is not free software.

//////////////////////////////////////////////////////////////////*/



class fs_schema_public {

	private $version 			= 'Beta-version 0.995';
	
	private $default_username	= ''; 
	
	private $default_password	= '';
	
	

	////////////////////////////////////////////////////////////////////////////////
	//
	// INITIALIZE OBJECT
	//
	////////////////////////////////////////////////////////////////////////////////

	public function __construct ( ) {

		// Initialization stuff
		
		add_action( 'wp_enqueue_scripts', 						array ( $this, 'load_scripts' ));
					
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	// INIT ADMIN
	//
	////////////////////////////////////////////////////////////////////////////////

	function load_scripts () {
	
		wp_enqueue_script 	( 'jquery' );
	
		wp_register_script	( 'fs-schema-public-script', 	plugins_url('fs-schema') . '/files/fs-schema-public-script.js' );

		wp_register_style 	( 'fs-schema-public-style', 	plugins_url('fs-schema') . '/files/fs-schema-public-styles.css' );
	
		//wp_register_script	( 'fs-schema-public-script', 	plugins_url('fs-schema-test') . '/files/fs-schema-public-script.js' );

		//wp_register_style 	( 'fs-schema-public-style', 	plugins_url('fs-schema-test') . '/files/fs-schema-public-styles.css' );
		
		wp_enqueue_script 	( 'fs-schema-public-script' );
		
		wp_enqueue_style 	( 'fs-schema-public-style' );
		
		wp_localize_script	( 'fs-schema-public-script', 	'fsschemavars', 	array(
		
											'ajaxurl' 	=> admin_url ( 'admin-ajax.php' ),
											
											'nonce' 		=> wp_create_nonce ( 'ajax-example-nonce' ) ) );
		
	}
	



	////////////////////////////////////////////////////////////////////////////////
	//
	// RENDER SCHEMA BY WALKING
	//
	////////////////////////////////////////////////////////////////////////////////
	
	public function login ( $username = '', $password = '' ) {
	
		global $fs_schema;
	
		return $fs_schema->data->login ( $username, $password );
	
	}
	
	
	

	////////////////////////////////////////////////////////////////////////////////
	//
	// RENDER SCHEMA BY WALKING
	//
	////////////////////////////////////////////////////////////////////////////////
	
	public function walk_schema ( $num_days, $date_info, $step, $username = '', $password = '', $session_key = '' ) {
	
		if ( $date_info ) {
		
			$r 					= unserialize ( base64_decode ( $date_info ));
			
			$date 				= fs_schema::get_date_from_string ( $r['date'] );
			
			$r['date']			= date( 'Y-m-d', mktime( 0, 0, 0, date( "m", $date ), date( "d", $date ) + ( $step * $num_days ) , date( "Y", $date )));
			
			$r['type']			= ( $num_days == 7 ? 'week' :( $num_days == 0 ? 'bookings' : 'day') ) ;
			
		} else {
		
			$r				= array();
		}
		
		$r['no_wrapper']		= true;
		
		$r['username']			= $username;
		
		$r['password']			= $password;
		
		$r['session_key']		= $session_key;
		
		return $this->render_schema ( $r );
	
	}


	////////////////////////////////////////////////////////////////////////////////
	//
	// RENDER SCHEMA
	//
	////////////////////////////////////////////////////////////////////////////////

	function render_schema ( $args ) {

		$defaults = array(
			'type'			=> 'week', 		// week, day, bookings
			'facility'		=> '',			// id, eller kommaseparerad id
			'date'			=> '',  			// format: YYYY-MM-DD
			'day_width'		=> '130',
			'booking'			=> '1',			// 0 = no booking
			'no_wrapper'		=> false,			// true = no wrapper div with fs_schema, used in ajax
			'username'		=> '',
			'password'		=> '',
			'session_key'		=> '',
			'enableweek'		=> true,
			'enableday'		=> true
		);
		
		$r = wp_parse_args( $args, $defaults );
		
		global $fs_schema;
		
		$settings 			= $fs_schema->data->settings();
		
		$output 				= '';
		
		date_default_timezone_set('Europe/Stockholm');



		// get data
		if ( $r['type'] == 'bookings' ) $s = $fs_schema->data->get_bookings( $r );
			
		else $s = $fs_schema->data->get_schema( $r );
		

		if ( $s['error'] != '' ) {
		
			if ( $r['no_wrapper'] === false ) 	{
			
				$output  		.= '<div class="fs_schema">';
			
				$output 		.= '<div class="fs_schema_error">' . $s['message'];;
				
				if ( $settings['fs_booking_fallback_url'] != '' ) 
				
					$output 	.= '<span>Om felet består, prova att använda <a href="' . $settings['fs_booking_fallback_url']  . '">den alternativa bokningsfunktionen</a>.</span>';
			
				$output 		.= '</div>' . $this->about_html();
				
				if ( $settings['fs_schema_show_debug'] == 'YES' ) 
			
					$output 	.= '<div class="debug"></div><div class="week_debug">' . $fs_schema->data->debug . '</div>';
				
				$output		.= '</div>';
				
			} else {
			
				$output 		.= '<div class="fs_schema_error">Fel. ' . $s['message'] . '</div>';
				
				if ( $settings['fs_schema_show_debug'] == 'YES' )
				
					$output 	.= '<div class="week_debug">' . $fs_schema->data->debug . '</div>';
			
			}
		
		} else {

		
			// some declarations
			$num_days						= $r['type'] == 'week' ? 7 : 1;
			
			$num_entries					= count( $s['schema'] );
			
			$weekdays 					= array ( 'ulldag', 'Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lördag', 'Söndag' );
			
			$month_names 					= array ( 'ullmo', 'jan', 'feb', 'mar', 'apr', 'maj', 'jun', 'jul', 'aug' , 'sep' , 'okt' , 'nov' , 'dec'  );
			
			$week_activities 				= array();
			
			$week_hour_info 				= array();
			
			$day_hour_info					= array();
			
			$earliest_hour 				= 16;
			
			$latest_hour 					= 17;
			
			$day_width_px					= $r['day_width'];
			
			$hours_width_px				= 30;
			
			$entry_width_px				= $day_width_px;
			
			$entry_padding_px				= 4;
			
			$day_header_height_px			= 27;
			
			$hour_height_px				= 62;  //45
			
			$min_hour_height_px				= 20;
			
			$day_height_px					= 40;
			
			$date_week_start 				= mktime( 0, 0, 0, date( "m", $s['start_date '] ), date( "d", $s['start_date '] ) - date( 'N', $s['start_date '] )+1, date( "Y", $s['start_date '] ));

			$today_date_array				= getdate();				
			
			$today_date					= mktime( 0, 0, 0, $today_date_array ['mon'], $today_date_array ['mday'],$today_date_array ['year']);
			
			$today_this_hour				= mktime( $today_date_array['hours'], 0, 0, $today_date_array ['mon'], $today_date_array ['mday'], $today_date_array ['year']);
			
			$this_hour_num					= date ( 'G', 	$today_this_hour );	
			
			$today_week 					= date ( 'W', $today_date );
			
			$this_week					= date ( 'W', $date_week_start );
			
			$this_year					= date ( 'Y', $date_week_start );
			
			$r['date']					= date ( 'Y-m-d', $s['start_date ']);

			
			// build the class name based on settings
			$class_name					= 'fs_schema ' . $r['type'] . ( $r['enableweek'] == true ? ' enableweek' : '' ) . ( $r['enableday'] == true ? ' enableday' : '' );
			

			if ( $r['no_wrapper'] === false ) {
				
				$output 					.= '<div class="' . $class_name . '" data-day-width="' . $day_width_px . '" data-hours-width="' . $hours_width_px . '" >';
				
				$output					.= '<div class="navigation above_schema' . ( $settings['fs_schema_show_my_bookings'] == 'YES' ? ' show_my_bookings' : '' ) . '">';
				
				$output					.= '<div class="fs_button previous">&lt;&nbsp;Föregående <span>' . ( $num_days == 1 ? 'dag' : 'vecka' ) . '</span></div>';
			
				if ( $r['booking'] == '1' )	
				
					$output 				.= '<div class="login_info fs_button">Logga in...</div>';
				
				if ( $settings['fs_schema_show_my_bookings'] == 'YES' ) {
				
					$output 				.= '<div class="my_bookings fs_button">Mina bokningar</div>';
					
					$output 				.= '<div class="update_schema fs_button">Refresh</div>';
					
				}
				
				$output 					.= '<div class="change_to_week fs_button ' . ( $r['enableweek'] != true ? ' disabled' : '' ) . '" ' .  ( $r['type'] == 'week' ? 'style="display: none;" ' : '' ) . '>Växla till veckoschema</div>';
				
				$output 					.= '<div class="change_to_day fs_button ' . ( $r['enableday'] != true ? ' disabled' : '' ) . '" ' .  ( $r['type'] == 'day' ? 'style="display: none;" ' : '' ) . '>Växla till lista</div>';
				
				$output 					.= '<div class="schema_print fs_button" title="Skriv ut schemat"><span></span></div>';
		
				$output					.= '<div class="fs_button next">Nästa <span>' . ( $num_days == 1 ? 'dag' : 'vecka' ) . '</span> &nbsp;&gt;</div></div><div class="message_above_schema"' . ( !get_option( 'fs_schema_valid_key' ) ? ' style="display: block;"' : '') . '>' . ( !get_option( 'fs_schema_valid_key' ) ? 'Detta schemat är inte kopplat till en giltig API-nyckel.' : '') . '</div>';
				
				$output					.= '<div class="weeks">';
			
			}
			
			$output 						.= '<div class="days" data-date-info="' . base64_encode (serialize (  $r ) ) . '" 
											data-week="v' . $this_week . ' ' . $this_year . '" data-cache-status="' . $fs_schema->data->last_cache_status . '">';
			

			// loop thru every schema entry and store the entry in the right position of a multidimensional array
			if ( is_Array ( $s['schema'] )) {
			
				foreach ( $s['schema'] as $entry ) {
					
					$start_time 				= fs_schema::get_date_from_string ( $entry['startdatetime'], true );
					
					$end_time 				= fs_schema::get_date_from_string ( $entry['enddatetime'] );
					
					$start_day 				= $num_days == 1 ? 1 : (int) date( 'N', $start_time );  // if show only one day, all events are at 'day 1'
					
					$start_hour 				= (int) date( 'H', $start_time );
					
					$end_hour 				= (int) date( 'H', $end_time );
					
					$start_minute				= (int) date ('i', $start_time );
					
					$end_minute				= (int) date ('i', $end_time );
					
					$before_now				= $start_time < $today_this_hour; 
					
					if ( $end_hour == $start_hour  )			$end_hour++;
					
					if ( $start_hour < $earliest_hour ) 		$earliest_hour = $start_hour;
					
					if ( $end_hour > $latest_hour ) 			$latest_hour = $end_hour;
					
					
					if ( $start_day > 0 && $start_day <=$num_days && $start_hour > 0 && $start_hour <= 24 ) {
					
						$entry['_start_hour'] 				= $start_hour;
						
						$entry['_end_hour'] 				= $end_hour;
						
						$entry['_start_minute']				= $start_minute;
						
						$entry['_before_now']				= $before_now;
						
						if ( isset ( $week_activities[$start_day][$start_hour] )) array_push( $week_activities[$start_day][$start_hour], $entry );
							
						else $week_activities[$start_day][$start_hour] = array ( $entry );
	
						
						// add the number of entries that occupy the same hours
						for ( $hour = $start_hour; $hour < $end_hour; $hour++ ) {
						
							if ( isset ( $week_hour_info[$start_day][$hour]['num_entries'] )) $week_hour_info[$start_day][$hour]['num_entries']++;
							
							else $week_hour_info[$start_day][$hour]['num_entries'] = 1;
							
							if ( !isset ( $day_hour_info[$hour]['max_entries'] )) $day_hour_info[$hour]['max_entries'] = 1;
							
							if ( $day_hour_info[$hour]['max_entries'] < $week_hour_info[$start_day][$hour]['num_entries'] ) 
							
								$day_hour_info[$hour]['max_entries'] = $week_hour_info[$start_day][$hour]['num_entries'];
						}
					}
					
				}
			}
			


			// loop thru every hour in the week and change hour height if there are to many/few entries in one hour
			for ( $h = $earliest_hour; $h <= $latest_hour; $h++ ) {


				// calculate hour height
				if ( isset ( $day_hour_info[ $h ]['max_entries'] ) && $day_hour_info[ $h ]['max_entries'] > 0 ) {
				
					$day_hour_info[ $h ]['height'] 		= $day_hour_info[ $h ]['max_entries']  * $hour_height_px; // hour height
					
				} else {
				
					$day_hour_info[ $h ]['max_entries'] 	= 0;
				
					$day_hour_info[ $h ]['height'] 		= $min_hour_height_px;	// default, min hour height
				}
				
				
				// accumulated hour height
				$day_hour_info[ $h ]['acc_height']  		= $h > $earliest_hour ? $day_hour_info[ $h - 1 ]['acc_height'] + $day_hour_info[ $h -1 ]['height'] + 5 : 0;  

			}
			
			
			
			// loop thry every day in the week
			for ( $d = 1; $d <= $num_days; $d++) {
			
				$day_date 		= $num_days == 7 ? mktime( 0, 0, 0, date ( "m", $date_week_start  ), date( "d", $date_week_start ) + $d - 1 , date( "Y", $date_week_start )) : date ( 'U', $s['start_date ']);
				
				$day_num			= $num_days == 7 ? $d : date ( 'N', $s['start_date ']);
				
				$today_class		= $today_date == $day_date ? ' today' : '';
				
				$day_height		= ( $r['type'] == 'week' ? ( $day_hour_info[ $latest_hour ]['acc_height'] + $day_header_height_px + $day_hour_info[ $latest_hour]['height'] + 5 ) : ( $num_entries * ( $hour_height_px + 1 ) ) + 20 );
			
				$output 			.= '<div class="day day' . $d . ' ' . $today_class . '" data-day="' . $d . '" style="height: ' . $day_height . 'px; ';
				
				$output 			.= ( $num_days == 1 ? '' : 'width: ' . $day_width_px . 'px;' ) . ' ">';
				
				$head_title		= ( $r['type'] == 'week' ?  $weekdays[ $d ] . ' ' . date('j', $day_date) . ' ' . $month_names[date('n', $day_date)] : ( $r['type'] == 'day' ? $weekdays[ date ( 'N' , $s['start_date '] )] . ' ' . date('j/n', $day_date) : 'Mina bokningar (under utveckling - får ej användas live)' ) );
				
				$output 			.= '<div class="head">' . $head_title . '</div>';
				
				$hours_field		= '';
				
				$entries_field 	= '';
				
				$entry_count 		= 0;
				

				// loop thru every hour in the day
				for ( $h = $earliest_hour; $h <= $latest_hour; $h++ ) {
				
					$this_hour_class	= $this_hour_num == $h && $today_week == $this_week ? ' this_hour' : '' ;
				
					$hours_field 		.= '<div class="clock" style="height: ' . $day_hour_info[ $h ]['height']. 'px; ' . ( $num_days == 1 ? '' : 'width: ' . ( $day_width_px - 2 ) . 'px;' ) . '"><div class="day_clock' . $this_hour_class . '">' . $h . '.00' . '</div></div>'; 

					if ( isset( $week_activities[$d][$h] ) && is_array( $week_activities[$d][$h] )) {
					
						$hour_entry_count = 0;
						
						$hour_entries = $week_activities[$d][$h];
						
						usort( $hour_entries, 'fs_schema_public::sort_hour_activities' );
					
						foreach ( $hour_entries as $entry ) {	
				
							$entry_count++;
							
							$hour_entry_count++; 
							
						
							// entry data
							$entry_class 				= $products = $title = $booking_info = '';
							
							if ( is_Array ( $entry['products']  )) {
							
								foreach ( $entry['products']  as $product ) { $products .= $product['name'] . ' '; }
								
							} else {
							
								$products				= $entry['products'];
							}
							
							$entry_data 				= ' data-id="' . $entry['id'] . '" data-start="' . str_replace ( ':', '.', $entry['starttime']) . '" data-end="' . str_replace ( ':', '.', $entry['endtime']) . '" data-product="' . $products . '"';
							
							$entry_data 			    .= ' data-staff="' . $entry['staff'] . '" data-room="' . $entry['room'] . '" data-freeslots="' . $entry['freeslots'] . '"';
							
							$entry_data 			    .= ' data-totalslots="' . $entry['totalslots'] . '"';
							
							$entry_data 			    .= ' data-bookableslots="' . $entry['bookableslots'] . '" data-dropinslots="' . $entry['dropinslots'] . '"';
							
							$entry_data 			    .= ' data-waitinglistsize="' . $entry['waitinglistsize'] . '" data-waitinglistposition="' . $entry['waitinglistposition'] . '"';
							
							$entry_data 			    .= ' data-startdate="' . $weekdays[ $day_num ] . ' ' . date('j', $day_date) . ' ' . $month_names[date('n', $day_date)] . ' ' . date('Y', $day_date)  . '"';
							
							$entry_data 			    .= ' data-datestamp="' . date( 'Y-m-d', $day_date)  . '"';
							
							$entry_data 			    .= ' data-bookingid="' . $entry['bookingid'] . '" data-h="' . $h . '" data-bookingtype="' . $entry['bookingtype'] . '"';
							
							$entry_data			    .= ' data-status="' . $entry['status'] . '" data-hour_entry_count="' . $hour_entry_count . '"';
							
							$entry_class			    .= ' entry_' . $entry['status'];
							
							
							// calculate entry height based on duration
							$entry_height 				= ' height: ' . ( $hour_height_px + 4 ) . 'px;';
							
							$entry_width 				= ( $num_days == 1 ? 'width: 100%;' : 'width: ' . ( $day_width_px ) . 'px;' );


							// calculate entry position from top
							$entry_top				= '';
							
							if ( $h > $earliest_hour || $hour_entry_count > 1 ) {
							
								// if week, based on what time it starts
								if ( $r['type'] == 'week' )
								
									$entry_top = 'top: ' . (int)((($day_hour_info[ $h ]['acc_height']) + ( $hour_entry_count -1) * $hour_height_px ) + $hour_entry_count  ) . 'px; ';
								
								// if day or bookings, just add height to it
								else
								
									$entry_top = 'top: ' . ( $entry_count - 1 ) * $hour_height_px . 'px; '; 
							}
							
							
							// is this entry before now, add a before now class
							if ( $entry['_before_now'] === true ) {
							
								$entry_class			.= ' before_now';
								
								$title				= ' Passet har varit. ';
							
							} else {
							
								$entry_class			.= ' openable';
								
								
								// if user has booked this event
								
								if ( $entry['bookingid'] != '' && $entry['_before_now'] !== true && $entry['status'] != 'cancelled' ) {
									
									switch ( $entry['bookingtype'] ) {
									
										case 'ordinary':
										default:
										
											$entry_class			.= ' booked';
											
											$title				.= 'Du är inbokad på det här passet. ';
											
											$booking_info 			.= 'Du är inbokad. ';										
											
											break;
											
										case 'waitinglist':
										
											$entry_class			.= ' reserve';
											
											$title				.= 'Du är reserv.'; // . $entry['waitinglistposition'] . '  av ' . $entry['waitinglistposition'];
											
											$booking_info 			.= 'Du är reserv.'; // . $entry['waitinglistposition'] . '  av ' . $entry['waitinglistposition'];										
											
											break;
									} 

								} else {
									
									switch ( $entry['status'] ) {
									
										case 'full':
										
											$entry_class			.= ' fully_booked';
											
											$title				.= 'Inga lediga platser. ';
										
											break;
											
										case 'dropin':
										
											$entry_class			.= ' dropin';
											
											$title				.= 'Drop-in. ';
										
											break;
											
										case 'notbookable':
										
											$entry_class			.= ' notbookable';
											
											$title				.= 'Går inte att boka. ';
										
											break;
											
										case 'cancelled':
										
											$entry_class			.= ' cancelled';
											
											$title				.= 'Inställd. ';
										
											break;
											
										case 'closed':
										
											$entry_class			.= ' notbookable';
											
											$title				.= 'Stängt för bokning. ';
											
											break;
											
										case 'not_opened_yet':
										
											$entry_class			.= ' notbookable';
											
											$title				.= 'Går inte att boka än. ';
											
											break;
											
										case 'reserve':
										
											$entry_class			.= '';
											
											$title				.= 'Reservplatser. ';
											
											break;
											
										default:
										
											$title				.= 'Går att boka. ';
											
											break;
									}
								}
							}
							
							
							// create the entry html object
							$entries_field 			.= '<div class="entry' . $entry_class . '" title="' . $title . '" style="' . $entry_top . $entry_width . $entry_height . '"' . $entry_data  . '>';
							
							$entries_field 			.= '<div class="time">';
							
							if ( $r['type'] == 'bookings' ) $entries_field .= $weekdays[ date ( 'N' , $s['start_date '] )] . ' ' . date('j/n, Y', $day_date) . ' kl. ';
							
							$entries_field 			.= str_replace ( ':', '.', $entry['starttime']) . '-' . str_replace ( ':', '.', $entry['endtime'])  . '</div>';
							
							$entries_field 			.= '<div class="product">' . $products . '</div>';
							
							$entries_field 			.= '<div class="staffroom">';
							
							if ( $entry['staff'] != '' ) 	$entries_field .= ' med ' . $entry['staff'];
							
							if ( $entry['room'] != '' ) 	$entries_field .= ' i ' . $entry['room'];
							
							$entries_field 			.= '</div>';
							
							$entries_field 			.= '<div class="booking_info">' .  $booking_info . '</div>';
							
							$entries_field 			.= '</div>';
							
						}
					}
				}
				
				$output 	.= '<div class="hours" style="' . ( $num_days == 1 ? '' : 'width: ' . $day_width_px . 'px;' ) . '">' . $hours_field . '</div><div class="entries">' . $entries_field . '</div></div>';
			}
			
			if ( $settings['fs_schema_show_debug'] == 'YES' )
			
				$output 		.= '<div class="week_debug">' . $fs_schema->data->debug . '</div>';
			 
			$output 			.= '<div class="clearfix"></div></div></div>';
			
			if ( $r['no_wrapper'] === false ) {
			
				$output 		.= '<div class="navigation below_schema"><div class="fs_button previous">&lt;&nbsp;Föregående <span>' . ( $num_days == 1 ? 'dag' : 'vecka' ) . '</span></div>';
				
				$output 		.= '<div class="fs_button next">Nästa <span>' . ( $num_days == 1 ? 'dag' : 'vecka' ) . '</span>&nbsp;&gt;</div></div>';
				
				$output 		.= $this->about_html();
				
				if ( $settings['fs_schema_show_debug'] == 'YES' )
				
					$output 	.= '<pre class="debug"></pre>';
				
				$output 		.= '<div class="week_overlay">v52, 2013</div>';
				
				$output 		.= '<div class="week_progress"><div></div></div>';
				
				$output 		.= '<div class="big_message week_message"><div class="head">Fel.</div><div class="info">Meddelande</div><div class="fs_button close_btn">OK</div></div>';
				
				$output 		.= '<div class="fs_booking_fallback_url">' . $settings['fs_booking_fallback_url']  . '</div>';
			
				
				$output 		.= '<div class="login dialogue">
									<div class="header">Logga in</div>
									<div class="loggedin"></div>
									<div class="loginform">
										<div class="username">Användarnamn:<span><input type="text" value="' . $this->default_username . '" autocomplete="off" /></span></div>
										<div class="password">Lösenord:<span><input type="password" value="' . $this->default_password . '" autocomplete="off" /></span></div>
										<!--<div class="save_me_cookie"><label for="save_me"><input type="checkbox" id="save_me" disabled> Förbli inloggad</label></div>-->
									</div>
									<div class="buttons">
										<div class="fs_button login_btn">Logga in</div>
										<div class="fs_button logout_btn">Logga ut</div>
										<div class="fs_button close_login_form">Avbryt</div>
									</div>
									<div class="message"></div>
									<div class="progress"><img src="' . plugins_url('fs-schema') . '/files/fs-schema-progress.gif" /><div class="doingwhat">Loggar in...</div></div>
									<div class="big_message"><div class="head">Fel.</div><div class="info">Meddelande</div><div class="fs_button close_btn">OK</div></div>
							    </div>
								';
				
				$output 		.= '<div id="open_event_dialogue" class="open_event dialogue">
								<div class="header">Rubrik</div>
								<div class="date">Datum:<span>x</span></div>
								<div class="time">Klockan:<span>x</span></div>
								<div class="room">Lokal:<span>x</span></div>
								<div class="totalslots">Platser:<span>x</span></div> 
								<div class="staff">Ledare:<span>x</span></div>
								<!--<div class="right_now">Just nu</div>-->
								<div class="bookableslots">Bokningsbara:<span>x</span></div>
								<div class="dropin">Drop-in:<span>Information saknas</span></div>
								<div class="waitinglist">Reserver:<span>x</span></div>';
								
				if ( $r['booking'] == '1' ) {				
				
					$output 	 .= '<div class="entry_info entry_info_dropin">Endast dropin på detta pass.</div>
								<div class="entry_info entry_info_full">Passet är fullt.</div>
								<div class="entry_info entry_info_reserve">Passet är fullt.</div>
								<div class="entry_info entry_info_cancelled">Passet är inställt.</div>
								<div class="entry_info entry_info_not_bookable">Passet går inte att boka för tillfället.</div>
								<div class="entry_info entry_info_not_opened_yet">Passet går inte att boka än.</div>
								<div class="entry_info entry_info_closed">Passet är stängt för bokning.</div>
								<div class="divider"></div>
								<div class="booked_info">Du är inbokad.</div>
								<div class="loggedin"></div>
								<div class="loginform">
									<div class="username">Användarnamn:<span><input type="text" value="' . $this->default_username . '" autocomplete="off" /></span></div>
									<div class="password">Lösenord:<span><input type="password" value="' . $this->default_password . '" autocomplete="off" /></span></div>
								</div>
								<div class="buttons">
									<div class="fs_button logout">Logga ut</div>
									<div class="fs_button book_event">Boka</div>
									<div class="fs_button book_waitinglist">Boka reservplats</div>
									<div class="fs_button unbook_event">Avboka</div>
									<div class="fs_button unbook_waitinglist">Avboka reservplats</div>
									<div class="fs_button login_book_event">Logga in och boka</div>
									<div class="fs_button login_book_waitinglist">Boka reservplats</div>
									<div class="fs_button close_open_event">Avbryt</div>
								</div>';
								
				} else { 
				
					$output 	.= ' <div class="divider"></div>
								<div class="no_booking">Bokning på nätet är avstängd.</div>
								<div class="buttons">
									<div class="fs_button close_open_event">Stäng</div>
								</div>';
				}
				
				$output 		.= '	<img src="' . plugins_url('fs-schema') . '/files/fs-schema-close-button.png" class="close_open_event close" title="Stäng" />
								<div class="message"></div>
								<div class="progress"><img src="' . plugins_url('fs-schema') . '/files/fs-schema-progress.gif" /><div class="doingwhat">Loggar in...</div></div>
								<div class="big_message"><div class="head">Fel.</div><div class="info">Meddelande</div><div class="fs_button close_btn">OK</div></div>
							</div>';
							
			
				$output 		.= '<div class="responsive_target"></div></div>';
				
			}
		}
		
		return $output; 
	
	}
	




	////////////////////////////////////////////////////////////////////////////////
	//
	// BOOK ACTIVITY
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function book_activity ( $username, $password, $activity_id, $session_key ) {

		global $fs_schema;
		
		$settings = $fs_schema->data->settings();
		
		$return = array( 'error' => '', 'message' => '', 'debug' => '' );
		
		if ( $username == '' || $password == '' ) {
		
			$return['error'] 		= 'YES';
			
			$return['message'] 		= 'Användarnamn och eller lösenord saknas.';
			
		} else if ( $activity_id == '' ) {
		
			$return['error'] 		= 'YES';
			
			$return['message'] 		= 'Information om aktiviteten saknas.';
			
		} else {
		
			$return = $fs_schema->data->book_activity ( $username, $password, $activity_id, $session_key );
			
		}
		
		return $return;

	}
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	// BOOK WAITINGLIST
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function book_waitinglist ( $username, $password, $activity_id, $session_key ) {

		global $fs_schema;
		
		$settings = $fs_schema->data->settings();
		
		$return = array( 'error' => '', 'message' => '', 'debug' => '' );
		
		if ( $username == '' || $password == '' ) {
		
			$return['error'] 		= 'YES';
			
			$return['message'] 		= 'Användarnamn och eller lösenord saknas.';
			
		} else if ( $activity_id == '' ) {
		
			$return['error'] 		= 'YES';
			
			$return['message'] 		= 'Information om aktiviteten saknas.';
			
		} else {
		
			$return = $fs_schema->data->book_waitinglist ( $username, $password, $activity_id, $session_key );
			
		}
		
		return $return;

	}
	


	////////////////////////////////////////////////////////////////////////////////
	//
	// UNBOOK ACTIVITY
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function unbook_activity ( $username, $password, $bookingid, $session_key ) {

		global $fs_schema;
		
		$settings = $fs_schema->data->settings();
		
		$return = array( 'error' => '', 'message' => '', 'debug' => '' );
		
		if ( $username == '' || $password == '' ) {
		
			$return['error'] 		= 'YES';
			
			$return['message'] 		= 'Användarnamn och eller lösenord saknas.';
			
		} else if ( $bookingid == '' ) {
		
			$return['error'] 		= 'YES';
			
			$return['message'] 		= 'Information om bookingen saknas.';
			
		} else {
		
			$return = $fs_schema->data->unbook_activity ( $username, $password, $bookingid, $session_key );
			
		}
		
		return $return;

	}
	



	////////////////////////////////////////////////////////////////////////////////
	//
	// UNBOOK WAITINGLIST
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function unbook_waitinglist ( $username, $password, $bookingid, $session_key ) {

		global $fs_schema;
		
		$settings = $fs_schema->data->settings();
		
		$return = array( 'error' => '', 'message' => '', 'debug' => '' );
		
		if ( $username == '' || $password == '' ) {
		
			$return['error'] 		= 'YES';
			
			$return['message'] 		= 'Användarnamn och eller lösenord saknas.';
			
		} else if ( $bookingid == '' ) {
		
			$return['error'] 		= 'YES';
			
			$return['message'] 		= 'Information om bookingen saknas.';
			
		} else {
		
			$return = $fs_schema->data->unbook_waitinglist ( $username, $password, $bookingid, $session_key );
			
		}
		
		return $return;

	}


	
	////////////////////////////////////////////////////////////////////////////////
	//
	// GENERATE ABOUT AND CACHE HTML
	//
	////////////////////////////////////////////////////////////////////////////////
	
	public function about_html() {
	
		global $fs_schema;
	
		$settings = $fs_schema->data->settings();
		
		$output = '<div class="about">Schema ' . $this->version . ' av <a href="http://klasehnemark.com">Klas Ehnemark</a>, kopplat till ';
		
		//$output = '<pre class="about">FS-Schema ' . $this->version . ', kopplat till ';
		
		switch ( $settings[ 'fs_schema_integration' ] ) {
		
			default:
			case 'BRP':
			
				$output .= '<a href="http://www.brpsystems.se">BRP</a>. ';
				break;
				
			case 'PROFIT':
			
				$output .= '<a href="http://www.pastelldata.se">Profit</a>. ';
				break;	
		}
		
		$output .= '</div>';
		
		if ( $settings['fs_schema_show_debug'] == 'YES' )
		
			$output .= '<span class="cache_status">' . $fs_schema->data->last_cache_status . '</span> <span class="show_debug" style="text-decoration: underline; cursor: pointer; ">Visa Debug</span></pre>';
		
		return $output;	
	}

	
	////////////////////////////////////////////////////////////////////////////////
	// Private function: sort_hour_activities
	////////////////////////////////////////////////////////////////////////////////	
	
	static function sort_hour_activities ($a, $b) { return strcmp( $a["_start_minute"], $b["_start_minute"] ); }
	
	

} //End Class
?>