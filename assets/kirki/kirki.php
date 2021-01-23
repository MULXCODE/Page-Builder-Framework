<?php
/**
 * Plugin Name:   Kirki Customizer Framework
 * Plugin URI:    https://kirki.org
 * Description:   The Ultimate WordPress Customizer Framework
 * Author:        David Vongries
 * Author URI:    https://wp-pagebuilderframework.com/
 * Version:       3.1.5
 * Text Domain:   kirki
 * Requires WP:   4.9
 * Requires PHP:  5.3
 * GitHub Plugin URI: kirki-framework/kirki
 * GitHub Plugin URI: https://github.com/kirki-framework/kirki
 *
 * @package   Kirki
 * @category  Core
 * @author    Ari Stathopoulos (@aristath)
 * @copyright Copyright (c) 2020, David Vongries
 * @license   https://opensource.org/licenses/MIT
 * @since     1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// No need to proceed if Kirki already exists.
if ( class_exists( 'Kirki' ) ) {
	return;
}

// Include the autoloader.
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class-kirki-autoload.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude
new Kirki_Autoload();

if ( ! defined( 'KIRKI_PLUGIN_FILE' ) ) {
	define( 'KIRKI_PLUGIN_FILE', __FILE__ );
}

// Define the KIRKI_VERSION constant.
if ( ! defined( 'KIRKI_VERSION' ) ) {
	define( 'KIRKI_VERSION', '3.1.3' );
}

// Make sure the path is properly set.
Kirki::$path = wp_normalize_path( dirname( __FILE__ ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
Kirki_Init::set_url();

new Kirki_Controls();

if ( ! function_exists( 'Kirki' ) ) {
	/**
	 * Returns an instance of the Kirki object.
	 */
	function kirki() {
		$kirki = Kirki_Toolkit::get_instance();
		return $kirki;
	}
}

// Start Kirki.
global $kirki;
$kirki = kirki();

// Instantiate the modules.
$kirki->modules = new Kirki_Modules();

Kirki::$url = plugins_url( '', __FILE__ );

// Instantiate classes.
new kirki();
new Kirki_L10n();

// Include the ariColor library.
require_once wp_normalize_path( dirname( __FILE__ ) . '/lib/class-aricolor.php' ); // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude

// Kirki configuration.
Kirki::add_config( 'wpbf', array(
    'capability'        => 'edit_theme_options',
    'option_type'       => 'theme_mod',
    'gutenberg_support' => true,
    'disable_output'    => true,
) );
