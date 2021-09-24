<?php declare(strict_types=1);
/**
 * Elastic Cloud formatter for Monolog
 *
 * Handles all features of Elastic Cloud formatter for Monolog.
 *
 * @package Formatters
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */

namespace Mailarchiver\Formatter;

use Mailarchiver\System\Http;
use MAMonolog\Formatter\ElasticsearchFormatter;
use Mailarchiver\Plugin\Feature\EventTypes;

/**
 * Define the Monolog Elastic Cloud formatter.
 *
 * Handles all features of Elastic Cloud formatter for Monolog.
 *
 * @package Formatters
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */
class ElasticCloudFormatter extends ElasticsearchFormatter {

	/**
	 * Formats a log record.
	 *
	 * @param  array $record A record to format.
	 * @return array The formatted record.
	 * @since   2.5.0
	 */
	public function format( array $record ): array {
		$record['@timestamp'] = date( 'c' );
		$record['_index']     = $this->index;
		$record['_type']      = $this->type;
		if ( array_key_exists( 'context', $record ) && array_key_exists( 'traceID', $record['context'] ) ) {
			$record['traceID'] = $record['context']['traceID'];
			unset( $record['context']['traceID'] );
		}
		if ( array_key_exists( 'extra', $record ) && array_key_exists( 'usersession', $record['extra'] ) ) {
			$record['sessionID'] = $record['extra']['usersession'];
			unset( $record['extra']['usersession'] );
		}
		return $record;
	}
}
