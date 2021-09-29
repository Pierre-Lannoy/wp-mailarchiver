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


use Mailarchiver\System\Hash;
use Mailarchiver\Plugin\Feature\Archive;
use Mailarchiver\System\Option;

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
				if ( false === strpos( $from_email, '@' ) ) {
					$from_email = '';
				}
			}
		}
		if ( '' === $from_email ) {
			if ( class_exists( 'PostmanOptions' ) ) {
				$from_email = \PostmanOptions::getInstance()->getMessageSenderEmail();
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
					if ( is_array( $attachment ) ) {
						foreach ( $attachment as $a ) {
							$att[] = basename( $a );
						}
					} else {
						$att[] = basename( $attachment );
					}
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
	 * Textualize message.
	 *
	 * @param   string  $message    The message.
	 * @return  string  The textualized message.
	 * @since    2.5.0
	 */
	private static function textualize( $message ) {
		$message = wp_strip_all_tags( $message, true );
		$message = preg_replace( '/[\x00-\x1F\x7F\xA0\s+]/u', ' ', $message );
		return $message;
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
			if ( array_key_exists( 'headers', $mail ) ) {
				if ( is_array( $mail['headers'] ) ) {
					foreach ( $mail['headers'] as $header ) {
						if ( false !== strpos( $header, 'X-Simulator: ' . MAILARCHIVER_PRODUCT_SHORTNAME ) ) {
							\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->debug( 'Skip archiving.' );
							return;
						}
					}
				}
				if ( is_string( $mail['headers'] ) ) {
					if ( false !== strpos( $mail['headers'], 'X-Simulator: ' . MAILARCHIVER_PRODUCT_SHORTNAME ) ) {
						\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->debug( 'Skip archiving.' );
						return;
					}
				}
			}
			$mail['body']['raw']  = $mail['message'];
			$mail['body']['text'] = self::textualize( $mail['message'] );
			$mail['body']['type'] = 'raw';
			if ( ! array_key_exists( 'from', $mail ) ) {
				$mail['from'] = self::from( $mail['headers'] );
			}
			$mail['attachments'] = self::attachments( $mail['attachments'] );
			$mail['headers']     = self::headers( $mail['headers'] );
			// phpcs:ignore
			$key = Hash::simple_hash( serialize( $mail['to'] ) . $mail['subject'] );
			unset( $mail['message'] );
			if ( '' === $message || ( array_key_exists( $key, self::$mails ) && ! array_key_exists( 'raw', self::$mails[ $key ] ) ) ) {
				self::$mails[ $key ]['raw'] = $mail;
			}
			if ( '' !== $message ) {
				self::$mails[ $key ]['message'] = $message;
			} else {
				self::$mails[ $key ]['message'] = 'Mail sent.';
			}
			$class   = 'unknown';
			$product = 'generic';
			$version = 'x';
			if ( array_key_exists( 'listener', $mail ) ) {
				if ( array_key_exists( 'class', $mail['listener'] ) ) {
					$class = $mail['listener']['class'];
				}
				if ( array_key_exists( 'product', $mail['listener'] ) ) {
					$product = $mail['listener']['product'];
				}
				if ( array_key_exists( 'version', $mail['listener'] ) ) {
					$version = $mail['listener']['version'];
				}
				unset( $mail['listener'] );
			}
			self::$mails[ $key ]['listener']['class']   = $class;
			self::$mails[ $key ]['listener']['product'] = $product;
			self::$mails[ $key ]['listener']['version'] = $version;

		} else {
			\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->error( 'Unable to archive a malformed email.' );
		}
	}

	/**
	 * Store (send) previously catched mails.
	 *
	 * @since    1.0.0
	 */
	public static function store_archives() {
		if ( 0 < count( self::$mails ) ) {
			$span = \DecaLog\Engine::tracesLogger( MAILARCHIVER_SLUG )->startSpan( 'Archiving', DECALOG_SPAN_SHUTDOWN );
			foreach ( self::$mails as $mail ) {
				$archiver = Archive::bootstrap( $mail['listener']['class'], $mail['listener']['product'], $mail['listener']['version'] );
				if ( array_key_exists( 'message', $mail ) && 'Mail sent.' !== $mail['message'] ) {
					\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->warning( sprintf( 'Unable to send mail "%s" from %s to %s.', esc_html( $mail['raw']['subject'] ), $mail['raw']['from'], implode( ', ', $mail['raw']['to'] ) ) );
					$archiver->error( $mail['raw'], $mail['message'] );
				} else {
					if ( 1 < Option::network_get( 'mode' ) ) {
						\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->info( sprintf( 'Mail "%s" not sent from %s to %s because MailArchiver settings doesn\'t allow it.', esc_html( $mail['raw']['subject'] ), $mail['raw']['from'], implode( ', ', $mail['raw']['to'] ) ) );
					} else {
						\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->info( sprintf( 'Mail "%s" sent from %s to %s.', esc_html( $mail['raw']['subject'] ), $mail['raw']['from'], implode( ', ', $mail['raw']['to'] ) ) );
					}
					$archiver->success( $mail['raw'], $mail['message'] );
				}
			}
			\DecaLog\Engine::tracesLogger( MAILARCHIVER_SLUG )->endSpan( $span );
		}
	}

}
