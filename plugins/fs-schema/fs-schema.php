<?php
/*/////////////////////////////////////////////////////////////////

	Plugin Name: FS SCHEMA
	Plugin URI: http://klasehnemark.com
	Description: Detta plugin ger funktionalitet att integrera både BRP och PROFITs bokningssystem på en Friskis & Svettis Wordpress sajt. 
	Author: Klas Ehnemark
	Version: 0.97
	Author URI: http://klasehnemark.com
	
	Copyright (C) 2013 Klas Ehnemark (http://klasehnemark.com)
	This program is not free software.
	
	BRP Demo 
	http://leverans1.brpsystems.se:8080/leverans09gog/api/ver2/ 
	ec65a5e56dfd4908b0a92842d98a0dbc
	Login: klas@ehnemark.com/test1280
	
	FS Södertälje
	http://brp.netono.se/fssodtgog/api/ver2/
	1a07fce1ac224a669dcae02412bcd084
	
	FS Danderyd
	http://brp2.netono.se/fsdanderydgog/api/ver2/
	6a8aa4c1c6d84e3f9d17ced2ae5bec93
	
	Profit
	http://bookapi.pastell16.pastelldata.se/v4186/MobileServices.asmx?op=processUnsafe
	Txx3453HgbPWW132
	1437

//////////////////////////////////////////////////////////////////*/


ob_start ();



////////////////////////////////////////////////////////////////////////////////
//
// REQUIRE FILES
//
////////////////////////////////////////////////////////////////////////////////

require_once ( "files/fs-schema-admin.php" );

require_once ( "files/fs-schema-ajax.php" );

require_once ( "files/fs-schema-data.php" );

require_once ( "files/fs-schema-public.php" );

require_once ( "files/fs-schema-shortcodes.php" );

require_once ( "files/fs-schema-static.php" );

require_once ( "files/fs-schema-brp.php" );

require_once ( "files/fs-schema-profit.php" );



////////////////////////////////////////////////////////////////////////////////
//
// ERROR HANDLING ACTIVATING PLUGIN
//
////////////////////////////////////////////////////////////////////////////////

add_action ( 'activated_plugin', 'fs_schema_save_error' );

function fs_schema_save_error () {

    update_option ( 'fs_schema_plugin_error',  ob_get_contents () );
    
}

update_option ( 'fs_schema_plugin_error',  '' );




////////////////////////////////////////////////////////////////////////////////
//
// MAIN CLASS
//
////////////////////////////////////////////////////////////////////////////////


class fs_schema extends fs_schema_static {

	public $admin;
	
	public $ajax;
	
	public $data;
	
	public $public;
	
	public $shortcodes;
	

	////////////////////////////////////////////////////////////////////////////////
	//
	// INITIALIZE OBJECT
	//
	////////////////////////////////////////////////////////////////////////////////

	public function __construct() {
	

		///////////////////////////////////////////////////////////////////////////
		//
		// Start initializing containing objects
		//
		///////////////////////////////////////////////////////////////////////////

		$this->admin 		= new fs_schema_admin ( plugin_basename(__FILE__) );
		
		$this->ajax 		= new fs_schema_ajax ();
		
		$this->data 		= new fs_schema_data ();
		
		$this->public 		= new fs_schema_public ();
		
		$this->shortcodes 	= new fs_schema_shortcodes ();
		


		///////////////////////////////////////////////////////////////////////////
		//
		// Register hooks for activate and deactivate plugin
		//
		///////////////////////////////////////////////////////////////////////////
		
		register_activation_hook 	(__FILE__, 	array( &$this, 'on_activate_plugin' ));
		
		register_deactivation_hook 	( __FILE__, 	array( &$this, 'on_deactivate_plugin' ));
		
		register_uninstall_hook 		( __FILE__, 	'fs_schema::on_uninstall_plugin' );



	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	// ACTIVATING AND DEACTIVATING PLUGIN FUNCTION
	//
	////////////////////////////////////////////////////////////////////////////////
	
	public function on_activate_plugin() {
	
	}
	
	public function on_deactivate_plugin() {
	
	}
	
	public static function on_uninstall_plugin() {
	
	}	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	// FOR DEBUGGING
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public static function debug ( $what, $die = false ) {
	
		$output = '<pre>' . print_r ( $what, true ) . '</pre>';
		
		if ( $die === true ) wp_die ( $output );
		else echo $output;
	
	}
	

	
} //End Class

$fs_schema = new fs_schema();

echo get_option( 'fs_schema_plugin_error' );

?>