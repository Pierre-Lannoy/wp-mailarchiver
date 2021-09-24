<?php
/**
 * Grafana Cloud handler for Monolog
 *
 * Handles all features of Grafana Cloud handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Handler;

use MAMonolog\Logger;
use MAMonolog\Formatter\FormatterInterface;
use Mailarchiver\Formatter\LokiFormatter;

/**
 * Define the Monolog Grafana Cloud handler.
 *
 * Handles  all features of Grafana Cloud handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */
class GrafanaHandler extends AbstractBufferedHTTPHandler {

	/**
	 * Labels template.
	 *
	 * @since  2.5.0
	 * @var    integer    $template    The label templates ID.
	 */
	protected $template;

	/**
	 * Fixed job name.
	 *
	 * @since  2.5.0
	 * @var    string    $job    The fixed job name.
	 */
	protected $job;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param   string  $host       The Loki hostname.
	 * @param   string  $user       The suer name.
	 * @param   string  $key        The API key.
	 * @param   int     $model      The model to use for labels.
	 * @param   string  $id         The job id.
	 * @param   integer $level      Optional. The min level to log.
	 * @param   boolean $bubble     Optional. Has the record to bubble?.
	 * @since    2.5.0
	 */
	public function __construct( string $host, string $user, string $key, int $model, string $id = 'wp_mailarchiver', $level = Logger::INFO, bool $bubble = true ) {
		parent::__construct( $level, true, $bubble );
		$this->template                             = $model;
		$this->job                                  = $id;
		$this->endpoint                             = 'https://' . $user . ':' . $key . '@' . $host . '.grafana.net/loki/api/v1/push';
		$this->post_args['headers']['Content-Type'] = 'application/json';
	}

	/**
	 * Post events to the service.
	 *
	 * @param   array $events    The record to post.
	 * @since    2.5.0
	 */
	protected function write( array $events ): void {
		if ( 1 === count( $events ) ) {
			$this->post_args['body'] = $events[0];
			parent::write( $this->post_args );
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getDefaultFormatter(): FormatterInterface {
		return new LokiFormatter( $this->template, $this->job );
	}

	/**
	 * {@inheritdoc}
	 */
	public function handleBatch( array $records ): void {
		foreach ( $records as $record ) {
			if ( $record['level'] < $this->level ) {
				continue;
			}
			$this->write( [ $this->getFormatter()->format( $record ) ] );
		}
	}

}
