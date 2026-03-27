<?php
/**
 * Plugin Name: WP WhatsApp CRM
 * Plugin URI:  https://example.com/wp-whatsapp-crm
 * Description: A complete WhatsApp CRM plugin with floating button and lead tracking.
 * Version:     1.0.0
 * Author:      Your Name
 * Author URI:  https://example.com
 * Text Domain: wp-whatsapp-crm
 * License:     GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants
define( 'WWC_VERSION', '1.0.0' );
define( 'WWC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WWC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Autoload classes
 */
require_once WWC_PLUGIN_DIR . 'includes/class-db.php';
require_once WWC_PLUGIN_DIR . 'includes/class-leads.php';
require_once WWC_PLUGIN_DIR . 'admin/class-admin.php';

/**
 * Activation Hook
 */
function wwc_activate_plugin() {
	WWC_DB::create_table();
}
register_activation_hook( __FILE__, 'wwc_activate_plugin' );

/**
 * Initialize the plugin
 */
function wwc_init_plugin() {
	// Initialize Admin
	if ( is_admin() ) {
		new WWC_Admin();
	}

	// Initialize Leads Tracking
	new WWC_Leads();
}
add_action( 'plugins_loaded', 'wwc_init_plugin' );

/**
 * Enqueue scripts and styles
 */
function wwc_enqueue_scripts() {
	$enabled = get_option( 'wwc_enabled', 'yes' );
	if ( $enabled !== 'yes' ) {
		return;
	}

	wp_enqueue_style( 'wwc-style', WWC_PLUGIN_URL . 'assets/css/style.css', array(), WWC_VERSION );
	wp_enqueue_script( 'wwc-script', WWC_PLUGIN_URL . 'assets/js/script.js', array( 'jquery' ), WWC_VERSION, true );

	wp_localize_script( 'wwc-script', 'wwc_ajax', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'wwc_lead_nonce' ),
		'phone'    => get_option( 'wwc_phone', '' ),
		'message'  => get_option( 'wwc_message', '' ),
		'error_msg' => __( 'There was an error tracking the lead. Redirecting to WhatsApp...', 'wp-whatsapp-crm' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'wwc_enqueue_scripts' );

/**
 * Display floating button
 */
function wwc_display_floating_button() {
	$enabled = get_option( 'wwc_enabled', 'yes' );
	if ( $enabled !== 'yes' ) {
		return;
	}

	$button_text = get_option( 'wwc_button_text', __( 'Chat with us', 'wp-whatsapp-crm' ) );
	?>
	<div id="wwc-floating-button">
		<a href="#" id="wwc-whatsapp-link">
			<img src="<?php echo esc_url( WWC_PLUGIN_URL . 'assets/images/whatsapp-icon.png' ); ?>" alt="<?php esc_attr_e( 'WhatsApp', 'wp-whatsapp-crm' ); ?>">
			<span><?php echo esc_html( $button_text ); ?></span>
		</a>
	</div>
	<?php
}
add_action( 'wp_footer', 'wwc_display_floating_button' );

/**
 * Load text domain for i18n
 */
function wwc_load_textdomain() {
	load_plugin_textdomain( 'wp-whatsapp-crm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'wwc_load_textdomain' );
