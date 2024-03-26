<?php
/**
 * Imap handling
 *
 * Handles all imap operation.
 *
 * @package System
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */

namespace Mailarchiver\System;

/**
 * Define the imap functionality.
 *
 * Handles all imap operations.
 *
 * @package System
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */
class Imap {

	/**
	 * Verify if imap is available.
	 *
	 * @return   boolean  True if it's available, false otherwise.
	 * @since    2.5.0
	 */
	public static function is_available() {
		return ( function_exists( 'imap_open' ) &&
				 function_exists( 'imap_createmailbox' ) &&
		         function_exists( 'imap_utf7_encode' ) &&
				 function_exists( 'imap_append' ) );
	}

	/**
	 * Verify if openSSLn is available.
	 *
	 * @return   boolean  True if it's available, false otherwise.
	 * @since    2.5.0
	 */
	public static function is_openssl_available() {
		return ( function_exists( 'openssl_random_pseudo_bytes' ) &&
		         function_exists( 'openssl_pbkdf2' ) &&
		         function_exists( 'openssl_decrypt' ) &&
		         function_exists( 'openssl_encrypt' ) &&
		         function_exists( 'hash_hmac' ) &&
		         defined( 'OPENSSL_VERSION_TEXT' ) );
	}

	/**
	 * Get a full mailbox name.
	 *
	 * @param   string  $root   Optional. Root (path) of the mailbox.
	 * @param   string  $sep    Optional. The separator character.
	 * @return   string  The full mailbox name.
	 * @since    2.5.0
	 */
	public static function get_mailbox_name( $root = 'INBOX', $sep = '.' ) {
		return self::get_mailbox_fullpath( $root, $sep ) . self::get_mailbox_folder( $root );
	}

	/**
	 * Get a mailbox fullpath.
	 *
	 * @param   string  $root   Optional. Root (path) of the mailbox.
	 * @param   string  $sep    Optional. The separator character.
	 * @return   string  The mailbox fullpath.
	 * @since    2.5.0
	 */
	public static function get_mailbox_fullpath( $root = 'INBOX', $sep = '.' ) {
		switch ( $root ) {
			case 'GMAIL':
			case 'OVH':
			case 'GANDI':
			case 'MICROSOFT':
			case 'HOSTERRA':
				return '';
			default:
				return $root . $sep . imap_utf7_encode( MAILARCHIVER_PRODUCT_NAME ) . $sep;
		}
	}

	/**
	 * Get a mailbox search string.
	 *
	 * @param   string  $root   Optional. Root (path) of the mailbox.
	 * @param   string  $sep    Optional. The separator character.
	 * @return   string  The mailbox search string.
	 * @since    2.5.0
	 */
	public static function get_mailbox_search( $root = 'INBOX', $sep = '.' ) {
		switch ( $root ) {
			case 'GMAIL':
			case 'OVH':
			case 'GANDI':
			case 'MICROSOFT':
			case 'HOSTERRA':
				return '*';
			default:
				return $root . $sep . imap_utf7_encode( MAILARCHIVER_PRODUCT_NAME ) . $sep . '*';
		}
	}

	/**
	 * Get a mailbox folder.
	 *
	 * @param   string  $root   Optional. Root (path) of the mailbox.
	 * @return   string  The mailbox folder.
	 * @since    2.5.0
	 */
	public static function get_mailbox_folder( $root ) {
		switch ( $root ) {
			case 'GMAIL':
				return imap_utf7_encode( Blog::get_current_blog_url() ) . ' (' . imap_utf7_encode( MAILARCHIVER_PRODUCT_NAME ) . ')';
			case 'OVH':
			case 'GANDI':
			case 'MICROSOFT':
			case 'HOSTERRA':
				return imap_utf7_encode( MAILARCHIVER_PRODUCT_NAME ) . '/' . imap_utf7_encode( Blog::get_current_blog_url() );
			default:
				return imap_utf7_encode( str_replace( [ '.', '/', '\\' ], '-', Blog::get_current_blog_url() ) );
		}
	}

	/**
	 * Open a mailbox.
	 *
	 * @param   resource  $imap         The imap stream.
	 * @param   string    $server         The server connexion string.
	 * @param   string    $root           The root mailbox.
	 * @param   string    $message      The message to save.
	 * @return  bool        True if it was successful, false otherwise.
	 * @since    2.5.0
	 */
	public static function save_mail( $imap, $server, $root, $message ) {
		$message = str_replace( "\n", "\r\n", $message );
		// phpcs:ignore
		set_error_handler( null );
		// phpcs:ignore
		$sent = @imap_append( $imap, $server . self::get_mailbox_name( $root ), $message );
		// phpcs:ignore
		restore_error_handler();
		self::errors( 'APPEND' );
		return $sent;
	}

