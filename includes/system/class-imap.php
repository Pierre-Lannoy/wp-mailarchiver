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

use Mailarchiver\System\Blog;

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
	 * Get a full mailbox name.
	 *
	 * @param   string  $root   Optional. Root (path) of the mailbox.
	 * @param   string  $sep    Optional. The separator character.
	 * @return   string  The full mailbox name.
	 * @since    2.5.0
	 */
	public static function get_mailbox_name( $root = 'INBOX', $sep = '/' ) {
		return $root . $sep . imap_utf7_encode( MAILARCHIVER_PRODUCT_NAME ) . $sep . imap_utf7_encode( str_replace( [ '.', '/', '\\' ], '-', Blog::get_current_blog_url() ) );
	}
}
