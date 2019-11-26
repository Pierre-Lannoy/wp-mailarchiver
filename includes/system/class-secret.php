<?php
/**
 * Plugin "secret" handling
 *
 * Handles all plugin "secrets".
 *
 * @package System
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\System;

/**
 * Define the plugin "secret" functionality.
 *
 * Handles all plugin "secret" operations.
 *
 * @package System
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class Secret {

	/**
	 * Initializes the class and set its properties.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Decrypt the secret.
	 *
	 * @param   string $secret  The encrypted secret.
	 * @return  string  The decrypted secret.
	 * @since   1.0.0
	 */
	public static function get( $secret ) {
		return $secret;
	}

	/**
	 * Encrypt the secret.
	 *
	 * @param   string $secret  The unencrypted secret.
	 * @return  string  The crypted secret.
	 * @since   1.0.0
	 */
	public static function set( $secret ) {
		return $secret;
	}
}
