<?php
/*/////////////////////////////////////////////////////////////////

	FS SCHEMA, PHP Class - PUBLIC

	Copyright (C) 2013 Klas Ehnemark (http://klasehnemark.com)
	This program is not free software.

//////////////////////////////////////////////////////////////////*/



class fs_schema_public {

	

	////////////////////////////////////////////////////////////////////////////////
	//
	// INITIALIZE OBJECT
	//
	////////////////////////////////////////////////////////////////////////////////

	public function __construct ( ) {

		// Initialization stuff
		//add_action( 'init', 								array ( $this, 'wordpress_init' ));
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
	
	public function walk_schema ( $date_info, $step, $username = '', $password = '' ) {
	
		if ( $date_info ) {
		
			$r 					= unserialize ( base64_decode ( $date_info ));
			
			$date 				= fs_schema::get_date_from_string ($r['datum'] );
			
			switch ( $r['typ'] ) {
			
				
				case 'vecka':
				
					$r['datum']	= date( 'Y-m-d', mktime( 0, 0, 0, date( "m", $date ), date( "d", $date ) + ( $step * 7 ) , date( "Y", $date )));
					
					break;
				
				default:
				case 'pass':
				
					$r['datum']	= date( 'Y-m-d', mktime( 0, 0, 0, date( "m", $date ), date( "d", $date ) + ( $step ) , date( "Y", $date )));
					
					break;
			
			}
			
		} else {
		
			$r				= array();
			
		}
		
		$r['no_wrapper']		= true;
		
		$r['username']			= $username;
		
		$r['password']			= $password;
		
		return $this->render_schema ( $r );
	
	}


	////////////////////////////////////////////////////////////////////////////////
	//
	// RENDER SCHEMA
	//
	////////////////////////////////////////////////////////////////////////////////

	function render_schema ( $args ) {

		$defaults = array(
			'typ'			=> 'vecka', 		// vecka, pass
			'anlaggning'		=> '',			// id, eller kommaseparerad id
			'datum'			=> '',  			// format: YYYY-MM-DD
			'day_width'		=> '130',
			'bokning'			=> '1',			// 0 = no booking
			'no_wrapper'		=> false,			// true = no wrapper div with fs_schema, used in ajax
			'username'		=> '',
			'password'		=> ''
		);
		
		$r = wp_parse_args( $args, $defaults );
		
		global $fs_schema;
		
		$output 				= '';
		
		//echo fs_schema::debug( $r );
		// get data
		$s = $fs_schema->data->get_schema( $r );
		
		
		if ( $s['error'] != '' ) {
			
			$output 			= '<div class="fs_schema_error">Fel. ' . $s['message'] . '</div>'; // . ' ' . print_r( $r, true );
		
		} else {
		
			//echo '<pre>' . $s['url'] . '</pre>';
		
			switch ( $s['typ'] ) {


				////////////////////////////////////////////////////////////////////////////////
				// VECKA
				////////////////////////////////////////////////////////////////////////////////
			
				case 'vecka':
				
				
					// some declarations
					$weekdays 					= array ( 'ulldag', 'måndag', 'tisdag', 'onsdag', 'torsdag', 'fredag', 'lördag', 'söndag' );
					
					$week_activities 				= array();
					
					$calendar_slots 				= array();
					
					$earliest_hour 				= 10;
					
					$latest_hour 					= 12;
					
					$day_width_px					= $r['day_width'];
					
					$hours_width_px				= 30;
					
					$entry_width_px				=  $day_width_px;
					
					$entry_padding_px				= 4;
					
					$day_header_height_px			= 27;
					
					$hour_height_px				= 75;
					
					$day_height_px					= 40;
					
					$date_week_start 				= mktime( 0, 0, 0, date( "m", $s['start_date '] ), date( "d", $s['start_date '] ) - date( 'N', $s['start_date '] )+1, date( "Y", $s['start_date '] ));
		
					$today_date_array				= getdate();				
					
					$today_date					= mktime( 0, 0, 0, $today_date_array ['mon'], $today_date_array ['mday'],$today_date_array ['year']);
					
					$today_this_hour				= mktime( $today_date_array['hours']+1, 0, 0, $today_date_array ['mon'], $today_date_array ['mday'], $today_date_array ['year']);
					
					$this_hour_num					= date ( 'G', 	$today_this_hour );	
					
					$today_week 					= date ( 'W', $today_date );
					
					$this_week					= date ( 'W', $date_week_start );
					
					$this_year					= date ( 'Y', $date_week_start );
					
					$r['datum']					= date ( 'Y-m-d', $s['start_date ']);
					
					//echo '$this_hour_num: ' . $this_hour_num;
					// build the class name based on settings
					$class_name					= 'fs_schema week ';
					
					if ( $r['no_wrapper'] === false ) {
						
						$output 					.= '<div class="' . $class_name . '" data-day-width="' . $day_width_px . '" data-hours-width="' . $hours_width_px . '" >';
						
						$output					.=  '<div class="navigation"><div class="fs_button previous">&lt;&nbsp;Föregående vecka</div><div class="login_info fs_button">Logga in...</div><div class="fs_button next">Nästa vecka&nbsp;&gt;</div></div>';
						
						$output					.=  '<div class="weeks">';
					
					}
					
					$output 						.= '<div class="days" data-date-info="' . base64_encode (serialize (  $r ) ) . '" 
													data-week="v' . $this_week . ' ' . $this_year . '" data-cache-status="' . $fs_schema->data->last_cache_status . '">';
					

					// loop thru every schema entry and store the entry in the right position of a multidimensional array
					foreach ( $s['schema'] as $entry ) {
						
						$start_time 				= fs_schema::get_date_from_string ( $entry['startdatetime'] );
						
						$end_time 				= fs_schema::get_date_from_string ( $entry['enddatetime'] );
						
						$start_day 				= (int) date( 'N', $start_time );
						
						$start_hour 				= (int) date( 'H', $start_time );
						
						$end_hour 				= (int) date( 'H', $end_time );
						
						$before_now				= $start_time < $today_this_hour; 
						
						if ( $start_hour < $earliest_hour ) 	$earliest_hour = $start_hour;
						
						if ( $end_hour > $latest_hour ) 		$latest_hour = $end_hour;
						
						//echo '$start_day:' . $start_day . ', $start_hour:' . $start_hour . ', products:' . print_r( $entry['products'], true) . '<br>';
						
						if ( $start_day > 0 && $start_day <=7 && $start_hour > 0 && $start_hour <= 24 ) {
						
							$entry['_start_hour'] 	= $start_hour;
							
							$entry['_end_hour'] 	= $end_hour;
							
							$entry['_before_now']	= $before_now;
							
							
							// set number of entries that occupy the same hours
							$entry['_hpos']		= 1;
							
							for ( $w = $start_hour; $w <= $end_hour; $w++ ) {
							
								if ( isset ( $calendar_slots[$start_day][$w] )) $calendar_slots[$start_day][$w]++;
								
								else $calendar_slots[$start_day][$w] = 1;
								
								if ( $calendar_slots[$start_day][$w] > $entry['_hpos'] ) $entry['_hpos'] = $calendar_slots[$start_day][$w];
							
							}
						
							if ( isset ( $week_activities[$start_day][$start_hour] )) array_push( $week_activities[$start_day][$start_hour], $entry );
								
							else $week_activities[$start_day][$start_hour] = array ( $entry );
							

								
						}
						
					}
					
					
					// loop thry every day in the week
					for ( $d = 1; $d <= 7; $d++) {
					
						$day_date 	= mktime( 0, 0, 0, date ( "m", $date_week_start  ), date( "d", $date_week_start ) + $d - 1 , date( "Y", $date_week_start ));
						
						$today_class	= $today_date == $day_date ? ' today' : '';
					
						$output 		.= '<div class="day day' . $d . ' ' . $today_class . '" data-day="' . $d . '" style="height: ' . (( ( $latest_hour - $earliest_hour + 1 ) * ( $hour_height_px + 5 ) ) + $day_header_height_px ). 'px; width: ' . $day_width_px . 'px; "><div class="head">' . $weekdays[ $d ] . ' ' . date('j/n', $day_date) . '</div>';
						
						$hours_field	= '';
						
						$entries_field = '';
						

						// loop thru every hour in the day
						for ( $h = $earliest_hour; $h <= $latest_hour; $h++ ) {
						
							$this_hour_class	= $this_hour_num == $h && $today_week == $this_week ? ' this_hour' : '' ;
						
							$hours_field 		.= '<div class="clock" style="height: ' . $hour_height_px . 'px; width: ' . ( $day_width_px - 2 ) . 'px; "><div class="day_clock' . $this_hour_class . '">' . $h . ':00' . '</div></div>'; 
						
							if ( isset( $week_activities[$d][$h] ) && is_array( $week_activities[$d][$h] )) {
							
								foreach ( $week_activities[$d][$h] as $entry ) {
								
								
									// entry data
									$entry_class 				= $products = '';
									
									foreach ( $entry['products']  as $product ) { $products .= $product['name'] . ' '; }
									
									$entry_data 				= ' data-id="' . $entry['id'] . '" data-start="' . $entry['starttime'] . '" data-end="' . $entry['endtime'] . '" data-product="' . $products . '"';
									
									$entry_data 			    .= ' data-staff="' . $entry['staff'] . '" data-room="' . $entry['room'] . '" data-freeslots="' . $entry['freeslots'] . '"';
									
									$entry_data 			    .= ' data-bookableslots="' . $entry['bookableslots'] . '" data-startdate="' . $weekdays[ $d ] . ' ' . date('j/n Y', $day_date)  . '"';
									
									$entry_data 			    .= ' data-bookingid="' . $entry['bookingid'] . '" ';
									
									
									// calculate entry height based on duration
									$entry_height 				= ' height: ' . ((( $entry['_end_hour'] - $entry['_start_hour'] ) * $hour_height_px ) - $entry_padding_px ) . 'px;';
								
									
									
									// calculate entry width based on how many entries occupy the same time slot
									$entry_width 				= ' width: ' . ( $entry_width_px - 8 ) . 'px; ';
									
									$entry_left 				= '';
									
									$max_same_hour 			= 1;
									
									for ( $w = $entry['_start_hour']; $w <= $entry['_end_hour']; $w++ ) {
									
										if ( $calendar_slots[$d][$w] > $max_same_hour ) $max_same_hour = $calendar_slots[$d][$w];
									
									}
									
									if ( $max_same_hour > 1 ) {
									
										$entry_width 			= ' width: ' . ( ( $entry_width_px - ( $entry_padding_px * 4 ) ) / $max_same_hour ) . 'px;';
										
										$entry_class			.= ' multiple';

									}
									
									
									// calculate entry position from left, based on number of entries on the same time slot and this entries hpos
									if ( $max_same_hour > 1 && $entry['_hpos'] > 1 ) {	
										
										$entry_left 			= ' left: ' . ( ( $entry['_hpos'] - 1 ) * ( ( $entry_width_px + $entry_padding_px ) / $max_same_hour )) . 'px;';
								
									}
									
									
									
									// calculate entry position from top, based on what time it starts
									$entry_top				= '';
									
									if ( $h > $earliest_hour ) 	$entry_top = 'top: ' . ( ( $h - $earliest_hour ) * ( $hour_height_px + 6	 ) ) . 'px;';
									
									
									// is this entry before now, add a before now class
									if ( $entry['_before_now'] === true ) {
									
										$entry_class			.= ' before_now';
										$tooltip				= ' title="Den här händelsen har redan varit" ';
									
									} else {
									
										$entry_class			.= ' openable';
										$tooltip				= '';
									}
									
									
									// if this entry is booked by the logged in user
									if ( $entry['bookingid'] != '' ) {
									
										$entry_class			.= ' booked';
										
									}
									
									
									// create the entry html object
									$entries_field .= '<div class="entry' . $entry_class . '" style="' . $entry_top . $entry_left . $entry_width . $entry_height . '"' . $entry_data . $tooltip . '>';
									
									$entries_field .= '<div class="time">' . $entry['starttime'] . '-' . $entry['endtime'] . '</div>';
									
									$entries_field .= '<div class="product">' . $products . '</div>';
									
									if ( $entry['staff'] != '' ) $entries_field .= ' med ' . $entry['staff'];
									
									if ( $entry['room'] != '' ) $entries_field .= ' i ' . $entry['room'];
									
									$entries_field .= '</div>';
									
								}
							}
						}
						
						$output 	.= '<div class="hours" style="width: ' . ( $day_width_px) . 'px; ">' . $hours_field . '</div><div class="entries">' . $entries_field . '</div></div>';
					}
					
					$output 		.= '<div class="week_debug">' . $fs_schema->data->debug . '</div>';
					 
					$output 		.= '<div class="clearfix"></div></div></div>';
					
					if ( $r['no_wrapper'] === false ) {
					
						$output 		.= '<div class="navigation"><div class="fs_button previous">&lt;&nbsp;Föregående vecka</div><div class="fs_button next">Nästa vecka&nbsp;&gt;</div></div>';
						
						$output 		.= $this->about_html();
						
						$output 		.= '<pre class="debug"></pre>';
						
						$output 		.= '<div class="week_overlay">v52, 2013</div>';
						
						$output 		.= '<div class="week_progress"><img src="' . plugins_url('fs-schema') . '/files/fs-schema-progress.gif" /></div>';
						
						$output 		.= '<div class="login dialogue">
											<div class="header">Logga in</div>
											<div class="loggedin"></div>
											<div class="loginform">
												<div class="username">Användarnamn:<span><input type="text" value="klas@ehnemark.com" /></span></div>
												<div class="password">Lösenord:<span><input type="password" value="gurka7394" /></span></div>
												<div class="save_me_cookie"><label for="save_me"><input type="checkbox" id="save_me" disabled> Förbli inloggad på den här datorn</label></div>
											</div>
											<div class="buttons">
												<div class="fs_button close_login_form">Avbryt</div>
												<div class="fs_button login_btn">Logga in</div>
												<div class="fs_button logout_btn">Logga ut</div>
											</div>
											<div class="message"></div>
											<div class="progress"><img src="' . plugins_url('fs-schema') . '/files/fs-schema-progress.gif" /><div class="doingwhat">Loggar in...</div></div>
											<div class="big_message"><div class="head">Fel.</div><div class="info">Meddelande</div><div class="fs_button close_btn">OK</div></div>
									    </div>
										';
						
						$output 		.= '<div class="open_event dialogue">
										<div class="header">Rubrik</div>
										<div class="date">Datum:<span>x</span></div>
										<div class="time">Tid:<span>x</span></div>
										<div class="room">Lokal:<span>x</span></div>
										<div class="staff">Personal:<span>x</span></div>
										<div class="freeslots">Lediga platser:<span>x</span></div>';
										
						if ( $r['bokning'] == '1' ) {				
						
							$output 	 .= '<div class="loggedin"></div>
										<div class="booked_info">Du är inbokad på denna aktivitet.</div>
										<div class="loginform">
											<div class="username">Användarnamn:<span><input type="text" value="klas@ehnemark.com" /></span></div>
											<div class="password">Lösenord:<span><input type="password" value="gurka7394" /></span></div>
										</div>
										<div class="buttons">
											<div class="fs_button logout">Logga ut</div>
											<div class="fs_button book_event">Boka</div>
											<div class="fs_button unbook_event">Avboka</div>
											<div class="fs_button login_book_event">Logga in och boka</div>
											<div class="fs_button close_open_event">Stäng</div>
										</div>';
										
						} else {
						
							$output 	.= ' <div class="no_booking">Schemat visas utan möjlighet till bokning.</div>
										<div class="buttons">
											<div class="fs_button close_open_event">Stäng</div>
										</div>';
						}
						
						$output 		.= '	<img src="' . plugins_url('fs-schema') . '/files/fs-schema-close-button.png" class="close_open_event close" />
										<div class="message"></div>
										<div class="progress"><img src="' . plugins_url('fs-schema') . '/files/fs-schema-progress.gif" /><div class="doingwhat">Loggar in...</div></div>
										<div class="big_message"><div class="head">Fel.</div><div class="info">Meddelande</div><div class="fs_button close_btn">OK</div></div>
									</div>';
									
					
						$output 	.= '</div>';
						
					}
					
					break;
					
					
				////////////////////////////////////////////////////////////////////////////////
				// PASS		
				////////////////////////////////////////////////////////////////////////////////
								
				case 'pass':
				
					foreach ( $s['schema'] as $entry ) {
					
						$resources = '';
						foreach ( $entry['resources'] as $resource ) {
						
							$resources .= '<span id="fs_schema_resource_' . $resource['id'] . '">' . $resource['name'] . '</span>';
						}
						
						$products = '';
						foreach ( $entry['products'] as $product ) {
						
							$products .= '<span id="fs_schema_product_' . $product['id'] . '">' . $product['name'] . '</span>';
						}
						
						$output .= '<tr id="fs_schema_' .  $entry['id'] . '">';
						
						$output .= '<td>' . $entry['starttime'] . '-' . $entry['endtime'] . '</td>';
						
						$output .= '<td>' . $entry['businessunit']  . '</td>';
						
						$output .= '<td>' . $products . '</td>';
						
						$output .= '<td>' . $resources . '</td>';
						
						$output .= '<td>' . $entry['freeslots'] . ' (' . $entry['bookableslots'] . ')</td>';
						
						$output .= '<td>' . $entry['bookingid'] . '</td>';
						
						$output .= '</tr>';
						
					}
					
					$output = '<table>' . $output . '</table>';	
				
					break;
					
				}
			}
		
		return $output; 
	
	}
	




	////////////////////////////////////////////////////////////////////////////////
	//
	// BOOK ACTIVITY
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function book_activity ( $username, $password, $activity_id ) {

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
		
			$return = $fs_schema->data->book_activity ( $username, $password, $activity_id );
			
		}
		
		return $return;

	}
	


