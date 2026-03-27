<?php
/**
 * Class WWC_Admin
 * Handles the admin interface for the plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WWC_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Add menu pages
		add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
		// Register settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		// Handle bulk actions
		add_action( 'admin_init', array( $this, 'handle_bulk_actions' ) );
		// Handle CSV export
		add_action( 'admin_init', array( $this, 'handle_csv_export' ) );
	}

	/**
	 * Add admin menu pages.
	 */
	public function add_menu_pages() {
		add_menu_page(
			__( 'WP WhatsApp CRM', 'wp-whatsapp-crm' ),
			__( 'WhatsApp CRM', 'wp-whatsapp-crm' ),
			'manage_options',
			'wwc-settings',
			array( $this, 'render_settings_page' ),
			'dashicons-whatsapp',
			30
		);

		add_submenu_page(
			'wwc-settings',
			__( 'Leads Dashboard', 'wp-whatsapp-crm' ),
			__( 'Leads Dashboard', 'wp-whatsapp-crm' ),
			'manage_options',
			'wwc-leads',
			array( $this, 'render_leads_page' )
		);
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings() {
		register_setting( 'wwc_settings_group', 'wwc_phone' );
		register_setting( 'wwc_settings_group', 'wwc_message' );
		register_setting( 'wwc_settings_group', 'wwc_button_text' );
		register_setting( 'wwc_settings_group', 'wwc_enabled' );

		add_settings_section(
			'wwc_general_section',
			__( 'General Settings', 'wp-whatsapp-crm' ),
			null,
			'wwc-settings'
		);

		add_settings_field(
			'wwc_phone',
			__( 'WhatsApp Phone Number', 'wp-whatsapp-crm' ),
			array( $this, 'render_phone_field' ),
			'wwc-settings',
			'wwc_general_section'
		);

		add_settings_field(
			'wwc_message',
			__( 'Default Message', 'wp-whatsapp-crm' ),
			array( $this, 'render_message_field' ),
			'wwc-settings',
			'wwc_general_section'
		);

		add_settings_field(
			'wwc_button_text',
			__( 'Button Text', 'wp-whatsapp-crm' ),
			array( $this, 'render_button_text_field' ),
			'wwc-settings',
			'wwc_general_section'
		);

		add_settings_field(
			'wwc_enabled',
			__( 'Enable Floating Button', 'wp-whatsapp-crm' ),
			array( $this, 'render_enabled_field' ),
			'wwc-settings',
			'wwc_general_section'
		);
	}

	/**
	 * Render phone field.
	 */
	public function render_phone_field() {
		$phone = get_option( 'wwc_phone', '' );
		echo '<input type="text" name="wwc_phone" value="' . esc_attr( $phone ) . '" class="regular-text" placeholder="+1234567890">';
		echo '<p class="description">' . esc_html__( 'Enter your WhatsApp phone number with country code (no + or spaces).', 'wp-whatsapp-crm' ) . '</p>';
	}

	/**
	 * Render message field.
	 */
	public function render_message_field() {
		$message = get_option( 'wwc_message', '' );
		echo '<textarea name="wwc_message" class="large-text" rows="3">' . esc_textarea( $message ) . '</textarea>';
		echo '<p class="description">' . esc_html__( 'The default message that will be pre-filled in WhatsApp.', 'wp-whatsapp-crm' ) . '</p>';
	}

	/**
	 * Render button text field.
	 */
	public function render_button_text_field() {
		$button_text = get_option( 'wwc_button_text', __( 'Chat with us', 'wp-whatsapp-crm' ) );
		echo '<input type="text" name="wwc_button_text" value="' . esc_attr( $button_text ) . '" class="regular-text">';
	}

	/**
	 * Render enabled field.
	 */
	public function render_enabled_field() {
		$enabled = get_option( 'wwc_enabled', 'yes' );
		?>
		<label class="switch">
			<input type="checkbox" name="wwc_enabled" value="yes" <?php checked( $enabled, 'yes' ); ?>>
			<span class="slider round"></span>
		</label>
		<?php
	}

	/**
	 * Render the settings page.
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'WP WhatsApp CRM Settings', 'wp-whatsapp-crm' ); ?></h1>
			
			<div class="welcome-panel" style="background-color: #f0f0f1; border: 1px solid #dcdcde; margin-bottom: 20px;">
				<div class="welcome-panel-content">
					<h2>🚀 <?php esc_html_e( 'Upgrade to Pro for Automation', 'wp-whatsapp-crm' ); ?></h2>
					<p><?php esc_html_e( 'Take your WhatsApp CRM to the level with Cloud API integration, auto-replies, lead tagging, and advanced analytics.', 'wp-whatsapp-crm' ); ?></p>
					<a href="#" class="button button-primary" style="background-color: #25D366; border-color: #128C7E; font-weight: bold;"><?php esc_html_e( 'Go Pro - Join Waitlist', 'wp-whatsapp-crm' ); ?></a>
				</div>
			</div>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'wwc_settings_group' );
				do_settings_sections( 'wwc-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the leads dashboard page.
	 */
	public function render_leads_page() {
		$leads = WWC_DB::get_leads();
		$phone = get_option( 'wwc_phone', '' );
		$message = get_option( 'wwc_message', '' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Leads Dashboard', 'wp-whatsapp-crm' ); ?></h1>
			
			<div class="welcome-panel" style="padding: 20px; margin-top: 20px;">
				<div class="welcome-panel-content">
					<h2><?php esc_html_e( 'Current Configuration', 'wp-whatsapp-crm' ); ?></h2>
					<p class="about-description"><?php esc_html_e( 'Below is your current WhatsApp configuration. Clicks on the floating button are tracked in the table below.', 'wp-whatsapp-crm' ); ?></p>
					<div class="welcome-panel-column-container">
						<div class="welcome-panel-column">
							<h3><?php esc_html_e( 'WhatsApp Phone', 'wp-whatsapp-crm' ); ?></h3>
							<p><strong><?php echo esc_html( $phone ?: __( 'Not set', 'wp-whatsapp-crm' ) ); ?></strong></p>
						</div>
						<div class="welcome-panel-column">
							<h3><?php esc_html_e( 'Default Message', 'wp-whatsapp-crm' ); ?></h3>
							<p><em><?php echo esc_html( $message ?: __( 'No message set', 'wp-whatsapp-crm' ) ); ?></em></p>
						</div>
						<div class="welcome-panel-column welcome-panel-last">
							<h3><?php esc_html_e( 'Quick Actions', 'wp-whatsapp-crm' ); ?></h3>
							<a class="button button-primary button-hero" href="<?php echo esc_url( admin_url( 'admin.php?page=wwc-settings' ) ); ?>"><?php esc_html_e( 'Edit Settings', 'wp-whatsapp-crm' ); ?></a>
						</div>
					</div>
				</div>
			</div>

			<form method="post" action="">
				<?php wp_nonce_field( 'wwc_bulk_leads_nonce', 'bulk_nonce' ); ?>
				
				<div class="tablenav top">
					<div class="alignleft actions bulkactions">
						<select name="action" id="bulk-action-selector-top">
							<option value="-1"><?php esc_html_e( 'Bulk Actions', 'wp-whatsapp-crm' ); ?></option>
							<option value="delete"><?php esc_html_e( 'Delete', 'wp-whatsapp-crm' ); ?></option>
						</select>
						<input type="submit" id="doaction" class="button action" value="<?php esc_attr_e( 'Apply', 'wp-whatsapp-crm' ); ?>">
					</div>
					<div class="alignright">
						<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'export_csv', 'nonce' => wp_create_nonce( 'wwc_export_csv' ) ) ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Export to CSV', 'wp-whatsapp-crm' ); ?></a>
					</div>
				</div>

				<table class="wp-list-table widefat fixed striped leads">
					<thead>
						<tr>
							<td id="cb" class="manage-column column-cb check-column">
								<input id="cb-select-all-1" type="checkbox">
							</td>
							<th scope="col"><?php esc_html_e( 'ID', 'wp-whatsapp-crm' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Phone', 'wp-whatsapp-crm' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Page URL', 'wp-whatsapp-crm' ); ?></th>
							<th scope="col"><?php esc_html_e( 'User IP', 'wp-whatsapp-crm' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Date', 'wp-whatsapp-crm' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Actions', 'wp-whatsapp-crm' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $leads ) ) : ?>
							<tr>
								<td colspan="7"><?php esc_html_e( 'No leads found.', 'wp-whatsapp-crm' ); ?></td>
							</tr>
						<?php else : ?>
							<?php foreach ( $leads as $lead ) : ?>
								<tr>
									<th scope="row" class="check-column">
										<input type="checkbox" name="lead_ids[]" value="<?php echo esc_attr( $lead['id'] ); ?>">
									</th>
									<td><?php echo esc_html( $lead['id'] ); ?></td>
									<td><?php echo esc_html( $lead['phone'] ); ?></td>
									<td><a href="<?php echo esc_url( $lead['page'] ); ?>" target="_blank"><?php echo esc_html( $lead['page'] ); ?></a></td>
									<td><?php echo esc_html( $lead['ip'] ); ?></td>
									<td><?php echo esc_html( $lead['created_at'] ); ?></td>
									<td>
										<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'delete', 'lead_id' => $lead['id'], 'nonce' => wp_create_nonce( 'wwc_delete_lead_' . $lead['id'] ) ) ) ); ?>" class="button button-link-delete" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this lead?', 'wp-whatsapp-crm' ); ?>');"><?php esc_html_e( 'Delete', 'wp-whatsapp-crm' ); ?></a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</form>
		</div>
		<?php
	}

	/**
	 * Handle deletion and bulk actions.
	 */
	public function handle_bulk_actions() {
		// Single delete
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['lead_id'] ) ) {
			$lead_id = (int) $_GET['lead_id'];
			if ( wp_verify_nonce( $_GET['nonce'], 'wwc_delete_lead_' . $lead_id ) ) {
				WWC_DB::delete_lead( $lead_id );
				wp_redirect( admin_url( 'admin.php?page=wwc_leads&message=deleted' ) );
				exit;
			}
		}

		// Bulk delete
		if ( isset( $_POST['action'] ) && $_POST['action'] === 'delete' && isset( $_POST['lead_ids'] ) ) {
			if ( wp_verify_nonce( $_POST['bulk_nonce'], 'wwc_bulk_leads_nonce' ) ) {
				$lead_ids = array_map( 'intval', $_POST['lead_ids'] );
				foreach ( $lead_ids as $id ) {
					WWC_DB::delete_lead( $id );
				}
				wp_redirect( admin_url( 'admin.php?page=wwc_leads&message=bulk_deleted' ) );
				exit;
			}
		}
	}

	/**
	 * Handle CSV export.
	 */
	public function handle_csv_export() {
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'export_csv' ) {
			if ( ! wp_verify_nonce( $_GET['nonce'], 'wwc_export_csv' ) ) {
				wp_die( __( 'Invalid nonce', 'wp-whatsapp-crm' ) );
			}

			$leads = WWC_DB::get_leads();

			header( 'Content-Type: text/csv' );
			header( 'Content-Disposition: attachment; filename=wwc-leads-' . date( 'Y-m-d' ) . '.csv' );

			$output = fopen( 'php://output', 'w' );
			fputcsv( $output, array( 'ID', 'Phone', 'Page URL', 'User IP', 'Timestamp' ) );

			foreach ( $leads as $lead ) {
				fputcsv( $output, array(
					$lead['id'],
					$lead['phone'],
					$lead['page'],
					$lead['ip'],
					$lead['created_at']
				) );
			}

			fclose( $output );
			exit;
		}
	}
}
