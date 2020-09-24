<?php
/**
 * Archiver types handling
 *
 * Handles all available archiver types.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Plugin\Feature;

use Monolog\Logger;

/**
 * Define the archiver types functionality.
 *
 * Handles all available archiver types.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class HandlerTypes {

	/**
	 * The array of available types.
	 *
	 * @since  1.0.0
	 * @var    array    $handlers    The available types.
	 */
	private $handlers = [];

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->handlers[] = [
			'id'            => 'NullHandler',
			'ancestor'      => 'NullHandler',
			'namespace'     => 'Monolog\Handler',
			'class'         => 'null',
			'minimal'       => Logger::INFO,
			'name'          => esc_html__( 'Blackhole', 'mailarchiver' ),
			'help'          => esc_html__( 'Any email it can handle will be thrown away.', 'mailarchiver' ),
			'icon'          => $this->get_base64_php_icon(),
			'params'        => [],
			'configuration' => [],
			'init'          => [],
		];
		$this->handlers[] = [
			'id'            => 'FluentHandler',
			'ancestor'      => 'SocketHandler',
			'namespace'     => 'Mailarchiver\\Handler',
			'class'         => 'logging',
			'minimal'       => Logger::INFO,
			'name'          => esc_html__( 'Fluentd', 'mailarchiver' ),
			'help'          => esc_html__( 'An archive sent to a Fluentd collector.', 'mailarchiver' ),
			'icon'          => $this->get_base64_fluentd_icon(),
			'params'        => [ 'processors', 'privacy' ],
			'configuration' => [
				'host'    => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Connection string', 'mailarchiver' ),
					'help'    => esc_html__( 'Connection string to Fluentd. Can be something like "tcp://127.0.0.1:24224" or something like "unix:///var/run/td-agent/td-agent.sock".', 'mailarchiver' ),
					'default' => 'tcp://localhost:24224',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'timeout' => [
					'type'    => 'integer',
					'show'    => true,
					'name'    => esc_html__( 'Socket timeout', 'mailarchiver' ),
					'help'    => esc_html__( 'Max number of milliseconds to wait for the socket.', 'mailarchiver' ),
					'default' => 800,
					'control' => [
						'type'    => 'field_input_integer',
						'cast'    => 'integer',
						'min'     => 100,
						'max'     => 10000,
						'step'    => 100,
						'enabled' => true,
					],
				],
			],
			'init'          => [
				[
					'type'  => 'configuration',
					'value' => 'host',
				],
				[
					'type'  => 'configuration',
					'value' => 'timeout',
				],
				[ 'type' => 'level' ],
				[
					'type'  => 'literal',
					'value' => true,
				],
			],
		];
		$this->handlers[] = [
			'id'            => 'LogentriesHandler',
			'ancestor'      => 'SocketHandler',
			'namespace'     => 'Mailarchiver\\Handler',
			'class'         => 'logging',
			'minimal'       => Logger::INFO,
			'name'          => esc_html__( 'Logentries & insightOps', 'mailarchiver' ),
			'help'          => esc_html__( 'An archive sent to Logentries & insightOps service.', 'mailarchiver' ),
			'icon'          => $this->get_base64_logentries_icon(),
			'params'        => [ 'processors', 'privacy' ],
			'configuration' => [
				'host'     => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Log endpoint region', 'mailarchiver' ),
					'help'    => esc_html__( 'The region of remote host receiving messages.', 'mailarchiver' ),
					'default' => 'eu',
					'control' => [
						'type'    => 'field_select',
						'cast'    => 'string',
						'enabled' => true,
						'list'    => [ [ 'eu', esc_html__( 'Europe', 'mailarchiver') ], [ 'us', esc_html__( 'USA', 'mailarchiver') ] ],
					],
				],
				'token' => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Log token', 'mailarchiver' ),
					'help'    => esc_html__( 'The token of the Logentries/insightOps log.', 'mailarchiver' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'timeout' => [
					'type'    => 'integer',
					'show'    => true,
					'name'    => esc_html__( 'Socket timeout', 'mailarchiver' ),
					'help'    => esc_html__( 'Max number of milliseconds to wait for the socket.', 'mailarchiver' ),
					'default' => 800,
					'control' => [
						'type'    => 'field_input_integer',
						'cast'    => 'integer',
						'min'     => 100,
						'max'     => 10000,
						'step'    => 100,
						'enabled' => true,
					],
				],
			],
			'init'          => [
				[
					'type'  => 'configuration',
					'value' => 'host',
				],
				[
					'type'  => 'configuration',
					'value' => 'token',
				],
				[
					'type'  => 'configuration',
					'value' => 'timeout',
				],
				[ 'type' => 'level' ],
			],
		];
		$this->handlers[] = [
			'id'            => 'LogglyHandler',
			'ancestor'      => 'LogglyHandler',
			'namespace'     => 'Monolog\\Handler',
			'class'         => 'logging',
			'minimal'       => Logger::ERROR,
			'name'          => esc_html__( 'Loggly', 'mailarchiver' ),
			'help'          => esc_html__( 'An archive sent to Solawinds Loggly service.', 'mailarchiver' ),
			'icon'          => $this->get_base64_loggly_icon(),
			'params'        => [ 'processors', 'privacy' ],
			'configuration' => [
				'token' => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Application token', 'mailarchiver' ),
					'help'    => esc_html__( 'The token of the Solawinds Loggly application.', 'mailarchiver' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
			],
			'init'          => [
				[
					'type'  => 'configuration',
					'value' => 'token',
				],
				[ 'type' => 'level' ],
			],
		];
		$this->handlers[] = [
			'id'            => 'PshHandler',
			'ancestor'      => 'SocketHandler',
			'namespace'     => 'Mailarchiver\\Handler',
			'class'         => 'alerting',
			'minimal'       => Logger::ERROR,
			'name'          => esc_html__( 'Pushover', 'mailarchiver' ),
			'help'          => esc_html__( 'Emails in error signaled to Pushover service.', 'mailarchiver' ),
			'icon'          => $this->get_base64_pushover_icon(),
			'params'        => [ 'processors', 'privacy' ],
			'configuration' => [
				'token' => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Application token', 'mailarchiver' ),
					'help'    => esc_html__( 'The token of the Pushover application.', 'mailarchiver' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'users' => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Recipient', 'mailarchiver' ),
					'help'    => esc_html__( 'The recipient key. It can be a "user key" or a "group key".', 'mailarchiver' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'title' => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Message title', 'mailarchiver' ),
					'help'    => esc_html__( 'The title of the message which will be sent.', 'mailarchiver' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'timeout' => [
					'type'    => 'integer',
					'show'    => true,
					'name'    => esc_html__( 'Socket timeout', 'mailarchiver' ),
					'help'    => esc_html__( 'Max number of milliseconds to wait for the socket.', 'mailarchiver' ),
					'default' => 800,
					'control' => [
						'type'    => 'field_input_integer',
						'cast'    => 'integer',
						'min'     => 100,
						'max'     => 10000,
						'step'    => 100,
						'enabled' => true,
					],
				],
			],
			'init'          => [
				[
					'type'  => 'configuration',
					'value' => 'token',
				],
				[
					'type'  => 'configuration',
					'value' => 'users',
				],
				[
					'type'  => 'configuration',
					'value' => 'title',
				],
				[
					'type'  => 'configuration',
					'value' => 'timeout',
				],
				[ 'type' => 'level' ],
			],
		];
		$this->handlers[] = [
			'id'            => 'RotatingFileHandler',
			'ancestor'      => 'StreamHandler',
			'namespace'     => 'Monolog\Handler',
			'class'         => 'storing',
			'minimal'       => Logger::INFO,
			'name'          => esc_html__( 'Rotating files', 'mailarchiver' ),
			'help'          => esc_html__( 'An archive sent to files that are rotated every day and a limited number of files are kept.', 'mailarchiver' ),
			'icon'          => $this->get_base64_rotatingfiles_icon(),
			'params'        => [ 'processors', 'privacy' ],
			'configuration' => [
				'filename' => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'File', 'mailarchiver' ),
					'help'    => esc_html__( 'The full absolute path and filename, like "/path/to/file".', 'mailarchiver' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'maxfiles' => [
					'type'    => 'integer',
					'show'    => true,
					'name'    => esc_html__( 'Maximum', 'mailarchiver' ),
					'help'    => esc_html__( 'The maximal number of files to keep (0 means unlimited).', 'mailarchiver' ),
					'default' => 60,
					'control' => [
						'type'    => 'field_input_integer',
						'cast'    => 'integer',
						'min'     => 0,
						'max'     => 730,
						'step'    => 1,
						'enabled' => true,
					],
				],
			],
			'init'          => [
				[
					'type'  => 'configuration',
					'value' => 'filename',
				],
				[
					'type'  => 'configuration',
					'value' => 'maxfiles',
				],
				[ 'type' => 'level' ],
				[
					'type'  => 'literal',
					'value' => true,
				],

				[
					'type'  => 'literal',
					'value' => 0666,
				],
			],
		];
		$this->handlers[] = [
			'id'            => 'SlackWebhookHandler',
			'ancestor'      => 'SlackWebhookHandler',
			'namespace'     => 'Monolog\Handler',
			'class'         => 'alerting',
			'minimal'       => Logger::ERROR,
			'name'          => esc_html__( 'Slack', 'mailarchiver' ),
			'help'          => esc_html__( 'Emails in error signaled through Slack Webhooks.', 'mailarchiver' ),
			'icon'          => $this->get_base64_slack_icon(),
			'params'        => [ 'processors', 'privacy' ],
			'configuration' => [
				'webhook' => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Webhook URL', 'mailarchiver' ),
					'help'    => esc_html__( 'The Webhook URL set in the Slack application.', 'mailarchiver' ),
					'default' => 'https://hooks.slack.com/services/...',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'short'   => [
					'type'    => 'boolean',
					'show'    => true,
					'name'    => esc_html__( 'Short version', 'mailarchiver' ),
					'help'    => esc_html__( 'Use a shortened version of details sent in channel.', 'mailarchiver' ),
					'default' => false,
					'control' => [
						'type'    => 'field_checkbox',
						'cast'    => 'boolean',
						'enabled' => true,
					],
				],
				'data'    => [
					'type'    => 'boolean',
					'show'    => true,
					'name'    => esc_html__( 'Full data', 'mailarchiver' ),
					'help'    => esc_html__( 'Whether the sent details should include context and extra data.', 'mailarchiver' ),
					'default' => true,
					'control' => [
						'type'    => 'field_checkbox',
						'cast'    => 'boolean',
						'enabled' => true,
					],
				],
			],
			'init'          => [
				[
					'type'  => 'configuration',
					'value' => 'webhook',
				],
				[
					'type'  => 'literal',
					'value' => null,
				],
				[
					'type'  => 'literal',
					'value' => null,
				],
				[
					'type'  => 'literal',
					'value' => true,
				],
				[
					'type'  => 'literal',
					'value' => null,
				],
				[
					'type'  => 'configuration',
					'value' => 'short',
				],
				[
					'type'  => 'configuration',
					'value' => 'data',
				],
				[ 'type' => 'level' ],
				[
					'type'  => 'literal',
					'value' => true,
				],
			],
		];
		$this->handlers[] = [
			'id'            => 'SyslogUdpHandler',
			'ancestor'      => 'UdpSocket',
			'namespace'     => 'Monolog\Handler',
			'class'         => 'logging',
			'minimal'       => Logger::INFO,
			'name'          => esc_html__( 'Syslog', 'mailarchiver' ),
			'help'          => esc_html__( 'An archive sent to a remote syslogd server.', 'mailarchiver' ),
			'icon'          => $this->get_base64_syslog_icon(),
			'params'        => [ 'processors', 'privacy' ],
			'configuration' => [
				'host'     => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Host', 'mailarchiver' ),
					'help'    => esc_html__( 'The remote host receiving syslog messages.', 'mailarchiver' ),
					'default' => '127.0.0.1',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'proto'    => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Protocol', 'mailarchiver' ),
					'help'    => esc_html__( 'The used syslog protocol.', 'mailarchiver' ),
					'default' => 'UDP',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => false,
					],
				],
				'port'     => [
					'type'    => 'integer',
					'show'    => true,
					'name'    => esc_html__( 'Port', 'mailarchiver' ),
					'help'    => esc_html__( 'The opened port on remote host to receive syslog messages.', 'mailarchiver' ),
					'default' => 514,
					'control' => [
						'type'    => 'field_input_integer',
						'cast'    => 'integer',
						'min'     => 1,
						'max'     => 64738,
						'step'    => 1,
						'enabled' => true,
					],
				],
				'facility' => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Facility', 'mailarchiver' ),
					'help'    => esc_html__( 'The syslog facility for messages sent by MailArchiver.', 'mailarchiver' ),
					'default' => 'LOG_USER',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => false,
					],
				],
				'ident'    => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Identifier', 'mailarchiver' ),
					'help'    => esc_html__( 'The program identifier for archive sent by MailArchiver.', 'mailarchiver' ),
					'default' => 'MailArchiver',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'format'   => [
					'type'    => 'integer',
					'show'    => true,
					'name'    => esc_html__( 'Time format', 'mailarchiver' ),
					'help'    => esc_html__( 'The time format standard to use.', 'mailarchiver' ),
					'default' => 514,
					'control' => [
						'type'    => 'field_select',
						'cast'    => 'integer',
						'enabled' => true,
						'list'    => [ [ 0, 'BSD (RFC 3164)' ], [ 1, 'IETF (RFC 5424)' ] ],
					],
				],
			],
			'init'          => [
				[
					'type'  => 'configuration',
					'value' => 'host',
				],
				[
					'type'  => 'configuration',
					'value' => 'port',
				],
				[
					'type'  => 'literal',
					'value' => 8,
				],
				[ 'type' => 'level' ],
				[
					'type'  => 'literal',
					'value' => true,
				],
				[
					'type'  => 'configuration',
					'value' => 'ident',
				],
				[
					'type'  => 'configuration',
					'value' => 'format',
				],

			],
		];
		$this->handlers[] = [
			'id'            => 'WordpressHandler',
			'ancestor'      => 'WordpressHandler',
			'namespace'     => 'Mailarchiver\Handler',
			'class'         => 'storing',
			'minimal'       => Logger::INFO,
			'name'          => esc_html__( 'WordPress archiver', 'mailarchiver' ),
			'help'          => esc_html__( 'An archive stored in your WordPress database and available right in your admin dashboard.', 'mailarchiver' ),
			'icon'          => $this->get_base64_wordpress_icon(),
			'params'        => [ 'processors', 'privacy' ],
			'configuration' => [
				'rotate' => [
					'type'    => 'integer',
					'show'    => true,
					'name'    => esc_html__( 'Mails', 'mailarchiver' ),
					'help'    => esc_html__( 'Maximum number of emails stored in this archive (0 for no limit).', 'mailarchiver' ),
					'default' => 10000,
					'control' => [
						'type'    => 'field_input_integer',
						'cast'    => 'integer',
						'min'     => 0,
						'max'     => 10000000,
						'step'    => 1000,
						'enabled' => true,
					],
				],
				'purge'  => [
					'type'    => 'integer',
					'show'    => true,
					'name'    => esc_html__( 'Days', 'mailarchiver' ),
					'help'    => esc_html__( 'Maximum age of emails stored in this archive (0 for no limit).', 'mailarchiver' ),
					'default' => 15,
					'control' => [
						'type'    => 'field_input_integer',
						'cast'    => 'integer',
						'min'     => 0,
						'max'     => 730,
						'step'    => 1,
						'enabled' => true,
					],
				],
				'local'  => [
					'type'    => 'boolean',
					'show'    => is_multisite(),
					'name'    => esc_html__( 'Multisite partitioning', 'mailarchiver' ),
					'help'    => esc_html__( 'Local administrators can view emails sent via their site.', 'mailarchiver' ),
					'default' => false,
					'control' => [
						'type'    => 'field_checkbox',
						'cast'    => 'boolean',
						'enabled' => true,
					],
				],
			],
			'init'          => [
				[
					'type'  => 'compute',
					'value' => 'tablename',
				],
				[ 'type' => 'level' ],
				[
					'type'  => 'literal',
					'value' => true,
				],

			],
		];

	}

	/**
	 * Get the types definition.
	 *
	 * @return  array   A list of all available types definitions.
	 * @since    1.0.0
	 */
	public function get_all() {
		return $this->handlers;
	}

	/**
	 * Get the types definition for a specific class.
	 *
	 * @param   string $class  The class of archivers ( 'alerting', 'debugging', 'logging').
	 * @return  array   A list of all available types definitions.
	 * @since    1.2.0
	 */
	public function get_for_class( $class ) {
		$result = [];
		foreach ( $this->handlers as $handler ) {
			if ( $handler['class'] === $class ) {
				$result[] = $handler;
			}
		}
		usort(
			$result,
			function( $a, $b ) {
				return strcmp( strtolower( $a['name'] ), strtolower( $b['name'] ) );
			}
		);
		return $result;
	}

	/**
	 * Get the types list.
	 *
	 * @return  array   A list of all available types.
	 * @since    1.0.0
	 */
	public function get_list() {
		$result = [];
		foreach ( $this->handlers as $handler ) {
			$result[] = $handler['id'];
		}
		return $result;
	}

	/**
	 * Get a specific handler.
	 *
	 * @param   string $id The handler id.
	 * @return  null|array   The detail of the handler, null if not found.
	 * @since    1.0.0
	 */
	public function get( $id ) {
		foreach ( $this->handlers as $handler ) {
			if ( $handler['id'] === $id ) {
				return $handler;
			}
		}
		return null;
	}

	/**
	 * Get a specific handler.
	 *
	 * @param   string $id The ancestor id.
	 * @return  null|array   The detail of the handler, null if not found.
	 * @since    1.0.0
	 */
	public function get_ancestor( $id ) {
		foreach ( $this->handlers as $handler ) {
			if ( $handler['ancestor'] === $id ) {
				return $handler;
			}
		}
		return null;
	}

	/**
	 * Returns a base64 svg resource for the mail icon.
	 *
	 * @param string $color Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_mail_icon( $color = '#0073AA' ) {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd"  fill="none" width="100%" height="100%"  viewBox="0 0 2000 2000">';
		$source .= '<g transform="translate(280, 340) scale(0.8, 0.7)">';
		$source .= '<path style="fill:' . $color . '" d="M1792 710v794q0 66-47 113t-113 47h-1472q-66 0-113-47t-47-113v-794q44 49 101 87 362 246 497 345 57 42 92.5 65.5t94.5 48 110 24.5h2q51 0 110-24.5t94.5-48 92.5-65.5q170-123 498-345 57-39 100-87zm0-294q0 79-49 151t-122 123q-376 261-468 325-10 7-42.5 30.5t-54 38-52 32.5-57.5 27-50 9h-2q-23 0-50-9t-57.5-27-52-32.5-54-38-42.5-30.5q-91-64-262-182.5t-205-142.5q-62-42-117-115.5t-55-136.5q0-78 41.5-130t118.5-52h1472q65 0 112.5 47t47.5 113z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the PHP icon.
	 *
	 * @param string $color Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_php_icon( $color = '#777BB3' ) {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd"  fill="none" width="100%" height="100%"  viewBox="0 0 100 100">';
		$source .= '<g transform="translate(10,30) scale(0.86,0.86)">';
		$source .= '<path style="fill:' . $color . '" d="m7.579 10.123 14.204 0c4.169 0.035 7.19 1.237 9.063 3.604 1.873 2.367 2.491 5.6 1.855 9.699-0.247 1.873-0.795 3.71-1.643 5.512-0.813 1.802-1.943 3.427-3.392 4.876-1.767 1.837-3.657 3.003-5.671 3.498-2.014 0.495-4.099 0.742-6.254 0.742l-6.36 0-2.014 10.07-7.367 0 7.579-38.001 0 0m6.201 6.042-3.18 15.9c0.212 0.035 0.424 0.053 0.636 0.053 0.247 0 0.495 0 0.742 0 3.392 0.035 6.219-0.3 8.48-1.007 2.261-0.742 3.781-3.321 4.558-7.738 0.636-3.71 0-5.848-1.908-6.413-1.873-0.565-4.222-0.83-7.049-0.795-0.424 0.035-0.83 0.053-1.219 0.053-0.353 0-0.724 0-1.113 0l0.053-0.053"/>';
		$source .= '<path style="fill:' . $color . '" d="m41.093 0 7.314 0-2.067 10.123 6.572 0c3.604 0.071 6.289 0.813 8.056 2.226 1.802 1.413 2.332 4.099 1.59 8.056l-3.551 17.649-7.42 0 3.392-16.854c0.353-1.767 0.247-3.021-0.318-3.763-0.565-0.742-1.784-1.113-3.657-1.113l-5.883-0.053-4.346 21.783-7.314 0 7.632-38.054 0 0"/>';
		$source .= '<path style="fill:' . $color . '" d="m70.412 10.123 14.204 0c4.169 0.035 7.19 1.237 9.063 3.604 1.873 2.367 2.491 5.6 1.855 9.699-0.247 1.873-0.795 3.71-1.643 5.512-0.813 1.802-1.943 3.427-3.392 4.876-1.767 1.837-3.657 3.003-5.671 3.498-2.014 0.495-4.099 0.742-6.254 0.742l-6.36 0-2.014 10.07-7.367 0 7.579-38.001 0 0m6.201 6.042-3.18 15.9c0.212 0.035 0.424 0.053 0.636 0.053 0.247 0 0.495 0 0.742 0 3.392 0.035 6.219-0.3 8.48-1.007 2.261-0.742 3.781-3.321 4.558-7.738 0.636-3.71 0-5.848-1.908-6.413-1.873-0.565-4.222-0.83-7.049-0.795-0.424 0.035-0.83 0.053-1.219 0.053-0.353 0-0.724 0-1.113 0l0.053-0.053"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the WordPress icon.
	 *
	 * @param string $color Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_wordpress_icon( $color = '#0073AA' ) {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd"  fill="none" width="100%" height="100%"  viewBox="0 0 100 100">';
		$source .= '<g transform="translate(-38,-12) scale(15,15)">';
		$source .= '<path style="fill:' . $color . '" d="m5.8465 1.9131c0.57932 0 1.1068 0.222 1.5022 0.58547-0.1938-0.0052-0.3872 0.11-0.3952 0.3738-0.0163 0.5333 0.6377 0.6469 0.2853 1.7196l-0.2915 0.8873-0.7939-2.3386c-0.0123-0.0362 0.002-0.0568 0.0465-0.0568h0.22445c0.011665 0 0.021201-0.00996 0.021201-0.022158v-0.13294c0-0.012193-0.00956-0.022657-0.021201-0.022153-0.42505 0.018587-0.8476 0.018713-1.2676 0-0.0117-0.0005-0.0212 0.01-0.0212 0.0222v0.13294c0 0.012185 0.00954 0.022158 0.021201 0.022158h0.22568c0.050201 0 0.064256 0.016728 0.076091 0.049087l0.3262 0.8921-0.4907 1.4817-0.8066-2.3758c-0.01-0.0298 0.0021-0.0471 0.0308-0.0471h0.25715c0.011661 0 0.021197-0.00996 0.021197-0.022158v-0.13294c0-0.012193-0.00957-0.022764-0.021197-0.022153-0.2698 0.014331-0.54063 0.017213-0.79291 0.019803 0.39589-0.60984 1.0828-1.0134 1.8639-1.0134l-0.0000029-0.0000062zm1.9532 1.1633c0.17065 0.31441 0.26755 0.67464 0.26755 1.0574 0 0.84005-0.46675 1.5712-1.1549 1.9486l0.6926-1.9617c0.1073-0.3036 0.2069-0.7139 0.1947-1.0443h-0.000004zm-1.2097 3.1504c-0.2325 0.0827-0.4827 0.1278-0.7435 0.1278-0.2247 0-0.4415-0.0335-0.6459-0.0955l0.68415-1.9606 0.70524 1.9284v-1e-7zm-1.6938-0.0854c-0.75101-0.35617-1.2705-1.1213-1.2705-2.0075 0-0.32852 0.071465-0.64038 0.19955-0.92096l1.071 2.9285 0.000003-0.000003zm0.95023-4.4367c1.3413 0 2.4291 1.0878 2.4291 2.4291s-1.0878 2.4291-2.4291 2.4291-2.4291-1.0878-2.4291-2.4291 1.0878-2.4291 2.4291-2.4291zm0-0.15354c1.4261 0 2.5827 1.1566 2.5827 2.5827s-1.1566 2.5827-2.5827 2.5827-2.5827-1.1566-2.5827-2.5827 1.1566-2.5827 2.5827-2.5827z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the WordPress icon.
	 *
	 * @param string $color Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_browserconsole_icon( $color = '#444466' ) {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd"  fill="none" width="100%" height="100%"  viewBox="0 0 100 100">';
		$source .= '<g transform="translate(12,12) scale(0.075,0.075)">';
		$source .= '<path style="fill:' . $color . '" d="M913.2,10.5H86.8C44.5,10.5,10,44.9,10,87.2v825.5c0,42.3,34.5,76.8,76.8,76.8h826.4c42.4,0,76.8-34.5,76.8-76.8V87.2C990,44.9,955.5,10.5,913.2,10.5z M861.1,97.9c18.2,0,32.9,14.7,32.9,32.9c0,18.2-14.7,32.9-32.9,32.9c-18.2,0-32.9-14.7-32.9-32.9C828.2,112.6,843,97.9,861.1,97.9z M743.2,97.9c18.2,0,32.9,14.7,32.9,32.9c0,18.2-14.7,32.9-32.9,32.9c-18.2,0-32.9-14.7-32.9-32.9C710.3,112.6,725,97.9,743.2,97.9z M902.2,901.8H97.8V245.4h804.5L902.2,901.8L902.2,901.8z M478.4,490.9L213.4,612.3v-58.5l204.8-87.8V465l-204.8-87.8v-58.5l264.9,121.4V490.9L478.4,490.9z M786.6,592.3h-276v-27.6h276V592.3z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Chrome icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @param string $color2 Optional. Color 2 of the icon.
	 * @param string $color3 Optional. Color 3 of the icon.
	 * @param string $color4 Optional. Color 4 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_chrome_icon( $color1 = '#EA4335', $color2 = '#4285F4', $color3 = '#34A853', $color4 = '#FBBC05' ) {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd"  fill="none" width="100%" height="100%"  viewBox="0 0 100 100">';
		$source .= '<g transform="translate(10,10) scale(1.66,1.66)">';
		$source .= '<path style="fill:' . $color1 . '" d="M5.795 8.361C16.952-4.624 37.64-2.06 45.373 13.107c-5.444.002-13.969-.001-18.586 0-3.349.001-5.51-.075-7.852 1.158-2.753 1.449-4.83 4.135-5.555 7.29L5.795 8.36z"/>';
		$source .= '<path style="fill:' . $color2 . '" d="M16.015 24c0 4.4 3.579 7.98 7.977 7.98s7.976-3.58 7.976-7.98c0-4.401-3.578-7.982-7.976-7.982s-7.977 3.58-7.977 7.981z"/>';
		$source .= '<path style="fill:' . $color3 . '" d="M27.088 34.446c-4.477 1.33-9.717-.145-12.587-5.1A7917.733 7917.733 0 0 1 3.892 10.898C-5.322 25.02 2.62 44.264 19.346 47.55l7.742-13.103z"/>';
		$source .= '<path style="fill:' . $color4 . '" d="M31.401 16.018c3.73 3.468 4.542 9.084 2.016 13.439-1.903 3.28-7.977 13.531-10.92 18.495C39.73 49.015 52.294 32.124 46.62 16.018H31.4z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the SysLog icon.
	 *
	 * @param string $color Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_syslog_icon( $color = '#983256' ) {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd"  fill="none" width="100%" height="100%"  viewBox="0 0 100 100">';
		$source .= '<g transform="translate(12,14) scale(0.075,0.075)">';
		$source .= '<path style="fill:' . $color . '" d="M254.1,213.7h491.7l4.8,0.3c12-1.1,22.8-6.7,31.3-14.7c8.8-9.4,14.4-21.7,14.4-35.6v-25.9c0-13.9-5.6-26.8-14.4-35.6l0,0c-9.1-9.4-22-15-36.1-15H254.1c-13.9,0-26.8,5.6-35.9,15c-9.1,8.8-14.5,21.7-14.5,35.6v25.9c0,13.9,5.4,26.2,14.5,35.6c8.5,8,18.7,13.6,31.1,14.7L254.1,213.7L254.1,213.7z M278.2,457L278.2,457c20.9,0,38,17.1,38,37.8c0,20.6-17.1,37.7-38,37.7c-20.9,0-37.5-17.1-37.5-37.7C240.7,474.2,257.4,457,278.2,457L278.2,457z M278.2,284.9L278.2,284.9c20.9,0,38,16.9,38,37.5c0,21.2-17.1,38-38,38c-20.9,0-37.5-16.9-37.5-38C240.7,301.8,257.4,284.9,278.2,284.9L278.2,284.9z M278.2,112.8L278.2,112.8c20.9,0,38,17.1,38,37.8c0,21.1-17.1,37.8-38,37.8c-20.9,0-37.5-16.6-37.5-37.8C240.7,129.9,257.4,112.8,278.2,112.8L278.2,112.8z M537.9,603.7L537.9,603.7v102.8c20.1,5.9,38.5,17.1,53,32.1c14.5,14.2,25.4,31.6,31.6,51.7h329.8c20.9,0,37.7,16.3,37.7,37.7c0,20.6-16.8,37.5-37.7,37.5H623.5c-6.2,21.1-17.1,40.1-32.7,54.9c-23,23.8-55.1,38-91,38c-35.3,0-68-14.2-91-38c-15-14.8-26.2-33.7-32.7-54.9H47.5C26.6,865.5,10,848.7,10,828c0-21.4,16.6-37.7,37.5-37.7h329.8c6.4-20.1,17.6-37.5,31.6-51.7c14.7-15,32.9-26.2,53.2-32.1V603.7h-208c-26.2,0-50.3-11-67.7-28.4v-0.6c-17.4-16.6-28.1-40.9-28.1-67.2v-26c0-26.2,10.7-50.3,28.1-67.7c1.9-1.9,3.5-3.5,5.9-5.6c-2.4-1.3-4-3.2-5.9-4.8c-17.4-17.6-28.1-41.5-28.1-67.7v-26.2c0-26.2,10.7-50.3,28.1-67.2c1.9-2.4,3.5-4,5.9-5.4c-2.4-2.1-4-4-5.9-5.6c-17.4-17.4-28.1-41.5-28.1-67.7v-25.9c0-26.2,10.7-50.6,28.1-67.7c17.4-17.4,41-28.4,67.7-28.4h491.7c26.5,0,50.3,11,67.7,28.4l0,0c17.1,17.1,28.1,41.5,28.1,67.7v25.9c0,26.2-11,50.3-28.1,67.7c-1.6,1.6-4,3.5-5.9,5.6c1.9,1.4,3.7,2.9,5.9,5.4l0,0c17.1,17.1,28.1,40.9,28.1,67.2v26.2c0,26.3-11,50.3-28.1,67.7c-1.6,1.6-4,3.5-5.9,4.8c1.9,2.1,3.7,3.8,5.9,5.6l0,0c17.1,17.4,28.1,41.5,28.1,67.7v26c0,26.2-11,50.6-28.1,67.2c-17.4,17.9-41.2,28.9-67.7,28.9H537.9L537.9,603.7z M750.7,259.2L750.7,259.2l-4.8,0.2H254.1l-4.8-0.2c-12.4,0.8-22.5,6.4-31.1,14.7c-9.1,9.1-14.5,21.7-14.5,35.6v26.2c0,13.7,5.4,26.5,14.5,35.6v0.5c8.5,7.8,18.7,13.4,31.1,14.2l4.8-0.2h491.7l4.8,0.2c12-0.8,22.8-6.4,31.3-14.7c8.8-9.1,14.4-21.9,14.4-35.6v-26.2c0-13.9-5.6-26.5-14.4-35.6v-0.5C773.5,265.7,762.7,260,750.7,259.2L750.7,259.2z M750.7,431.3L750.7,431.3l-4.8,0.3H254.1l-4.8-0.3c-12.4,1.1-22.5,6.4-31.1,14.7c-9.1,9.4-14.5,21.7-14.5,35.6v26c0,13.9,5.4,26.5,14.5,35.6c9.1,9.4,21.9,15,35.9,15h491.7c14.2,0,26.5-5.6,36.1-15c8.8-9.1,14.4-21.7,14.4-35.6v-26c0-13.9-5.6-26.2-14.4-35.6l0,0C773.5,437.8,762.7,432.4,750.7,431.3L750.7,431.3z M558.8,770.2L558.8,770.2c-15-15-35.3-24.4-58.9-24.4c-23,0-43.9,9.4-58.9,24.4c-15,15.3-24.6,35.9-24.6,59.2c0,23.3,9.6,44.2,24.6,59.1c15,15,35.9,24.4,58.9,24.4c23.6,0,43.9-9.4,58.9-24.4c15.5-15,24.6-35.8,24.6-59.1C583.4,806.1,574.3,785.5,558.8,770.2L558.8,770.2z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the files icon.
	 *
	 * @param string $color Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_rotatingfiles_icon( $color = '#FF9920' ) {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd"  fill="none" width="100%" height="100%"  viewBox="0 0 1085 1024">';
		$source .= '<g transform="translate(140,0) scale(0.9,0.9)">';
		$source .= '<path style="fill:' . $color . '" d="M818.695498 905.89199c-7.809239 32.224332-25.761513 57.447276-57.447276 57.447276l-718.090955 0 100.532734-473.94003c7.539955-33.750275 25.58199-57.447276 57.357515-57.447276l660.823202 0c31.685763 0 57.357515 25.761513 57.357515 57.447276L818.695498 905.89199 818.695498 905.89199 818.695498 905.89199zM157.962058 388.866502l646.371621 0L804.333679 332.406601c0-31.775525-25.761513-57.447276-57.447276-57.447276l-344.683658 0 0-52.061594c0-31.685763-25.761513-57.447276-57.447276-57.447276L57.429324 165.450454c-31.685763 0-57.447276 25.761513-57.447276 57.447276l0 682.99426 100.442972-459.578211C107.875214 415.076822 126.276294 388.866502 157.962058 388.866502L157.962058 388.866502 157.962058 388.866502zM392.020214 845.683654c0.919156 4.136204 3.217047 7.81283 6.893673 10.570299l70.775045 54.689807c3.217047 2.297891 6.893673 3.676626 10.570299 3.676626 2.757469 0 5.514939-0.459578 7.81283-1.838313 5.974517-2.757469 9.651142-9.191564 9.651142-16.085237l0-17.92355 0-15.166081c47.336556-6.434095 92.37522-31.251318 123.166961-73.072936 56.52812-76.749561 44.119508-185.669597-28.493849-247.253078-79.966609-68.477153-199.916522-55.608964-263.338315 26.655536-37.685413 48.255712-46.876978 109.379614-31.251318 164.069421 4.595782 15.166081 21.600176 22.519332 35.387522 15.166081l0 0c11.489455-5.974517 17.463972-19.302285 13.787346-31.251318-11.029877-39.983304-3.217047-84.562391 25.73638-119.030757 43.65993-51.47276 120.409491-61.123902 175.558877-22.059754 60.664324 43.200352 72.153779 127.762743 26.655536 185.669597-20.221441 25.73638-47.796134 42.281195-77.668718 47.796134l0-0.459578 0-4.595782 0-18.383128c0-6.893673-3.676626-13.327768-9.651142-16.085237-2.757469-1.378735-5.514939-1.838313-7.81283-1.838313-3.676626 0-7.353251 1.378735-10.570299 3.676626l-70.775045 55.149385c-4.595782 3.217047-6.893673 9.191564-7.353251 14.706503 0 0.919156 0 1.838313 0 2.757469 0 0-0.459578 0.459578-0.919156 0.919156C391.101058 845.683654 391.560636 845.683654 392.020214 845.683654z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Chrome icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @param string $color2 Optional. Color 2 of the icon.
	 * @param string $color3 Optional. Color 3 of the icon.
	 * @param string $color4 Optional. Color 4 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_slack_icon( $color1 = '#36c5f0', $color2 = '#2eb67d', $color3 = '#ecb22e', $color4 = '#e01e5a' ) {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd"  fill="none" width="100%" height="100%"  viewBox="0 0 100 100">';
		$source .= '<g transform="translate(16,16) scale(1.16,1.16)">';
		$source .= '<path style="fill:' . $color1 . '" d="m 21.902,0.148 c -3.299,0 -5.973,2.68 -5.973,5.985 a 5.979,5.979 0 0 0 5.973,5.985 h 5.974 V 6.133 A 5.98,5.98 0 0 0 21.902,0.148 m 0,15.96 H 5.973 C 2.674,16.108 0,18.788 0,22.094 c 0,3.305 2.674,5.985 5.973,5.985 h 15.93 c 3.298,0 5.973,-2.68 5.973,-5.985 0,-3.306 -2.675,-5.986 -5.974,-5.986"/>';
		$source .= '<path style="fill:' . $color2 . '" d="m 59.734,22.094 c 0,-3.306 -2.675,-5.986 -5.974,-5.986 -3.299,0 -5.973,2.68 -5.973,5.986 v 5.985 h 5.973 a 5.98,5.98 0 0 0 5.974,-5.985 m -15.929,0 V 6.133 A 5.98,5.98 0 0 0 37.831,0.148 c -3.299,0 -5.973,2.68 -5.973,5.985 v 15.96 c 0,3.307 2.674,5.987 5.973,5.987 a 5.98,5.98 0 0 0 5.974,-5.985"/>';
		$source .= '<path style="fill:' . $color3 . '" d="m 37.831,60 a 5.98,5.98 0 0 0 5.974,-5.985 5.98,5.98 0 0 0 -5.974,-5.985 h -5.973 v 5.985 c 0,3.305 2.674,5.985 5.973,5.985 m 0,-15.96 h 15.93 c 3.298,0 5.973,-2.68 5.973,-5.986 A 5.98,5.98 0 0 0 53.76,32.069 H 37.831 c -3.299,0 -5.973,2.68 -5.973,5.985 a 5.979,5.979 0 0 0 5.973,5.985"/>';
		$source .= '<path style="fill:' . $color4 . '" d="m 0,38.054 a 5.979,5.979 0 0 0 5.973,5.985 5.98,5.98 0 0 0 5.974,-5.985 V 32.069 H 5.973 C 2.674,32.069 0,34.749 0,38.054 m 15.929,0 v 15.96 c 0,3.306 2.674,5.986 5.973,5.986 a 5.98,5.98 0 0 0 5.974,-5.985 V 38.054 a 5.979,5.979 0 0 0 -5.974,-5.985 c -3.299,0 -5.973,2.68 -5.973,5.985"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Chrome icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @param string $color2 Optional. Color 2 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_stackdriver_icon( $color1 = '#FFFFFF', $color2 = '#4387fd' ) {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd"  fill="none" width="100%" height="100%"  viewBox="0 0 100 100">';
		$source .= '<g transform="translate(8,8) scale(0.66,0.66)">';
		$source .= '<path style="fill:' . $color2 . '" d="M27.7911,115.183L1.5406,69.7158a11.499,11.499,0,0,1,0-11.499L27.7911,12.75A11.499,11.499,0,0,1,37.75,7H90.25a11.499,11.499,0,0,1,9.9585,5.75l26.25,45.4672a11.499,11.499,0,0,1,0,11.499l-26.25,45.4672a11.499,11.499,0,0,1-9.9585,5.75H37.75A11.499,11.499,0,0,1,27.7911,115.183Z"/>';
		$source .= '<polygon points="94 64 80.147 39.913 66.294 64 80.147 88.087 94 64" fill="' . $color1 . '"/>';
		$source .= '<polygon points="34 66.001 47.803 90 75.481 90 61.678 66.001 34 66.001" fill="' . $color1 . '"/>';
		$source .= '<polygon points="61.5 62 75.525 38 47.846 38 34 62 61.5 62" fill="' . $color1 . '"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Pushover icon.
	 *
	 * @param string $color Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_pushover_icon( $color = '#249DF1' ) {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" opacity="0.91" width="602px" height="602px" viewBox="57 57 602 602">';
		$source .= '<g transform="translate(116, 116) scale(0.82,0.82)">';
		$source .= '<ellipse style="fill:' . $color . '; fill-rule: evenodd; stroke:#FFFFFF; stroke-width: 0;" transform="matrix(-0.674571, 0.73821, -0.73821, -0.674571, 556.833239, 241.613465)" cx="216.308" cy="152.076" rx="296.855" ry="296.855"/>';
		$source .= '<path style="fill:#FFFFFF; fill-rule: nonzero; white-space: pre;" transform="matrix(1, 0, 0, 1, 0, 0)" d="M 280.949 172.514 L 355.429 162.714 L 282.909 326.374 L 282.909 326.374 C 295.649 325.394 308.142 321.067 320.389 313.394 L 320.389 313.394 L 320.389 313.394 C 332.642 305.714 343.916 296.077 354.209 284.484 L 354.209 284.484 L 354.209 284.484 C 364.496 272.884 373.396 259.981 380.909 245.774 L 380.909 245.774 L 380.909 245.774 C 388.422 231.561 393.812 217.594 397.079 203.874 L 397.079 203.874 L 397.079 203.874 C 399.039 195.381 399.939 187.214 399.779 179.374 L 399.779 179.374 L 399.779 179.374 C 399.612 171.534 397.569 164.674 393.649 158.794 L 393.649 158.794 L 393.649 158.794 C 389.729 152.914 383.766 148.177 375.759 144.584 L 375.759 144.584 L 375.759 144.584 C 367.759 140.991 356.899 139.194 343.179 139.194 L 343.179 139.194 L 343.179 139.194 C 327.172 139.194 311.409 141.807 295.889 147.034 L 295.889 147.034 L 295.889 147.034 C 280.376 152.261 266.002 159.857 252.769 169.824 L 252.769 169.824 L 252.769 169.824 C 239.542 179.784 228.029 192.197 218.229 207.064 L 218.229 207.064 L 218.229 207.064 C 208.429 221.924 201.406 238.827 197.159 257.774 L 197.159 257.774 L 197.159 257.774 C 195.526 263.981 194.546 268.961 194.219 272.714 L 194.219 272.714 L 194.219 272.714 C 193.892 276.474 193.812 279.577 193.979 282.024 L 193.979 282.024 L 193.979 282.024 C 194.139 284.477 194.462 286.357 194.949 287.664 L 194.949 287.664 L 194.949 287.664 C 195.442 288.971 195.852 290.277 196.179 291.584 L 196.179 291.584 L 196.179 291.584 C 179.519 291.584 167.349 288.234 159.669 281.534 L 159.669 281.534 L 159.669 281.534 C 151.996 274.841 150.119 263.164 154.039 246.504 L 154.039 246.504 L 154.039 246.504 C 157.959 229.191 166.862 212.694 180.749 197.014 L 180.749 197.014 L 180.749 197.014 C 194.629 181.334 211.122 167.531 230.229 155.604 L 230.229 155.604 L 230.229 155.604 C 249.342 143.684 270.249 134.214 292.949 127.194 L 292.949 127.194 L 292.949 127.194 C 315.656 120.167 337.789 116.654 359.349 116.654 L 359.349 116.654 L 359.349 116.654 C 378.296 116.654 394.219 119.347 407.119 124.734 L 407.119 124.734 L 407.119 124.734 C 420.026 130.127 430.072 137.234 437.259 146.054 L 437.259 146.054 L 437.259 146.054 C 444.446 154.874 448.936 165.164 450.729 176.924 L 450.729 176.924 L 450.729 176.924 C 452.529 188.684 451.959 200.934 449.019 213.674 L 449.019 213.674 L 449.019 213.674 C 445.426 229.027 438.646 244.464 428.679 259.984 L 428.679 259.984 L 428.679 259.984 C 418.719 275.497 406.226 289.544 391.199 302.124 L 391.199 302.124 L 391.199 302.124 C 376.172 314.697 358.939 324.904 339.499 332.744 L 339.499 332.744 L 339.499 332.744 C 320.066 340.584 299.406 344.504 277.519 344.504 L 277.519 344.504 L 275.069 344.504 L 212.839 484.154 L 142.279 484.154 L 280.949 172.514 Z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Fluentd icon.
	 *
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_fluentd_icon() {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" opacity="0.91" width="602px" height="602px" viewBox="0 0 1274 1047">';
		$source .= '<g transform="translate(120, 100) scale(0.84,0.84)">';
		$source .= '<style type="text/css"> .st0{fill:url(#SVGID_1_);} .st1{fill:url(#SVGID_2_);} .st2{fill:url(#SVGID_3_);} .st3{fill:url(#SVGID_4_);} .st4{fill:url(#SVGID_5_);} .st5{fill:url(#SVGID_6_);} .st6{fill:url(#SVGID_7_);} .st7{fill:url(#SVGID_8_);} .st8{fill:#FFFFFF;}</style>';
		$source .= '<linearGradient id="SVGID_1_" gradientUnits="userSpaceOnUse" x1="21.0001" y1="1105.2095" x2="1258.6993" y2="1105.2095" gradientTransform="matrix(1 0 0 -1 0 1630)"><stop  offset="0" style="stop-color:#2A59A2"/><stop  offset="1" style="stop-color:#2A59A2"/></linearGradient>';
		$source .= '<path class="st0" d="M1252.1,450.3c-6.5,0-16.5,0-22.1,0c-40.2-0.5-117.3,3.8-187.2,64.2c-166.9,144.5-205,357.2-490.6,472 C314.3,1082.1,21,991.6,21,991.6s261.7-18.2,296.4-217.9c27.1-156.2-115-267.1-204.2-408C22.5,222.2-0.8,103,42.9,51.5 C166.8-94.8,508,300.7,677.4,354c165.8,52.1,277.7-68.9,415-51.1c43.8,5.6,65.2,24.5,79.4,48.6c4.8,8.1,16.6,35.7,27.1,45.9 c10.3,10,24,17.4,36.8,24.9C1252,431.9,1268.1,450.3,1252.1,450.3z"/>';
		$source .= '<linearGradient id="SVGID_2_" gradientUnits="userSpaceOnUse" x1="-28.9755" y1="1316.5281" x2="1280.1417" y2="1316.5281" gradientTransform="matrix(1 0 0 -1 0 1630)"><stop  offset="0" style="stop-color:#91D3F2"/><stop  offset="0.2664" style="stop-color:#6FB2DE"/><stop  offset="0.5214" style="stop-color:#5598CE"/><stop  offset="0.6729" style="stop-color:#4B8FC8"/></linearGradient>';
		$source .= '<path class="st1" d="M468,221.5c-41-31.2-83.3-63.9-124.9-93.7l0,0c-9.4-6.7-18.8-13.3-28.1-19.7l0,0 C203.5,31.8,100.1-16,42.9,51.5C-0.8,103,22.5,222.2,113.3,365.7c0.9,1.5,1.9,2.9,2.8,4.4c47.3,73.2,184.5,244.7,509.7,237.4 c21.4-22.7,53.9-65.6,95.9-112.3C653.6,385.1,551,289.1,468,221.5z"/>';
		$source .= '<linearGradient id="SVGID_3_" gradientUnits="userSpaceOnUse" x1="21.1001" y1="817.0737" x2="1267.8341" y2="817.0737" gradientTransform="matrix(1 0 0 -1 0 1630)"><stop  offset="0" style="stop-color:#2C9EC7"/><stop  offset="0.4037" style="stop-color:#2C63A5"/><stop  offset="1" style="stop-color:#395DA1"/></linearGradient>';
		$source .= '<path class="st2" d="M795.8,738.9c1.3-48.8-7.6-96.9-24.5-143.4c-42.7,6.4-90.8,10.8-145.5,12C586,649.8,495.1,762.8,344.5,864.6 c-200.5,135.6-323.4,127-323.4,127s293.4,90.5,531.2-5.1c85.3-34.3,148.6-77.3,199.8-124.7C760.5,853.8,794,812,795.8,738.9z"/>';
		$source .= '<linearGradient id="SVGID_4_" gradientUnits="userSpaceOnUse" x1="21.0793" y1="1078.65" x2="1267.8134" y2="1078.65" gradientTransform="matrix(1 0 0 -1 0 1630)"><stop  offset="0" style="stop-color:#4FAAC4"/><stop  offset="1.554530e-03" style="stop-color:#2F75B1"/><stop  offset="1" style="stop-color:#356EAC"/></linearGradient>';
		$source .= '<path class="st3" d="M721.8,495.2c-42,46.6-74.6,89.6-95.9,112.3c54.8-1.2,102.9-5.6,145.5-12c-5-13.9-10.8-27.6-17.2-41.1 C744.6,534.3,733.7,514.5,721.8,495.2z"/>';
		$source .= '<linearGradient id="SVGID_5_" gradientUnits="userSpaceOnUse" x1="467.9999" y1="1271.65" x2="1274.9838" y2="1271.65" gradientTransform="matrix(1 0 0 -1 0 1630)"><stop  offset="0" style="stop-color:#4FAAC4"/><stop  offset="1.554530e-03" style="stop-color:#2F81B6"/><stop  offset="1" style="stop-color:#3B5EA9"/></linearGradient>';
		$source .= '<path class="st4" d="M965.6,318.3c-87.8,26.6-175.5,71.1-288.2,35.7c-55.8-17.5-130.3-72.2-209.4-132.5 c83,67.6,185.6,163.5,253.8,273.7C784,426.3,867,349.3,965.6,318.3z"/>';
		$source .= '<linearGradient id="SVGID_6_" gradientUnits="userSpaceOnUse" x1="467.9684" y1="962.7" x2="1274.9521" y2="962.7" gradientTransform="matrix(1 0 0 -1 0 1630)"><stop  offset="0" style="stop-color:#4FAAC4"/><stop  offset="1.554530e-03" style="stop-color:#1E3773"/><stop  offset="1" style="stop-color:#203370"/></linearGradient>';
		$source .= '<path class="st5" d="M771.4,595.5c16.9,46.5,25.8,94.6,24.5,143.4c-1.9,73-35.4,114.8-43.8,122.9 c120.1-111.1,173.8-246,290.8-347.3c21.8-18.8,44.2-32.2,66.1-41.7h-0.1C1032.2,505,949,568.7,771.4,595.5z"/>';
		$source .= '<linearGradient id="SVGID_7_" gradientUnits="userSpaceOnUse" x1="990.2508" y1="895.2981" x2="990.2508" y2="1337.8136" gradientTransform="matrix(1 0 0 -1 0 1630)"><stop  offset="0" style="stop-color:#4FAAC4"/><stop  offset="1.554530e-03" style="stop-color:#2C5A9A"/><stop  offset="1" style="stop-color:#374580"/></linearGradient>';
		$source .= '<path class="st6" d="M1252.1,450.3c16,0-0.1-18.4-16.3-27.9c-12.8-7.6-26.5-15-36.8-24.9c-10.6-10.2-22.4-37.9-27.1-45.9 c-14.3-24.2-35.6-43-79.4-48.6c-44-5.7-85.4,2.9-126.8,15.4c-98.6,31-181.7,108-243.9,176.9c11.9,19.3,22.9,39,32.4,59.1 c6.4,13.6,12.2,27.3,17.2,41.1c177.7-26.8,260.8-90.5,337.5-122.7h0.1c48.3-21,93.5-22.9,121.1-22.5 C1235.7,450.3,1245.7,450.3,1252.1,450.3z"/>';
		$source .= '<linearGradient id="SVGID_8_" gradientUnits="userSpaceOnUse" x1="-113.5052" y1="949.0955" x2="804.8284" y2="949.0955" gradientTransform="matrix(1 0 0 -1 0 1630)"><stop  offset="0.1115" style="stop-color:#38B1DA"/><stop  offset="1" style="stop-color:#326FB5"/></linearGradient>';
		$source .= '<path class="st7" d="M344.5,864.6C495.1,762.8,586,649.8,625.8,607.5c-325.2,7.3-462.4-164.2-509.7-237.4 C205.4,509,344.3,619.2,317.5,773.7C282.8,973.4,21.1,991.6,21.1,991.6S144,1000.2,344.5,864.6z"/>';
		$source .= '<ellipse class="st8" cx="1083.4" cy="377.4" rx="26" ry="25.8"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Logentrie icon.
	 *
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_logentries_icon() {
		$source  = '<svg width="256px" height="256px" viewBox="0 0 256 256" version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve">';
		$source .= '<g transform="translate(34, 34) scale(0.74,0.74)">';
		$source .= '<path style="fill:#F47721" d="M201.73137,255.654114 L53.5857267,255.654114 C24.0701195,255.654114 0.230590681,231.814585 0.230590681,202.298978 L0.230590681,53.5857267 C0.230590681,24.0701195 24.0701195,0.230590681 53.5857267,0.230590681 L201.73137,0.230590681 C231.246977,0.230590681 255.086506,24.0701195 255.086506,53.5857267 L255.086506,202.298978 C255.086506,231.814585 231.246977,255.654114 201.73137,255.654114Z" />';
		$source .= '<path style="fill:#FFFFFF" d="M202.298978,71.7491772 C204.569409,70.0463537 207.407448,68.3435302 209.67788,66.6407067 C208.542664,62.6674519 206.272233,59.261805 204.001801,55.856158 C201.163762,56.9913736 198.893331,58.6941971 196.055292,59.8294128 C194.352468,58.6941971 192.649645,56.9913736 190.379214,55.856158 C190.946821,53.0181188 191.514429,49.6124719 192.082037,46.7744327 C188.108782,45.639217 184.135527,43.9363936 180.162273,43.3687857 C179.027057,46.2068249 178.459449,49.044864 177.324234,51.8829032 C175.053802,51.8829032 172.783371,52.450511 171.080547,53.0181188 C169.377724,50.7476875 167.6749,47.9096484 165.972077,45.639217 C161.998822,46.7744327 158.593175,49.044864 155.187528,51.8829032 C156.322744,54.7209423 157.457959,57.5589815 159.160783,60.3970206 C157.457959,62.0998441 156.322744,63.8026676 155.187528,65.5054911 C152.349489,64.9378832 148.943842,64.3702754 146.105803,63.8026676 C144.970587,67.7759224 143.835372,71.7491772 142.700156,75.722432 C145.538195,76.8576477 148.376234,77.9928633 151.214273,78.5604712 C151.214273,80.8309025 151.781881,83.1013338 151.781881,85.3717651 C149.51145,87.0745886 146.673411,88.7774121 144.402979,90.4802356 C145.538195,94.4534904 147.808626,97.8591374 150.646666,101.264784 C153.484705,100.129569 156.322744,98.4267452 159.160783,97.2915295 C160.863606,98.994353 162.56643,100.129569 164.269253,101.264784 C163.701646,104.102823 163.134038,107.50847 162.56643,110.34651 C166.539685,112.049333 170.51294,112.616941 174.486194,113.184549 C175.053802,110.34651 176.189018,107.50847 177.324234,104.670431 C179.594665,104.670431 181.865096,104.102823 184.135527,104.102823 C185.838351,106.373255 187.541174,109.211294 189.243998,111.481725 C193.217253,109.778902 196.6229,108.076078 199.460939,105.238039 C198.325723,102.4 196.6229,99.5619609 195.487684,96.7239217 C196.6229,95.0210982 198.325723,93.3182747 199.460939,91.6154512 C202.298978,92.1830591 205.704625,92.7506669 208.542664,93.3182747 C209.67788,89.3450199 211.380703,85.3717651 211.948311,81.3985103 C209.110272,80.8309025 206.272233,79.6956868 203.434194,78.5604712 C203.434194,76.2900398 202.866586,74.0196085 202.298978,71.7491772 L202.298978,71.7491772 Z M189.811606,79.6956868 C189.811606,87.0745886 181.865096,92.1830591 175.053802,89.9126277 C168.810116,88.2098043 164.836861,80.8309025 167.107293,74.5872164 C168.242508,70.6139615 171.648155,68.3435302 175.053802,67.2083146 C182.432704,64.9378832 190.379214,71.7491772 189.811606,79.6956868 L189.811606,79.6956868 Z"/>';
		$source .= '<circle style="fill:#F36D21" cx="177.324234" cy="78.5604712" r="17.0282349"/>';
		$source .= '<path style="fill:#FFFFFF" d="M127.374745,193.217253 C140.997332,192.649645 150.079058,202.298978 160.863606,207.975056 C176.756626,216.489174 192.082037,214.78635 204.001801,200.596155 C209.67788,193.784861 212.515919,186.973567 212.515919,179.594665 L212.515919,179.594665 C212.515919,172.783371 209.67788,165.404469 204.569409,159.160783 C192.649645,144.402979 177.324234,144.402979 161.431214,152.349489 C155.755136,155.187528 150.646666,159.728391 144.402979,162.56643 C129.645176,169.377724 115.45498,168.810116 102.4,156.890352 C89.3450199,144.402979 84.8041573,130.212784 92.7506669,113.752157 C95.588706,108.076078 99.5619609,102.4 102.4,96.7239217 C111.481725,80.2632946 113.184549,63.8026676 97.8591374,50.7476875 C91.6154512,45.0716092 84.2365495,42.8011779 77.4252555,42.8011779 L77.4252555,42.8011779 C70.6139615,42.8011779 63.2350598,45.639217 56.4237658,50.7476875 C40.5307466,63.2350598 38.8279231,80.8309025 49.6124719,96.1563139 C65.5054911,118.293019 67.2083146,138.159293 50.1800797,160.295999 C39.3955309,174.486194 39.3955309,190.946821 53.0181188,204.001801 C59.8294128,210.813095 67.2083146,213.651135 74.5872164,213.651135 L74.5872164,213.651135 C81.9661181,213.651135 89.9126277,210.813095 97.2915295,206.272233 C106.940863,200.028547 115.45498,192.082037 127.374745,193.217253 L127.374745,193.217253 Z" />';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Loggly icon.
	 *
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_loggly_icon() {
		$source  = '<svg width="256px" height="256px" viewBox="240 0 347.7 80"  version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve">';
		$source .= '<g transform="translate(-590, -70) scale(3.3,3.3)">';
		$source .= '<path style="fill:#F99D1C" d="M302.8,32.3c0.3-0.1,0.5-0.1,0.7-0.2c5.5-1.6,10.8-3.7,16.2-5.9c5.2-2.2,10.3-4.8,14.9-8.5 C339.2,13.9,342.9,9,345,3c0.3-0.8,0.9-2.1,0.9-3c-7.2,9.9-35.9,10.9-35.9,10.9l6.8-6.2c-27.2,0.1-46.2,11.6-54.9,17.8 c11.1,1.2,21.2,5.9,29.1,13C294.9,34.3,298.9,33.4,302.8,32.3z"/>';
		$source .= '<path style="fill:#F99D1C" d="M347.7,31.3c0,0-26.4-2-53.9,6.8c3.6,3.8,6.7,8.2,9.1,12.9C317.3,43.1,337.4,32.8,347.7,31.3z"/>';
		$source .= '<path style="fill:#F99D1C" d="M304.7,55.2c1.7,4.3,2.8,8.9,3.3,13.7L333.2,43L304.7,55.2z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

}
