<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://10up.com
 * @since             1.0.0
 * @package           wpazure_describe_image
 *
 * @wordpress-plugin
 * Plugin Name:       WP Azure Describe Image
 * Plugin URI:        http://10up.com/
 * Description:       Uses the Microsoft Azure Congnitive API to generate optional captions for images uploaded to the WordPress Media Library
 * Version:           1.0.0
 * Author:            10up
 * Author URI:        http://10up.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpazure-describe-image
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WPAZURE_DESCRIBE_IMAGE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wpazure-describe-image-activator.php
 */
function activate_wpazure_describe_image() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpazure-describe-image-activator.php';
	Wpazure_Describe_Image_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wpazure-describe-image-deactivator.php
 */
function deactivate_wpazure_describe_image() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpazure-describe-image-deactivator.php';
	Wpazure_Describe_Image_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wpazure_describe_image' );
register_deactivation_hook( __FILE__, 'deactivate_wpazure_describe_image' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpazure-describe-image.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wpazure_describe_image() {

	$plugin = new Wpazure_Describe_Image();
	$plugin->run();

}
run_wpazure_describe_image();