	////////////////////////////////////////////////////////////////////////////////
	//
	// BOOK ACTIVITY
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public function unbook_activity ( $username, $password, $bookingid ) {

		global $fs_schema;
		
		$settings = $fs_schema->data->settings();
		
		$return = array( 'error' => '', 'message' => '', 'debug' => '' );
		
		if ( $username == '' || $password == '' ) {
		
			$return['error'] 		= 'YES';
			
			$return['message'] 		= 'Användarnamn och eller lösenord saknas.';
			
		} else if ( $bookingid == '' ) {
		
			$return['error'] 		= 'YES';
			
			$return['message'] 		= 'Information om bokningen saknas.';
			
		} else {
		
			$return = $fs_schema->data->unbook_activity ( $username, $password, $bookingid );
			
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
		
		$output = '<pre class="about">Schema av <a href="http://klasehnemark.com">Klas Ehnemark</a>, kopplat till ';
		
		switch ( $settings[ 'fs_schema_integration' ] ) {
		
			default:
			case 'BRP':
			
				$output .= '<a href="http://www.brp.se">BRP</a>. ';
				break;
				
			case 'PROFIT':
			
				$output .= '<a href="http://www.pastelldata.se">Profit</a>. ';
				break;	
		}
		
		$output .= '<span class="cache_status">' . $fs_schema->data->last_cache_status . '</span>. <span class="show_debug" style="text-decoration: underline; cursor: pointer; ">Visa Debug</span></pre>';
		
		return $output;	
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	// Private function: filter array
	////////////////////////////////////////////////////////////////////////////////
	
	private function filter_array_by_value ( $array, $index, $value ){
	
		if( is_array( $array ) && count( $array ) > 0 ) {
		
			foreach( array_keys($array) as $key ){
			
				$temp[$key] = $array[$key][$index];
				
				if ($temp[$key] == $value) {
				
					$newarray[ $key ] = $array[ $key ];

				}
			}
		}
		
		return $newarray;
	} 
} //End Class
?>