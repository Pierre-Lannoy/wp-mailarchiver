<?php
/**
 * Plugin initialization handling.
 *
 * @package Plugin
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Plugin;

/**
 * Fired after 'init' hook.
 *
 * This class defines all code necessary to run during the plugin's initialization.
 *
 * @package Plugin
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class Initializer {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

	}

	/**
	 * Initialize the plugin.
	 *
	 * @since 1.0.0
	 */
	public function initialize() {
		\Mailarchiver\System\Cache::init();
		\Mailarchiver\System\Sitehealth::init();
		\Mailarchiver\System\APCu::init();
		//if ( 'en_US' !== determine_locale() ) {
			unload_textdomain( MAILARCHIVER_SLUG );
			load_plugin_textdomain( MAILARCHIVER_SLUG );
		//}
	}

	/**
	 * Initialize the plugin.
	 *
	 * @since 1.0.0
	 */
	public function late_initialize() {
		require_once MAILARCHIVER_PLUGIN_DIR . 'perfopsone/init.php';
	}

}
