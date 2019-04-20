<?php

/**
 *  Read more about creating tables with wordpress
 * 
 *  https://codex.wordpress.org/Creating_Tables_with_Plugins
 */


 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


global $jal_db_version;
$jal_db_version = '1.0';

function wptrus_install() {
	global $wpdb;
	global $jal_db_version;

	$table_name = $wpdb->prefix . WPTRUS_TABLE_NAME;

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time int(11) NOT NULL,
		name tinytext ,
		email tinytext ,
		message text NOT NULL,
		postid int(11),
        ip varchar(15) ,
		archive boolean DEFAULT 0,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'jal_db_version', $jal_db_version );
}

// function wptrus_install_data() {
// 	global $wpdb;
    
//     $table_name = $wpdb->prefix . 'consultingrequests';
// 	$welcome_name = 'Mr. WordPress';
// 	$welcome_text = 'Congratulations, you just completed the installation!';
	

// 	$wpdb->insert( 
// 		$table_name, 
// 		array( 
// 			'time' => current_time( 'mysql' ), 
// 			'name' => $welcome_name, 
// 			'text' => $welcome_text, 
// 		) 
// 	);
// }
