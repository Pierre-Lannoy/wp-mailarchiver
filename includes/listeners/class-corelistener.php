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
use Mailarchiver\Plugin\Feature\Capture;

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
		$this->class   = 'mail';
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
		add_filter( 'wp_mail', [ $this, 'wp_mail' ], PHP_INT_MAX );
		add_filter( 'wp_mail_failed', [ $this, 'wp_mail_failed' ], PHP_INT_MAX );
		add_action( 'phpmailer_init', [ $this, 'phpmailer_init' ], PHP_INT_MAX );
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
			$result[] = trim( $a) ;
		}
	}

	/**
	 * "wp_mail" event.
	 *
	 * @since    1.0.0
	 */
	public function wp_mail( $mail, $message = '' ) {
		$recipients = null;
		if ( is_string( $mail['to'] ) ) {
			foreach ( [ ',', ';' ] as $sep ) {
				if ( false !== strpos( $mail['to'], $sep ) ) {
					$recipients = explode( $sep, $mail['to'] );
					break;
				}
			}
			if ( ! isset( $recipients ) ) {
				$recipients = [ $mail['to'] ];
			}
		} else {
			$recipients = $mail['to'];
		}
		$tos = [];
		$this->get_all_emails( $recipients, $tos );
		natcasesort( $tos );
		$mail['to']                  = $tos;
		$mail['listener']['class']   = $this->class;
		$mail['listener']['product'] = $this->product;
		$mail['listener']['version'] = $this->version;
		Capture::put( $mail, $message );
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
			$this->wp_mail( $error->get_error_data(), $message );
		}
	}

	/**
	 * "phpmailer_init" action.
	 *
	 * @since    1.0.0
	 */
	public function phpmailer_init( &$phpmailer ) {
		$mail = [];
		if ( method_exists( $phpmailer, 'getToAddresses' ) ) {
			$tos = [];
			$this->get_all_emails( $phpmailer->getToAddresses(), $tos );
			natcasesort( $tos );
			$mail['to'] = $tos;
		} else {
			$mail['to'] = [];
		}
		if ( property_exists( $phpmailer, 'Body' ) ) {
			$mail['message'] = $phpmailer->Body;
		} else {
			$mail['message'] = '';
		}
		if ( property_exists( $phpmailer, 'From' ) ) {
			$mail['from'] = $phpmailer->From;
		} else {
			$mail['from'] = '';
		}
		if ( property_exists( $phpmailer, 'Subject' ) ) {
			$mail['subject'] = $phpmailer->Subject;
		} else {
			$mail['subject'] = '';
		}
		if ( method_exists( $phpmailer, 'getAttachments' ) ) {
			$mail['attachments'] = $phpmailer->getAttachments();
		} else {
			$mail['attachments'] = [];
		}
		if ( method_exists( $phpmailer, 'createHeader' ) ) {
			$mail['headers'] = $phpmailer->createHeader();
		} else {
			$mail['headers'] = [];
		}
		$mail['listener']['class']   = $this->class;
		$mail['listener']['product'] = $this->product;
		$mail['listener']['version'] = $this->version;
		Capture::put( $mail, '' );
	}

}
