<?php
/*
 * Plugin Name: Crime Classifier
 * Plugin URI: https://myrotvorets.center/
 * Description: Crime Classifier for Purgatory
 * Version: 1.0.0
 * Author: Myrotvorets
 * Author URI: https://myrotvorets.center/
 * License: MIT
 * Domain Path: /lang
 */

use Myrotvorets\WordPress\Classifier\Admin;

if ( defined( 'ABSPATH' ) ) {
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	} elseif ( file_exists( ABSPATH . 'vendor/autoload.php' ) ) {
		require_once ABSPATH . 'vendor/autoload.php';
	}

	if ( is_admin() ) {
		add_action( 'init', [ Admin::class, 'instance' ] );
	}
}
