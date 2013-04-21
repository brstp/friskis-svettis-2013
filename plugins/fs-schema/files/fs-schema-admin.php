<?php
/*/////////////////////////////////////////////////////////////////

	FS SCHEMA, PHP Class - ADMIN

	Copyright (C) 2013 Klas Ehnemark (http://klasehnemark.com)
	This program is not free software.

//////////////////////////////////////////////////////////////////*/


class fs_schema_admin {

	public $admin_notices = array();


	////////////////////////////////////////////////////////////////////////////////
	//
	// INITIALIZE OBJECT
	//
	////////////////////////////////////////////////////////////////////////////////

	public function __construct ( $plugin_basename ) {

		// Initialization stuff
		add_action ( 'admin_init', 							array ( &$this, 'wordpress_admin_init' ));
		
		add_action ( 'admin_menu', 							array ( &$this, 'wordpress_admin_menu' ));
		
		add_filter ( 'plugin_action_links_' . $plugin_basename, 	array ( &$this, 'plugin_settings_link' ));
				
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	// INIT ADMIN
	//
	////////////////////////////////////////////////////////////////////////////////

	function wordpress_admin_init () {

		wp_register_script	( 'fs-schema-admin', 	plugins_url('fs-schema') . '/files/fs-schema-admin-script.js' );
		
		wp_enqueue_script 	( 'fs-schema-admin' );

		wp_register_style 	( 'fs-schema-admin', 	plugins_url('fs-schema') . '/files/fs-schema-admin-styles.css' );
		
		wp_enqueue_style 	( 'fs-schema-admin' );
		
		add_action		( 'admin_notices', array ( $this, 'admin_notices' ));
		
	}		


	////////////////////////////////////////////////////////////////////////////////
	//
	// DISPLAY ADMIN NOTICES
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function admin_notices () {

		$admin_notice_details = array();
		
		if ( get_current_screen()->id != 'settings_page_fs-schema' ) {
			
			$fs_schema_link = '<a href="options-general.php?page=fs-schema">Friskis & Svettis SCHEMA</a>';
			
			if ( get_option( 'fs_schema_integration' ) == 'BRP' ) {
				
				if ( get_option( 'fs_schema_brp_server_url' ) == '' ) 			array_push ( $admin_notice_details, 'Sökväg till BRP-servern' );
				
				if ( get_option( 'fs_booking_bpi_api_key' ) == '' ) 			array_push ( $admin_notice_details, 'BRP API-Nyckel' );
				
				if ( count ( $admin_notice_details ) > 0 ) 					array_push ( $this->admin_notices, $fs_schema_link . ': Du har angivit BRP som bokningssystem men måste fylla i följande fält för att schemat ska fungera: ' . implode ( $admin_notice_details, ', ' ) );
				
			} else if ( get_option( 'fs_schema_integration' ) == 'PROFIT' ) {
			
				if ( get_option( 'fs_schema_profit_server_url' ) == '' ) 		array_push ( $admin_notice_details, 'Sökväg till PROFIT-servern' );
				
				if ( get_option( 'fs_booking_profit_contract_name' ) == '' ) 	array_push ( $admin_notice_details, 'PROFIT Kontraktsnamn' );
				
				if ( get_option( 'fs_booking_profit_license_key' ) == '' ) 		array_push ( $admin_notice_details, 'PROFIT Licensnyckel' );
				
				if ( count ( $admin_notice_details ) > 0 ) 					array_push ( $this->admin_notices, $fs_schema_link . ': Du har angivit PROFIT som bokningssystem men måste fylla i följande fält för att schemat ska fungera: ' . implode ( $admin_notice_details, ', ' ) );
					
			} else {
			
				array_push ( $this->admin_notices, $fs_schema_link .': Du har inte angivit något bokningssystem som du måste göra om schemat ska fungera.' );
	
			}
			
		}
		
		if ( count ( $this->admin_notices ) > 0 ) {
		
			echo '<div class="error" style="padding: 7px; "><strong>' . implode ( $this->admin_notices ) . '</strong></div>';
		
		}
		
	}

	
	////////////////////////////////////////////////////////////////////////////////
	//
	// RENDER ADMIN MENU AND ADMIN OPTIONS PAGE
	//
	////////////////////////////////////////////////////////////////////////////////		
	
	function wordpress_admin_menu () {

		add_options_page ( 'FS-Schema', 'Friskis & Svettis Schema', 'administrator', 'fs-schema', array ( &$this, 'admin_page') );
		
	}
	
	function plugin_settings_link ($links) {
	
		$settings_link = '<a href="options-general.php?page=fs-schema">Settings</a>';
		
		array_unshift($links, $settings_link);
		
		return $links; 		
	}



	////////////////////////////////////////////////////////////////////////////////
	//
	// SHOW ADMIN PAGE
	//
	////////////////////////////////////////////////////////////////////////////////		
		
	function admin_page () {
	
	
		// check previligies
		if ( !current_user_can ( 'manage_options' )) wp_die( __('You do not have sufficient permissions to access this page.') ); 
		
		
		
		// See if the user has posted us some information. If they did, this hidden field will be set to 'Y'
		if( isset ( $_POST [ 'submit_options_hidden' ] ) && $_POST [ 'submit_options_hidden' ] == 'Y' ) {
				
				
			// save options
			$this->update_option_from_form ( 'fs_schema_integration' );
			
			$this->update_option_from_form ( 'fs_schema_brp_server_url' );
			
			$this->update_option_from_form ( 'fs_schema_profit_server_url' );
			
			$this->update_option_from_form ( 'fs_booking_bpi_api_key' );
			
			$this->update_option_from_form ( 'fs_booking_bpi_businessunitids' );
			
			$this->update_option_from_form ( 'fs_booking_profit_contract_name' );
			
			$this->update_option_from_form ( 'fs_booking_profit_license_key' );
			
			$this->update_option_from_form ( 'fs_schema_extra_column' );
			
			$this->update_option_from_form ( 'fs_schema_update_inteval' );
			
			
			// remove any spaces in businessunitids
			$fs_booking_bpi_businessunitids 	=  get_option( '$fs_booking_bpi_businessunitids' );
			
			if ( strpos ( $fs_booking_bpi_businessunitids, ' ' ) ) update_option ( 'fs_booking_bpi_businessunitids', str_replace( ' ', '',  $fs_booking_bpi_businessunitids ));

			
			// Put an settings updated message on the screen
			echo '<div class="updated"><p><strong>Inställningana är sparade</strong></p></div>';
	
		}
		
		// get saved settings
		global $fs_schema;
		
		$settings = $fs_schema->data->settings();
		
		// get stored variables
		$fs_schema_integration				= $settings[ 'fs_schema_integration' ] 			== '' ? 'BRP' 						: $settings[ 'fs_schema_integration' ] ;
		
		$fs_schema_brp_server_url 			= $settings[ 'fs_schema_brp_server_url' ] 		== '' ? 'http://BRP Server default' 	: $settings[ 'fs_schema_brp_server_url' ];
		
		$fs_schema_profit_server_url 			= $settings[ 'fs_schema_profit_server_url' ] 	== '' ? 'http://PROFIT Server default' 	: $settings[ 'fs_schema_profit_server_url' ];
		
		$fs_booking_bpi_api_key				= $settings[ 'fs_booking_bpi_api_key' ];
		
		$fs_booking_bpi_businessunitids		= $settings[ 'fs_booking_bpi_businessunitids' ];
		
		$fs_booking_profit_contract_name 		= $settings[ 'fs_booking_profit_contract_name' ];
		
		$fs_booking_profit_license_key 		= $settings[ 'fs_booking_profit_license_key' ];
		
		$fs_schema_extra_column				= $settings[ 'fs_schema_extra_column' ];
		
		$fs_schema_update_inteval			= $settings[ 'fs_schema_update_inteval' ];
		
		
		$fs_booking_bpi_businessunitids_html 	= $fs_schema->data->get_businessunits ( 'BRP' );
		
	
		echo 	'<div class="wrap"><form method="post" action=""><input type="hidden" name="submit_options_hidden" value="Y" />';
		echo 	'<div id="icon-options-general" class="icon32"><br></div><h2>Friskis & Svettis Schema</h2>';
		
		wp_nonce_field ( 'update-options' );
		
		echo		'<p>Dessa inställningar gör det möjligt att använda följande s.k. shortcode för att generera schema och bokningsmöjligheter på din Frisiks & Svettis Wordpress sajt:</p>

				<table class="form-table" cellspacing="0">
					<tr>
						<th scope="row" class="desc"><code>[fs-schema]</code></th>
						<td class="desc howto" style="padding-left: 12px; ">Genererar ett schema och bokningsfunktion på sidan som hämtas från bokningssystemet och anpassas efter inställningarna nedan. <a class="fs_shema_shortcode_help" href="javascript:fs_schema_admin.show_shortcode_help();">Visa mer</a>
							<div class="fs_shema_shortcode_help">
								<p style="position: relative; top:10px; "><strong>Parametrar</strong></p>
								<ul>
									<li><code>typ</code> - anger typ av schema; <code>vecka</code> (förvalt) eller <code>pass</code>.</li>
									<li><code>anlaggning</code> - visar bara schemat för en specifik anläggning, anges med ID (se nedan).</li>
									<li><code>bokning</code> - sätts till 0 (noll) om man vill stänga av bokningsfunktionen.</li>
								</ul>
								<p style="position: relative; top:10px; "><strong>Exempel</strong></p>
								<ul>
									<li>Följande exempel visar ett pass-schema för anläggningen md ID 3 utan möjlighet till bokning: <code>[fs-schema typ=pass anlaggning=3 bokning=0]</code></li>
								</ul>
							</div>
						</td>
					</tr>
				</table>
				
				<h2>Inställningar</h2>
				 
				<table class="form-table">
				
					<tr>
						<th scope="row">Bokningssystem</th>
						<td>
							<fieldset><legend class="screen-reader-text"><span>Integration</span></legend>
								<label title="BRP">		<input type="radio" name="fs_schema_integration"	value="BRP" ' . ( $fs_schema_integration == 'BRP' ? 'checked="checked"' : '' ) . ' /> <span>BRP</span></label><br />
								<label title="Profit">	<input type="radio" name="fs_schema_integration" 	value="PROFIT" ' . ( $fs_schema_integration == 'PROFIT' ? 'checked="checked"' : '' ) . '/> Profit </label>
								<p class="description">Välj vilket bokningssystem din förening använder. För att integrera mot ett bokningssystem måste du har ett avtal med dem.</p>
							</fieldset>
						</td>
					</tr>
					
					<tr valign="top" class="fs_schema_brp" ' . ( $fs_schema_integration != 'BRPs' ? ' style="display: none;"' : '' ) . '>
						<th scope="row"><label for="fs_schema_brp_server_url">BRP API-url</label></th>
						<td>
							<input name="fs_schema_brp_server_url" type="text" id="fs_schema_brp_server_url" value="' . $fs_schema_brp_server_url . '" class="regular-text" />
							<p class="description">Ange sökvägen till den server som BRP ligger på. Ändra bara denna om du fått instruktioner från BRP.</p>
						</td>
					</tr>
					
					<tr valign="top" class="fs_schema_profit"' . ( $fs_schema_integration != 'PROFIT' ? ' style="display: none;"' : '' ) . '>
						<th scope="row"><label for="fs_schema_profit_server_url">Sökväg till Profit-servern</label></th>
						<td>
							<input name="fs_schema_profit_server_url" type="text" id="fs_schema_profit_server_url" value="' . $fs_schema_profit_server_url . '" class="regular-text" />
							<p class="description">Ange sökvägen till den server som BRP/Profit ligger på. Ändra bara denna om du fått instruktioner från BRP/Profit.</p>
						</td>
					</tr>
					
					<tr valign="top" class="fs_schema_brp"' . ( $fs_schema_integration != 'BRP' ? ' style="display: none;"' : '' ) . '>
						<th scope="row"><label for="fs_booking_bpi_api_key">BRP API-nyckel</label></th>
						<td>
							<input name="fs_booking_bpi_api_key" type="text" id="fs_booking_bpi_api_key" value="' . $fs_booking_bpi_api_key . '" class="regular-text" />
							<p class="description">Ange den API-nyckel som BRP har tillhandahållit din förening.</p>
						</td>
					</tr>
					
					<tr valign="top" class="fs_schema_brp"' . ( $fs_schema_integration != 'BRP' ? ' style="display: none;"' : '' ) . '>
						<th scope="row"><label for="fs_booking_bpi_businessunitids">Visa anläggningar</label></th>
						<td>
							<div class="brp_no_businessunitsloaded"' . ( $fs_booking_bpi_businessunitids_html  != '' ? ' style="display: none;"' : '' ) . '>
								<input class="button-primary" type="button" value="Hämta info om anläggningar" onclick="fs_schema_admin.brp_update_businessunitids();">
							</div>
							<div class="brp_businessunitsloaded brp_businessunitslist" ' . ( $fs_booking_bpi_businessunitids_html  == '' ? ' style="display: none;"' : '' ) . '>
							' . $fs_booking_bpi_businessunitids_html . '
							</div>
							<p class="description">Ange vilka anläggningar som ska listas i schemat. Du måste välja minst en.
							<span class="description brp_businessunitsloaded" ' . ( $fs_booking_bpi_businessunitids_html  == '' ? ' style="display: none;"' : '' ) . '><a href="javascript: fs_schema_admin.brp_update_businessunitids();">Uppdatera listan från BRP</a>.</span>
							<br />Du kan välja att visa bara en anläggning när du skriver shortcode genom att ange id på anläggningen, exempelvis [fs_schema anlaggning=3]
							</p>
						</td>
					</tr>
					
					<tr valign="top" class="fs_schema_profit"' . ( $fs_schema_integration != 'PROFIT' ? ' style="display: none;"' : '' ) . '>
						<th scope="row"><label for="fs_booking_profit_contract_name">Profit "3-Party Kontraktsnamn"</label></th>
						<td>
							<input name="fs_booking_profit_contract_name" type="text" id="fs_booking_profit_contract_name" value="' . $fs_booking_profit_contract_name . '" class="regular-text" />
							<p class="description">Ange det kontraktnamn som Profit har tillhandahållit din förening.</p>
						</td>
					</tr>
					
					<tr valign="top" class="fs_schema_profit"' . ( $fs_schema_integration != 'PROFIT' ? ' style="display: none;"' : '' ) . '>
						<th scope="row"><label for="fs_booking_profit_license_key">Profit Licensnyckel</label></th>
						<td>
							<input name="fs_booking_profit_license_key" type="text" id="fs_booking_profit_license_key" value="' . $fs_booking_profit_license_key . '" class="regular-text" />
							<p class="description">Ange den licensnyckel som Profit har tillhandahållit din förening.</p>
						</td>
					</tr>
					
					<!--<tr>
						<th scope="row"><label for="fs_schema_extra_column">Ev. extrakolumn</label></th>
						<td>
							<select id="fs_schema_extra_column" name="fs_schema_extra_column">
								<option value="">Ingen</option>
								<option value="Anläggning" 	' . ( $fs_schema_extra_column == 'Anläggning' ? ' selected="selected"' : '' ) . '>Anläggning</option>
								<option value="Lokal" 		' . ( $fs_schema_extra_column == 'Lokal' ? 	' selected="selected"' : '' ) . '>Lokal</option>
							</select>
							<p class="description">Ange om schemat på startsidan ska visa en extrakolumn, och i så fall vilken. Alla val kan eventuellt inte fungera med alla bokningssystem.</p>
						</td>
						
					</tr>-->
					
					<tr valign="top">
						<th scope="row"><label for="fs_booking_profit_license_key">Uppdateingsintervall</label></th>
						<td>
							<input name="fs_schema_update_inteval" type="text" id="fs_schema_update_inteval" value="' . $fs_schema_update_inteval . '" class="small-text" /> minuter
							<p class="description">Ange hur ofta Wordpress ska uppdatera schemat från bokningssystemet. 60 minuter är ganska rimligt.<br>När användaren är inloggad hämtas informationen alltid direkt, utan mellanlagring.</p>
						</td>
					</tr>										

				</table>
				<p class="submit"><input type="submit" name="Submit" class="button-primary" value="Spara inställningarna" /></p></div></form></div>';
		
	}
	

	////////////////////////////////////////////////////////////////////////////////
	//
	// Private function: Update option from admin form
	//
	////////////////////////////////////////////////////////////////////////////////		
		
	private function update_option_from_form ( $option_name, $array = false ) {
	
		if ( ISSET ( $_POST [ $option_name ] )) {
		
			if ( is_array ( $_POST [ $option_name ] )) 
			
				update_option( $option_name, implode ( ',', $_POST[ $option_name ] ));
				
			else 
				
				update_option( $option_name, $_POST[ $option_name ] );
			
		}
		
		else delete_option( $option_name );
	
	}
	
} //End Class
?>