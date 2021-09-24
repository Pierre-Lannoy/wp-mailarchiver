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
				 function_exists( 'imap_append' ) );
	}
}
