<?php
/**
 * Main plugin file.
 *
 * @package Bootstrap
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       MailArchiver
 * Plugin URI:        https://perfops.one/mailarchiver
 * Description:       Automatically archive and store all emails sent from your site.
 * Version:           4.0.0
 * Requires at least: 6.2
 * Requires PHP:      8.1
 * Author:            Pierre Lannoy / PerfOps One
 * Author URI:        https://perfops.one
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       mailarchiver
 * Network:           true
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/includes/system/class-option.php';
require_once __DIR__ . '/includes/system/class-environment.php';
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/includes/libraries/class-libraries.php';
require_once __DIR__ . '/includes/libraries/autoload.php';
require_once __DIR__ . '/includes/features/class-wpcli.php';

/**
 * The code that runs during plugin activation.
 *
 * @since 1.0.0
 */
function mailarchiver_activate() {
	Mailarchiver\Plugin\Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 *
 * @since 1.0.0
 */
function mailarchiver_deactivate() {
	Mailarchiver\Plugin\Deactivator::deactivate();
}

/**
 * The code that runs during plugin uninstallation.
 *
 * @since 1.0.0
 */
function mailarchiver_uninstall() {
	Mailarchiver\Plugin\Uninstaller::uninstall();
}

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */
function mailarchiver_run() {
	\DecaLog\Engine::initPlugin( MAILARCHIVER_SLUG, MAILARCHIVER_PRODUCT_NAME, MAILARCHIVER_VERSION, \Mailarchiver\Plugin\Core::get_base64_logo() );
	$plugin = new Mailarchiver\Plugin\Core();
	$plugin->run();
}
if ( ! defined( 'MAILARCHIVER_MAX_SHUTDOWN_PRIORITY' ) ) {
	define( 'MAILARCHIVER_MAX_SHUTDOWN_PRIORITY', PHP_INT_MAX - 2000 );
}
register_activation_hook( __FILE__, 'mailarchiver_activate' );
register_deactivation_hook( __FILE__, 'mailarchiver_deactivate' );
register_uninstall_hook( __FILE__, 'mailarchiver_uninstall' );
mailarchiver_run();
