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
class WpmsListener extends AbstractListener {

	/**
	 * Sets the listener properties.
	 *
	 * @since    1.0.0
	 */
	protected function init() {
		$this->id      = 'wpms';
		$this->class   = 'plugin';
		$this->product = 'WP Mail SMTP';
		$this->name    = 'WP Mail SMTP';
		if ( defined( 'WPMS_PLUGIN_VER' ) ) {
			$this->version = WPMS_PLUGIN_VER;
		} else {
			$this->version = 'x';
		}
	}

	/**
	 * Verify if this listener is needed, mainly by verifying if the listen plugin/theme is loaded.
	 *
	 * @return  boolean     True if listener is needed, false otherwise.
	 * @since    1.0.0
	 */
	protected function is_available() {
		return function_exists( 'wp_mail_smtp' );
	}

	/**
	 * "Launch" the listener.
	 *
	 * @return  boolean     True if listener was launched, false otherwise.
	 * @since    1.0.0
	 */
	protected function launch() {
		add_action( 'wp_mail_smtp_mailcatcher_send_after', [ $this, 'wp_mail_smtp_mailcatcher_send_after' ], PHP_INT_MAX, 2 );
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
	 * "phpmailer_init" action.
	 *
	 * @since    1.0.0
	 */
	public function wp_mail_smtp_mailcatcher_send_after( $mailer, $mailcatcher ) {
		$mail = [];
		if ( method_exists( $mailcatcher, 'getToAddresses' ) ) {
			$tos = [];
			$this->get_all_emails( $mailcatcher->getToAddresses(), $tos );
			natcasesort( $tos );
			$mail['to'] = $tos;
		} else {
			$mail['to'] = [];
		}
		if ( property_exists( $mailcatcher, 'Body' ) ) {
			$mail['message'] = $mailcatcher->Body;
		} else {
			$mail['message'] = '';
		}
		if ( property_exists( $mailcatcher, 'From' ) ) {
			$mail['from'] = $mailcatcher->From;
		} else {
			$mail['from'] = '';
		}
		if ( property_exists( $mailcatcher, 'Subject' ) ) {
			$mail['subject'] = $mailcatcher->Subject;
		} else {
			$mail['subject'] = '';
		}
		if ( method_exists( $mailcatcher, 'getAttachments' ) ) {
			$mail['attachments'] = $mailcatcher->getAttachments();
		} else {
			$mail['attachments'] = [];
		}
		if ( method_exists( $mailcatcher, 'createHeader' ) ) {
			$mail['headers'] = $mailcatcher->createHeader();
		} else {
			$mail['headers'] = [];
		}
		$message = '';
		if ( method_exists( $mailcatcher, 'is_email_sent' ) && method_exists( $mailcatcher, 'get_debug_info' ) ) {
			if ( ! $mailer->is_email_sent() ) {
				$message = $mailer->get_debug_info();
				$message = wp_kses( str_replace( '<br>', '. ', $message ), [] );
			}
		}
		$mail['listener']['class']   = $this->class;
		$mail['listener']['product'] = $this->product;
		$mail['listener']['version'] = $this->version;
		Capture::put( $mail, $message );
	}

}
