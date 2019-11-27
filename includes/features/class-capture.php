<?php
/**
 * Mail capture
 *
 * Handles all captures operations.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Plugin\Feature;

use Mailarchiver\System\Logger;
use Mailarchiver\System\Hash;
use Mailarchiver\Plugin\Feature\Archive;

/**
 * Define the captures functionality.
 *
 * Handles all captures operations.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class Capture {

	/**
	 * Mail sent with success or error.
	 *
	 * @since  1.0.0
	 * @var    array    $mails    The start times.
	 */
	private static $mails = [];

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Extract "from" email from headers, fallback to filter if none is found.
	 *
	 * @param array|string $headers Mail headers.
	 * @return string The from email.
	 * @since    1.0.0
	 */
	private static function from( $headers ) {
		$from_email = '';
		if ( ! is_array( $headers ) ) {
			$headers = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
		}
		if ( ! empty( $headers ) ) {
			foreach ( (array) $headers as $header ) {
				if ( strpos( $header, ':' ) === false ) {
					continue;
				}
				list( $name, $content ) = explode( ':', trim( $header ), 2 );
				$name                   = trim( $name );
				$content                = trim( $content );
				switch ( strtolower( $name ) ) {
					case 'from':
						$bracket_pos = strpos( $content, '<' );
						if ( false !== $bracket_pos ) {
							$from_email = substr( $content, $bracket_pos + 1 );
							$from_email = str_replace( '>', '', $from_email );
							$from_email = trim( $from_email );
						} elseif ( '' !== trim( $content ) ) {
							$from_email = trim( $content );
						}
						break;
				}
			}
		}
		if ( '' === $from_email ) {
			$sitename = strtolower( filter_input( INPUT_SERVER, 'SERVER_NAME' ) );
			if ( 'www.' === substr( $sitename, 0, 4 ) ) {
				$sitename = substr( $sitename, 4 );
			}
			$from_email = 'wordpress@' . $sitename;
			$from_email = apply_filters( 'wp_mail_from', $from_email );
		}
		return $from_email;
	}

	/**
	 * Normalize attachments filenames.
	 *
	 * @param array|string $attachments The attachments.
	 * @return array The normalized filenames.
	 * @since    1.0.0
	 */
	private static function attachments( $attachments ) {
		$att = [];
		if ( ! is_array( $attachments ) ) {
			$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
		}
		if ( ! empty( $attachments ) ) {
			foreach ( $attachments as $attachment ) {
				if ( '' !== $attachment ) {
					$att[] = basename( $attachment );
				}
			}
		}
		return $att;
	}

	/**
	 * Normalize headers.
	 *
	 * @param array|string $headers The headers.
	 * @return array The normalized headers.
	 * @since    1.0.0
	 */
	private static function headers( $headers ) {
		$hdr = [];
		if ( ! is_array( $headers ) ) {
			$headers = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
		}
		if ( ! empty( $headers ) ) {
			foreach ( $headers as $header ) {
				if ( '' !== $header ) {
					$hdr[] = $header;
				}
			}
		}
		return $hdr;
	}

	/**
	 * Put the mail in the store queue.
	 *
	 * @param array  $mail      The raw mail.
	 * @param string $message   Optional. The message if it's an error.
	 * @since    1.0.0
	 */
	public static function put( $mail, $message = '' ) {
		if ( is_array( $mail ) ) {
			$mail['body']['raw']  = $mail['message'];
			$mail['body']['type'] = 'raw';
			$mail['from']         = self::from( $mail['headers'] );
			$mail['attachments']  = self::attachments( $mail['attachments'] );
			$mail['headers']      = self::headers( $mail['headers'] );
			// phpcs:ignore
			$key = Hash::simple_hash( $mail['from'] . serialize( $mail['to'] ) . $mail['subject'] );
			unset( $mail['message'] );
			self::$mails[ $key ]['raw'] = $mail;
			if ( '' !== $message ) {
				self::$mails[ $key ]['message'] = $message;
			}
		} else {
			Logger::error( 'Unable to archive a malformed email.' );
		}
	}

	/**
	 * Store (send) previously catched mails.
	 *
	 * @since    1.0.0
	 */
	public static function store_archives() {
		global $wp_version;
		if ( 0 < count( self::$mails ) ) {
			$archiver = Archive::bootstrap( 'mail', 'wp_mail', $wp_version );
			foreach ( self::$mails as $mail ) {
				if ( array_key_exists( 'message', $mail ) && '' !== $mail['message'] ) {
					Logger::warning( sprintf( 'Unable to send mail "%s" from %s to %s.', esc_html( $mail['raw']['subject'] ), $mail['raw']['from'], implode( ', ', $mail['raw']['to'] ) ) );
					$archiver->error( $mail['raw'], $mail['message'] );
				} else {
					Logger::info( sprintf( 'Mail "%s" sent from %s to %s.', esc_html( $mail['raw']['subject'] ), $mail['raw']['from'], implode( ', ', $mail['raw']['to'] ) ) );
					$archiver->success( $mail['raw'] );
				}
			}
		}
	}

}
