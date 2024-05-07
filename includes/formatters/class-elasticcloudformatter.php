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
use Mailarchiver\Plugin\Feature\ChannelTypes;

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
		$record['event']['kind']     = 'event';
		$record['event']['category']     = 'email';
		$record['event']['dataset'] = 'Mail';
		if ( array_key_exists( 'channel', $record ) ) {
			$record['event']['dataset'] = ChannelTypes::$channel_names_en[ strtoupper( $record['channel'] ) ];
			unset( $record['channel'] );
		} else {
			$record['event']['dataset'] = ChannelTypes::$channel_names_en['UNKNOWN'];
		}
		if ( array_key_exists( 'context', $record ) && array_key_exists( 'class', $record['context'] ) ) {
			$record['event']['module'] = $record['context']['class'];
			unset( $record['context']['class'] );
		}
		if ( array_key_exists( 'context', $record ) && array_key_exists( 'component', $record['context'] ) ) {
			$record['event']['provider'] = $record['context']['component'];
			unset( $record['context']['component'] );
		}
		if ( array_key_exists( 'context', $record ) && array_key_exists( 'code', $record['context'] ) ) {
			$record['event']['code'] = $record['context']['code'];
			unset( $record['context']['code'] );
		}
		if ( array_key_exists( 'level', $record ) ) {
			$record['log']['syslog']['severity']['name'] = ucfirst( strtolower( EventTypes::$level_names[ $record['level'] ] ) );
			$record['log']['level'] = ucfirst( strtolower( EventTypes::$level_names[ $record['level'] ] ) );
			unset( $record['level'] );
		}
		if ( array_key_exists( 'context', $record ) && array_key_exists( 'subject', $record['context'] ) ) {
			$record['message'] = $record['context']['subject'];
		}
		if ( array_key_exists( 'context', $record ) && array_key_exists( 'traceID', $record['context'] ) ) {
			$record['trace']['id'] = $record['context']['traceID'];
			unset( $record['context']['traceID'] );
		}
		if ( array_key_exists( 'context', $record ) && array_key_exists( 'instance', $record['context'] ) ) {
			$record['host']['name'] = $record['context']['instance'];
			$record['host']['hostname'] = $record['context']['instance'];
			unset( $record['context']['instance'] );
		}
		if ( array_key_exists( 'extra', $record ) && array_key_exists( 'usersession', $record['extra'] ) ) {
			$record['session']['id'] = $record['extra']['usersession'];
			unset( $record['extra']['usersession'] );
		}
		if ( array_key_exists( 'extra', $record ) && array_key_exists( 'ip', $record['extra'] ) ) {
			$record['client']['address'] = $record['extra']['ip'];
			$record['client']['ip'] = $record['extra']['ip'];
			unset( $record['extra']['ip'] );
		}
		if ( array_key_exists( 'extra', $record ) && array_key_exists( 'server', $record['extra'] ) ) {
			$record['server']['domain'] = $record['extra']['server'];
			unset( $record['extra']['server'] );
		}
		if ( array_key_exists( 'extra', $record ) && array_key_exists( 'siteid', $record['extra'] ) ) {
			$record['site']['id'] = $record['extra']['siteid'];
			unset( $record['extra']['siteid'] );
		}
		if ( array_key_exists( 'extra', $record ) && array_key_exists( 'sitename', $record['extra'] ) ) {
			$record['site']['name'] = $record['extra']['sitename'];
			unset( $record['extra']['sitename'] );
		}
		return $record;
	}
}
