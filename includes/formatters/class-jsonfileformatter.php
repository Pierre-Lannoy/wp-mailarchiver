<?php declare(strict_types=1);
/**
 * Fluentd formatter for Monolog
 *
 * Handles all features of Fluentd formatter for Monolog.
 *
 * @package Formatters
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */

namespace Mailarchiver\Formatter;

use MAMonolog\Formatter\FormatterInterface;
use Mailarchiver\Plugin\Feature\EventTypes;

/**
 * Define the Monolog Fluentd formatter.
 *
 * Handles all features of Fluentd formatter for Monolog.
 *
 * @package Formatters
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */
class JsonFileFormatter implements FormatterInterface {

	/**
	 * {@inheritDoc}
	 */
	public function format( array $record ): array {
		$message             = [];
		$now                 = new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
		$message['filename'] = $now->format( 'Y-m-d-H-i-s-u-' ) . ( isset( $record['level'] ) && 200 === (int) $record['level'] ? 'OK' : 'KO' ) . '.json';
		$content             = [];
		foreach ( [ 'to', 'from', 'subject', 'headers', 'attachments' ] as $item ) {
			if ( array_key_exists( $item, $record['context'] ) ) {
				$content[ $item ] = $record['context'][ $item ];
			}
		}
		if ( array_key_exists( 'body', $record['context'] ) && array_key_exists( 'raw', $record['context']['body'] ) ) {
			$content['body'] = $record['context']['body']['raw'];
		}
		$message['content'] = wp_json_encode( $content );
		return $message;
	}

	/**
	 * {@inheritDoc}
	 */
	public function formatBatch( array $records ): array {
		return [];
	}
}
