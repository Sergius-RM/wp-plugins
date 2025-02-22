<?php

/*
 * Plugin Name: iThemes Security Pro
 * Plugin URI: https://ithemes.com/security
 * Description: Take the guesswork out of WordPress security. iThemes Security offers 30+ ways to lock down WordPress in an easy-to-use WordPress security plugin.
 * Author: iThemes
 * Author URI: https://ithemes.com
 * Version: 6.7.1
 * Text Domain: it-l10n-ithemes-security-pro
 * Domain Path: /lang
 * Network: True
 * License: GPLv2
 * Requires PHP: 5.6
 * iThemes Package: ithemes-security-pro
 */

if ( version_compare( phpversion(), '5.6.0', '<' ) ) {
	function itsec_minimum_php_version_notice() {
		echo '<div class="notice notice-error"><p>' . esc_html__( 'iThemes Security Pro requires PHP 5.6 or higher.', 'it-l10n-ithemes-security-pro' ) . '</p></div>';
	}

	add_action( 'admin_notices', 'itsec_minimum_php_version_notice' );

	return;
}

if ( version_compare( $GLOBALS['wp_version'], '5.4.0', '<' ) ) {
	function itsec_minimum_wp_version_notice() {
		echo '<div class="notice notice-error"><p>' . esc_html__( 'iThemes Security Pro requires WordPress 5.4 or later.', 'it-l10n-ithemes-security-pro' ) . '</p></div>';
	}

	add_action( 'admin_notices', 'itsec_minimum_wp_version_notice' );

	return;
}

function itsec_pro_load_textdomain() {
	$locale = determine_locale();
	$locale = apply_filters( 'plugin_locale', $locale, 'it-l10n-ithemes-security-pro' );

	load_textdomain( 'it-l10n-ithemes-security-pro', WP_LANG_DIR . "/plugins/ithemes-security-pro/it-l10n-ithemes-security-pro-$locale.mo" );
	load_plugin_textdomain( 'it-l10n-ithemes-security-pro', false, basename( dirname( __FILE__ ) ) . '/lang/' );
}

add_action( 'plugins_loaded', 'itsec_pro_load_textdomain' );

if ( isset( $itsec_dir ) || class_exists( 'ITSEC_Core' ) ) {
	include( dirname( __FILE__ ) . '/core/show-multiple-version-notice.php' );
	return;
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once( __DIR__ . '/vendor/autoload.php' );
} elseif ( file_exists( __DIR__ . '/vendor-prod/autoload.php' ) ) {
	require_once( __DIR__ . '/vendor-prod/autoload.php' );
}

if ( ! function_exists( 'itsec_pro_register_modules' ) ) {
	// Add pro modules at priority 11 so they are added after core modules (thus taking precedence)
	add_action( 'itsec-register-modules', 'itsec_pro_register_modules', 11 );

	function itsec_pro_register_modules() {
		$path = dirname( __FILE__ );

		ITSEC_Modules::register_module( 'pro', "$path/pro/pro", 'always-active' );
		ITSEC_Modules::register_module( 'dashboard-widget', "$path/pro/dashboard-widget", 'always-active' );
		ITSEC_Modules::register_module( 'magic-links', "$path/pro/magic-links", 'default-active' );
		ITSEC_Modules::register_module( 'malware-scheduling', "$path/pro/malware-scheduling", 'default-active' );
		ITSEC_Modules::register_module( 'online-files', "$path/pro/online-files", 'always-active' );
		ITSEC_Modules::register_module( 'passwordless-login', "$path/pro/passwordless-login" );
		ITSEC_Modules::register_module( 'password-expiration', "$path/pro/password-expiration", 'always-active' );
		ITSEC_Modules::register_module( 'privilege', "$path/pro/privilege" );
		ITSEC_Modules::register_module( 'recaptcha', "$path/pro/recaptcha" );
		ITSEC_Modules::register_module( 'import-export', "$path/pro/import-export", 'always-active' );
		ITSEC_Modules::register_module( 'dashboard', "$path/pro/dashboard" );
		ITSEC_Modules::register_module( 'two-factor', "$path/pro/two-factor", 'default-active' );
		ITSEC_Modules::register_module( 'user-logging', "$path/pro/user-logging", 'default-active' );
		ITSEC_Modules::register_module( 'user-security-check', "$path/pro/user-security-check", 'always-active' );
		ITSEC_Modules::register_module( 'version-management', "$path/pro/version-management", 'default-active' );
		ITSEC_Modules::register_module( 'fingerprinting', "$path/pro/fingerprinting" );
		ITSEC_Modules::register_module( 'geolocation', "$path/pro/geolocation", 'always-active' );

		if ( get_site_option( 'itsec-enable-grade-report' ) ) {
			ITSEC_Modules::register_module( 'grade-report', "$path/pro/grade-report", 'always-active' );
		}

		ITSEC_Modules::register_module( 'hibp', "$path/pro/hibp", 'always-active' );

		if ( defined( 'WP_CLI' ) && WP_CLI && class_exists( 'WP_CLI_Command' ) ) {
			require( "$path/pro/wp-cli/load.php" );
		}
	}
}


$itsec_dir = dirname( __FILE__ );

require( "$itsec_dir/core/core.php" );
$itsec_core = ITSEC_Core::get_instance();
$itsec_core->init( __FILE__, 'iThemes Security Pro' );

if ( is_admin() ) {
	require( "$itsec_dir/lib/icon-fonts/load.php" );
}


if ( ! function_exists( 'ithemes_repository_name_updater_register' ) ) {
	function ithemes_repository_name_updater_register( $updater ) {
		$updater->register( 'ithemes-security-pro', __FILE__ );
	}
	add_action( 'ithemes_updater_register', 'ithemes_repository_name_updater_register' );

	require( "$itsec_dir/lib/updater/load.php" );
}
