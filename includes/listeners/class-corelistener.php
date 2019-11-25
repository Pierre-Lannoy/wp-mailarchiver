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

use Mailarchiver\System\Logger;

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
		add_filter( 'wp_mail', [ $this, 'wp_mail' ] );
		add_filter( 'wp_mail_failed', [ $this, 'wp_mail_failed' ] );
		return true;
	}

	/**
	 * Recursively get all "to" email adresses.
	 *
	 * @since    1.0.0
	 */
	private function get_all_emails( $a, &$result ) {
		if ( is_array( $a ) ) {
			foreach ( $a as $item ) {
				$this->get_all_emails( $item, $result );
			}
		}
		if ( is_object( $a ) ) {
			foreach ( (array) $a as $item ) {
				$this->get_all_emails( $item, $result );
			}
		}
		if ( is_string( $a ) && false !== strpos( $a, '@' ) ) {
			$result[] = $a;
		}
	}

	/**
	 * "wp_mail" event.
	 *
	 * @since    1.0.0
	 */
	public function wp_mail( $mail ) {
		$tos = [];
		$this->get_all_emails( $mail['to'], $tos );
		$data['to'] = $tos;
		\Mailarchiver\Plugin\Feature\Capture::put( $mail );
	}

	/**
	 * "wp_mail" event.
	 *
	 * @since    1.0.0
	 */
	public function wp_mail_failed( $error ) {
		if ( $error instanceof \WP_Error ) {
			$message = $error->get_error_message();
			if ( '' === $message ) {
				$message = 'Unknown error.';
			}
			$data = $error->get_error_data();
			$tos  = [];
			$this->get_all_emails( $data['to'], $tos );
			$data['to'] = $tos;
			\Mailarchiver\Plugin\Feature\Capture::put( $data, $message );
		}
	}

}
