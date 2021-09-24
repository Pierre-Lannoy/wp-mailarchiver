<?php declare(strict_types=1);
/**
 * WordPress formatter for Monolog
 *
 * Handles all features of WordPress formatter for Monolog.
 *
 * @package Formatters
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Formatter;

use Mailarchiver\Plugin\Feature\ClassTypes;
use Mailarchiver\Plugin\Feature\EventTypes;
use MAMonolog\Formatter\FormatterInterface;

/**
 * Define the Monolog WordPress formatter.
 *
 * Handles all features of WordPress formatter for Monolog.
 *
 * @package Formatters
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class WordpressFormatter implements FormatterInterface {

	/**
	 * Formats a log record.
	 *
	 * @param  array $record A record to format.
	 * @return string The formatted record.
	 * @since   1.0.0
	 */
	public function format( array $record ): string {
		$message             = [];
		$values              = [];
		$values['timestamp'] = date( 'Y-m-d H:i:s' );
		if ( array_key_exists( 'level', $record ) ) {
			if ( array_key_exists( $record['level'], EventTypes::$level_names ) ) {
				$values['level'] = strtolower( EventTypes::$level_names[ $record['level'] ] );
			}
		}
		if ( array_key_exists( 'channel', $record ) ) {
			$values['channel'] = strtolower( $record['channel'] );
		}
		if ( array_key_exists( 'message', $record ) ) {
			$values['error'] = substr( $record['message'], 0, 250 );
		}
		// Context formatting.
		if ( array_key_exists( 'context', $record ) ) {
			$context = $record['context'];
			if ( array_key_exists( 'class', $context ) ) {
				if ( in_array( $context['class'], ClassTypes::$classes, true ) ) {
					$values['class'] = strtolower( $context['class'] );
				}
			}
			if ( array_key_exists( 'component', $context ) ) {
				$values['component'] = substr( $context['component'], 0, 26 );
			}
			if ( array_key_exists( 'version', $context ) ) {
				$values['version'] = substr( $context['version'], 0, 13 );
			}
			if ( array_key_exists( 'subject', $context ) ) {
				$values['subject'] = substr( $context['subject'], 0, 250 );
			}
			if ( array_key_exists( 'from', $context ) ) {
				$values['from'] = substr( $context['from'], 0, 256 );
			}
			unset( $context['body']['text'] );
			foreach ( [ 'to', 'headers', 'attachments', 'body' ] as $field ) {
				if ( array_key_exists( $field, $context ) ) {
					// phpcs:ignore
					$values[ $field ] = wp_json_encode( $context[ $field ] );
				}
			}
		}
		// Extra formatting.
		if ( array_key_exists( 'extra', $record ) ) {
			$extra = $record['extra'];
			if ( array_key_exists( 'siteid', $extra ) ) {
				$values['site_id'] = (int) $extra['siteid'];
			}
			if ( array_key_exists( 'sitename', $extra ) && is_string( $extra['sitename'] ) ) {
				$values['site_name'] = substr( $extra['sitename'], 0, 250 );
			}
			if ( array_key_exists( 'userid', $extra ) && is_numeric( $extra['userid'] ) ) {
				$values['user_id'] = substr( (string) $extra['userid'], 0, 66 );
			}
			if ( array_key_exists( 'username', $extra ) && is_string( $extra['username'] ) ) {
				$values['user_name'] = substr( $extra['username'], 0, 250 );
			}
			if ( array_key_exists( 'ip', $extra ) && is_string( $extra['ip'] ) ) {
				$values['remote_ip'] = substr( $extra['ip'], 0, 66 );
			}
		}
		$message[] = $values;
		// phpcs:ignore
		return serialize( $message );
	}
	/**
	 * Formats a set of log records.
	 *
	 * @param  array $records A set of records to format.
	 * @return string The formatted set of records.
	 * @since   1.0.0
	 */
	public function formatBatch( array $records ): string {
		$messages = [];
		foreach ( $records as $record ) {
			// phpcs:ignore
			$a = unserialize( $this->format( $record ) );
			if ( 1 === count( $a ) ) {
				$messages[] = $a[0];
			}
		}
		// phpcs:ignore
		return serialize( $messages );
	}
}
