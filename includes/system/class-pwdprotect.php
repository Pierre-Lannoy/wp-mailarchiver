<?php
/**
 * Simple password protection.
 *
 * WARNING: DO NOT USE FOR STRONG SECURITY FEATURE.
 *
 * Handles all password protection operation.
 *
 * @package System
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */

namespace Mailarchiver\System;

/**
 * Define the password protection functionality.
 *
 * Handles all password protection operations.
 *
 * @package System
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */
class PwdProtect {

	private const HMAC_LENGTH = 32;
	private const SALT_LENGTH = 32;
	private const ITERATIONS  = 1000;
	private const KEY_LENGTH  = 32;
	private const BLOCK_SIZE  = 16;
	private const CIPHER      = 'aes-256-cbc';
	private const HASH        = 'sha256';
	private const HASH_KEY    = '3babb2f4-7499-4289-afbb-eca3141f4e6b';

	/**
	 * Verify if password protection is available.
	 *
	 * @return   boolean  True if it's available, false otherwise.
	 * @since    2.5.0
	 */
	public static function is_available() {
		return ( function_exists( 'openssl_random_pseudo_bytes' ) &&
				 function_exists( 'openssl_pbkdf2' ) &&
				 function_exists( 'openssl_decrypt' ) &&
				 function_exists( 'openssl_encrypt' ) &&
				 function_exists( 'hash_hmac' ) &&
				 defined( 'OPENSSL_VERSION_TEXT' ) );
	}

	/**
	 * Get OpenSSL version.
	 *
	 * @return   string  The OpenSSL version.
	 * @since    2.5.0
	 */
	public static function get_openssl_version() {
		if ( self::is_available() ) {
			return OPENSSL_VERSION_TEXT;
		}
		return __( 'OpenSSL is not installed', 'mailarchiver' );
	}

	/**
	 * Get encryption details.
	 *
	 * @return   string  The encryption details.
	 * @since    2.5.0
	 */
	public static function get_encryption_details() {
		if ( self::is_available() ) {
			return sprintf( __( '%1$s cypher / %2$s hash', 'mailarchiver' ), self::CIPHER, self::HASH );
		}
		return __( 'OpenSSL is not installed', 'mailarchiver' );
	}

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param   string  $password   The password to use.
	 * @since    2.5.0
	 */
	public function __construct( $password ) {
		$this->password = $password;
	}

	/**
	 * Decrypt a previously encrypted block.
	 *
	 * @param   string  $encrypted_block   The block to decrypt.
	 * @return  string  The decrypted text contained in the block.
	 * @since    2.5.0
	 */
	public function decrypt( $encrypted_block ) {
		// phpcs:ignore
		$secure_message    = base64_decode( $encrypted_block );
		$mac               = substr( $secure_message, 0, self::HMAC_LENGTH );
		$salt              = substr( $secure_message, self::HMAC_LENGTH, self::SALT_LENGTH );
		$encrypted_message = substr( $secure_message, self::HMAC_LENGTH + self::SALT_LENGTH );
		$test_mac          = $this->hash_message( $encrypted_message );
		if ( 0 !== strcmp( $mac, $test_mac ) ) {
			return '';
		}
		$iv              = substr( $encrypted_message, 0, self::BLOCK_SIZE );
		$encrypted_bytes = substr( $encrypted_message, self::BLOCK_SIZE );
		$plain_text      = $this->decrypt_inner( $salt, $iv, $encrypted_bytes );
		if ( $plain_text ) {
			return $plain_text;
		}
		return '';
	}

	/**
	 * Encrypt a text.
	 *
	 * @param   string  $plain_text   The text to encrypt.
	 * @return  string  The encrypted block.
	 * @since    2.5.0
	 */
	public function encrypt( $plain_text ) {
		$iv                = openssl_random_pseudo_bytes( self::BLOCK_SIZE );
		$salt              = openssl_random_pseudo_bytes( self::SALT_LENGTH );
		$encrypted_bytes   = $this->encrypt_inner( $salt, $iv, $plain_text );
		$encrypted_message = $iv . $encrypted_bytes;
		$mac               = $this->hash_message( $encrypted_message );
		$secure_message    = $mac . $salt . $encrypted_message;
		// phpcs:ignore
		return base64_encode( $secure_message );
	}

	/**
	 * Decrypt a previously encrypted text.
	 *
	 * @param   string  $salt               The salt.
	 * @param   string  $iv                 The initialization vector.
	 * @param   string  $encrypted_bytes    The text to decrypt.
	 * @return  string  The decrypted text.
	 * @since    2.5.0
	 */
	private function decrypt_inner( $salt, $iv, $encrypted_bytes ) {
		$encryption_key = openssl_pbkdf2( $this->password, $salt, self::KEY_LENGTH, self::ITERATIONS );
		return openssl_decrypt( $encrypted_bytes, self::CIPHER, $encryption_key, OPENSSL_RAW_DATA, $iv );
	}

	/**
	 * Encrypt a text to insert in a block.
	 *
	 * @param   string  $salt           The salt.
	 * @param   string  $iv             The initialization vector.
	 * @param   string  $plain_text     The text to encrypt.
	 * @return  string  The encrypted text.
	 * @since    2.5.0
	 */
	private function encrypt_inner( $salt, $iv, $plain_text ) {
		$encryption_key = openssl_pbkdf2( $this->password, $salt, self::KEY_LENGTH, self::ITERATIONS );
		return openssl_encrypt( $plain_text, self::CIPHER, $encryption_key, OPENSSL_RAW_DATA, $iv );
	}

	/**
	 * Get a hash.
	 *
	 * @param   string  $encrypted_message   The text to compute.
	 * @return  string  The hash of the text.
	 * @since    2.5.0
	 */
	private function hash_message( $encrypted_message ) {
		return pack( 'H*', hash_hmac( self::HASH, $encrypted_message, self::HASH_KEY ) );
	}
}
