<?php

/**
 * WP core listener for MailArchiver.
 *
 * Defines class for WP core listener.
 *
 * @package Listeners
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Listener;

/**
 * WP core listener for MailArchiver.
 *
 * Defines methods and properties for WP core listener class.
 *
 * @package Listeners
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class CoreListener extends AbstractListener {

	/**
	 * Sets the listener properties.
	 *
	 * @since    1.0.0
	 */
	protected function init() {
		global $wp_version;
		$this->id      = 'wpcore';
		$this->name    = esc_html__( 'WordPress core', 'mailarchiver' );
		$this->class   = 'core';
		$this->product = 'WordPress';
		$this->version = $wp_version;
	}

	/**
	 * Verify if this listener is needed, mainly by verifying if the listen plugin/theme is loaded.
	 *
	 * @return  boolean     True if listener is needed, false otherwise.
	 * @since    1.0.0
	 */
	protected function is_available() {
		return true;
	}

	/**
	 * "Launch" the listener.
	 *
	 * @return  boolean     True if listener was launched, false otherwise.
	 * @since    1.0.0
	 */
	protected function launch() {
		add_filter( 'wp_mail', [ $this, 'log_email' ], PHP_INT_MAX );
		return true;
	}

	function log_email( $mail ) {
		error_log(print_r($mail, true));
	}

}
