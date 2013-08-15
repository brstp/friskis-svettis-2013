/*/////////////////////////////////////////////////////////////////

	FS SCHEMA, Javascript for public pages

	Copyright (C) 2013 Klas Ehnemark (http://klasehnemark.com)
	This program is not free software.

//////////////////////////////////////////////////////////////////*/


fs_schema_public = {

	username : '',
	
	password : '',
	
	session_key : '',
	
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
	
	add_source_class_on_close_event : '',
	
	remove_source_class_on_close_event : '',
	
	last_step : 0,
	
	book_event_after_login : false,
	
	enableweek : false,
	
	enableday : false,
	
	forceday : false,
	
	after_refresh : function () {},
	
	after_book_event : function () {},
	
	init : function () {
	
		fs_schema_public.set_values();

		jQuery(window).resize( function() { fs_schema_public.set_values(); });
	
		jQuery('.fs_schema .entry.openable').click( function( ev_data ) { fs_schema_public.open_event ( this ); });
		
		jQuery('.fs_schema .close_open_event').click( function() { fs_schema_public.close_event (); });
		
		jQuery('.fs_schema .login_book_event').click( function() { fs_schema_public.login_and_book_event (); });
		
		jQuery('.fs_schema .book_event ').click( function() { fs_schema_public.book_event (); });
		
		jQuery( '.fs_schema .open_event .unbook_event' ).click( function() { fs_schema_public.unbook_event (); });
		
		jQuery('.fs_schema .big_message .close_btn').click( function() { fs_schema_public.close_dialogue_big_message (); fs_schema_public.close_event(); });
		
		jQuery('.fs_schema .navigation .previous').click( function() { fs_schema_public.previous (); });
		
		jQuery('.fs_schema .navigation .next').click( function() { fs_schema_public.next (); });
		
		jQuery('.fs_schema .navigation .fs_button.login_info').click( function() { fs_schema_public.show_login (); });
		
		jQuery('.fs_schema .close_login_form').click( function() { fs_schema_public.close_login (); });
		
		jQuery('.fs_schema .login_btn').click( function() { fs_schema_public.login (); });
		
		jQuery('.fs_schema .logout_btn ').click( function() { fs_schema_public.logout (); });
		
		jQuery('.fs_schema .change_to_day').click( function() { fs_schema_public.change_to_day (); });
		
		jQuery('.fs_schema .change_to_week').click( function() { fs_schema_public.change_to_week (); });
		
		jQuery( '.fs_schema .debug').html( jQuery( '.fs_schema .week_debug' ).html() );
		
		jQuery('.fs_schema .show_debug').click( function() { jQuery( '.fs_schema .debug').show(); jQuery('.fs_schema .show_debug').hide(); });

		fs_schema_public.show_hud( jQuery('.fs_schema .days').attr('data-week') );

	},
	
	
	change_to_day : function () {
	
		if ( fs_schema_public.num_days == 7 && fs_schema_public.enableday == true && jQuery('.fs_schema .change_to_day:not(.disabled) ').length > 0 ) {
		
			fs_schema_public.show_hud ( 'Växlar till dagsvy...' );
			
			fs_schema_public.num_days = 1;
			
			fs_schema_public.after_refresh = function() {
			
				jQuery('.fs_schema').removeClass('week').addClass('day');
			
				jQuery('.fs_button.change_to_week').css('display', 'inline');
			
				jQuery('.fs_button.change_to_day').css('display', 'none'); 
				
				jQuery('.fs_button.previous').html('&lt; Föregående dag');
				
				jQuery('.fs_button.next').html('Nästa dag &gt;');
			}
			
			fs_schema_public.refresh();
		}
	},
	
	
	change_to_week : function () {
	
		if ( fs_schema_public.num_days == 1 && fs_schema_public.enableweek == true && jQuery('.fs_schema .change_to_week:not(.disabled) ').length > 0 ) {
	
			fs_schema_public.show_hud ( 'Växlar till veckovy...' );
			
			fs_schema_public.num_days = 7;
			
			fs_schema_public.after_refresh = function() {
			
				jQuery('.fs_schema').removeClass('day').addClass('week');
			
				jQuery('.fs_button.change_to_week').css('display', 'none');
			
				jQuery('.fs_button.change_to_day').css('display', 'inline'); 
				
				jQuery('.fs_button.previous').html('&lt; Föregående vecka');
				
				jQuery('.fs_button.next').html('Nästa vecka &gt;');
			}
			
			fs_schema_public.refresh();
		}
	},
	
	
	show_login : function () {
	
		if (  fs_schema_public.is_busy == false ) {
		
			jQuery('.fs_schema .dialogue.open').hide().removeClass('open');
			
			jQuery('.fs_schema .login.dialogue')
				.css('left',  ( fs_schema_public.schema_width / 2 ) - ( fs_schema_public.open_event_width / 2 ))
				.css('display', 'block')
				.addClass('open')
				.css( '-webkit-transform', 'scale(1.1)').css( '-moz-transform', 'scale(1.1)').css( 'transform', 'scale(1.1)');
				
			window.setTimeout(function() {
				jQuery( '.fs_schema .login' ).css( '-webkit-transform', 'scale(1)').css( '-moz-transform', 'scale(1)').css( 'transform', 'scale(1)');
			}, 100);
			
			fs_schema_public.book_event_after_login = false;
			
		}
	},
	
	
	
	close_login : function () {
	
		if ( jQuery( '.fs_schema .login' ).is(":visible") ) {
	
			jQuery('.fs_schema .login').css( '-webkit-transform', 'scale(1.05)').css( '-moz-transform', 'scale(1.05)').css( 'transform', 'scale(1.05)');
				
			window.setTimeout(function() { 
				jQuery( '.fs_schema .login' ).css( '-webkit-transform', 'scale(1)').css( '-moz-transform', 'scale(1)').css( 'transform', 'scale(1)');
			}, 100);
				
			window.setTimeout(function() {
				jQuery( '.fs_schema .login' ).css( 'display', 'none');
			}, 200);
			
		}
	},
	
	

	
	login : function () {
	
		username_ = jQuery('.fs_schema .dialogue.open .username input');
		
		password_ = jQuery('.fs_schema .dialogue.open .password input');
	
		username = jQuery( username_ ).val();
		
		password = jQuery( password_ ).val();
		
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
					
						fs_schema_public.display_dialogue_big_message ( 'Fel.', data.message, true );
					
						fs_schema_public.is_busy = false;
					
					} else {
								
						fs_schema_public.username = username;
						
						fs_schema_public.password = password;
								
						fs_schema_public.personid = data.personid;
						
						fs_schema_public.session_key = data.session_key;
						
						fs_schema_public.forceday = data.forceday;
					
						fs_schema_public.close_login();
						
						window.setTimeout(function() {
						
							jQuery('.fs_schema .login_info.fs_button').html ('Inloggad som ' + data.name );
							
							jQuery('.fs_schema .login_info.fs_button').addClass('green');
							
							jQuery('.fs_schema .loggedin').html ( 'Du är inloggad som ' + data.name ).css( 'display', 'block' );
							
							jQuery('.fs_schema .loginform ').css( 'display', 'none' );
							
							jQuery('.fs_schema .login_btn ').css( 'display', 'none' );
							
							jQuery('.fs_schema .login_book_event ').css( 'display', 'none' );
							
							jQuery('.fs_schema .logout_btn ').css( 'display', 'inline' );
							
							jQuery('.fs_schema .book_event ').css( 'display', 'inline' );
							
							jQuery('.fs_schema .login.dialogue .header').html('Logga ut');
							
						}, 500);
						
						if ( fs_schema_public.book_event_after_login == true ) {
						
							if ( data.forceday && fs_schema_public.num_days == 7 ) {
							
								fs_schema_public.after_book_event = function() {
							
									fs_schema_public.change_to_day();
									
									jQuery('.fs_schema .change_to_week').addClass('disabled').attr('title',  'Profit bokningssystem kan inte visa veckoschemat utan bara en dag i taget när man är inloggad.');
								};
								
							} else {
							
								fs_schema_public.after_book_event = function() {
								
									fs_schema_public.refresh();
									
								};
							
							}
						
							fs_schema_public.book_event();
							
						} else {
					
							fs_schema_public.is_busy = false;
							
							if ( data.forceday && fs_schema_public.num_days == 7 ) { 
							
								fs_schema_public.change_to_day();
								
								jQuery('.fs_schema .change_to_week').addClass('disabled').attr('title',  'Profit bokningssystem kan inte visa veckoschemat utan bara en dag i taget när man är inloggad.');
								
							} else {
							
								fs_schema_public.refresh();
								
							}
						}
						
						if ( fs_schema_public.book_event_after_login == false ) fs_schema_public.show_hud ( 'Inloggad' );
					}
					
					jQuery( '.fs_schema .debug').html( data.debug );
			
				});
		
		}
	},
	
	
	
	logout : function () {
				
		fs_schema_public.username = '';
		
		fs_schema_public.password = '';
		
		fs_schema_public.personid = 0;
		
		fs_schema_public.refresh();
	
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
		
		jQuery('.fs_schema .login.dialogue .header').html('Logga in');
		
		jQuery('.fs_schema .change_to_week').removeClass('disabled').attr('title', '');
	},
	
	
	
	show_hud : function ( week_info ) {
	
		jQuery('.fs_schema .week_overlay').html( week_info ).delay(200).fadeIn(200).delay(800).fadeOut(500);

	},
	
	
	
	previous : function () {
	
		fs_schema_public.walk ( -1 );
		
	},
	
	
	
	next : function () {
	
		fs_schema_public.walk ( 1 );
		
	},
	
		
	refresh : function () {
	
		if (  fs_schema_public.is_busy == false ) {
		
			jQuery('.fs_schema .week_progress').css('display', 'block');
		
			date_info = jQuery('.fs_schema .days').attr('data-date-info');
			
			fs_schema_public.ajax (
			
				{ action : 'walk_schema', num_days: fs_schema_public.num_days, date_info : date_info, step: 0, username: fs_schema_public.username, password: fs_schema_public.password, session_key: fs_schema_public.session_key }, 'html', function ( data ) {
				
					jQuery( '.fs_schema .weeks' ).html(data);
					
					jQuery('.fs_schema .week_progress').css('display', 'none');
					
					fs_schema_public.after_refresh();
					
					jQuery('.fs_schema .entry.openable').click( function( ev_data ) { fs_schema_public.open_event ( this ); });
					
					fs_schema_public.after_refresh = function() {};
				});
		}
	
	},
	

	walk : function ( step ) {
	
		if (  fs_schema_public.is_busy == false ) {
		
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

	
	
	open_event : function ( event_el ) {
	
		if (  fs_schema_public.is_busy == false ) {
		
			jQuery('.fs_schema .dialogue.open').hide().removeClass('open');
		
			fs_schema_public.animate_source_on_close_event = false;
			
			fs_schema_public.remove_source_class_on_close_event = '';
						
			fs_schema_public.add_source_class_on_close_event = '';
	
			event_target_x = ( fs_schema_public.schema_width / 2 ) - ( fs_schema_public.open_event_width / 2 );
			
			//event_target_y = jQuery( event_el ).parent().parent().position().top;
			
			event_el_position_top = jQuery( event_el ).position().top;
			
			event_target_y = event_el_position_top > 30 ? event_el_position_top - 150 : 0;
			
			event_current_x = ( (jQuery( event_el ).parent().parent().attr('data-day') -1 ) * fs_schema_public.day_width ) + jQuery( event_el ).position().left - ( fs_schema_public.day_width / 2);
			
			event_current_y = jQuery( event_el ).position().top - 80; // - fs_schema_public.day_width;
			
			event_diff_x = event_target_x-event_current_x;
			
			event_diff_y = event_target_y-event_current_y + 30;
		
			fs_schema_public.open_event_el = event_el;
		
			jQuery( '.fs_schema .open_event' )
			
				.addClass('open')
			
				.css( 'display', 'block').css('top', event_current_y ).css('left', event_current_x )
				
				.css('transition' , 'none').css('-moz-transition' , 'none').css('-webkit-transition' , 'none').css('-o-transition' , 'none')
				
				.css( '-webkit-transform', 'scale(0.1)').css( '-moz-transform', 'scale(0.1)').css( 'transform', 'scale(0.1)');
				
			bookable = jQuery( event_el ).attr( 'data-bookableslots' );	
			
			if ( bookable > 1 ) bookable = bookable + ' bokningsbara';
			
			else bookable = bookable + ' bokningsbar';
			
			fs_schema_public.open_event_id = jQuery( event_el ).attr( 'data-id' );
			
			jQuery( '.fs_schema .open_event .header' ).html( jQuery( event_el ).attr( 'data-product' ));
			
			jQuery( '.fs_schema .open_event .date span' ).html( jQuery( event_el ).attr( 'data-startdate' ));
			
			jQuery( '.fs_schema .open_event .time span' ).html( jQuery( event_el ).attr( 'data-start' ) + '-' + jQuery( event_el ).attr( 'data-end' ));
			
			jQuery( '.fs_schema .open_event .staff span' ).html( jQuery( event_el ).attr( 'data-staff' ));
			
			jQuery( '.fs_schema .open_event .room span' ).html( jQuery( event_el ).attr( 'data-room' ));
			
			jQuery( '.fs_schema .open_event .freeslots span' ).html(  jQuery( event_el ).attr( 'data-freeslots' ) + ' (' + bookable + ')' );
			
			
			// adjust event on what status the event has got
			entry_status = jQuery( event_el ).attr( 'data-status' );
			
			entry_info_dropin = jQuery( '.fs_schema .open_event .entry_info_dropin' );
			
			entry_info_full = jQuery( '.fs_schema .open_event .entry_info_full' );
			
			entry_info_cancelled = jQuery( '.fs_schema .open_event .entry_info_cancelled' );
			
			entry_info_not_bookable = jQuery( '.fs_schema .open_event .entry_info_not_bookable' );
			
			entry_info_dropin.css( 'display', 'none' );
			
			entry_info_full.css( 'display', 'none' );
			
			entry_info_cancelled.css( 'display', 'none' );
			
			entry_info_not_bookable.css( 'display', 'none' );
			
			entry_is_bookable = false;
			
			switch ( entry_status ) {
			
				default: entry_is_bookable = true; break;
					
				case 'dropin': entry_info_dropin.css( 'display', 'block' ); break;
					
				case 'reserve': break;
					
				case 'notbookable': entry_info_not_bookable.css( 'display', 'block' ); break;
					
				case 'full': entry_info_full.css( 'display', 'block' ); break;
					
				case 'closed': entry_info_not_bookable.css( 'display', 'block' ); break;
				
				case 'cancelled': entry_info_cancelled.css( 'display', 'block' ); break;
			}
									
			fs_schema_public.open_bookingid = jQuery( event_el ).attr( 'data-bookingid' );
			
			// user has allready booked this event
			if ( fs_schema_public.open_bookingid != '' ) {
			
				jQuery( '.fs_schema .open_event .booked_info' ).css( 'display', 'block' );
			
				jQuery( '.fs_schema .open_event .unbook_event' ).css( 'display', 'inline' );
				
				jQuery( '.fs_schema .open_event .book_event' ).css( 'display', 'none' );
				
				jQuery( '.fs_schema .open_event .login_book_event' ).css( 'display', 'none' );

			// user has not booked this event
			} else {
			
				jQuery( '.fs_schema .open_event .booked_info' ).css( 'display', 'none' );
			
				jQuery( '.fs_schema .open_event .unbook_event' ).css( 'display', 'none' );
				
				// this event is not bookable
				if ( entry_is_bookable == false ) {
					
					jQuery( '.fs_schema .open_event .book_event' ).css( 'display', 'none' );
					
					jQuery( '.fs_schema .open_event .login_book_event' ).css( 'display', 'none' );
					
					jQuery( '.fs_schema .open_event .loginform' ).css( 'display', 'none' );
				
					jQuery( '.fs_schema .open_event .login_book_event' ).css( 'display', 'none' );
					
					jQuery( '.fs_schema .open_event .loggedin' ).css( 'display', 'none' );
				
				// this event is bookable, and user has not booked it yet
				} else {
			
					// user is logged in
					if ( fs_schema_public.password != '' ) {
					
						jQuery( '.fs_schema .open_event .book_event' ).css( 'display', 'inline' );
						
						jQuery( '.fs_schema .open_event .loggedin' ).css( 'display', 'block' );
						
					// user is not logged in
					} else {
					
						jQuery( '.fs_schema .open_event .loginform' ).css( 'display', 'block' );
						
						jQuery( '.fs_schema .open_event .login_book_event' ).css( 'display', 'inline' );
						
					}
				}
			}

			window.setTimeout(function() {
			
				jQuery( '.fs_schema .open_event' )
				
					.css('transition' , '0.1s').css('-moz-transition' , '0.1s').css('-webkit-transition' , '0.1s').css('-o-transition' , '0.1s')
					
					.css( '-webkit-transform', 'translate(' + event_diff_x + 'px, ' + event_diff_y + 'px) scale(1)')
					
					.css( '-moz-transform', 'translate(' + event_diff_x + 'px, ' + event_diff_y + 'px) scale(1)')
					
					.css( 'transform', 'translate(' + event_diff_x + 'px, ' + event_diff_y + 'px) scale(1)');
				
				}, 100);
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
		
		big_message = jQuery( '.fs_schema .dialogue.open .big_message' );
	
		jQuery( big_message ).css ( 'display', 'block' ).addClass('open');
		
		if ( error == true ) jQuery( big_message ).addClass ( 'error' );
		
		else jQuery( big_message ).removeClass ( 'error' );
		
		jQuery( big_message ).find( '.head' ).html( header );
		
		jQuery( big_message ).find( '.info' ).html( message );		
	
	},
	
	
	
	close_dialogue_big_message : function () {
	
		jQuery( '.fs_schema .dialogue.open .big_message.open' ).css ( 'display', 'none' ).removeClass('open');

	},
		
	
	login_and_book_event : function () {
	
		fs_schema_public.book_event_after_login = true;
		
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
				
				refresh_after_booked = false;
				
				jQuery( '.fs_schema .open_event .progress').css( 'display', 'none' );
				
				if ( data.error != '' ) {
				
					fs_schema_public.display_dialogue_big_message ( 'Fel.', data.message, true );
				
				} else {
				
					if ( data.bookingid == '' ) {	// profit dont give us a booking id when booking, what kind of booking system is that?
					
						refresh_after_booked = true;
					
					} else {
				
						jQuery( fs_schema_public.open_event_el ).attr( 'data-bookingid',  data.bookingid );
						
					}
					
					fs_schema_public.animate_source_on_close_event = true;
					
					fs_schema_public.add_source_class_on_close_event = 'booked';
					
					fs_schema_public.close_event();
					
					fs_schema_public.show_hud ( 'Bokat!' );
				}
				
				if ( refresh_after_booked == true ) {
				
					if ( fs_schema_public.forceday && fs_schema_public.num_days == 7 ) {
					
						fs_schema_public.change_to_day();
							
						jQuery('.fs_schema .change_to_week').addClass('disabled').attr('title',  'Profit bokningssystem kan inte visa veckoschemat utan bara en dag i taget när man är inloggad.');
						
					} else {
						
						fs_schema_public.refresh();
					
					}
				
				} else {
				
					fs_schema_public.after_book_event ();
						
					fs_schema_public.after_book_event = function() {};
					
				}
					
				jQuery( '.fs_schema .debug').html( data.debug );
			}
		);
	
	},
	
	
	unbook_event : function () {
	
		if ( fs_schema_public.open_bookingid != '' ) {
		
			if ( fs_schema_public.username != '' && fs_schema_public.password != '' ) {
	
				fs_schema_public.is_busy = true;
				
				window.clearTimeout( fs_schema_public.dialogue_message_timeout );
				
				jQuery( '.fs_schema .open_event .message' ).css( 'display', 'none' );
		
				jQuery( '.fs_schema .open_event .progress .doingwhat').html('Avbokar aktiviteten');
				
				jQuery( '.fs_schema .open_event .progress').css( 'display', 'block' );
				
				fs_schema_public.ajax (
				
					{ action : 'unbook_activity', bookingid: fs_schema_public.open_bookingid, username: fs_schema_public.username, password: fs_schema_public.password, session_key: fs_schema_public.session_key }, 'json', function ( data ) { 
					
						//jQuery( '.fs_schema .debug').html( data );
						
						jQuery( '.fs_schema .debug').html( data.debug );
						
						fs_schema_public.is_busy = false;
						
						jQuery( '.fs_schema .open_event .progress').css( 'display', 'none' );
						
						if ( data.error != '' ) {
						
							fs_schema_public.display_dialogue_big_message ( 'Fel.', data.message, true );
						
						} else {
						
							//fs_schema_public.display_dialogue_big_message ( 'Avbokat.', data.message, false );
							
							fs_schema_public.animate_source_on_close_event = true;
							
							fs_schema_public.remove_source_class_on_close_event = 'booked';
							
							jQuery( fs_schema_public.open_event_el ).attr( 'data-bookingid', '' );
							
							fs_schema_public.close_event();
					
							fs_schema_public.show_hud ( 'Avbokat!' );
							
						}
					}
				);
				
			} else {
			
				fs_schema_public.display_dialogue_big_message ( 'Fel', 'Saknar av någon anledning ditt använarnamn och lösenord. Prova att ladda om sidan, logga in igen och avboka därefter igen.', true );
			
			}
			
		} else {
		
			fs_schema_public.display_dialogue_big_message ( 'Fel', 'Saknar av någon anledning ID på din bokning och kan därför inte göra en avbokning just nu. Prova att ladda om sidan och gör om proceduren.', true );

		}
	}, 

	
	
	close_event : function () {
		
		jQuery( '.fs_schema .open_event' )
				
			.css( 'transition' , '0.1s').css('-moz-transition' , '0.1s').css('-webkit-transition' , '0.1s').css('-o-transition' , '0.1s')
			
			.css( '-webkit-transform', 'translate(0px, 0px) scale(0.1)').css( '-moz-transform', 'translate(0px, 0px) scale(0.1)').css( 'transform', 'translate(0px, 0px) scale(0.1)');
			
		window.setTimeout(function() {
			
			jQuery( '.fs_schema .open_event' )
			
				.css( 'display', 'none')
			
				.css('transition' , 'none').css('-moz-transition' , 'none').css('-webkit-transition' , 'none').css('-o-transition' , 'none');
				
				if ( fs_schema_public.animate_source_on_close_event == true ) {

					jQuery( fs_schema_public.open_event_el )
						
						.removeClass ( fs_schema_public.remove_source_class_on_close_event )
						
						.addClass ( fs_schema_public.add_source_class_on_close_event )
						
						.css( '-webkit-transform', 'translate(-5px, -5px) scale(1.5)').css( '-moz-transform', 'translate(-5px, -5px) scale(1.5)').css( 'transform', 'translate(-5px, -5px) scale(1.5)');
		
					window.setTimeout(function() {
					
						jQuery( fs_schema_public.open_event_el )
					
							.css( '-webkit-transform', 'translate(0px, 0px) scale(1)').css( '-moz-transform', 'translate(0px, 0px) scale(1)').css( 'transform', 'translate(0px, 0px) scale(1)');					
					
					}, 100);
	
					fs_schema_public.open_event_id = 0;
					
				} else {
				
					fs_schema_public.open_event_id = 0;			
				
				}
			
			}, 100);
	},
	
	
	
	set_values : function () {
	
		fs_schema_public.schema_width = jQuery('.fs_schema').outerWidth(true);
		
		fs_schema_public.day_width = jQuery('.fs_schema').attr('data-day-width');
		
		fs_schema_public.hours_width = jQuery('.fs_schema').attr('data-hours-width');
		
		jQuery( '.fs_schema .week_overlay' ).css('left', ((fs_schema_public.schema_width/2)-100));
		
		if ( jQuery('.fs_schema.enableday ').length > 0 ) fs_schema_public.enableday = true;
		
		if ( jQuery('.fs_schema.enableweek ').length > 0 ) fs_schema_public.enableweek = true;
		
		if ( jQuery('.fs_schema.day').length > 0 ) fs_schema_public.num_days = 1;
		
	},
	
	

	ajax : function ( data, datatype, fn_success, fn_error ) {
	
		//data.nonce: fsschemavars.nonce;
	
		jQuery.ajax({
		
			type: "POST", url: fsschemavars.ajaxurl, dataType: datatype, data: data, context: document.body,
			
			success: function( data) { fn_success ( data ); },
			
			error: function ( jqXHR, textStatus, errorThrown )  { 
				
				if ( fn_error ) { fn_error(); } 
				
				else { 
				
					fs_schema_public.display_dialogue_big_message ( 'Fel.', 'Det uppstod ett oväntat fel. Detta är den tekniska beskrivningen. Ledsen om det verkar kryptiskt:<br><br>' + textStatus + ' ' + errorThrown , true ); 
				
					fs_schema_public.is_busy = false;
				
				}
			}
			
		});      
     }
}

jQuery(document).ready(function() { fs_schema_public.init(); });