	/**
	 * Open a mailbox.
	 *
	 * @param   string  $server         The server connexion string.
	 * @param   string  $root           The root mailbox.
	 * @param   string  $user           The mailbox's user.
	 * @param   string  $pwd            The mailbox's password.
	 * @param   array   $options        optional. The opening options.
	 * @return   resource|false  The imap stream on success or False on error.
	 * @since    2.5.0
	 */
	public static function open_mailbox( $server, $root, $user, $pwd, $options = [] ) {
		switch ( $root ) {
			case 'GMAIL':
			case 'OVH':
			case 'GANDI':
			case 'MICROSOFT':
			case 'HOSTERRA':
				$conn = $server;
				break;
			default:
				$conn = $server . $root;
		}
		// phpcs:ignore
		set_error_handler( null );
		// phpcs:ignore
		$imap = @imap_open( $conn, $user, $pwd, 0, 0, $options );
		// phpcs:ignore
		restore_error_handler();
		self::errors( 'OPEN' );
		if ( $imap ) {
			\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->debug( '[IMAP OPEN] Connexion established.' );
			// phpcs:ignore
			set_error_handler( null );
			// phpcs:ignore
			$list = @imap_list( $imap, $server, self::get_mailbox_search( $root ) );
			// phpcs:ignore
			restore_error_handler();
			$exist = false;
			if ( is_array( $list ) ) {  // Non empty folder
				foreach ( $list as $val ) {
					if ( false !== strpos( $val, self::get_mailbox_folder( $root ) ) ) {
						$exist = true;
						\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->debug( '[IMAP OPEN] Mailbox already exists.' );
					}
				}
			}
			if ( ! $exist ) {  // Empty folder
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->debug( '[IMAP OPEN] Mailbox doesn\'t exist.' );
				// phpcs:ignore
				set_error_handler( null );
				// phpcs:ignore
				$create = @imap_createmailbox( $imap, $server . self::get_mailbox_name( $root ) );
				// phpcs:ignore
				$list = @imap_list( $imap, $server, self::get_mailbox_search( $root ) );
				// phpcs:ignore
				restore_error_handler();
				if ( is_array( $list ) ) {
					foreach ( $list as $val ) {
						if ( false !== strpos( $val, self::get_mailbox_folder( $root ) ) ) {
							$exist = true;
							\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->notice( sprintf( '[IMAP OPEN] Mailbox "%s" successfully created.' , self::get_mailbox_name( $root, '/' ) ) );
						}
					}
				}
			}
			if ( ! $exist ) {
				self::close_mailbox( $imap );
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->warning( sprintf( '[IMAP OPEN] Unable to access or create "%s" mailbox.' , self::get_mailbox_name( $root, '/' ) ) );
				return false;
			}
		}
		return $imap;
	}

	/**
	 * Close a mailbox.
	 *
	 * @param   resource|false  $mailbox    The previously opened mailbox.
	 * @since    2.5.0
	 */
	public static function close_mailbox( $mailbox ) {
		self::errors( 'CLOSE' );
		if ( $mailbox ) {
			imap_close( $mailbox );
		}
	}

	/**
	 * Get Imap Errors.
	 *
	 * @param    string     $operation  Optional. The current operation.
	 * @return   array      The error strings.
	 * @since    2.5.0
	 */
	private static function errors( $operation = '' ) {
		$errors = self::get_errors( $operation );
		self::get_alerts( $operation );
		return $errors;
	}

	/**
	 * Get Imap Errors.
	 *
	 * @param    string     $operation  Optional. The current operation.
	 * @return   array      The error strings.
	 * @since    2.5.0
	 */
	private static function get_errors( $operation = '' ) {
		$errors = imap_errors();
		if ( $errors ) {
			foreach ( $errors as $error ) {
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->warning( '[IMAP ' . $operation . '] ' . $error );
			}
		}
		return $errors;
	}

	/**
	 * Get Imap Alerts.
	 *
	 * @param    string     $operation  Optional. The current operation.
	 * @return   array      The alert strings.
	 * @since    2.5.0
	 */
	private static function get_alerts( $operation = '' ) {
		$alerts = imap_alerts();
		if ( $alerts ) {
			foreach ( $alerts as $alert ) {
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->notice( '[IMAP ' . $operation . '] ' . $alert );
			}
		}
		return $alerts;
	}
}
