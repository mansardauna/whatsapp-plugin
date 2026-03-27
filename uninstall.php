<?php
/**
 * WP WhatsApp CRM Uninstall
 *
 * This file is called when the plugin is deleted.
 * It cleans up all the data created by the plugin.
 */

// If uninstall not called from WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete options
delete_option( 'wwc_phone' );
delete_option( 'wwc_message' );
delete_option( 'wwc_button_text' );
delete_option( 'wwc_enabled' );

// Drop custom table
global $wpdb;
$table_name = $wpdb->prefix . 'wwc_leads';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
