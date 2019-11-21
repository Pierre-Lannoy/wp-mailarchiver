<?php
/**
 * Pseudo PSR-3 listener for MailArchiver.
 *
 * Defines class for WP pseudo PSR-3 listener.
 *
 * @package Listeners
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Decalog\Listener;

/**
 * Pseudo PSR-3 listener for MailArchiver.
 *
 * Defines methods and properties for WP pseudo PSR-3 listener class.
 *
 * @package Listeners
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.3.0
 */
class PsrListener extends AbstractListener {

	/**
	 * Sets the listener properties.
	 *
	 * @since    1.3.0
	 */
	protected function init() {
		$this->id      = 'psr3';
		$this->name    = esc_html__( 'PSR-3 compliant listeners', 'mailarchiver' );
		$this->class   = 'psr3';
		$this->product = MAILARCHIVER_PRODUCT_NAME;
		$this->version = MAILARCHIVER_VERSION;
	}

	/**
	 * Verify if this listener is needed, mainly by verifying if the listen plugin/theme is loaded.
	 *
	 * @return  boolean     True if listener is needed, false otherwise.
	 * @since    1.3.0
	 */
	protected function is_available() {
		return true;
	}

	/**
	 * "Launch" the listener.
	 *
	 * @return  boolean     True if listener was launched, false otherwise.
	 * @since    1.3.0
	 */
	protected function launch() {
		return true;
	}

}
