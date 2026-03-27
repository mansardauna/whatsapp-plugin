<?php
/**
 * Class WWC_DB
 * Handles database operations for the plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WWC_DB {

	/**
	 * Create the leads table on activation.
	 */
	public static function create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wwc_leads';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id INT NOT NULL AUTO_INCREMENT,
			phone VARCHAR(20) NOT NULL,
			page TEXT NOT NULL,
			ip VARCHAR(45) NOT NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	/**
	 * Insert a new lead into the database.
	 *
	 * @param array $data Lead data.
	 * @return int|bool Insertion result.
	 */
	public static function insert_lead( $data ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wwc_leads';

		return $wpdb->insert(
			$table_name,
			array(
				'phone'      => sanitize_text_field( $data['phone'] ),
				'page'       => esc_url_raw( $data['page'] ),
				'ip'         => sanitize_text_field( $data['ip'] ),
				'created_at' => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%s' )
		);
	}

	/**
	 * Delete a lead by ID.
	 *
	 * @param int $id Lead ID.
	 * @return int|bool Deletion result.
	 */
	public static function delete_lead( $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wwc_leads';

		return $wpdb->delete(
			$table_name,
			array( 'id' => (int) $id ),
			array( '%d' )
		);
	}

	/**
	 * Get all leads.
	 *
	 * @return array Leads.
	 */
	public static function get_leads() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wwc_leads';

		return $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC", ARRAY_A );
	}
}
