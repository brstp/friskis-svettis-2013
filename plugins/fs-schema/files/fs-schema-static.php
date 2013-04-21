<?php
/*/////////////////////////////////////////////////////////////////

	FS SCHEMA, PHP Class - STATIC, inherits by fs_schema

	Copyright (C) 2013 Klas Ehnemark (http://klasehnemark.com)
	This program is not free software.

//////////////////////////////////////////////////////////////////*/


class fs_schema_static {

	////////////////////////////////////////////////////////////////////////////////
	//
	// CONSTRUCTOR
	//
	////////////////////////////////////////////////////////////////////////////////

	public function __construct () {}
	


	////////////////////////////////////////////////////////////////////////////////
	// GET DATE FROM STRING
	////////////////////////////////////////////////////////////////////////////////	
	
	public static function get_date_from_string ( $string ) {
	
		$date_time_parts = explode( ' ', $string );
		
		if ( isset ( $date_time_parts[0] ) && isset ( $date_time_parts[1] ) && !isset ( $date_time_parts[2] )) {
		
			$date_parts = explode( '-', $date_time_parts[0] );
			
			$time_parts = explode( ':', $date_time_parts[1] );
			
			if ( isset( $date_parts[0] ) && isset( $date_parts[1] ) && isset( $date_parts[2] ) && !isset( $date_parts[3] ) && isset( $time_parts[0] ) && isset( $time_parts[1] ) && !isset( $time_parts[2] )) {
			
				if ( checkdate ( $date_parts[1], $date_parts[2], $date_parts[0] ) === true && $time_parts[0] > -1 && $time_parts[0] < 24 && $time_parts[1] > -1 && $time_parts[1] < 60  ) {
				
					return mktime($time_parts[0], $time_parts[1], 0, $date_parts[1], $date_parts[2], $date_parts[0]);
				
				}
			}		
		
		} else {
	
			$date_parts = explode( '-', $string );
			
			if ( isset( $date_parts[0] ) && isset( $date_parts[1] ) && isset( $date_parts[2] ) && !isset( $date_parts[3] )) {
			
				if ( checkdate ( $date_parts[1], $date_parts[2], $date_parts[0] ) === true ) {
				
					return mktime(0, 0, 0, $date_parts[1], $date_parts[2], $date_parts[0]);
				
				}
			}
		}
		
		return false;
	}
}

?>