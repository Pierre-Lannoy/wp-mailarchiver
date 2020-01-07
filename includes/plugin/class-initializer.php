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
		\Mailarchiver\System\Logger::init();
		\Mailarchiver\System\Cache::init();
		\Mailarchiver\System\Sitehealth::init();
		add_filter( 'set-screen-option', [ 'Mailarchiver\Plugin\Feature\Events', 'save_screen_option' ], 100, 3 );
	}

}
