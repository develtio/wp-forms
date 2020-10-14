<?php
/**
 * Develtio Forms
 *
 * Plugin Name: Develtio Forms
 * Plugin URI:  https://develtio.com
 * Description: Make forms easier to develop
 * Version:     1.0
 * Author:      Develtio
 * Author URI:  https://develtio.com
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: develtio-forms
 * Domain Path: /lang
 */

// Abort if this file is called directly
defined( 'ABSPATH' ) or die( 'Hey, what are you doing here?' );

// Composer autoloader
if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Plugin activation function
 */
function activate_develtio_plugin()
{
    \Develtio\Core\Base\Activate::activate();
}

register_activation_hook( __FILE__, 'activate_develtio_plugin' );

/**
 * Plugin deactivation function
 */
function deactivate_develtio_plugin()
{
    \Develtio\Core\Base\Deactivate::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_develtio_plugin' );

/**
 *  Init all core classes
 */
if ( class_exists( 'Develtio\\Init' ) ) {
    Develtio\Init::register_services();
}