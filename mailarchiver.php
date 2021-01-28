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
 * Plugin URI:        https://github.com/Pierre-Lannoy/wp-mailarchiver
 * Description:       Automatically archive and store all emails sent from your site.
 * Version:           2.1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Pierre Lannoy
 * Author URI:        https://pierre.lannoy.fr
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
require_once __DIR__ . '/includes/system/class-option.php';
require_once __DIR__ . '/includes/system/class-environment.php';
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/includes/libraries/class-libraries.php';
require_once __DIR__ . '/includes/libraries/autoload.php';

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
	if ( ! Mailarchiver\System\Environment::is_editor_scrapping() ) {
		require_once __DIR__ . '/includes/features/class-watchdog.php';
		require_once __DIR__ . '/includes/features/class-wpcli.php';
		\Mailarchiver\System\Logger::init();
		$plugin = new Mailarchiver\Plugin\Core();
		$plugin->run();
	}
}

register_activation_hook( __FILE__, 'mailarchiver_activate' );
register_deactivation_hook( __FILE__, 'mailarchiver_deactivate' );
register_uninstall_hook( __FILE__, 'mailarchiver_uninstall' );
mailarchiver_run();
