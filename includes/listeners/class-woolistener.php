<?php
/**
 * WooCommerce listener for MailArchiver.
 *
 * Defines class for WooCommerce listener.
 *
 * @package Listeners
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.6.0
 */

namespace Mailarchiver\Listener;

/**
 * WooCommerce listener for MailArchiver.
 *
 * Defines methods and properties for WooCommerce listener class.
 *
 * @package Listeners
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.6.0
 */
class WooListener extends AbstractListener {

	/**
	 * Sets the listener properties.
	 *
	 * @since    1.0.0
	 */
	protected function init() {
		$this->id      = 'woo';
		$this->class   = 'plugin';
		$this->product = 'WooCommerce';
		$this->name    = 'WooCommerce';
		if ( function_exists( 'WC' ) ) {
			$woocommerce   = WC();
			$this->version = $woocommerce->version;
		} else {
			$this->version = 'x';
		}
	}

	/**
	 * Verify if this listener is needed, mainly by verifying if the listen plugin/theme is loaded.
	 *
	 * @return  boolean     True if listener is needed, false otherwise.
	 * @since    1.6.0
	 */
	protected function is_available() {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * "Launch" the listener.
	 *
	 * @return  boolean     True if listener was launched, false otherwise.
	 * @since    1.6.0
	 */
	protected function launch() {
		add_filter( 'woocommerce_register_log_handlers', [ $this, 'woocommerce_register_log_handlers' ], 0, 1 );
		return true;
	}

	/**
	 * "woocommerce_register_log_handlers" filter.
	 *
	 * @since    1.6.0
	 */
	public function woocommerce_register_log_handlers( $handlers ) {
		array_push( $handlers, new \Mailarchiver\Integration\WCLogger( $this->class, $this->name, $this->version ) );
		return $handlers;

	}
}
