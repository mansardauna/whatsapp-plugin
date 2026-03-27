<?php
/**
 * Class WWC_Leads
 * Handles lead tracking logic.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WWC_Leads {

	/**
	 * Constructor
	 */
	public function __construct() {
		// AJAX handlers for lead tracking
		add_action( 'wp_ajax_wwc_track_lead', array( $this, 'track_lead' ) );
		add_action( 'wp_ajax_nopriv_wwc_track_lead', array( $this, 'track_lead' ) );
	}

	/**
	 * Track a lead via AJAX.
	 * Stores the lead information in the database.
	 */
	public function track_lead() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wwc_lead_nonce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce', 'wp-whatsapp-crm' ) ) );
			return;
		}

		// Get lead data
		$phone = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
		$page  = isset( $_POST['page'] ) ? esc_url_raw( $_POST['page'] ) : '';
		$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '';

		if ( empty( $phone ) ) {
			wp_send_json_error( array( 'message' => __( 'Phone number is required', 'wp-whatsapp-crm' ) ) );
			return;
		}

		// Insert lead to DB
		$result = WWC_DB::insert_lead( array(
			'phone' => $phone,
			'page'  => $page,
			'ip'    => $ip,
		) );

		if ( $result ) {
			wp_send_json_success( array( 'message' => __( 'Lead tracked successfully', 'wp-whatsapp-crm' ) ) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Error tracking lead', 'wp-whatsapp-crm' ) ) );
		}
	}
}
