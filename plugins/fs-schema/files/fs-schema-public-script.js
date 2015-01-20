/*/////////////////////////////////////////////////////////////////

	FS SCHEMA, Javascript for public pages

	Copyright (C) 2013-2014 Klas Ehnemark (http://klasehnemark.com)
	This program is not free software.

//////////////////////////////////////////////////////////////////*/


fs_schema_public = {

	username : '',
	
	password : '',
	
	session_key : '',
	
	responsive : '',
	
	clear_creds : true, 
	
	schema_width : 0,
	
	day_width : 0,
	
	hours_width : 0,
	
	open_event_el : false,
	
	num_days : 7,
	
	open_event_height : 300,
	
	open_event_width : 300,
	
	dialogue_message_timeout : null,
	
	is_busy : false,
	
	open_event_id : 0,
	
	open_bookingid : 0,
	
	animate_source_on_close_event : false,
	
	close_event_after_big_message : true,
	
	add_source_class_on_close_event : '',
	
	refresh_on_close_event : false,
	
	remove_source_class_on_close_event : '',
	
	fallback_url : '',
	
	last_step : 0,
	
	schema_offset_top : 0,
	
	book_event_after_login : false,
	
	book_waitinglist_after_login : false,
	
	enableweek : false,
	
	enableday : false,
	
	forceday : false,
	
	show_my_bookings : false,
	
	after_refresh : function () {},
	
	after_book_event : function () {},
	
	after_close_big_message : function () {},
	
	init : function () { 
	
		fs_schema_public.set_values();
			
		if ( fs_schema_public.check_browser() == true ) {

			jQuery(window).resize( function() { fs_schema_public.set_values(); });
		
			jQuery('.fs_schema .entry.openable').click( function( ev_data ) { fs_schema_public.open_event ( this ); });
			
			jQuery('.fs_schema .close_open_event').click( function() { fs_schema_public.close_event (); });
			
			jQuery('.fs_schema .login_book_event').click( function() { fs_schema_public.login_and_book_event (); });
			
			jQuery('.fs_schema .login_book_waitinglist').click( function() { fs_schema_public.login_book_waitinglist (); });
			
			jQuery('.fs_schema .book_event ').click( function() { fs_schema_public.book_event (); });
			
			jQuery('.fs_schema .book_waitinglist ').click( function() { fs_schema_public.book_waitinglist (); });
			
			jQuery( '.fs_schema .open_event .unbook_event' ).click( function() { fs_schema_public.unbook_event (); });
			
			jQuery( '.fs_schema .open_event .unbook_waitinglist' ).click( function() { fs_schema_public.unbook_event (); });
			
			jQuery('.fs_schema .big_message .close_btn').click( function() { fs_schema_public.close_dialogue_big_message (); });
			
			jQuery('.fs_schema .navigation .previous').click( function() { fs_schema_public.previous (); });
			
			jQuery('.fs_schema .navigation .next').click( function() { fs_schema_public.next (); });
			
			jQuery('.fs_schema .navigation .fs_button.login_info').click( function() { fs_schema_public.show_login (); });
			
			jQuery('.fs_schema .close_login_form').click( function() { fs_schema_public.close_login (); });
			
			jQuery('.fs_schema .login_btn').click( function() { fs_schema_public.login (); });
			
			jQuery('.fs_schema .my_bookings ').click( function() { fs_schema_public.my_bookings (); });
			
			jQuery('.fs_schema .update_schema ').click( function() { fs_schema_public.refresh (); });
			
			jQuery('.fs_schema .logout_btn ').click( function() { fs_schema_public.logout (); });
			
			jQuery('.fs_schema .change_to_day').click( function() { fs_schema_public.change_to_day (); });
			
			jQuery('.fs_schema .change_to_week').click( function() { fs_schema_public.change_to_week (); });
			
			jQuery( '.fs_schema .debug').html( jQuery( '.fs_schema .week_debug' ).html() );
			
			jQuery('.fs_schema .show_debug').click( function() { jQuery( '.fs_schema .debug').show(); jQuery('.fs_schema .show_debug').hide(); });
			
			jQuery('.fs_schema .schema_print').click( function() { fs_schema_public.print(); });
			
			fs_schema_public.show_hud( jQuery('.fs_schema .days').attr('data-week') );
			
			jQuery(document).keydown( function(e) { fs_schema_public.key_down ( e.keyCode ); });
			
			fs_schema_public.log_in_by_cookie();
			
		}
	},
	
	print : function () {
	
		window.print();
	
		/*jQuery('.fs_schema .big_message .close_btn').html('Skriv ut').css('left', '100px');
		
		fs_schema_public.after_close_big_message = function() {
		
			window.print();
			
			jQuery('.fs_schema .big_message .close_btn').html('OK').css('left', '110px');;
		
		};
	
		fs_schema_public.display_dialogue_big_message ( 'Utskrift', 'Det bästa resultatet vid utskrift får du om du via skrivarinställningarna väljer att anpassa schemat storlek, så att hela schemat skrivs ut på en enda sida.', false);*/
	},
	
	
	key_down : function ( key_code ) {
	
		 // ESCAPE key pressed
		if ( key_code == 27 ) {
		
			if ( jQuery( '.fs_schema .open_event' ).length > 0 ) {
		
				fs_schema_public.close_event();
		
			}
		}
	},
	
	
	change_to_day : function () {
	
		if ( fs_schema_public.num_days == 7 && fs_schema_public.enableday == true && jQuery('.fs_schema .change_to_day:not(.disabled) ').length > 0 ) {
		
			fs_schema_public.show_hud ( 'Växlar till lista...' );
			
			fs_schema_public.num_days = 1;
			
			fs_schema_public.after_refresh = function() {
			
				jQuery('.fs_schema').removeClass('week').addClass('day');
			
				jQuery('.fs_button.change_to_week').css('display', 'inline');
			
				jQuery('.fs_button.change_to_day').css('display', 'none'); 
				
				jQuery('.fs_button.previous').html('&lt;&nbsp;Föregående <span>dag</span>');
				
				jQuery('.fs_button.next').html('Nästa <span>dag</span>&nbsp;&gt;');
			}
			
			fs_schema_public.refresh();
		}
	},
	
	
	change_to_week : function () {
	
		if ( fs_schema_public.num_days == 1 && fs_schema_public.enableweek == true && jQuery('.fs_schema .change_to_week:not(.disabled) ').length > 0 ) {
	
			fs_schema_public.show_hud ( 'Växlar till schema...' );
			
			fs_schema_public.num_days = 7;
			
			fs_schema_public.after_refresh = function() {
			
				jQuery('.fs_schema').removeClass('day').addClass('week');
			
				jQuery('.fs_button.change_to_week').css('display', 'none');
			
				jQuery('.fs_button.change_to_day').css('display', 'inline'); 
				
				jQuery('.fs_button.previous').html('&lt;&nbsp;Föregående <span>vecka</span>');
				
				jQuery('.fs_button.next').html('Nästa <span>vecka</span>&nbsp;&gt;');
			}
			
			fs_schema_public.refresh();
		}
	},
	
	
	show_login : function () {
	
		if (  fs_schema_public.is_busy == false ) {
		
			if ( fs_schema_public.clear_creds == true ) {
			
				jQuery( '.fs_schema .login.dialogue .loginform .username input' ).val('');
			
				jQuery( '.fs_schema .login.dialogue .loginform .password input' ).val('');
			}
		
			jQuery('.fs_schema .dialogue.open').hide().removeClass('open');
			
			jQuery('.fs_schema .login.dialogue')
			
				.css('left',  ( fs_schema_public.schema_width / 2 ) - ( fs_schema_public.open_event_width / 2 ))
				
				.css('display', 'block')
				
				.addClass('open')
				
				.css( '-webkit-transform', 'scale(1.1)').css( '-moz-transform', 'scale(1.1)').css( '-ms-transform', 'scale(1.1)').css( 'transform', 'scale(1.1)');
				
			window.setTimeout(function() {
			
				jQuery( '.fs_schema .login' ).css( '-webkit-transform', 'scale(1)').css( '-moz-transform', 'scale(1)').css( '-ms-transform', 'scale(1)').css( 'transform', 'scale(1)');
				
				jQuery( '.fs_schema .login.dialogue .loginform .username input' ).focus();
				
			}, 100);
			
			fs_schema_public.book_event_after_login = false;
			
			fs_schema_public.book_waitinglist_after_login = false;
			
		}
	},
	
	
	
	close_login : function () {
	
		if ( jQuery( '.fs_schema .login' ).is(":visible") ) {
	
			jQuery('.fs_schema .login').css( '-webkit-transform', 'scale(1.05)').css( '-moz-transform', 'scale(1.05)').css( '-ms-transform', 'scale(1.05)').css( 'transform', 'scale(1.05)');
				
			window.setTimeout(function() { 
				jQuery( '.fs_schema .login' ).css( '-webkit-transform', 'scale(1)').css( '-moz-transform', 'scale(1)').css( '-ms-transform', 'scale(1)').css( 'transform', 'scale(1)');
			}, 100);
				
			window.setTimeout(function() {
				jQuery( '.fs_schema .login' ).css( 'display', 'none');
			}, 200);
			
		}
	},
	
	

	
	login : function ( username, password ) {
	
		username_ = jQuery('.fs_schema .dialogue.open .username input');
		
		password_ = jQuery('.fs_schema .dialogue.open .password input');
		
		if (!username || !password ) {
	
			username = jQuery( username_ ).val();
			
			password = jQuery( password_ ).val();
			
		}
		
		jQuery( username_, password_ ).removeClass('error');
		
		if ( username == '' && password == '' ) {
		
			fs_schema_public.display_dialogue_message ( 'Fyll i ditt användarnamn och lösenord.', true );
			
			jQuery( username_, password_ ).addClass('error');
			
		} else if ( username == '' ) {
		
			fs_schema_public.display_dialogue_message ( 'Användarnamnet får inte vara tomt.', true );
			
			jQuery( username_ ).addClass('error');		
		
		} else if ( password == '' ) {
		
			fs_schema_public.display_dialogue_message ( 'Lösenordet får inte vara tomt.', true );
			
			jQuery( password_ ).addClass('error');		
		
		} else { 
		
			window.clearTimeout( fs_schema_public.dialogue_message_timeout );
			
			jQuery( '.fs_schema .dialogue.open .message' ).css( 'display', 'none' );

			jQuery( '.fs_schema .dialogue.open .progress .doingwhat').html('Loggar in...');
			
			jQuery( '.fs_schema .dialogue.open .progress').css( 'display', 'block' );
			
			fs_schema_public.is_busy = true;
			
			fs_schema_public.ajax (
			
				{ action : 'login', username: username, password: password }, 'json', function ( data ) {	
				
					//jQuery( '.fs_schema .debug').html( data );
					
					jQuery( '.fs_schema .dialogue.open .progress').css( 'display', 'none' );
					
					if ( data.error != '' ) {
					
						fs_schema_public.close_event_after_big_message = true;
					
						fs_schema_public.display_dialogue_big_message ( 'Fel.', data.message, true );
						
						
						// remove any cookie with login
						fs_schema_public.erase_cookie('un');
						
						fs_schema_public.erase_cookie('pw');
						
					
						fs_schema_public.is_busy = false;
					
					} else {
								
						fs_schema_public.username = username;
						
						fs_schema_public.password = password;
								
						fs_schema_public.personid = data.personid;
						
						fs_schema_public.session_key = data.session_key;
						
						fs_schema_public.forceday = data.forceday;
						
						
						// set cookie with login
						fs_schema_public.create_cookie('un', username, 365);
						
						fs_schema_public.create_cookie('pw', password, 365);
						
					
						fs_schema_public.close_login();
						
						window.setTimeout(function() {
						
							jQuery('.fs_schema .login_info.fs_button').html ('<span>Inloggad som </span>' + data.name );
							
							jQuery('.fs_schema .login_info.fs_button').addClass('green');
							
							jQuery('.fs_schema .loggedin').html ( 'Du är inloggad som ' + data.name + '.').css( 'display', 'block' );
							
							jQuery('.fs_schema .loginform ').css( 'display', 'none' );
							
							jQuery('.fs_schema .login_btn ').css( 'display', 'none' );
							
							jQuery('.fs_schema .login_book_event ').css( 'display', 'none' );
							
							jQuery('.fs_schema .logout_btn ').css( 'display', 'inline' );
							
							jQuery('.fs_schema .book_event ').css( 'display', 'inline' );
							
							jQuery('.fs_schema .login.dialogue .header').html('Logga ut');
							
							jQuery('.fs_schema .my_bookings').css( 'display', 'inline' );
							
							jQuery('.fs_schema .update_schema').css( 'display', 'inline' );
							
							jQuery('.fs_schema .navigation.above_schema').addClass( 'logged_in' );
							
							jQuery('.fs_schema ').addClass( 'logged_in' );
							
						}, 500);
						
						if ( fs_schema_public.book_event_after_login == true ) {
						
							if ( data.forceday && fs_schema_public.num_days == 7 ) { fs_schema_public.after_book_event = function() { fs_schema_public.disable_change_to_week(true); };
								
							} else { fs_schema_public.after_book_event = function() { fs_schema_public.refresh(); }; }
						
							fs_schema_public.book_event();
							
						} else if ( fs_schema_public.book_waitinglist_after_login == true ) {
						
							if ( data.forceday && fs_schema_public.num_days == 7 ) { fs_schema_public.after_book_event = function() { fs_schema_public.disable_change_to_week(true); };
								
							} else { fs_schema_public.after_book_event = function() { fs_schema_public.refresh(); }; }
						
							fs_schema_public.book_waitinglist();
							
						} else {
					
							fs_schema_public.is_busy = false;
							
							if ( data.forceday && fs_schema_public.num_days == 7 ) {  fs_schema_public.disable_change_to_week(true);
								
							} else { fs_schema_public.refresh(); }
						}
						
						if ( fs_schema_public.book_event_after_login == false && fs_schema_public.book_waitinglist_after_login == false ) fs_schema_public.show_hud ( 'Inloggad' );
					}
					
					jQuery( '.fs_schema .debug').html( data.debug );
			
				});
		
		}
	},
	
	
	disable_change_to_week : function ( change_to_day ) {
	
		if ( change_to_day === true ) { 
		
			fs_schema_public.change_to_day();
			
			jQuery('.fs_schema .change_to_week').css('display','inline').addClass('disabled');
		}
		
		jQuery('.fs_schema .message_above_schema').css('display','block').html('Du som är inloggad kan tyvärr inte se veckoschemat. Det beror på begränsningar i bokningsssytemet. Du kan <span class="logout_text">logga ut</span> för att se veckoschemat eller stega fram dag för dag.');

		jQuery('.fs_schema .logout_text ').click( function() { fs_schema_public.logout (); });
	},
	
	
	log_in_by_cookie : function () {
	
		var username = fs_schema_public.read_cookie('un');
		
		var password = fs_schema_public.read_cookie('pw');
		
		if ( username && password && username != '' && password != '' && username != 'undefined' && password != 'undefined' ) {
			
			fs_schema_public.login ( username, password );
		}
	},
	
	
	
	logout : function () {
				
		fs_schema_public.username = '';
		
		fs_schema_public.password = '';
		
		fs_schema_public.personid = 0;
		
		fs_schema_public.refresh();
		
		fs_schema_public.erase_cookie('un');
		
		fs_schema_public.erase_cookie('pw');
	
		fs_schema_public.close_login();
		
		fs_schema_public.show_hud ( 'Utloggad' );
		
		jQuery('.fs_schema .login_info.fs_button').html ( 'Logga in...' );
		
		jQuery('.fs_schema .login_info.fs_button').removeClass('green');
		
		jQuery('.fs_schema .loggedin').html ( '' ).css( 'display', 'none' );
		
		jQuery('.fs_schema .loginform ').css( 'display', 'block' );
		
		jQuery('.fs_schema .login_btn ').css( 'display', 'inline' );
					
		jQuery('.fs_schema .login_book_event ').css( 'display', 'inline' );
		
		jQuery('.fs_schema .logout_btn ').css( 'display', 'none' );
					
		jQuery('.fs_schema .book_event ').css( 'display', 'none' );
			
		jQuery('.message_above_schema').css('display', 'none'); 
		
		jQuery('.fs_schema .login.dialogue .header').html('Logga in');
		
		jQuery('.fs_schema .change_to_week').css('display','inline').removeClass('disabled');
		
		jQuery('.fs_schema .my_bookings').css( 'display', 'none' );
							
		jQuery('.fs_schema .update_schema').css( 'display', 'none' );
							
		jQuery('.fs_schema .navigation.above_schema').removeClass( 'logged_in' );
		
		jQuery('.fs_schema ').removeClass( 'logged_in' );
		
		if ( fs_schema_public.show_my_bookings === true ) fs_schema_public.close_my_bookings();
	},
	
	
	
	show_hud : function ( week_info ) {
	
		jQuery('.fs_schema .week_overlay').html( week_info );
		
		jQuery('.fs_schema .week_overlay').fadeIn(200).delay(1000).fadeOut(500);

	},
	
	
	
	previous : function () {
	
		fs_schema_public.walk ( -1 );
		
	},
	
	
	
	next : function () {
	
		fs_schema_public.walk ( 1 );
		
	},
	
		
	refresh : function () {
	
		if (  fs_schema_public.is_busy == false ) {
		
			if ( fs_schema_public.show_my_bookings === true ) fs_schema_public.my_bookings();
			
			else {
		
				jQuery('.fs_schema .week_progress').css('display', 'block');
			
				date_info = jQuery('.fs_schema .days').attr('data-date-info');
				
				fs_schema_public.ajax (
				
					{ action : 'walk_schema', num_days: fs_schema_public.num_days, date_info : date_info, step: 0, username: fs_schema_public.username, password: fs_schema_public.password, session_key: fs_schema_public.session_key }, 'html', function ( data ) {
					
						jQuery( '.fs_schema .weeks' ).html(data);
						
						jQuery('.fs_schema .week_progress').css('display', 'none');
						
						fs_schema_public.after_refresh();
						
						jQuery('.fs_schema .entry.openable').click( function( ev_data ) { fs_schema_public.open_event ( this ); });
						
						fs_schema_public.after_refresh = function() {};
						
						jQuery( '.fs_schema .debug').html( jQuery( '.fs_schema .week_debug' ).html() );
					});
			}
		}
	},
	

	walk : function ( step ) {
	
		if (  fs_schema_public.is_busy == false ) {
		
			fs_schema_public.close_event();
		
			jQuery('.fs_schema .week_progress').css('display', 'block');
		
			fs_schema_public.last_step = step;
		
			date_info = jQuery('.fs_schema .days').attr('data-date-info');
			
			fs_schema_public.ajax (
			
				{ action : 'walk_schema', num_days: fs_schema_public.num_days, date_info : date_info, step: step, username: fs_schema_public.username, password: fs_schema_public.password, session_key: fs_schema_public.session_key }, 'html', function ( data ) {
				
					weeks = jQuery( '.fs_schema .weeks' );
					
					current_days = jQuery( '.fs_schema .weeks .days' );
					
					new_days = jQuery( data );
					
					if ( jQuery( '.fs_schema.day' ).length == 0 ) fs_schema_public.show_hud( jQuery( '.days',  jQuery( '<div>' + data + '</div>' ) ).attr('data-week') );
	
					weeks_width = fs_schema_public.weeks_width = jQuery( weeks ).outerWidth(true);
			
					weeks_height = fs_schema_public.weeks_height = jQuery( weeks ).outerHeight(true);
					
					jQuery( weeks ).css ( 'width', weeks_width ).css ( 'height', weeks_height ).css ( 'overflow', 'hidden' );
					
					jQuery( current_days ).wrap( '<div class="week_animation" style="position: absolute; width: ' + ( weeks_width * 2 ) + 'px; " />' );
					
					week_animation = jQuery( '.fs_schema .weeks .days' ).parent();
				
					jQuery( new_days ).css( 'position', 'absolute' ).css( 'top', '0px' );
	
					if ( fs_schema_public.last_step == 1 ) {
						
						jQuery( week_animation ).append( new_days );
						
						animate_to = -weeks_width;
					
						jQuery( new_days ).css( 'left', weeks_width + 'px' );
						
					} else {
					
						jQuery( week_animation ).append( new_days );
						
						animate_to = 0;
						
						jQuery( current_days ).css( 'left', weeks_width + 'px' );
						
						jQuery( new_days ).css( 'left', '0px' );
						
						jQuery( week_animation ).css( 'left', -weeks_width + 'px' );
						
					}
					
					jQuery('.fs_schema .week_progress').css('display', 'none');
					
					jQuery( new_days ).css( 'position', 'absolute' ).css( 'top', '0px' );
					
					jQuery( week_animation ).animate({
					
						left: animate_to
						
						}, 700, function() {
						
							jQuery( current_days ).remove();	
							
							jQuery( new_days ).css( 'position', 'relative' );	
							
							jQuery( weeks ).html( data ).css( 'width', 'inherit' ).css( 'height', 'inherit' ).css( 'overflow', 'inherit' );
							
							jQuery('.fs_schema .entry.openable').click( function( ev_data ) { fs_schema_public.open_event ( this ); });
							
							jQuery( '.fs_schema .cache_status').html( jQuery( new_days ).attr( 'data-cache-status' ) );
							
							jQuery( '.fs_schema .debug').html( jQuery( '.fs_schema .week_debug' ).html() );
	
						});	
				}
			);
		}
	},

	
	my_bookings : function () {
			
		if (  fs_schema_public.is_busy == false ) {
		
			// show my bookings
			
			if ( fs_schema_public.show_my_bookings === false ) { 
			
				fs_schema_public.show_my_bookings = true;
			
				jQuery('.fs_schema .change_to_day').css('display', 'none');
			
				jQuery('.message_above_schema').css('display', 'none'); 
				
				jQuery('.fs_schema .my_bookings').html('Visa schemat');
			
				jQuery('.fs_schema .week_progress').css('display', 'block');
				
				fs_schema_public.ajax (
			
					{ action : 'walk_schema', num_days: 0, date_info : date_info, step: 0, username: fs_schema_public.username, password: fs_schema_public.password, session_key: fs_schema_public.session_key }, 'html', function ( data ) {
					
						jQuery( '.fs_schema .weeks' ).html(data);
						
						jQuery('.fs_schema .week_progress').css('display', 'none');
						
						jQuery('.fs_schema').removeClass('week').removeClass('day').addClass('bookings');
					
						jQuery('.fs_button.change_to_week').css('display', 'none');
					
						jQuery('.fs_button.change_to_day').css('display', 'none'); 
						
						jQuery('.fs_button.previous').css('display', 'none'); 
						
						jQuery('.fs_button.next').css('display', 'none'); 
				
						jQuery('.fs_schema .entry.openable').click( function( ev_data ) { fs_schema_public.open_event ( this ); });
						
						jQuery( '.fs_schema .debug').html( jQuery( '.fs_schema .week_debug' ).html() );
					});
					
			
			// close my bookings and show schema again
			
			} else {
			
				fs_schema_public.close_my_bookings();	
			}
		}
	},
	
	
	close_my_bookings : function () {
	
		fs_schema_public.show_my_bookings = false;
				 
		jQuery('.fs_button.change_to_day').css('display', 'none');
		
		jQuery('.fs_button.change_to_week').css('display', 'none');
		
		jQuery('.fs_schema .my_bookings').html('Mina bokningar');
		
		jQuery('.fs_schema').removeClass('bookings');
				
		jQuery('.fs_button.previous').css('display', 'inline'); 
				
		jQuery('.fs_button.next').css('display', 'inline'); 
		
		if ( fs_schema_public.num_days == 7 ) {
		
			jQuery('.fs_schema').addClass('week');
			
			jQuery('.fs_button.change_to_day').css('display', 'inline');
			
		} else { 
		
			if ( fs_schema.forceday == true ) fs_schema.disable_change_to_week (false);
		
			jQuery('.fs_schema').addClass('day');
			
			jQuery('.fs_button.change_to_week').css('display', 'inline');
	
		}
		
		fs_schema_public.refresh();
	},
	
	
	
	open_event : function ( event_el ) {
	
		if (  fs_schema_public.is_busy == false ) {
		
			
			// store some variables into the fs_schema object
			
			fs_schema_public.open_event_id = jQuery( event_el ).attr( 'data-id' );
			
			fs_schema_public.open_bookingid = jQuery( event_el ).attr( 'data-bookingid' );
			
			fs_schema_public.open_event_el = event_el;
			
			fs_schema_public.animate_source_on_close_event = false;
			
			fs_schema_public.refresh_on_close_event = false;
			
			fs_schema_public.remove_source_class_on_close_event = '';
						
			fs_schema_public.add_source_class_on_close_event = '';
			
			
			
			// get some variables from DOM
			
			var waitinglistsize =  jQuery( event_el ).attr( 'data-waitinglistsize' );

			var waitinglistposition = jQuery( event_el ).attr( 'data-waitinglistposition' );
			
			var entry_status = jQuery( event_el ).attr( 'data-status' );
			
			var bookableslots = jQuery( event_el ).attr( 'data-bookableslots' );
			
			var dropinslots = jQuery( event_el ).attr( 'data-dropinslots' );
			
			
			
			// calculate event window position and animation
			
			win_top = jQuery(document).scrollTop() - jQuery('.fs_schema').offset().top;
			
			event_el_position_top = jQuery( event_el ).position().top;

			if ( fs_schema_public.responsive != '' ) {
			
				event_target_x = ( fs_schema_public.schema_width / 2 ) - ( fs_schema_public.open_event_width / 2 );
				
				event_target_y = win_top;
			
			} else {
			
				event_target_y = event_el_position_top > 30 ? event_el_position_top - 350 : 0;
			
				event_target_x = ( fs_schema_public.schema_width / 2 ) - ( fs_schema_public.open_event_width / 2 );
				
				if ( win_top > fs_schema_public.schema_offset_top || jQuery(window).height() < 700 ) {
				
					if ( event_target_y < win_top ) event_target_y = win_top;
				
				} else {
				
					if ( event_target_y < win_top + fs_schema_public.schema_offset_top ) event_target_y = win_top + fs_schema_public.schema_offset_top;
				}
			}
			
			event_current_x = jQuery(event_el).offset().left - fs_schema_public.offsetleft; /*( (jQuery( event_el ).parent().parent().attr('data-day') -1 ) * fs_schema_public.day_width ) + jQuery( event_el ).position().left - ( fs_schema_public.day_width / 2);*/
			
			event_current_y = jQuery( event_el ).position().top - 80; // - fs_schema_public.day_width;
			
			event_diff_x = event_target_x-event_current_x;
			
			event_diff_y = event_target_y-event_current_y + 30;
		
			
			// set values into the DOM
			
			jQuery('.fs_schema .dialogue.open').hide().removeClass('open');
			
			if ( fs_schema_public.responsive != '' ) {
			
				jQuery( '.fs_schema .open_event' )
			
				.addClass('open')
			
				.css( 'display', 'block').css('top',  event_target_y ).css('left', event_target_x);
				
			} else {
		
				jQuery( '.fs_schema .open_event' )
				
					.addClass('open')
				
					.css( 'display', 'block').css('top', event_current_y ).css('left', event_current_x )
					
					.css('transition' , 'none').css('-moz-transition' , 'none').css('-webkit-transition' , 'none').css('-o-transition' , 'none').css('-ms-transition' , 'none')
					
					.css( '-webkit-transform', 'scale(0.1)').css( '-moz-transform', 'scale(0.1)').css( 'ms-transform', 'scale(0.1)').css( 'transform', 'scale(0.1)');
			}

			jQuery( '.fs_schema .open_event .header' ).html( jQuery( event_el ).attr( 'data-product' ));
			
			jQuery( '.fs_schema .open_event .date span' ).html( jQuery( event_el ).attr( 'data-startdate' ));
			
			jQuery( '.fs_schema .open_event .time span' ).html( jQuery( event_el ).attr( 'data-start' ) + '&#8211;' + jQuery( event_el ).attr( 'data-end' ));
			
			jQuery( '.fs_schema .open_event .staff span' ).html( jQuery( event_el ).attr( 'data-staff' ));
			
			jQuery( '.fs_schema .open_event .room span' ).html( jQuery( event_el ).attr( 'data-room' ));
			
			jQuery( '.fs_schema .open_event .totalslots span' ).html( jQuery( event_el ).attr( 'data-totalslots' ) );
						
						
				
			// reset all buttons and other visual things
			
			jQuery( '.fs_schema .open_event .book_event' ).css( 'display', 'none' );
			
			jQuery( '.fs_schema .open_event .book_waitinglist' ).css( 'display', 'none' );
			
			jQuery( '.fs_schema .open_event .login_book_event' ).css( 'display', 'none' );
					
			jQuery( '.fs_schema .open_event .login_book_waitinglist' ).css( 'display', 'none' );
		
			jQuery( '.fs_schema .open_event .login_book_event' ).css( 'display', 'none' );
			
			jQuery( '.fs_schema .open_event .unbook_event' ).css( 'display', 'none' );
			
			jQuery( '.fs_schema .open_event .unbook_waitinglist' ).css( 'display', 'none' );
			
			jQuery( '.fs_schema .open_event .unbook_waitinglist' ).css( 'display', 'none' );
			
			jQuery( '.fs_schema .open_event .loginform' ).css( 'display', 'none' );
			
			jQuery( '.fs_schema .open_event .bookableslots' ).css( 'display', 'none' );
			
			jQuery( '.fs_schema .open_event .waitinglist' ).css( 'display', 'none' );
			
			jQuery( '.fs_schema .open_event .dropin' ).css( 'display', 'none' );
					


			// adjust event on what status the event has got
			
			jQuery( '.fs_schema .open_event .entry_info_dropin' ).css( 'display', entry_status == 'dropin' ? 'block' : 'none' );
			
			jQuery( '.fs_schema .open_event .entry_info_full' ).css( 'display', entry_status == 'full' ? 'block' : 'none' );
			
			jQuery( '.fs_schema .open_event .entry_info_cancelled' ).css( 'display', entry_status == 'cancelled' ? 'block' : 'none' );
			
			jQuery( '.fs_schema .open_event .entry_info_not_bookable' ).css( 'display', entry_status == 'notbookable' ? 'block' : 'none' );
			
			jQuery( '.fs_schema .open_event .entry_info_reserve' ).css( 'display', entry_status == 'reserve' ? 'block' : 'none' );
			
			jQuery( '.fs_schema .open_event .entry_info_not_opened_yet' ).css( 'display', entry_status == 'not_opened_yet' ? 'block' : 'none' );
			
			jQuery( '.fs_schema .open_event .entry_info_closed' ).css( 'display', entry_status == 'closed' ? 'block' : 'none' );
			
			
			
			// add entry message if there is one
			
			entry_message = jQuery( event_el ).attr( 'data-message' );
			
			if ( entry_message != '' ) jQuery( '.fs_schema .open_event .entry_message' ).html( entry_message ).css( 'display', 'block' );
			
			else jQuery( '.fs_schema .open_event .entry_message' ).html( '' ).css( 'display', 'none' );
			


			// determine if the event is bookable
			
			event_is_bookable = true;
			
			if ( entry_status == 'full' || entry_status == 'cancelled' || entry_status == 'notbookable' || entry_status == 'dropin' || entry_status == 'not_opened_yet' || entry_status == 'closed' )
			
				event_is_bookable = false;
				
			
			// put info into and show/hide waitinglist
			
			if ( (waitinglistsize != '' && waitinglistsize != '0' && waitinglistsize != '-1' ) ) {
			
				jQuery( '.fs_schema .open_event .waitinglist span' ).html( waitinglistsize + ' i kö' );
			
				jQuery( '.fs_schema .open_event .waitinglist' ).css( 'display', 'block' );
			}
			
			
			// put info into and show/hide bookable slots
			
			if ( bookableslots != '' && bookableslots != '0' && bookableslots != '-1' ) {
			
				jQuery( '.fs_schema .open_event .bookableslots span' ).html( bookableslots );
			
				jQuery( '.fs_schema .open_event .bookableslots' ).css( 'display', 'block' );
			}
			

			// put info into and show/hide dropin slots
			
			has_dropin = false;
			
			if ( dropinslots != '' && dropinslots != '0' && dropinslots != '-1' ) {
			
				jQuery( '.fs_schema .open_event .dropin span' ).html( dropinslots );
			
				jQuery( '.fs_schema .open_event .dropin' ).css( 'display', 'block' );
				
				jQuery( '.fs_schema .open_event .waitinglist' ).css( 'display', 'none' );
				
				has_dropin = true;
			}
			
			
			// make some adjustements to bookable slots and waitinglist
			
			if ( bookableslots == '0' && ( waitinglistsize == '0' || waitinglistsize == '' )) {
			
				jQuery( '.fs_schema .open_event .waitinglist span' ).html( '0 i kö' );
				
				jQuery( '.fs_schema .open_event .bookableslots span' ).html( '0' );
			
				jQuery( '.fs_schema .open_event .waitinglist' ).css( 'display', has_dropin == true ? 'none' : 'block' );
				
				jQuery( '.fs_schema .open_event .bookableslots' ).css( 'display', 'block' );
			}
			
			
			// if user is logged in, show login info and hide log in form
			
			if ( fs_schema_public.password != '' ) {
							
				jQuery( '.fs_schema .open_event .loggedin' ).css( 'display', 'block' );
				
				jQuery( '.fs_schema .open_event .loginform' ).css( 'display', 'none' );
				
			
			// if user is NOT logged in, show log in form if the event is bookable
			
			} else { 
							
				jQuery( '.fs_schema .open_event .loggedin' ).css( 'display', 'none' );
				
				if ( event_is_bookable == true ) jQuery( '.fs_schema .open_event .loginform' ).css( 'display', 'block' );
			}
			
				
			
			// user HAS already booked this event, hide booking function and show booking info
			
			if ( fs_schema_public.open_bookingid != '' || (waitinglistposition != '' && waitinglistposition != '0' )) {
			
				jQuery( '.fs_schema .open_event .book_event' ).css( 'display', 'none' );
					
				jQuery( '.fs_schema .open_event .login_book_event' ).css( 'display', 'none' );
				
				jQuery( '.fs_schema .open_event .booked_info' ).css( 'display', 'block' );
				
				if ( waitinglistposition != '' && waitinglistposition != '0' ) {
					
					jQuery( '.fs_schema .open_event .unbook_waitinglist' ).css( 'display', 'inline' );
					
					jQuery( '.fs_schema .open_event .booked_info' ).addClass('reserve');
					
				} else {
					
					jQuery( '.fs_schema .open_event .unbook_event' ).css( 'display', 'inline' );
					
					jQuery( '.fs_schema .open_event .booked_info' ).removeClass('reserve');
				}
				
				
				// if booking is a reserve list, show that, otherwise not
				
				if (waitinglistposition != '' && waitinglistposition != '0' ) 
				
					jQuery( '.fs_schema .open_event .booked_info' ).addClass('reserve').html('Du har reservplats ' + waitinglistposition + '.');
				
				else 
					
					jQuery( '.fs_schema .open_event .booked_info' ).removeClass('reserve').html('Du är inbokad.');
				
					

			// user has NOT booked this event
			
			} else {
			
				jQuery( '.fs_schema .open_event .booked_info' ).css( 'display', 'none' );
			
				jQuery( '.fs_schema .open_event .unbook_event' ).css( 'display', 'none' );
				
				
				// if this event is bookable, and user has not booked it yet
				
				if ( event_is_bookable == true ) {
				
	
					// if there are no free slots, user has to book a waiting-list
					
					if ( jQuery( event_el ).attr( 'data-bookableslots' ) < 1 ) {
						
						
						// user is logged in
						
						if ( fs_schema_public.password != '' ) jQuery( '.fs_schema .open_event .book_waitinglist' ).css( 'display', 'inline' );
						
						// user is not logged in
						else jQuery( '.fs_schema .open_event .login_book_waitinglist' ).css( 'display', 'inline' );
					
					
					// user can book this! Bingo.
					
					} else {
						
					
						// user is logged in
						if ( fs_schema_public.password != '' ) {
						
							jQuery( '.fs_schema .open_event .book_event' ).css( 'display', 'inline' );
							
							jQuery( '.fs_schema .open_event .loggedin' ).css( 'display', 'block' );
							
							
						// user is not logged in
						} else {
						
							jQuery( '.fs_schema .open_event .loginform' ).css( 'display', 'block' );
							
							jQuery( '.fs_schema .open_event .login_book_event' ).css( 'display', 'inline' );
							
							if ( fs_schema_public.clear_creds == true ) {
							
								jQuery( '.fs_schema .open_event .loginform .username input' ).val('');
							
								jQuery( '.fs_schema .open_event .loginform .password input' ).val('');
							}
				
							jQuery( '.fs_schema .open_event .loginform .username input' ).focus();	
						}
					}
				}
			}

			if ( fs_schema_public.responsive == '' ) {
			
				window.setTimeout(function() {
				
					jQuery( '.fs_schema .open_event' )
					
						.css('transition' , '0.1s').css('-moz-transition' , '0.1s').css('-webkit-transition' , '0.1s').css('-ms-transition' , '0.1s').css('-o-transition' , '0.1s')
						
						.css( '-webkit-transform', 'translate(' + event_diff_x + 'px, ' + event_diff_y + 'px) scale(1)')
						
						.css( '-moz-transform', 'translate(' + event_diff_x + 'px, ' + event_diff_y + 'px) scale(1)')
						
						.css( '-ms-transform', 'translate(' + event_diff_x + 'px, ' + event_diff_y + 'px) scale(1)')
						
						.css( 'transform', 'translate(' + event_diff_x + 'px, ' + event_diff_y + 'px) scale(1)');
					
					}, 100);
					
			} else {
			
				jQuery('html, body').animate({scrollTop:jQuery( '#open_event_dialogue' ).offset().top}, 'slow');
			}
		}
	},
	


	display_dialogue_message : function ( message, autohide ) {
	
		window.clearTimeout( fs_schema_public.dialogue_message_timeout );

		jQuery( '.fs_schema .dialogue.open .message' ).css( 'display', 'none' ).html( message ).addClass('open').slideDown('fast');	
	
		if ( autohide == true ) fs_schema_public.dialogue_message_timeout = window.setTimeout(function() { 
		
			jQuery( '.fs_schema .dialogue .message.open' ).slideUp('slow', function() {
			
				jQuery( '.fs_schema .dialogue .message.open' ).removeClass('open');
				
			});
			
		}, 5000);
	},
	
	
	
	display_dialogue_big_message : function ( header, message, error ) {
	
		window.clearTimeout( fs_schema_public.dialogue_message_timeout );
		
		fs_schema_public.is_busy = false;
		
		jQuery( '.fs_schema .dialogue.open .progress').css( 'display', 'none' );
		
		jQuery( '.fs_schema .week_progress' ).css('display', 'none');
		
		big_message = jQuery( '.fs_schema .dialogue.open .big_message' );
		
		if ( big_message.length == 0 ) big_message = jQuery( '.fs_schema .big_message.week_message' );
	
		jQuery( big_message ).css ( 'display', 'block' ).addClass('open');
		
		if ( error == true ) { 
			
			jQuery( big_message ).addClass ( 'error' );
			
			if ( fs_schema_public.fallback_url  != '' ) 
			
				message = message + '<div class="fallback_url">Om du tror att felet är en bugg kan du prova att använda <a href="' + fs_schema_public.fallback_url  + '">den alternativa bokningsfunktionen</a>.</div>';
			
		} else jQuery( big_message ).removeClass ( 'error' );
		
		jQuery( big_message ).find( '.head' ).html( header );
		
		jQuery( big_message ).find( '.info' ).html( message );		
	
	},
	
	
	
	close_dialogue_big_message : function () {
	
		jQuery( '.fs_schema .big_message.open' ).css ( 'display', 'none' ).removeClass('open');
		
		 if ( fs_schema_public.close_event_after_big_message == true ) fs_schema_public.close_event(); 
		 
		 fs_schema_public.after_close_big_message();

	},
		
	
	login_and_book_event : function () {
	
		fs_schema_public.book_event_after_login = true;
		
		fs_schema_public.login();
		
	},
	
	login_book_waitinglist : function () {
	
		fs_schema_public.book_waitinglist_after_login = true;

		fs_schema_public.login();
	},
	

	book_event : function () {
	
		fs_schema_public.is_busy = true;
		
		window.clearTimeout( fs_schema_public.dialogue_message_timeout );
	
		jQuery( '.fs_schema .open_event .message' ).css( 'display', 'none' );

		jQuery( '.fs_schema .open_event .progress .doingwhat').html('Bokar aktiviteten');
		
		jQuery( '.fs_schema .open_event .progress').css( 'display', 'block' );
		
		fs_schema_public.ajax (
		
			{ action : 'book_activity', username : fs_schema_public.username, password: fs_schema_public.password, activityid: fs_schema_public.open_event_id, session_key: fs_schema_public.session_key }, 'json', function ( data ) { 
			
				//jQuery( '.fs_schema .debug').html( data );
				
				fs_schema_public.is_busy = false;
				
				jQuery( '.fs_schema .open_event .progress').css( 'display', 'none' );
				
				if ( data.error != '' ) {
				
					fs_schema_public.close_event_after_big_message = true;
				
					fs_schema_public.display_dialogue_big_message ( 'Fel.', data.message, true );
							
					fs_schema_public.refresh_on_close_event = true;
				
				} else {
				
					jQuery( fs_schema_public.open_event_el ).attr( 'data-bookingid',  data.bookingid );
						
					jQuery( fs_schema_public.open_event_el ).find('.booking_info').css('display', 'block').html('Du är inbokad');
					
					fs_schema_public.animate_source_on_close_event = true;
					
					fs_schema_public.add_source_class_on_close_event = 'booked';
					
					fs_schema_public.close_event();
					
					fs_schema_public.show_hud ( 'Bokat!' );
				}
				
				fs_schema_public.after_book_event ();
						
				fs_schema_public.after_book_event = function() {};
					
				jQuery( '.fs_schema .debug').html( data.debug );
			}
		);
	
	},
	
	
	unbook_event : function () {
	
		if ( fs_schema_public.open_bookingid != '' && fs_schema_public.open_bookingid != 'x' ) {
		
			if ( fs_schema_public.username != '' && fs_schema_public.password != '' ) {
	
				fs_schema_public.is_busy = true;
				
				window.clearTimeout( fs_schema_public.dialogue_message_timeout );
				
				jQuery( '.fs_schema .open_event .message' ).css( 'display', 'none' );
		
				jQuery( '.fs_schema .open_event .progress .doingwhat').html('Avbokar aktiviteten');
				
				jQuery( '.fs_schema .open_event .progress').css( 'display', 'block' );
				
				var booking_type = jQuery( fs_schema_public.open_event_el ).attr( 'data-bookingtype' );
				
				if ( booking_type == 'ordinary' )
				
					post_object = { action : 'unbook_activity', bookingid: fs_schema_public.open_bookingid, username: fs_schema_public.username, password: fs_schema_public.password, session_key: fs_schema_public.session_key };
	
				else 
				
					post_object = { action : 'unbook_waitinglist', bookingid: fs_schema_public.open_bookingid, username: fs_schema_public.username, password: fs_schema_public.password, session_key: fs_schema_public.session_key };

				fs_schema_public.ajax (
				
					post_object , 'json', function ( data ) { 
						
						jQuery( '.fs_schema .debug').html( data.debug );
						
						fs_schema_public.is_busy = false;
						
						jQuery( '.fs_schema .open_event .progress').css( 'display', 'none' );
						
						if ( data.error != '' ) {
						
							fs_schema_public.close_event_after_big_message = true;
						
							fs_schema_public.display_dialogue_big_message ( 'Fel.', data.message, true );
							
							fs_schema_public.refresh_on_close_event = true;
						
						} else {
							
							fs_schema_public.animate_source_on_close_event = true;
							
							fs_schema_public.remove_source_class_on_close_event = ( booking_type == 'ordinary' ? 'booked' : 'reserve' );
							
							jQuery( fs_schema_public.open_event_el ).attr( 'data-bookingid', '' );
							
							jQuery( fs_schema_public.open_event_el ).attr( 'data-waitinglistposition', '' );
							
							jQuery( fs_schema_public.open_event_el ).attr( 'data-bookingtype', 'ordinary' );
							
							jQuery( fs_schema_public.open_event_el ).find('.booking_info').css('display', 'block').html('Avbokad');
							
							jQuery( fs_schema_public.open_event_el ).attr( 'title',  '');
							
							fs_schema_public.close_event();
					
							fs_schema_public.show_hud ( 'Avbokat!' );
							
						}
					}
				);
				
			} else {
			
				fs_schema_public.close_event_after_big_message = true;
			
				fs_schema_public.display_dialogue_big_message ( 'Fel', 'Saknar av någon anledning ditt använarnamn och lösenord. Prova att ladda om sidan, logga in igen och avboka därefter igen.', true );
			
			}
			
		} else {		
		
			fs_schema_public.refresh_on_close_event = true;
		
			fs_schema_public.close_event_after_big_message = true;
		
			fs_schema_public.display_dialogue_big_message ( 'Fel', 'Saknar ID på din bokning p.g.a begränsningar i bokningssystemet, och kan därför inte göra en avbokning just nu. Vi laddar om schemat nu för att hämta boknings-id. Därefter kan du försöka avboka aktiviteten igen.', true );

		}
	}, 
	
	
	book_waitinglist : function () {
	
		fs_schema_public.is_busy = true;
		
		window.clearTimeout( fs_schema_public.dialogue_message_timeout );
	
		jQuery( '.fs_schema .open_event .message' ).css( 'display', 'none' );

		jQuery( '.fs_schema .open_event .progress .doingwhat').html('Bokar reservplats');
		
		jQuery( '.fs_schema .open_event .progress').css( 'display', 'block' );
		
		var book_waitinglist = 0;
		
		if ( jQuery( fs_schema_public.open_event_el ).attr( 'data-bookableslots' ) < 1 ) book_waitinglist = 1;
		
		fs_schema_public.ajax (
		
			{ action : 'book_waitinglist', username : fs_schema_public.username, password: fs_schema_public.password, activityid: fs_schema_public.open_event_id, session_key: fs_schema_public.session_key }, 'json', function ( data ) { 
			
				//jQuery( '.fs_schema .debug').html( data );
				
				fs_schema_public.is_busy = false;
				
				refresh_after_booked = false;
				
				jQuery( '.fs_schema .open_event .progress').css( 'display', 'none' );
				
				if ( data.error != '' ) {
				
					fs_schema_public.close_event_after_big_message = true;
				
					fs_schema_public.display_dialogue_big_message ( 'Fel.', data.message, true );
				
				} else {
				
					if ( data.bookingid == '' ) {	// profit dont give us a booking id when booking Ahh, what kind of booking system is that?
					
						refresh_after_booked = true;
					
					} else {
					
						jQuery( fs_schema_public.open_event_el ).attr( 'data-bookingtype',  'waitinglist' );
				
						jQuery( fs_schema_public.open_event_el ).attr( 'data-bookingid',  data.bookingid );
						
						jQuery( fs_schema_public.open_event_el ).attr( 'data-waitinglistposition',  data.waitinglistposition );
						
						jQuery( fs_schema_public.open_event_el ).attr( 'data-waitinglistsize',  data.waitinglistsize );
						
						jQuery( fs_schema_public.open_event_el ).attr( 'title',  'Du är reserv.');
						
						jQuery( fs_schema_public.open_event_el ).find('.booking_info').css('display', 'block').html('Du är reserv.');

					}
					
					fs_schema_public.animate_source_on_close_event = true;
					
					fs_schema_public.add_source_class_on_close_event = 'reserve';
					
					fs_schema_public.close_event();
					
					fs_schema_public.show_hud ( 'Reservplats bokad!' );
				}
				
				if ( refresh_after_booked == true ) {
				
					if ( fs_schema_public.forceday && fs_schema_public.num_days == 7 ) { fs_schema_public.disable_change_to_week(true); 
					
					} else { fs_schema_public.refresh(); }
				
				} else {
				
					fs_schema_public.after_book_event ();
						
					fs_schema_public.after_book_event = function() {};
				}
					
				jQuery( '.fs_schema .debug').html( data.debug );
			}
		);	
	},
	
	
	
	update_waitinglist : function() {
	
		fs_schema_public.is_busy = true;
		
		window.clearTimeout( fs_schema_public.dialogue_message_timeout );
	
		jQuery( '.fs_schema .open_event .message' ).css( 'display', 'none' );

		jQuery( '.fs_schema .open_event .progress .doingwhat').html('Hämtar information');
		
		jQuery( '.fs_schema .open_event .progress').css( 'display', 'block' );
		
		fs_schema_public.ajax (
		
			{ action : 'update_waitinglist', bookingid: fs_schema_public.open_bookingid, username : fs_schema_public.username, password: fs_schema_public.password, activityid: fs_schema_public.open_event_id, session_key: fs_schema_public.session_key }, 'json', function ( data ) { 
			
				jQuery( '.fs_schema .debug').html( data );
				
				fs_schema_public.is_busy = false;
				
				jQuery( '.fs_schema .open_event .progress').css( 'display', 'none' );
				
				if ( data.error != '' ) {
				
					fs_schema_public.close_event_after_big_message = false;
				
					fs_schema_public.display_dialogue_big_message ( 'Fel.', data.message, true );
				
				} else {
				
					fs_schema_public.close_event_after_big_message = false;
				
					fs_schema_public.display_dialogue_big_message ( 'Din plats i kön.', data.message, false );

					jQuery( fs_schema_public.open_event_el ).attr( 'data-waitinglistposition', data.waitinglistposition );	
					
					jQuery( '.fs_schema .open_event .waitinglist .your_place ').html( data.waitinglistposition );
				}
			}
		);
	},

	
	
	close_event : function () {
	
		if ( jQuery( '.fs_schema .open_event' ).length > 0 ) {
		
			if ( fs_schema_public.responsive == '' ) {
			
				jQuery( '.fs_schema .open_event' )
						
					.css( 'transition' , '0.1s').css('-moz-transition' , '0.1s').css('-webkit-transition' , '0.1s').css('-ms-transition' , '0.1s').css('-o-transition' , '0.1s')
					
					.css( '-webkit-transform', 'translate(0px, 0px) scale(0.1)').css( '-moz-transform', 'translate(0px, 0px) scale(0.1)').css( '-ms-transform', 'translate(0px, 0px) scale(0.1)').css( 'transform', 'translate(0px, 0px) scale(0.1)');
					
				window.setTimeout(function() {
					
					jQuery( '.fs_schema .open_event' )
					
						.css( 'display', 'none')
					
						.css('transition' , 'none').css('-moz-transition' , 'none').css('-webkit-transition' , 'none').css('-ms-transition' , 'none').css('-o-transition' , 'none')
						
						.removeClass ( 'open' );
						
						if ( fs_schema_public.animate_source_on_close_event == true ) {
		
							jQuery( fs_schema_public.open_event_el )
								
								.removeClass ( fs_schema_public.remove_source_class_on_close_event )
								
								.addClass ( fs_schema_public.add_source_class_on_close_event )
								
								.css( '-webkit-transform', 'translate(-5px, -5px) scale(1.5)').css( '-moz-transform', 'translate(-5px, -5px) scale(1.5)').css( '-ms-transform', 'translate(-5px, -5px) scale(1.5)').css( 'transform', 'translate(-5px, -5px) scale(1.5)');
				
							window.setTimeout(function() {
							
								jQuery( fs_schema_public.open_event_el )
							
									.css( '-webkit-transform', 'translate(0px, 0px) scale(1)').css( '-moz-transform', 'translate(0px, 0px) scale(1)').css( '-ms-transform', 'translate(0px, 0px) scale(1)').css( 'transform', 'translate(0px, 0px) scale(1)');					
							
							}, 100);
			
							fs_schema_public.open_event_id = 0;
							
							if ( fs_schema_public.refresh_on_close_event == true ) fs_schema_public.refresh();
							
						} else {
						
							fs_schema_public.open_event_id = 0;
							
							if ( fs_schema_public.refresh_on_close_event == true ) fs_schema_public.refresh();
						}
					
					}, 100);
					
			} else {
			
				jQuery( '.fs_schema .open_event' )
					
					.css( 'display', 'none')
					
					.removeClass ( 'open' );
			
				if ( fs_schema_public.animate_source_on_close_event == true ) {
		
					jQuery( fs_schema_public.open_event_el )
						
						.removeClass ( fs_schema_public.remove_source_class_on_close_event )
						
						.addClass ( fs_schema_public.add_source_class_on_close_event );
	
					fs_schema_public.open_event_id = 0;
					
					if ( fs_schema_public.refresh_on_close_event == true ) fs_schema_public.refresh();
					
				} else {
				
					fs_schema_public.open_event_id = 0;
					
					if ( fs_schema_public.refresh_on_close_event == true ) fs_schema_public.refresh();
				}
			}
		}
	},
	
	
	
	set_values : function () {
	
		fs_schema_public.schema_width = jQuery('.fs_schema').outerWidth(true);
		
		fs_schema_public.day_width = jQuery('.fs_schema').attr('data-day-width');
		
		fs_schema_public.hours_width = jQuery('.fs_schema').attr('data-hours-width');
		
		fs_schema_public.fallback_url = jQuery ( '.fs_schema .fs_booking_fallback_url').html();
		
		fs_schema_public.schema_offset_top = jQuery('.fs_schema').offset().top;
		
		jQuery( '.fs_schema .week_overlay' ).css('left', ((fs_schema_public.schema_width/2)-150));
		
		jQuery( '.fs_schema .big_message.week_message' ).css('left', ((fs_schema_public.schema_width/2)-150));
		
		if ( jQuery('.fs_schema.enableday ').length > 0 ) fs_schema_public.enableday = true;
		
		if ( jQuery('.fs_schema.enableweek ').length > 0 ) fs_schema_public.enableweek = true;
		
		if ( jQuery('.fs_schema.day').length > 0 ) fs_schema_public.num_days = 1;
		
		fs_schema_public.offsetleft = jQuery('#wrapper').offset().left + 100;
		
		switch (jQuery( '.fs_schema .responsive_target').css('width')) {
		
			case '1px': fs_schema_public.responsive = 'mobile'; break;
			
			case '2px': fs_schema_public.responsive = 'mobile_landscape'; break;
			
			case '3px': fs_schema_public.responsive = 'tablet'; break;
		}
	},
	
	
	// make sure th user has a modern ok browser
	
	check_browser : function () {
	
		var browser_ok = true;
		
		var style = document.documentElement.style;
		
		if (( typeof ( style.webkitTransform ) !== 'undefined'||
			typeof ( style.MozTransform ) !== 'undefined'  ||
			typeof ( style.OTransform ) !== 'undefined'  ||
			typeof ( style.MsTransform ) !== 'undefined'  ||
			typeof ( style.msTransform ) !== 'undefined'  ||
			typeof ( style.transform ) !== 'undefined' ) == false ) 
			
			browser_ok  = false;
		
		if (navigator.appName.indexOf("Microsoft")!=-1) {
			
			var ie_version = parseInt( navigator.userAgent.toLowerCase().split('msie')[1]);

			if ( ie_version < 9 ) browser_ok  = false;
		
		}
		
		if ( browser_ok  == false ) {
		
			var message = 'Din webbläsare är för gammal för att kunna visa schemat. Vi rekommenderar att du använder senaste versionerna av någon av de vanligaste webbläsarna.';
			
			if ( fs_schema_public.fallback_url  != '' ) message = message + '<span>Tills dess kan du också använda <a href="' + fs_schema_public.fallback_url  + '">den alternativa bokningsfunktionen</a>.</span>';
		
			jQuery ( '.fs_schema' ).html('<div class="fs_schema_error">' + message + '</div>');

		}
		
		return browser_ok;
	},
	
	create_cookie : function (name, value, days) {
	
	    var expires;
	
	    if (days) {
	    
		   var date = new Date();
		   
		   date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		   
		   expires = "; expires=" + date.toGMTString();
		   
	    } else {
	    
		   expires = "";
	    }
	    
	    document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
	},
	
	read_cookie : function (name) {
	
	    var nameEQ = escape(name) + "=";
	    
	    var ca = document.cookie.split(';');
	    
	    for (var i = 0; i < ca.length; i++) {
	    
		   var c = ca[i];
		   
		   while (c.charAt(0) === ' ') c = c.substring(1, c.length);
		   
		   if (c.indexOf(nameEQ) === 0) return unescape(c.substring(nameEQ.length, c.length));
	    }
	    
	    return null;
	},
	
	erase_cookie : function (name) {
	
	    fs_schema_public.create_cookie(name, "", -1);
	},

	ajax : function ( data, datatype, fn_success, fn_error ) {
	
		//data.nonce: fsschemavars.nonce;
	
		jQuery.ajax({
		
			type: "POST", url: fsschemavars.ajaxurl, dataType: datatype, data: data, context: document.body,
			
			timeout: 20000,  // 20 seconds
			
			success: function( data) { fn_success ( data ); },
			
			error: function ( jqXHR, textStatus, errorThrown )  { 
				switch ( textStatus ) {
				
					case 'timeout':
					
						fs_schema_public.close_event_after_big_message = false;
						
						fs_schema_public.display_dialogue_big_message ( 'Fel.', 'Det gick inte att kommunicera med servern (timeout). Vänligen försök igen.', true );
						
						break;
						
					default:
							
						if ( fn_error ) { fn_error(); } 
						
						else { 
						
							fs_schema_public.close_event_after_big_message = false;
						
							fs_schema_public.display_dialogue_big_message ( 'Fel.', 'Det uppstod ett oväntat fel. Var vänlig försök igen.' , true ); 
								
							// do it again and save the returned data as text in debug window
							
							fs_schema_public.ajax ( data, 'html', function ( data ) { 
								
									jQuery( '.fs_schema .debug').html( data );
									
								}, function ( data ) { 
								
									jQuery( '.fs_schema .debug').html( data );
									
								}
							);
							
							fs_schema_public.is_busy = false;
						
						}
						break;
				}
			}
			
		});      
     }
}

jQuery(document).ready(function() { fs_schema_public.init(); });



