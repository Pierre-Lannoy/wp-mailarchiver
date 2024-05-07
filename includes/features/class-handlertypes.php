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

use MAMonolog\Logger;
use Mailarchiver\System\Imap;

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
	 * The array of available class names.
	 *
	 * @since  2.5.0
	 * @var    array    $handlers_class    The available class names.
	 */
	private $handlers_class = [];

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->handlers_class = [
			'alerting'   => esc_html__( 'Alerting', 'mailarchiver' ),
			'forwarding' => esc_html__( 'Forwarding', 'mailarchiver' ),
			'logging'    => esc_html__( 'Logging', 'mailarchiver' ),
			'storing'    => esc_html__( 'Archive storing', 'mailarchiver' ),
			'istoring'   => esc_html__( 'Individual storing', 'mailarchiver' ),
			'testing'    => esc_html__( 'Testing & Preview', 'mailarchiver' ),
		];

		// TESTING
		$this->handlers[] = [
			'version'       => MAILARCHIVER_VERSION,
			'id'            => 'LitmusHandler',
			'ancestor'      => 'LitmusHandler',
			'namespace'     => 'Mailarchiver\\Handler',
			'class'         => 'testing',
			'minimal'       => Logger::INFO,
			'name'          => 'Litmus',
			'help'          => esc_html__( 'A mail copy sent to Litmus service.', 'mailarchiver' ),
			'icon'          => $this->get_base64_litmus_icon(),
			'params'        => [ 'processors', 'privacy', 'security' ],
			'configuration' => [
				'box'     => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Mailbox', 'mailarchiver' ),
					'help'    => esc_html__( 'The mailbox to which send the copy.', 'mailarchiver' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'host'     => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Email domain', 'mailarchiver' ),
					'help'    => esc_html__( 'The email domain to which send the copy.', 'mailarchiver' ),
					'default' => '@litmusemail.com',
					'control' => [
						'type'    => 'field_select',
						'cast'    => 'string',
						'enabled' => false,
						'list'    => [ [ '@litmusemail.com', '@litmusemail.com' ] ],
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
					'value' => 'box',
				],
				[ 'type' => 'level' ],
				[
					'type'  => 'literal',
					'value' => true,
				],
			],
		];
		$this->handlers[] = [
			'version'       => MAILARCHIVER_VERSION,
			'id'            => 'EOAHandler',
			'ancestor'      => 'EOAHandler',
			'namespace'     => 'Mailarchiver\\Handler',
			'class'         => 'testing',
			'minimal'       => Logger::INFO,
			'name'          => 'Email on Acid',
			'help'          => esc_html__( 'A mail copy sent to Email on Acid service.', 'mailarchiver' ),
			'icon'          => $this->get_base64_eoa_icon(),
			'params'        => [ 'processors', 'privacy', 'security' ],
			'configuration' => [
				'box'     => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Mailbox', 'mailarchiver' ),
					'help'    => esc_html__( 'The mailbox to which send the copy.', 'mailarchiver' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'host'     => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Email domain', 'mailarchiver' ),
					'help'    => esc_html__( 'The email domain to which send the copy.', 'mailarchiver' ),
					'default' => '@precheck.emailonacid.com',
					'control' => [
						'type'    => 'field_select',
						'cast'    => 'string',
						'enabled' => true,
						'list'    => [ [ '@precheck.emailonacid.com', '@precheck.emailonacid.com' ], [ '@previews.emailonacid.com', '@previews.emailonacid.com' ] ],
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
					'value' => 'box',
				],
				[ 'type' => 'level' ],
				[
					'type'  => 'literal',
					'value' => true,
				],
			],
		];
		$this->handlers[] = [
			'version'       => MAILARCHIVER_VERSION,
			'id'            => 'MailerCheckHandler',
			'ancestor'      => 'MailerCheckHandler',
			'namespace'     => 'Mailarchiver\\Handler',
			'class'         => 'testing',
			'minimal'       => Logger::INFO,
			'name'          => 'MailerCheck',
			'help'          => esc_html__( 'A mail copy sent to MailerCheck service.', 'mailarchiver' ),
			'icon'          => $this->get_base64_mailercheck_icon(),
			'params'        => [ 'processors', 'privacy', 'security' ],
			'configuration' => [
				'box'     => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Seed list', 'mailarchiver' ),
					'help'    => esc_html__( 'The seed list given by the service.', 'mailarchiver' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_textarea',
						'cast'    => 'string',
						'enabled' => true,
						'lines'   => 5,
						'columns' => 200,
					],
				],
			],
			'init'          => [
				[
					'type'  => 'configuration',
					'value' => 'box',
				],
				[ 'type' => 'level' ],
				[
					'type'  => 'literal',
					'value' => true,
				],
			],
		];

		// FORWARDING
		$this->handlers[] = [
			'version'       => MAILARCHIVER_VERSION,
			'id'            => 'MailForwardHandler',
			'ancestor'      => 'MailForwardHandler',
			'namespace'     => 'Mailarchiver\\Handler',
			'class'         => 'forwarding',
			'minimal'       => Logger::INFO,
			'name'          => 'Mail forwarder',
			'help'          => esc_html__( 'A mail copy sent to an email address.', 'mailarchiver' ),
			'icon'          => $this->get_base64_mail_icon(),
			'params'        => [ 'processors', 'privacy', 'security' ],
			'configuration' => [
				'box'     => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Mailbox', 'mailarchiver' ),
					'help'    => esc_html__( 'The email address to which forward the copy.', 'mailarchiver' ),
					'default' => 'name@example.com',
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
					'value' => 'box',
				],
				[ 'type' => 'level' ],
				[
					'type'  => 'literal',
					'value' => true,
				],
			],
		];

		// LOGGING
		$this->handlers[] = [
			'version'       => MAILARCHIVER_VERSION,
			'id'            => 'FluentHandler',
			'ancestor'      => 'SocketHandler',
			'namespace'     => 'Mailarchiver\\Handler',
			'class'         => 'logging',
			'minimal'       => Logger::INFO,
			'name'          => esc_html__( 'Fluentd', 'mailarchiver' ),
			'help'          => esc_html__( 'An archive sent to a Fluentd collector.', 'mailarchiver' ),
			'icon'          => $this->get_base64_fluentd_icon(),
			'params'        => [ 'processors', 'privacy', 'security' ],
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
			'version'       => MAILARCHIVER_VERSION,
			'id'            => 'LogentriesHandler',
			'ancestor'      => 'SocketHandler',
			'namespace'     => 'Mailarchiver\\Handler',
			'class'         => 'logging',
			'minimal'       => Logger::INFO,
			'name'          => esc_html__( 'Logentries & insightOps', 'mailarchiver' ),
			'help'          => esc_html__( 'An archive sent to Logentries & insightOps service.', 'mailarchiver' ),
			'icon'          => $this->get_base64_logentries_icon(),
			'params'        => [ 'processors', 'privacy', 'security' ],
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
			'version'       => MAILARCHIVER_MONOLOG_VERSION,
			'id'            => 'LogglyHandler',
			'ancestor'      => 'LogglyHandler',
			'namespace'     => 'MAMonolog\\Handler',
			'class'         => 'logging',
			'minimal'       => Logger::ERROR,
			'name'          => esc_html__( 'Loggly', 'mailarchiver' ),
			'help'          => esc_html__( 'An archive sent to Solawinds Loggly service.', 'mailarchiver' ),
			'icon'          => $this->get_base64_loggly_icon(),
			'params'        => [ 'processors', 'privacy', 'security' ],
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
			'version'       => MAILARCHIVER_MONOLOG_VERSION,
			'id'            => 'SyslogUdpHandler',
			'ancestor'      => 'UdpSocket',
			'namespace'     => 'MAMonolog\Handler',
			'class'         => 'logging',
			'minimal'       => Logger::INFO,
			'name'          => esc_html__( 'Syslog', 'mailarchiver' ),
			'help'          => esc_html__( 'An archive sent to a remote syslogd server.', 'mailarchiver' ),
			'icon'          => $this->get_base64_syslog_icon(),
			'params'        => [ 'processors', 'privacy', 'security' ],
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
			'version'       => MAILARCHIVER_VERSION,
			'id'            => 'GrafanaHandler',
			'namespace'     => 'Mailarchiver\\Handler',
			'class'         => 'logging',
			'minimal'       => Logger::INFO,
			'name'          => 'Grafana Cloud',
			'help'          => esc_html__( 'An archive sent to Grafana Cloud.', 'mailarchiver' ),
			'icon'          => $this->get_base64_grafana_icon(),
			'needs'         => [],
			'params'        => [ 'processors', 'privacy', 'security' ],
			'configuration' => [
				'host'  => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Loki host', 'mailarchiver' ),
					'help'    => sprintf( esc_html__( 'The host name portion of the Loki instance url. Something like %s.', 'mailarchiver' ), '<code>logs-prod-us-central1</code>' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'user'  => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Username', 'mailarchiver' ),
					'help'    => sprintf( esc_html__( 'The user name for Basic Auth authentication. Something like %s.', 'mailarchiver' ), '<code>21087</code>' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'key'   => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'API key', 'mailarchiver' ),
					'help'    => esc_html__( 'The Grafana.com API Key.', 'mailarchiver' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'model' => [
					'type'    => 'integer',
					'show'    => true,
					'name'    => esc_html__( 'Labels', 'mailarchiver' ),
					'help'    => esc_html__( 'Template for labels. If you are unsure of the implications on cardinality, choose the first one.', 'mailarchiver' ),
					'default' => 0,
					'control' => [
						'type'    => 'field_select',
						'cast'    => 'string',
						'enabled' => true,
						'list'    => [ [ 0, '{job="x", instance="y"} - ' . esc_html__( 'Recommended in most cases', 'mailarchiver' ) ], [ 1, '{job="x", instance="y", level="z"} - ' . esc_html__( 'Classical level segmentation', 'mailarchiver' ) ], [ 2, '{job="x", instance="y", env="z"} - ' . esc_html__( 'Classical environment segmentation', 'mailarchiver' ) ], [ 3, '{job="x", instance="y", version="z"} - ' . esc_html__( 'Classical version segmentation', 'mailarchiver' ) ], [ 4, '{job="x", level="y", env="z"} - ' . esc_html__( 'Double level / environment segmentation', 'mailarchiver' ) ], [ 5, '{job="x", site="y"} - ' . esc_html__( 'WordPress Multisite segmentation', 'mailarchiver' ) ] ],
					],
				],
				'id'    => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Job', 'mailarchiver' ),
					'help'    => esc_html__( 'The fixed job name for some templates.', 'mailarchiver' ),
					'default' => 'wp_mailarchiver',
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
					'value' => 'host',
				],
				[
					'type'  => 'configuration',
					'value' => 'user',
				],
				[
					'type'  => 'configuration',
					'value' => 'key',
				],
				[
					'type'  => 'configuration',
					'value' => 'model',
				],
				[
					'type'  => 'configuration',
					'value' => 'id',
				],
				[ 'type' => 'level' ],
			],
		];
		$this->handlers[] = [
			'version'       => MAILARCHIVER_VERSION,
			'id'            => 'LokiHandler',
			'namespace'     => 'Mailarchiver\\Handler',
			'class'         => 'logging',
			'minimal'       => Logger::INFO,
			'name'          => 'Loki',
			'help'          => esc_html__( 'An archive sent to a Loki instance.', 'mailarchiver' ),
			'icon'          => $this->get_base64_loki_icon(),
			'needs'         => [],
			'params'        => [ 'processors', 'privacy', 'security' ],
			'configuration' => [
				'url'   => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Service URL', 'mailarchiver' ),
					'help'    => sprintf( esc_html__( 'URL where to send archives. Format: %s.', 'mailarchiver' ), '<code>' . htmlentities( '<proto>://<host>:<port>' ) . '</code>' ),
					'default' => 'http://localhost:3100',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'model' => [
					'type'    => 'integer',
					'show'    => true,
					'name'    => esc_html__( 'Labels', 'mailarchiver' ),
					'help'    => esc_html__( 'Template for labels. If you are unsure of the implications on cardinality, choose the first one.', 'mailarchiver' ),
					'default' => 0,
					'control' => [
						'type'    => 'field_select',
						'cast'    => 'string',
						'enabled' => true,
						'list'    => [ [ 0, '{job="x", instance="y"} - ' . esc_html__( 'Recommended in most cases', 'mailarchiver' ) ], [ 1, '{job="x", instance="y", level="z"} - ' . esc_html__( 'Classical level segmentation', 'mailarchiver' ) ], [ 2, '{job="x", instance="y", env="z"} - ' . esc_html__( 'Classical environment segmentation', 'mailarchiver' ) ], [ 3, '{job="x", instance="y", version="z"} - ' . esc_html__( 'Classical version segmentation', 'mailarchiver' ) ], [ 4, '{job="x", level="y", env="z"} - ' . esc_html__( 'Double level / environment segmentation', 'mailarchiver' ) ], [ 5, '{job="x", site="y"} - ' . esc_html__( 'WordPress Multisite segmentation', 'mailarchiver' ) ] ],
					],
				],
				'id'    => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Job', 'mailarchiver' ),
					'help'    => esc_html__( 'The fixed job name for some templates.', 'mailarchiver' ),
					'default' => 'wp_mailarchiver',
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
					'value' => 'url',
				],
				[
					'type'  => 'configuration',
					'value' => 'model',
				],
				[
					'type'  => 'configuration',
					'value' => 'id',
				],
				[ 'type' => 'level' ],
			],
		];

		// ALERTING
		$this->handlers[] = [
			'version'       => MAILARCHIVER_VERSION,
			'id'            => 'PshHandler',
			'ancestor'      => 'SocketHandler',
			'namespace'     => 'Mailarchiver\\Handler',
			'class'         => 'alerting',
			'minimal'       => Logger::ERROR,
			'name'          => esc_html__( 'Pushover', 'mailarchiver' ),
			'help'          => esc_html__( 'Emails in error signaled to Pushover service.', 'mailarchiver' ),
			'icon'          => $this->get_base64_pushover_icon(),
			'params'        => [ 'processors', 'privacy', 'security' ],
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
			'version'       => MAILARCHIVER_MONOLOG_VERSION,
			'id'            => 'SlackWebhookHandler',
			'ancestor'      => 'SlackWebhookHandler',
			'namespace'     => 'MAMonolog\Handler',
			'class'         => 'alerting',
			'minimal'       => Logger::ERROR,
			'name'          => esc_html__( 'Slack', 'mailarchiver' ),
			'help'          => esc_html__( 'Emails in error signaled through Slack Webhooks.', 'mailarchiver' ),
			'icon'          => $this->get_base64_slack_icon(),
			'params'        => [ 'processors', 'privacy', 'security' ],
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

		// INDIVIDUAL STORING
		$this->handlers[] = [
			'version'       => MAILARCHIVER_VERSION,
			'id'            => 'IndividualFileHandler',
			'ancestor'      => 'IndividualFileHandler',
			'namespace'     => 'Mailarchiver\Handler',
			'class'         => 'istoring',
			'minimal'       => Logger::INFO,
			'name'          => esc_html__( 'Local files', 'mailarchiver' ),
			'help'          => esc_html__( 'Each mail stored on your server as an individual file.', 'mailarchiver' ),
			'icon'          => $this->get_base64_files_icon(),
			'params'        => [],
			'configuration' => [
				'format'   => [
					'type'    => 'integer',
					'show'    => true,
					'name'    => esc_html__( 'Format', 'mailarchiver' ),
					'help'    => esc_html__( 'The format in which the file is saved.', 'mailarchiver' ),
					'default' => 100,
					'control' => [
						'type'    => 'field_select',
						'cast'    => 'integer',
						'enabled' => true,
						'list'    => [ [ 100, 'Electronic Mail Format (.eml)' ], [ 200, 'JavaScript Object Notation (.json)' ] ],
					],
				],
				'filename' => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Path', 'mailarchiver' ),
					'help'    => esc_html__( 'The full absolute path, like "/path/to/files/".', 'mailarchiver' ),
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
					'value' => 'filename',
				],
				[
					'type'  => 'configuration',
					'value' => 'format',
				],
				[ 'type' => 'level' ],
				[
					'type'  => 'literal',
					'value' => true,
				],
			],
		];

		// ARCHIVE STORING
		$this->handlers[] = [
			'version'       => MAILARCHIVER_MONOLOG_VERSION,
			'id'            => 'RotatingFileHandler',
			'ancestor'      => 'StreamHandler',
			'namespace'     => 'MAMonolog\Handler',
			'class'         => 'storing',
			'minimal'       => Logger::INFO,
			'name'          => esc_html__( 'Rotating files', 'mailarchiver' ),
			'help'          => esc_html__( 'An archive sent to files that are rotated every day and a limited number of files are kept.', 'mailarchiver' ),
			'icon'          => $this->get_base64_rotatingfiles_icon(),
			'params'        => [ 'processors', 'privacy', 'security' ],
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
			'version'       => MAILARCHIVER_VERSION,
			'id'            => 'WordpressHandler',
			'ancestor'      => 'WordpressHandler',
			'namespace'     => 'Mailarchiver\Handler',
			'class'         => 'storing',
			'minimal'       => Logger::INFO,
			'name'          => esc_html__( 'WordPress archiver', 'mailarchiver' ),
			'help'          => esc_html__( 'An archive stored in your WordPress database and available right in your admin dashboard.', 'mailarchiver' ),
			'icon'          => $this->get_base64_wordpress_icon(),
			'params'        => [ 'processors', 'privacy', 'security' ],
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
		$this->handlers[] = [
			'version'       => MAILARCHIVER_VERSION,
			'id'            => 'ElasticCloudHandler',
			'namespace'     => 'Mailarchiver\\Handler',
			'class'         => 'storing',
			'minimal'       => Logger::INFO,
			'name'          => 'Elastic Cloud',
			'help'          => esc_html__( 'An archive stored in Elastic Cloud.', 'mailarchiver' ),
			'icon'          => $this->get_base64_elasticcloud_icon(),
			'needs'         => [],
			'params'        => [ 'processors', 'privacy', 'security' ],
			'configuration' => [
				'cloudid' => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Cloud ID', 'mailarchiver' ),
					'help'    => esc_html__( 'The generated cloud ID.', 'mailarchiver' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'user'    => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Username', 'mailarchiver' ),
					'help'    => esc_html__( 'The username of the instance.', 'mailarchiver' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'pass'    => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Password', 'mailarchiver' ),
					'help'    => esc_html__( 'The password of the instance.', 'mailarchiver' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'index'   => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Index', 'mailarchiver' ),
					'help'    => esc_html__( 'The index name.', 'mailarchiver' ),
					'default' => 'logs-mailarchiver',
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
					'value' => 'cloudid',
				],
				[
					'type'  => 'configuration',
					'value' => 'user',
				],
				[
					'type'  => 'configuration',
					'value' => 'pass',
				],
				[
					'type'  => 'configuration',
					'value' => 'index',
				],
				[ 'type' => 'level' ],
				[
					'type'  => 'literal',
					'value' => true,
				],
			],
		];
		$this->handlers[] = [
			'version'       => MAILARCHIVER_VERSION,
			'id'            => 'ElasticHandler',
			'namespace'     => 'Mailarchiver\\Handler',
			'class'         => 'storing',
			'minimal'       => Logger::INFO,
			'name'          => 'Elasticsearch',
			'help'          => esc_html__( 'An archive stored in Elasticsearch.', 'mailarchiver' ),
			'icon'          => $this->get_base64_elasticsearch_icon(),
			'needs'         => [],
			'params'        => [ 'processors', 'privacy', 'security' ],
			'configuration' => [
				'url'   => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Service URL', 'mailarchiver' ),
					'help'    => sprintf( esc_html__( 'URL where to send archives. Format: %s.', 'mailarchiver' ), '<code>' . htmlentities( '<proto>://<host>:<port>' ) . '</code>' ),
					'default' => 'http://localhost:9200',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'user'  => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Username', 'mailarchiver' ),
					'help'    => esc_html__( 'The username of the instance.', 'mailarchiver' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'pass'  => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Password', 'mailarchiver' ),
					'help'    => esc_html__( 'The password of the instance.', 'mailarchiver' ),
					'default' => '',
					'control' => [
						'type'    => 'field_input_text',
						'cast'    => 'string',
						'enabled' => true,
					],
				],
				'index' => [
					'type'    => 'string',
					'show'    => true,
					'name'    => esc_html__( 'Index', 'mailarchiver' ),
					'help'    => esc_html__( 'The index name.', 'mailarchiver' ),
					'default' => 'logs-mailarchiver',
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
					'value' => 'url',
				],
				[
					'type'  => 'configuration',
					'value' => 'user',
				],
				[
					'type'  => 'configuration',
					'value' => 'pass',
				],
				[
					'type'  => 'configuration',
					'value' => 'index',
				],
				[ 'type' => 'level' ],
				[
					'type'  => 'literal',
					'value' => true,
				],
			],
		];
		if ( Imap::is_available() ) {
			$this->handlers[] = [
				'version'       => MAILARCHIVER_VERSION,
				'id'            => 'ImapHandler',
				'namespace'     => 'Mailarchiver\\Handler',
				'class'         => 'storing',
				'minimal'       => Logger::INFO,
				'name'          => 'Imap',
				'help'          => esc_html__( 'An archive stored on a remote Imap server.', 'mailarchiver' ),
				'icon'          => $this->get_base64_imap_icon(),
				'needs'         => [],
				'params'        => [ 'processors', 'privacy', 'security' ],
				'configuration' => [
					'url'   => [
						'type'    => 'string',
						'show'    => true,
						'name'    => esc_html__( 'Host', 'mailarchiver' ),
						'help'    => sprintf( esc_html__( 'URL where to send archives. Format: %s.', 'mailarchiver' ), '<code>' . htmlentities( '<host>:<port>' ) . '</code>' ),
						'default' => 'imap.example.org:143',
						'control' => [
							'type'    => 'field_input_text',
							'cast'    => 'string',
							'enabled' => true,
						],
					],
					'index' => [
						'type'    => 'string',
						'show'    => true,
						'name'    => esc_html__( 'Root', 'mailarchiver' ),
						'help'    => esc_html__( 'The mailbox\'s root. Do not change it if you don\'t know what it is.', 'mailarchiver' ) . '<br/>' . sprintf( esc_html__( 'Note: the archives will be stored in %s.', 'mailarchiver' ), '<code>' . Imap::get_mailbox_name( '{root}', '/' ) . '</code>' ),
						'default' => 'INBOX',
						'control' => [
							'type'    => 'field_input_text',
							'cast'    => 'string',
							'enabled' => true,
						],
					],
					'encryption' => [
						'type'    => 'string',
						'show'    => true,
						'name'    => esc_html__( 'Encryption', 'mailarchiver' ),
						'help'    => esc_html__( 'Method to use for session\'s encryption.', 'mailarchiver' ),
						'default' => 'notls',
						'control' => [
							'type'    => 'field_select',
							'cast'    => 'string',
							'enabled' => Imap::is_openssl_available(),
							'list'    => [ [ 'notls', esc_html__( 'None', 'mailarchiver' ) ], [ 'ssl', esc_html__( 'SSL/TLS', 'mailarchiver' ) ], [ 'tls', esc_html__( 'start-TLS', 'mailarchiver' ) ] ],
						],
					],
					'validation' => [
						'type'    => 'string',
						'show'    => true,
						'name'    => esc_html__( 'Certificate', 'mailarchiver' ),
						'help'    => esc_html__( 'Does the server certificate need to be validated.', 'mailarchiver' ) . '<br/>' . esc_html__( 'Note: if the Imap server uses self-signed certificate, choose "no validation".', 'mailarchiver' ),
						'default' => 'validate-cert',
						'control' => [
							'type'    => 'field_select',
							'cast'    => 'string',
							'enabled' => Imap::is_openssl_available(),
							'list'    => [ [ 'novalidate-cert', esc_html__( 'No validation', 'mailarchiver' ) ], [ 'validate-cert', esc_html__( 'Full validation', 'mailarchiver' ) ] ],
						],
					],
					'user'  => [
						'type'    => 'string',
						'show'    => true,
						'name'    => esc_html__( 'Username', 'mailarchiver' ),
						'help'    => esc_html__( 'The username of the mailbox.', 'mailarchiver' ),
						'default' => '',
						'control' => [
							'type'    => 'field_input_text',
							'cast'    => 'string',
							'enabled' => true,
						],
					],
					'pass'  => [
						'type'    => 'string',
						'show'    => true,
						'name'    => esc_html__( 'Password', 'mailarchiver' ),
						'help'    => esc_html__( 'The password of the mailbox.', 'mailarchiver' ),
						'default' => '',
						'control' => [
							'type'    => 'field_input_password',
							'cast'    => 'string',
							'enabled' => true,
						],
					],

				],
				'init'          => [
					[
						'type'  => 'configuration',
						'value' => 'url',
					],
					[
						'type'  => 'configuration',
						'value' => 'index',
					],
					[
						'type'  => 'configuration',
						'value' => 'encryption',
					],
					[
						'type'  => 'configuration',
						'value' => 'validation',
					],
					[
						'type'  => 'configuration',
						'value' => 'user',
					],
					[
						'type'  => 'configuration',
						'value' => 'pass',
					],
					[ 'type' => 'level' ],
					[
						'type'  => 'literal',
						'value' => true,
					],
				],
			];
			if ( Imap::is_openssl_available() ) {
				$this->handlers[] = [
					'version'       => MAILARCHIVER_VERSION,
					'id'            => 'GMailHandler',
					'namespace'     => 'Mailarchiver\\Handler',
					'class'         => 'storing',
					'minimal'       => Logger::INFO,
					'name'          => 'GMail',
					'help'          => esc_html__( 'An archive stored on GMail via Imap.', 'mailarchiver' ),
					'icon'          => $this->get_base64_gmail_icon(),
					'needs'         => [],
					'params'        => [ 'processors', 'privacy', 'security' ],
					'configuration' => [
						'user'  => [
							'type'    => 'string',
							'show'    => true,
							'name'    => esc_html__( 'Username', 'mailarchiver' ),
							'help'    => esc_html__( 'The username of the mailbox.', 'mailarchiver' ),
							'default' => '',
							'control' => [
								'type'    => 'field_input_text',
								'cast'    => 'string',
								'enabled' => true,
							],
						],
						'pass'  => [
							'type'    => 'string',
							'show'    => true,
							'name'    => esc_html__( 'Password', 'mailarchiver' ),
							'help'    => esc_html__( 'The password of the mailbox.', 'mailarchiver' ),
							'default' => '',
							'control' => [
								'type'    => 'field_input_password',
								'cast'    => 'string',
								'enabled' => true,
							],
						],

					],
					'init'          => [
						[
							'type'  => 'configuration',
							'value' => 'user',
						],
						[
							'type'  => 'configuration',
							'value' => 'pass',
						],
						[ 'type' => 'level' ],
						[
							'type'  => 'literal',
							'value' => true,
						],
					],
				];
				$this->handlers[] = [
					'version'       => MAILARCHIVER_VERSION,
					'id'            => 'OVHHandler',
					'namespace'     => 'Mailarchiver\\Handler',
					'class'         => 'storing',
					'minimal'       => Logger::INFO,
					'name'          => 'OVH',
					'help'          => esc_html__( 'An archive stored on OVH via Imap.', 'mailarchiver' ),
					'icon'          => $this->get_base64_ovh_icon(),
					'needs'         => [],
					'params'        => [ 'processors', 'privacy', 'security' ],
					'configuration' => [
						'user'  => [
							'type'    => 'string',
							'show'    => true,
							'name'    => esc_html__( 'Username', 'mailarchiver' ),
							'help'    => esc_html__( 'The username of the mailbox.', 'mailarchiver' ),
							'default' => '',
							'control' => [
								'type'    => 'field_input_text',
								'cast'    => 'string',
								'enabled' => true,
							],
						],
						'pass'  => [
							'type'    => 'string',
							'show'    => true,
							'name'    => esc_html__( 'Password', 'mailarchiver' ),
							'help'    => esc_html__( 'The password of the mailbox.', 'mailarchiver' ),
							'default' => '',
							'control' => [
								'type'    => 'field_input_password',
								'cast'    => 'string',
								'enabled' => true,
							],
						],

					],
					'init'          => [
						[
							'type'  => 'configuration',
							'value' => 'user',
						],
						[
							'type'  => 'configuration',
							'value' => 'pass',
						],
						[ 'type' => 'level' ],
						[
							'type'  => 'literal',
							'value' => true,
						],
					],
				];
				$this->handlers[] = [
					'version'       => MAILARCHIVER_VERSION,
					'id'            => 'GandiHandler',
					'namespace'     => 'Mailarchiver\\Handler',
					'class'         => 'storing',
					'minimal'       => Logger::INFO,
					'name'          => 'Gandi',
					'help'          => esc_html__( 'An archive stored on Gandi Mail via Imap.', 'mailarchiver' ),
					'icon'          => $this->get_base64_gandi_icon(),
					'needs'         => [],
					'params'        => [ 'processors', 'privacy', 'security' ],
					'configuration' => [
						'user'  => [
							'type'    => 'string',
							'show'    => true,
							'name'    => esc_html__( 'Username', 'mailarchiver' ),
							'help'    => esc_html__( 'The username of the mailbox.', 'mailarchiver' ),
							'default' => '',
							'control' => [
								'type'    => 'field_input_text',
								'cast'    => 'string',
								'enabled' => true,
							],
						],
						'pass'  => [
							'type'    => 'string',
							'show'    => true,
							'name'    => esc_html__( 'Password', 'mailarchiver' ),
							'help'    => esc_html__( 'The password of the mailbox.', 'mailarchiver' ),
							'default' => '',
							'control' => [
								'type'    => 'field_input_password',
								'cast'    => 'string',
								'enabled' => true,
							],
						],

					],
					'init'          => [
						[
							'type'  => 'configuration',
							'value' => 'user',
						],
						[
							'type'  => 'configuration',
							'value' => 'pass',
						],
						[ 'type' => 'level' ],
						[
							'type'  => 'literal',
							'value' => true,
						],
					],
				];
				$this->handlers[] = [
					'version'       => MAILARCHIVER_VERSION,
					'id'            => 'HosterraHandler',
					'namespace'     => 'Mailarchiver\\Handler',
					'class'         => 'storing',
					'minimal'       => Logger::INFO,
					'name'          => 'Hosterra Email',
					'help'          => esc_html__( 'An archive stored on Hosterra Email via Imap.', 'mailarchiver' ),
					'icon'          => $this->get_base64_hosterra_icon(),
					'needs'         => [],
					'params'        => [ 'processors', 'privacy', 'security' ],
					'configuration' => [
						'user'  => [
							'type'    => 'string',
							'show'    => true,
							'name'    => esc_html__( 'Username', 'mailarchiver' ),
							'help'    => esc_html__( 'The username of the mailbox.', 'mailarchiver' ),
							'default' => '',
							'control' => [
								'type'    => 'field_input_text',
								'cast'    => 'string',
								'enabled' => true,
							],
						],
						'pass'  => [
							'type'    => 'string',
							'show'    => true,
							'name'    => esc_html__( 'Password', 'mailarchiver' ),
							'help'    => esc_html__( 'The password of the mailbox.', 'mailarchiver' ),
							'default' => '',
							'control' => [
								'type'    => 'field_input_password',
								'cast'    => 'string',
								'enabled' => true,
							],
						],

					],
					'init'          => [
						[
							'type'  => 'configuration',
							'value' => 'user',
						],
						[
							'type'  => 'configuration',
							'value' => 'pass',
						],
						[ 'type' => 'level' ],
						[
							'type'  => 'literal',
							'value' => true,
						],
					],
				];
				$this->handlers[] = [
					'version'       => MAILARCHIVER_VERSION,
					'id'            => 'MicrosoftHandler',
					'namespace'     => 'Mailarchiver\\Handler',
					'class'         => 'storing',
					'minimal'       => Logger::INFO,
					'name'          => 'Microsoft',
					'help'          => esc_html__( 'An archive stored on Outlook.Com via Imap.', 'mailarchiver' ),
					'icon'          => $this->get_base64_microsoft_icon(),
					'needs'         => [],
					'params'        => [ 'processors', 'privacy', 'security' ],
					'configuration' => [
						'user'  => [
							'type'    => 'string',
							'show'    => true,
							'name'    => esc_html__( 'Username', 'mailarchiver' ),
							'help'    => esc_html__( 'The username of the mailbox.', 'mailarchiver' ),
							'default' => '',
							'control' => [
								'type'    => 'field_input_text',
								'cast'    => 'string',
								'enabled' => true,
							],
						],
						'pass'  => [
							'type'    => 'string',
							'show'    => true,
							'name'    => esc_html__( 'Password', 'mailarchiver' ),
							'help'    => esc_html__( 'The password of the mailbox.', 'mailarchiver' ),
							'default' => '',
							'control' => [
								'type'    => 'field_input_password',
								'cast'    => 'string',
								'enabled' => true,
							],
						],

					],
					'init'          => [
						[
							'type'  => 'configuration',
							'value' => 'user',
						],
						[
							'type'  => 'configuration',
							'value' => 'pass',
						],
						[ 'type' => 'level' ],
						[
							'type'  => 'literal',
							'value' => true,
						],
					],
				];
			}
		}

		// SYSTEM
		$this->handlers[] = [
			'version'       => MAILARCHIVER_VERSION,
			'id'            => 'NullHandler',
			'ancestor'      => 'NullHandler',
			'namespace'     => 'MAMonolog\Handler',
			'class'         => 'system',
			'minimal'       => Logger::INFO,
			'name'          => esc_html__( 'Blackhole', 'mailarchiver' ),
			'help'          => esc_html__( 'Any email it can handle will be thrown away.', 'mailarchiver' ),
			'icon'          => $this->get_base64_php_icon(),
			'params'        => [],
			'configuration' => [],
			'init'          => [],
		];

		uasort(
			$this->handlers,
			function ( $a, $b ) {
				return strcmp( strtolower( $a['name'] ), strtolower( $b['name'] ) );
			}
		);

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
	 * Get the name for a specific class.
	 *
	 * @param   string $class  The class of loggers ( 'alerting', 'debugging', 'logging').
	 * @return  string   The name of the class.
	 * @since    2.5.0
	 */
	public function get_class_name( $class ) {
		$result = '-';
		if ( array_key_exists( $class, $this->handlers_class ) ) {
			$result = $this->handlers_class[ $class ];
		}
		return $result;
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
	 * Returns a base64 svg resource for the RAM icon.
	 *
	 * @param string $color Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_ram_icon( $color = '#808080' ) {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd"  fill="none" width="100%" height="100%"  viewBox="0 0 100 100">';
		$source .= '<g transform="translate(13,20) scale(0.61,0.61)">';
		$source .= '<path style="fill:' . $color . '" d="M13.78,13.25V3.84C13.78,1.72,15.5,0,17.62,0s3.84,1.72,3.84,3.84v9.41h9.73V3.84c0-2.12,1.72-3.84,3.84-3.84 c2.12,0,3.84,1.72,3.84,3.84v9.41h9.73V3.84c0-2.12,1.72-3.84,3.84-3.84c2.12,0,3.84,1.72,3.84,3.84v9.41h9.73V3.84 c0-2.12,1.72-3.84,3.84-3.84c2.12,0,3.84,1.72,3.84,3.84v9.41h9.73V3.84c0-2.12,1.72-3.84,3.84-3.84c2.12,0,3.84,1.72,3.84,3.84 v9.41h9.73V3.84c0-2.12,1.72-3.84,3.84-3.84c2.12,0,3.84,1.72,3.84,3.84v9.41h8.6c1.59,0,3.03,0.65,4.07,1.69 c1.04,1.04,1.69,2.48,1.69,4.07v60.66c0,1.57-0.65,3.01-1.69,4.06l0.01,0.01c-1.04,1.04-2.48,1.69-4.07,1.69h-8.6v8.82 c0,2.12-1.72,3.84-3.84,3.84c-2.12,0-3.84-1.72-3.84-3.84v-8.82h-9.73v8.82c0,2.12-1.72,3.84-3.84,3.84 c-2.12,0-3.84-1.72-3.84-3.84v-8.82H73.7v8.82c0,2.12-1.72,3.84-3.84,3.84c-2.12,0-3.84-1.72-3.84-3.84v-8.82h-9.73v8.82 c0,2.12-1.72,3.84-3.84,3.84c-2.12,0-3.84-1.72-3.84-3.84v-8.82h-9.73v8.82c0,2.12-1.72,3.84-3.84,3.84 c-2.12,0-3.84-1.72-3.84-3.84v-8.82h-9.73v8.82c0,2.12-1.72,3.84-3.84,3.84s-3.84-1.72-3.84-3.84v-8.82H5.75 c-1.59,0-3.03-0.65-4.07-1.69C0.65,82.69,0,81.25,0,79.67V19.01c0-1.59,0.65-3.03,1.69-4.07c0.12-0.12,0.25-0.23,0.38-0.33 c1.01-0.84,2.29-1.35,3.69-1.35H13.78L13.78,13.25z M30.76,62.77l-5.2-9.85v9.85h-8.61V35.31h12.8c2.22,0,4.12,0.39,5.7,1.18 c1.58,0.79,2.76,1.86,3.55,3.22c0.79,1.36,1.18,2.89,1.18,4.6c0,1.84-0.51,3.47-1.53,4.89c-1.02,1.42-2.49,2.44-4.4,3.06 l5.97,10.51H30.76L30.76,62.77z M25.56,47.18h3.41c0.83,0,1.45-0.19,1.86-0.56c0.41-0.38,0.62-0.96,0.62-1.77 c0-0.72-0.21-1.29-0.64-1.71c-0.43-0.41-1.04-0.62-1.84-0.62h-3.41V47.18L25.56,47.18z M60.55,58.62h-9.15l-1.36,4.15H41 l10.05-27.46h9.93l10.01,27.46h-9.08L60.55,58.62L60.55,58.62z M58.45,52.15l-2.48-7.64l-2.48,7.64H58.45L58.45,52.15z M105.93,35.31v27.46h-8.57V49.08l-4.23,13.69h-7.37l-4.23-13.69v13.69h-8.61V35.31h10.55l6.05,16.49l5.9-16.49H105.93 L105.93,35.31z M115.2,20.93H7.68v56.81H115.2V20.93L115.2,20.93z"/>';
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
	 * Returns a base64 svg resource for the Bugsnag icon.
	 *
	 * @param string $color Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_bugsnag_icon( $color = '#000D47' ) {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd"  fill="none" width="100%" height="100%"  viewBox="0 0 256 256">';
		$source .= '<g transform="translate(70,44) scale(0.47,0.47)">';
		$source .= '<path style="fill:' . $color . '" d="M65.0423885,2.17395258 C70.4545453,5.19866092 73.8066154,10.9148738 73.8039753,17.1148976 L73.9684716,167.519284 L128,167.519284 C157.528128,167.514849 184.151192,185.29875 195.45419,212.57791 C206.757189,239.857069 200.514033,271.258943 179.636062,292.140051 C158.758091,313.021159 127.357155,319.269032 100.076298,307.970132 C72.7954405,296.671231 55.0075397,270.050839 55.0075394,240.522711 L54.9417409,186.567948 L19.0376971,186.567948 L19.0376971,240.522711 C19.0376971,300.700929 67.8217818,349.485014 128,349.485014 C188.178218,349.485014 236.962303,300.700929 236.962303,240.522711 C236.962303,180.344493 188.178218,131.560408 128,131.560408 L111.484578,131.560408 C106.227464,131.560408 101.96573,127.298675 101.96573,122.04156 C101.96573,116.784445 106.227464,112.522711 111.484578,112.522711 L128,112.522711 C198.692448,112.522711 256,169.830263 256,240.522711 C256,311.215159 198.692448,368.522711 128,368.522711 C57.3401216,368.444143 0.0785681282,311.18259 0,240.522711 L0,177.049099 C0,171.790207 4.25996023,167.525336 9.51884853,167.519284 L54.9198081,167.519284 L54.7662783,20.5693185 L19.0376971,42.5569813 L19.0376971,126.23073 C19.0376971,131.487845 14.7759634,135.749579 9.51884853,135.749579 C4.26173365,135.749579 0,131.487845 0,126.23073 L0,41.4713061 C0.0146388647,35.5314746 3.0957178,30.0203657 8.14804661,26.8969401 L47.7258396,2.54053164 C53.0051433,-0.710507348 59.6302316,-0.85075577 65.0423885,2.17395258 Z M127.945168,186.567948 L73.9904033,186.567948 L73.9904033,240.511745 C73.9859687,262.335407 87.1288121,282.012637 107.289974,290.367265 C127.451136,298.721892 150.65984,294.108459 166.093068,278.678368 C181.526296,263.248276 186.144447,240.040511 177.793918,219.877651 C169.443389,199.714791 149.768831,186.567948 127.945168,186.567948 Z M128,225.257461 C136.430765,225.257461 143.26525,232.091946 143.26525,240.522711 C143.26525,248.953476 136.430765,255.787961 128,255.787961 C119.569235,255.787961 112.73475,248.953476 112.73475,240.522711 C112.73475,232.091946 119.569235,225.257461 128,225.257461 Z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Sentry icon.
	 *
	 * @param string $color Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_sentry_icon( $color = '#362d59' ) {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd"  fill="none" width="100%" height="100%"  viewBox="0 0 256 256">';
		$source .= '<g transform="translate(-8,0) scale(3.8,3.8)">';
		$source .= '<path d="M29,2.26a4.67,4.67,0,0,0-8,0L14.42,13.53A32.21,32.21,0,0,1,32.17,40.19H27.55A27.68,27.68,0,0,0,12.09,17.47L6,28a15.92,15.92,0,0,1,9.23,12.17H4.62A.76.76,0,0,1,4,39.06l2.94-5a10.74,10.74,0,0,0-3.36-1.9l-2.91,5a4.54,4.54,0,0,0,1.69,6.24A4.66,4.66,0,0,0,4.62,44H19.15a19.4,19.4,0,0,0-8-17.31l2.31-4A23.87,23.87,0,0,1,23.76,44H36.07a35.88,35.88,0,0,0-16.41-31.8l4.67-8a.77.77,0,0,1,1.05-.27c.53.29,20.29,34.77,20.66,35.17a.76.76,0,0,1-.68,1.13H40.6q.09,1.91,0,3.81h4.78A4.59,4.59,0,0,0,50,39.43a4.49,4.49,0,0,0-.62-2.28Z" transform="translate(11, 11)" fill="' . $color . '"></path>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Raygun icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @param string $color2 Optional. Color 2 of the icon.
	 * @param string $color3 Optional. Color 3 of the icon.
	 * @param string $color4 Optional. Color 4 of the icon.
	 * @param string $color5 Optional. Color 5 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_raygun_icon( $color1 = '#F4DB12', $color2 = '#DF282B', $color3 = '#D3D2D3', $color4 = '#C02123', $color5 = '#FFFFFF' ) {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd"  fill="none" width="100%" height="100%"  viewBox="0 0 100 100">';
		$source .= '<g transform="translate(6,3) scale(2.2,2.2)">';
		$source .= '<path style="fill:' . $color1 . '" d="M23.0848 8.81499C23.0848 8.81499 15.7216 4.938 6.06982 8.51676L8.55739 11.2008L6.16933 12.4932L9.2539 16.0719L23.0848 8.81499Z"/>';
		$source .= '<path style="fill:' . $color1 . '" d="M29.9502 16.0719V18.6566V20.4459V23.0306C29.9502 23.0306 31.9402 20.4459 35.9203 20.4459V18.6566C31.9402 18.6566 29.9502 16.0719 29.9502 16.0719Z"/>';
		$source .= '<path style="fill:' . $color2 . '" d="M14.0298 28.7964C14.0298 28.7964 18.9054 30.7846 16.5173 36.5504C16.1193 37.5445 18.9054 38.638 21.2935 37.5445C21.393 37.4451 20.0994 36.7493 20.0994 35.7552C20.0994 35.1587 21.2935 30.8841 21.2935 30.8841L14.0298 28.7964Z"/>';
		$source .= '<path style="fill:' . $color3 . '" d="M21.1941 37.4451C21.1941 37.4451 17.214 35.5563 19.9006 30.3869C22.4876 25.5159 23.9802 31.381 23.9802 31.381C23.9802 31.381 19.9006 34.4628 21.1941 37.4451Z"/>';
		$source .= '<path style="fill:' . $color4 . '" d="M37.5125 21.9371C38.8863 21.9371 40 20.8244 40 19.4519C40 18.0793 38.8863 16.9666 37.5125 16.9666C36.1386 16.9666 35.0249 18.0793 35.0249 19.4519C35.0249 20.8244 36.1386 21.9371 37.5125 21.9371Z"/>';
		$source .= '<path style="fill:' . $color2 . '" d="M28.2586 8.11912C27.6616 8.11912 27.0646 8.01971 26.4676 8.01971C16.0198 8.01971 8.55713 12.1949 8.55713 19.9489C8.55713 27.7029 16.0198 31.8781 26.4676 31.8781C27.0646 31.8781 27.6616 31.8781 28.2586 31.7787V8.11912Z" fill="#DF282B"/>';
		$source .= '<path style="fill:' . $color4 . '" d="M8.55713 19.9489C8.55713 27.7029 16.0198 31.8781 26.3681 31.8781C26.9651 31.8781 27.5621 31.8781 28.1591 31.7787V20.0483"/>';
		$source .= '<path style="fill:' . $color2 . '" d="M19.0102 26.0129C22.3074 26.0129 24.9803 23.3424 24.9803 20.0483C24.9803 16.7541 22.3074 14.0837 19.0102 14.0837C15.713 14.0837 13.04 16.7541 13.04 20.0483C13.04 23.3424 15.713 26.0129 19.0102 26.0129Z"/>';
		$source .= '<path style="fill:' . $color1 . '" d="M19.5076 14.2826C22.4927 14.2826 24.9802 16.8672 24.9802 19.9489C24.9802 23.0307 22.4927 25.6153 19.5076 25.6153C16.5225 25.6153 14.035 23.1301 14.035 19.9489C14.035 16.7678 16.5225 14.2826 19.5076 14.2826ZM19.5076 12.2944C15.428 12.2944 12.0449 15.7737 12.0449 19.9489C12.0449 24.2236 15.428 27.6035 19.5076 27.6035C23.5872 27.6035 26.9703 24.2236 26.9703 19.9489C26.9703 15.7737 23.5872 12.2944 19.5076 12.2944Z"/>';
		$source .= '<path style="fill:' . $color5 . '" d="M22.6466 19.4929L20.1579 19.0849L21.1317 13.9843L17.02 20.5131L19.5087 20.9211L18.4267 26.3277L22.6466 19.4929Z"/>';
		$source .= '<path style="fill:' . $color1 . '" d="M13.9353 16.0719C13.9353 16.0719 7.46766 17.1654 4.28358 20.5454L7.46766 21.6389C7.46766 21.6389 3.18906 22.7324 1 25.0188C1 25.0188 8.46269 21.6389 14.9303 25.0188L12.5423 21.8377L12.9403 21.0424L12.3433 20.0483L13.9353 16.0719Z"/>';
		$source .= '<path style="fill:' . $color3 . '" d="M28.2588 31.7787C29.3533 31.6793 30.3483 31.5799 31.3434 31.4805V8.51674C30.3483 8.31792 29.2538 8.21851 28.2588 8.21851V31.7787Z"/>';
		$source .= '<path style="fill:' . $color2 . '" d="M39.9005 18.756C39.602 17.7619 38.607 16.9666 37.5125 16.9666C36.1194 16.9666 35.0249 18.0601 35.0249 19.4519C35.0249 19.7501 35.1244 20.0483 35.2239 20.3466C35.7214 20.7442 36.4179 21.0424 37.1145 21.0424C38.408 21.0424 39.5025 20.0483 39.9005 18.756Z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Raygun icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @param string $color2 Optional. Color 2 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_ganalytics_icon( $color1 = '#F9AB00', $color2 = '#E37400' ) {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd"  fill="none" width="100%" height="100%"  viewBox="0 0 100 100">';
		$source .= '<g transform="translate(15,15) scale(1.4,1.4)">';
		$source .= '<path style="fill:' . $color1 . '" d="M45.3,41.6c0,3.2-2.6,5.9-5.8,5.9c-0.2,0-0.5,0-0.7,0c-3-0.4-5.2-3.1-5.1-6.1V6.6c-0.1-3,2.1-5.6,5.1-6.1c3.2-0.4,6.1,1.9,6.5,5.1c0,0.2,0,0.5,0,0.7V41.6z"/>';
		$source .= '<path style="fill:' . $color2 . '" d="M8.6,35.9c3.2,0,5.8,2.6,5.8,5.8c0,3.2-2.6,5.8-5.8,5.8s-5.8-2.6-5.8-5.8c0,0,0,0,0,0C2.7,38.5,5.4,35.9,8.6,35.9z M23.9,18.2c-3.2,0.2-5.7,2.9-5.7,6.1V40c0,4.2,1.9,6.8,4.6,7.4c3.2,0.6,6.2-1.4,6.9-4.6c0.1-0.4,0.1-0.8,0.1-1.2V24.1c0-3.2-2.6-5.9-5.8-5.9C24,18.2,23.9,18.2,23.9,18.2z"/>';
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
	private function get_base64_stackdriver_icon( $color1 = '#FFFFFF', $color2 = '#4386FA' ) {
		$source  = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd"  fill="none" width="100%" height="100%"  viewBox="0 0 190 190">';
		$source .= '<g transform="translate(20,26) scale(1.2,1.2)">';
		$source .= '<path d="M2.52031008,63.4098189 C0.482133333,59.8145684 0.482133333,55.3850274 2.52031008,51.7897768 L28.5674171,5.84397474 C30.6055938,2.24872421 34.3723659,0.0339536842 38.4487194,0.0339536842 L90.5429333,0.0339536842 C94.6192868,0.0339536842 98.3860589,2.24872421 100.424236,5.84397474 L126.471343,51.7897768 C128.509519,55.3850274 128.509519,59.8145684 126.471343,63.4098189 L100.424236,109.355621 C98.3860589,112.950872 94.6192868,115.165642 90.5429333,115.165642 L38.4487194,115.165642 C34.3723659,115.165743 30.605693,112.950973 28.5674171,109.355722 L2.52031008,63.4098189 Z" id="Shape" fill="' . $color2 . '"></path>';
		$source .= '<path d="M94.2635659,31.3263158 L76.8,39.4105263 L60.5271318,39.4105263 L49.6124031,28.2947368 L42.8956775,39.4105263 L42.6666667,39.4105263 L34.7286822,43.4526316 L42.3193798,51.1831579 L40.6821705,82.8631579 L72.4004713,115.165743 L90.5429333,115.165743 C94.6192868,115.165743 98.3860589,112.950973 100.424236,109.355722 L126.213457,63.8648589 L94.2635659,31.3263158 L94.2635659,31.3263158 L94.2635659,31.3263158 Z" id="Shape" fill="#000000" opacity="0.0800000057"></path>';
		$source .= '<rect id="Rectangle-path" fill="' . $color1 . '" x="57.5503876" y="31.3263158" width="36.7131783" height="11.1157895"></rect>';
		$source .= '<rect id="Rectangle-path" fill="' . $color1 . '" x="42.6666667" y="57.6" width="15.875969" height="3.03157895"></rect>';
		$source .= '<rect id="Rectangle-path" fill="' . $color1 . '" x="57.5503876" y="53.5578947" width="36.7131783" height="11.1157895"></rect>';
		$source .= '<rect id="Rectangle-path" fill="' . $color1 . '" x="42.6666667" y="79.8315789" width="15.875969" height="3.03157895"></rect>';
		$source .= '<rect id="Rectangle-path" fill="' . $color1 . '" x="40.6821705" y="43.4526316" width="2.97674419" height="39.4105263"></rect>';
		$source .= '<g id="Group" transform="translate(34.728682, 28.294737)" fill="' . $color1 . '"><rect id="Rectangle-path" x="0" y="0" width="14.8837209" height="15.1578947"></rect><rect id="Rectangle-path" x="22.8217054" y="47.4947368" width="36.7131783" height="11.1157895"></rect></g>';
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
		$source  = '<svg width="256px" height="256px" viewBox="240 0 347.7 80"  version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve">';
		$source .= '<g transform="translate(-590, -70) scale(3.3,3.3)">';
		$source .= '<path style="fill:#F99D1C" d="M302.8,32.3c0.3-0.1,0.5-0.1,0.7-0.2c5.5-1.6,10.8-3.7,16.2-5.9c5.2-2.2,10.3-4.8,14.9-8.5 C339.2,13.9,342.9,9,345,3c0.3-0.8,0.9-2.1,0.9-3c-7.2,9.9-35.9,10.9-35.9,10.9l6.8-6.2c-27.2,0.1-46.2,11.6-54.9,17.8 c11.1,1.2,21.2,5.9,29.1,13C294.9,34.3,298.9,33.4,302.8,32.3z"/>';
		$source .= '<path style="fill:#F99D1C" d="M347.7,31.3c0,0-26.4-2-53.9,6.8c3.6,3.8,6.7,8.2,9.1,12.9C317.3,43.1,337.4,32.8,347.7,31.3z"/>';
		$source .= '<path style="fill:#F99D1C" d="M304.7,55.2c1.7,4.3,2.8,8.9,3.3,13.7L333.2,43L304.7,55.2z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Sematext icon.
	 *
	 * @param string $color Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_sematext_icon( $color = '#1fa0ed' ) {
		$source  = '<svg width="256px" height="256px" viewBox="0 0 256 256"  version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve">';
		$source .= '<g transform="translate(16, 44) scale(4.6,4.6)">';
		$source .= '<path style="fill:' . $color . '" d="M29.57.15c-1.3 0-2.22.13-2.22.13C18.29.16 17.8 12 17.8 12c-.43 0-.95.05-1.32.31-2.43 1.85-.37 4.12-.37 4.12-.19.15-.35.3-.52.45a3.34 3.34 0 0 1-.34-.73c-2.64-.05-3.7-.74-3.9-1.84-.27-1.32.47-2.8 1.74-2.75 1.84.06.94 1.22.68 1.22a.7.7 0 0 1-.53-.22c-.16-.15-.37-.05-.37.16 0 .53.58 1.27 1.74.69 1.06-.53 1.11-1.37.8-2.06a2.18 2.18 0 0 0-1.7-1.42l-.05-.16c0-.16-.15-.26-.26-.26l-.53-.05c-.15 0-.26.05-.31.2l-.05.27c-.48.05-.95.16-1.32.32l-.27-.21c-.1-.11-.26-.06-.37 0l-.36.31c-.11.1-.16.21-.11.37l.1.21c-.26.27-.57.63-.78 1l-.16-.05c-.16-.05-.27.05-.37.16l-.21.47a.33.33 0 0 0 .05.37l.16.21c-.37 1.43.05 3.17 2.42 4.75.32.2.58.37.85.52l-.1.53a.36.36 0 0 0 .2.42l.58.27a.42.42 0 0 0 .44-.08c-1.01 1.5-1.46 2.8-3.37 4.89a4.63 4.63 0 0 1-3.38 1.68 2.99 2.99 0 0 1-3-3.37 2.82 2.82 0 0 1 2.42-2.59c.16-.05.32-.05.48-.05 1.32 0 1.95 1.11 1.42 2.16 0 0-.42 1.06-1.26 1.11h-.06c-.79 0-.68-.9-.42-1.1.05-.06.16-.06.21-.06.16 0 .27.1.42.1.06 0 .11 0 .16-.05.37-.15.32-.47.32-.47-.05-.42-.37-.58-.8-.58-.26 0-.57.1-.78.21-.8.42-1 1.58-.48 2.32.32.48.85.74 1.48.74 1.1 0 2.48-.8 3-2.37.53-1.53-.63-4.12-3.32-4.12a5.25 5.25 0 0 0-1.9.37c-5.8 2.11-3.1 11.92 2 11.92.27 0 .48 0 .75-.06 1.1-.2 2.1-.73 3.05-1.42l.11.1c.1.11.21.17.32.17.1 0 .2-.06.31-.11l.48-.42c.16-.1.2-.32.15-.53l-.1-.32c.37-.31.68-.68 1-1l.16.1a.47.47 0 0 0 .26.06.48.48 0 0 0 .37-.16l.37-.47c.1-.16.16-.37.05-.53l-.16-.26c.32-.42.64-.8.95-1.16l.11.05c.05.05.16.05.26.05.16 0 .27-.05.37-.2l.37-.48c.1-.16.16-.37.05-.53l-.1-.21c.58-.69 1.05-1.1 1.42-1.1.21 0 .37.15.53.47.1.2.21.47.42.79l-.05.05c-.21.16-.27.42-.1.63l.3.53a.48.48 0 0 0 .43.21h.21c.21.37.42.74.69 1.16-.21.16-.27.42-.1.63l.3.53c.11.16.27.21.43.21h.1l.16-.05c.21.37.48.74.74 1.1l-.05.06a.48.48 0 0 0-.05.63l.36.48c.11.1.22.2.37.2h.16l.1-.05c2.38 3.38 5.28 6.54 7.5 6.54h.1c3.95-.16 3.74-8.33.95-8.33-.16 0-.32 0-.48.05-1.79.53-1.68 2.75-1.68 2.75s.9-1.27 1.42-1.27c.42 0 .68.63.37 2.8-.1.78-.42 1.15-.8 1.15-2 0-6.95-9.64-6.95-11.33 0-.16.1-.21.26-.21.21 0 .48.1.9.26l-.1.37a.53.53 0 0 0 .26.58l.58.21c.05 0 .1.06.15.06a.57.57 0 0 0 .37-.16l.27-.32c.26.16.58.32.9.48l-.11.42a.53.53 0 0 0 .26.58l.58.2c.05 0 .1.06.16.06a.57.57 0 0 0 .37-.16l.26-.26c.37.2.8.42 1.22.68l-.11.37a.53.53 0 0 0 .26.58l.58.21c.06 0 .1.06.16.06a.57.57 0 0 0 .37-.16l.21-.21c.42.2.84.47 1.21.68l-.1.37a.53.53 0 0 0 .26.58l.58.21c.05 0 .1.06.16.06a.57.57 0 0 0 .37-.16l.21-.21c1.58.84 3.22 1.68 4.74 2.37 2 .9 4.06 1.42 5.8 1.42 2.27 0 4.06-.9 4.48-3.21 0-.06 0-.16.06-.21l.2-.06c.22-.05.43-.26.43-.42l.05-.47c.1-.21 0-.32-.16-.37h-.31c0-.58-.1-1.06-.21-1.53l.16-.21c.15-.21.15-.48.05-.58l-.27-.37c-.05-.05-.31-.1-.58-.1h-.05a3.99 3.99 0 0 0-1.1-1L46 21.7c-.05-.21-.21-.42-.42-.48l-.48-.05c-.1 0-.37.21-.52.42a5.82 5.82 0 0 0-1-.1c-1.74 0-3.38.9-3.54 2.58-.05.74-.05 1.63 1.48 2.53.42.26.9.37 1.32.37 1.26 0 2.42-.8 2.1-1.95-.2-.9-.84-1.21-1.47-1.21-.48 0-.9.2-1.1.42-.38.52-.11 1.16.26 1.16s.26-.32.26-.32c-.21-.37.05-.53.42-.53.32 0 .69.16.69.58.05.58-.69.8-.95.85H43c-1.68 0-1.63-1.58-1.31-1.95a2.12 2.12 0 0 1 1.74-.95c1.52 0 2.95 1.63 2 3.58a2 2 0 0 1-2 1.27c-.69 0-1.64-.21-2.9-.8-4.32-1.85-10.38-4.67-11.84-7.25.92.26 2.3.56 2.77.72v.37c0 .16.1.32.26.37l.58.2c.16.06.37 0 .48-.15l.2-.32c.43.11.9.16 1.38.21l.16.48a.4.4 0 0 0 .37.26l.63-.05c.16 0 .32-.16.37-.32l.05-.31a8.2 8.2 0 0 0 1.58-.27l.37.37a.5.5 0 0 0 .48.1l.58-.25a.36.36 0 0 0 .2-.43l-.05-.42c.37-.16.69-.37 1.06-.63 4.37-2.9 2.16-5.33.74-6.49-1.37-1.16.2-2.26 1.9-2.26.47 0 1.31.05 1.84.1.1 0 .16-.15.05-.2a4.6 4.6 0 0 0-3.48-.59l-.1-.1c-.1-.1-.21-.16-.37-.1l-.48.2c-.15.05-.2.21-.2.32v.16c-.27.16-.48.31-.64.52l-.21-.1a.22.22 0 0 0-.18.01c1.02-2.9-.65-6.37-.65-6.37C37.22.83 32.44.17 29.57.15zm9.06 11.99a.3.3 0 0 0 .05.08l.1.1c-.15.37-.2.69-.2 1l-.21.21a.33.33 0 0 0-.06.37l.27.48c.05.1.2.21.37.16l.2-.06c.48.53 1.06.85.9 1.74-.2 1.16-1.47 1.9-4.64 1.85-1.66-.05-3.91-.99-5.07-1.6.63-.24 1.34-.44 2.1-.78.93-.13 2.31-.48 4.48-1.82a4.98 4.98 0 0 0 1.71-1.73zm-13.29 1.24a2 2 0 1 1 0 4 2 2 0 1 1 0-4z"/>';
		$source .= '<path style="fill:' . $color . '" d="M24.13 14.75s-.26 1.47 1.16 1.47c1.43 0 1.16-1.47 1.16-1.47-1.21.74-2.32 0-2.32 0"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Elastic Cloud icon.
	 *
	 * @param string $color1 Optional. Color of the icon.
	 * @param string $color2 Optional. Color of the icon.
	 * @param string $color3 Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_elasticcloud_icon( $color1 = '#00BFB3', $color2 = '#0077CC', $color3 = '#343741' ) {
		$source  = '<svg width="256px" height="256px" viewBox="0 0 256 256"  version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve">';
		$source .= '<g transform="translate(182,-74) scale(0.51,0.51)">';
		$source .= '<path style="fill:' . $color1 . '" d="M-30.5,460.5c-8.7-2.6-18-1.8-26.1,2.3c-19.9,9.7-43.2,9.7-63.2,0c-8.1-4-17.4-4.8-26.1-2.3 c-35.9,11.1-67.8,32.4-91.8,61.2c68.9,82.6,191.7,93.6,274.2,24.8c9-7.5,17.3-15.8,24.8-24.8C37.3,492.9,5.4,471.6-30.5,460.5z"/>';
		$source .= '<path style="fill:' . $color2 . '" d="M-88.2,202.3c-57.8-0.1-112.5,25.6-149.5,70c24,28.8,55.9,50.1,91.8,61.2c8.7,2.6,18,1.8,26.1-2.3 c19.9-9.7,43.2-9.7,63.2,0c8.1,4,17.4,4.8,26.1,2.3c35.9-11.1,67.8-32.4,91.8-61.2C24.3,227.9-30.4,202.2-88.2,202.3z"/>';
		$source .= '<path style="fill:' . $color3 . '" d="M-119.5,331.2L-119.5,331.2c-8.1,4-17.4,4.8-26.1,2.3c-36-11-67.9-32.3-92.1-61.1l0,0c-51.2,61.6-59.5,148.3-20.9,218.4c27.6-30.4,62.7-52.8,101.9-65.1h1.2c-15.1-36,0.7-77.4,36-94.2V331.2z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Elasticsearch icon.
	 *
	 * @param string $color1 Optional. Color of the icon.
	 * @param string $color2 Optional. Color of the icon.
	 * @param string $color3 Optional. Color of the icon.
	 * @param string $color4 Optional. Color of the icon.
	 * @param string $color5 Optional. Color of the icon.
	 * @param string $color6 Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 2.4.0
	 */
	private function get_base64_elasticsearch_icon( $color1 = '#f0bf1a', $color2 = '#3ebeb0', $color3 = '#07a5de', $color4 = '#231f20', $color5 = '#d7a229', $color6 = '#019b8f' ) {
		$source  = '<svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="-4 -2 82 82" width="2500" height="2500">';
		$source .= '<style>.st0{clip-path:url(#SVGID_2_);fill:' . $color1 . '}.st1{clip-path:url(#SVGID_4_);fill:' . $color2 . '}.st2{clip-path:url(#SVGID_6_);fill:' . $color3 . '}.st3{clip-path:url(#SVGID_8_);fill:' . $color4 . '}.st4{fill:' . $color5 . '}.st5{fill:' . $color6 . '}.st6{fill:none}</style>';
		$source .= '<defs><circle id="SVGID_1_" cx="40" cy="40" r="32"/></defs><clipPath id="SVGID_2_"><use xlink:href="#SVGID_1_" overflow="visible"/></clipPath><path class="st0" d="M53.7 26H10c-1.1 0-2-.9-2-2V10c0-1.1.9-2 2-2h57c1.1 0 2 .9 2 2v.7C68.9 19.1 62.1 26 53.7 26z"/><defs><circle id="SVGID_3_" cx="40" cy="40" r="32"/></defs><clipPath id="SVGID_4_"><use xlink:href="#SVGID_3_" overflow="visible"/></clipPath><path class="st1" d="M69.1 72H8.2V54h45.7c8.4 0 15.2 6.8 15.2 15.2V72z"/><g><defs><circle id="SVGID_5_" cx="40" cy="40" r="32"/></defs><clipPath id="SVGID_6_"><use xlink:href="#SVGID_5_" overflow="visible"/></clipPath><path class="st2" d="M50.1 49H4.8V31h45.3c5 0 9 4 9 9s-4.1 9-9 9z"/></g><g><defs><circle id="SVGID_7_" cx="40" cy="40" r="32"/></defs><clipPath id="SVGID_8_"><use xlink:href="#SVGID_7_" overflow="visible"/></clipPath><path class="st3" d="M36 31H6.4v18H36c.7-2.7 1.1-5.7 1.1-9s-.4-6.3-1.1-9z"/></g><path class="st4" d="M23.9 12.3c-5.4 3.2-9.9 8-12.7 13.7h23.6c-2.4-5.5-6.2-10.1-10.9-13.7z"/><path class="st5" d="M24.9 68.2c4.6-3.7 8.3-8.6 10.6-14.2H11.2c3 6 7.8 11 13.7 14.2z"/><path class="st6" d="M0 0h80v80H0z"/>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Sumo icon.
	 *
	 * @param string $color1 Optional. Color of the icon.
	 * @param string $color2 Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 1.0.0
	 */
	private function get_base64_sumosys_icon( $color1 = '#000099', $color2 = '#FEFEFE' ) {
		$source  = '<svg width="256px" height="256px" viewBox="0 0 256 256"  version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve">';
		$source .= '<g id="v1" transform="translate(26,26) scale(0.068,0.068)">';
		$source .= '<g id="modern-app-report-hero-banner-mobile" transform="translate(-20.000000, -19.000000)">';
		$source .= '<g id="Nav">';
		$source .= '<g id="Group" transform="translate(20.000000, 19.000000)">';
		$source .= '<polygon id="Fill-1" fill="' . $color1 . '" points="128.948,2871.053 2871.053,2871.053 2871.053,128.948 128.948,128.948"/>';
		$source .= '<g id="Group-10" transform="translate(4.030534, 4.648885)">';
		$source .= '<path id="Fill-2" fill="' . $color2 . '" d="M1192.155,817.998l-84.861,103.039c-72.772-56.974-118.837-72.72-185.519-72.72 c-52.213,0-89.433,16.594-100.718,43.685v35.072c7.073,14.949,24.342,24.282,49.841,31.543 c16.961,4.88,47.221,12.141,90.897,21.841c135.197,30.019,204.981,63.019,229.511,129.821c0,22.511,0.12,110.788,0.12,131.466 c-29.591,85.168-121.156,138.793-255.13,138.793c-117.56,0-201.196-30.319-299.415-117.62l90.897-101.821 c44.9,36.355,81.264,60.578,111.583,72.719c30.319,13.357,63.019,19.402,100.599,19.402c51.425,0,90.717-15.31,103.466-43.985 v-42.22c-9.152-19.642-31.482-31.235-57.41-38.676c-16.894-3.597-47.281-10.857-90.897-20.618 c-132.262-28.983-194.125-60.646-216.942-120.856V854.602c29.411-77.352,116.832-133.545,247.261-133.545 C1032.074,721.056,1106.078,746.495,1192.155,817.998"/>';
		$source .= '<path id="Fill-4" fill="' . $color2 . '" d="M2332.12,741.614v619.523h-144.342v-65.519c-32.693,53.377-93.338,84.92-181.795,84.92 c-146.656,0-219.504-76.384-219.504-196.444V741.614h157.641v398.864c0,64.243,35.139,103.039,103.098,103.039 c78.705,0,127.262-44.841,127.262-128.478V741.614H2332.12z"/>';
		$source .= '<path id="Fill-6" fill="' . $color2 . '" d="M1565.641,1817.343v432.902h-157.641v-385.561c0-73.943-30.327-118.844-98.219-118.844 c-69.063,0-109.083,51.004-109.083,123.725v380.68h-157.64v-385.561c0-78.824-32.699-118.844-98.158-118.844 c-69.123,0-109.143,51.004-109.143,123.725v380.68H678.178v-619.584h146.723v69.182c36.356-59.42,95.718-89.68,176.975-89.68 c78.764,0,138.186,32.699,172.102,90.777c42.46-60.518,104.322-90.777,185.519-90.777 C1490.421,1610.164,1565.641,1690.083,1565.641,1817.343"/>';
		$source .= '<path id="Fill-8" fill="' . $color2 . '" d="M1909.108,2032.328c24.643,65.398,77.23,103.953,147.023,103.953 c68.934,0,121.275-38.555,145.926-103.953c0-31.236,0-150.809,0-183.748c-24.521-65.279-76.871-105.18-145.926-105.18 c-69.793,0-122.502,39.9-147.023,105.18C1909.108,1879.566,1909.108,2004.019,1909.108,2032.328z M2354.938,2070.394 c-45.756,122.502-157.273,199.252-298.807,199.252c-142.393,0-254.154-76.75-299.912-199.252v-261.105 c45.758-122.621,157.52-199.125,299.912-199.125c141.533,0,253.051,76.504,298.807,199.125V2070.394z"/>';
		$source .= '</g>';
		$source .= '</g>';
		$source .= '</g>';
		$source .= '</g>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Sematext icon.
	 *
	 * @param string $color1 Optional. Color of the icon.
	 * @param string $color2 Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 2.4.0
	 */
	private function get_base64_loki_icon( $color1 = '#F9EC1C', $color2 = '#F05A2B' ) {
		$style = '';
		for ( $i = 1; $i < 16; $i++ ) {
			$style .= ' .st' . $i . '{fill:url(#SVGID_' . $i . '_);}';
		}
		$source  = '<svg width="256px" height="256px" viewBox="0 0 256 256" version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve">';
		$source .= '<style type="text/css">' . $style . '</style>';
		$source .= '<g transform="translate(162,70) scale(0.6,0.6)">';
		$source .= '<linearGradient id="SVGID_1_" gradientUnits="userSpaceOnUse" x1="342.6804" y1="897.3058" x2="342.6804" y2="547.4434" gradientTransform="matrix(0.9805 -0.1964 0.1964 0.9805 -567.5302 -509.0906)"><stop  offset="0" style="stop-color:' . $color1 . '"/><stop  offset="1" style="stop-color:' . $color2 . '"/></linearGradient>';
		$source .= '<polygon class="st1" points="-127.3,-58.2 -127.5,-59.1 -128.3,-58.9"/>';
		$source .= '<linearGradient id="SVGID_2_" gradientUnits="userSpaceOnUse" x1="295.8044" y1="887.3397" x2="295.8044" y2="537.4772" gradientTransform="matrix(0.9805 -0.1964 0.1964 0.9805 -567.5302 -509.0906)"><stop  offset="0" style="stop-color:' . $color1 . '"/><stop  offset="1" style="stop-color:' . $color2 . '"/></linearGradient>';
		$source .= '<polygon class="st2" points="-108.3,219.3 -130.7,223.8 -126.2,246.3 -103.8,241.8"/>';
		$source .= '<linearGradient id="SVGID_3_" gradientUnits="userSpaceOnUse" x1="442.4363" y1="887.3397" x2="442.4363" y2="537.4772" gradientTransform="matrix(0.9805 -0.1964 0.1964 0.9805 -567.5302 -509.0906)"><stop  offset="0" style="stop-color:' . $color1 . '"/><stop  offset="1" style="stop-color:' . $color2 . '"/></linearGradient>';
		$source .= '<polygon class="st3" points="-27.8,190 71.3,170.1 66.8,147.7 -32.3,167.5"/>';
		$source .= '<linearGradient id="SVGID_4_" gradientUnits="userSpaceOnUse" x1="367.5056" y1="887.3397" x2="367.5056" y2="537.4772" gradientTransform="matrix(0.9805 -0.1964 0.1964 0.9805 -567.5302 -509.0906)"><stop  offset="0" style="stop-color:' . $color1 . '"/><stop  offset="1" style="stop-color:' . $color2 . '"/></linearGradient>';
		$source .= '<polygon class="st4" points="-67.5,174.6 -63,197 -40.5,192.5 -45,170.1"/>';
		$source .= '<linearGradient id="SVGID_5_" gradientUnits="userSpaceOnUse" x1="331.6549" y1="887.3397" x2="331.6549" y2="537.4772" gradientTransform="matrix(0.9805 -0.1964 0.1964 0.9805 -567.5302 -509.0906)"><stop  offset="0" style="stop-color:' . $color1 . '"/><stop  offset="1" style="stop-color:' . $color2 . '"/></linearGradient>';
		$source .= '<polygon class="st5" points="-68.6,234.7 -73.1,212.3 -95.6,216.8 -91.1,239.2"/>';
		$source .= '<linearGradient id="SVGID_6_" gradientUnits="userSpaceOnUse" x1="295.8044" y1="887.3397" x2="295.8044" y2="537.4772" gradientTransform="matrix(0.9805 -0.1964 0.1964 0.9805 -567.5302 -509.0906)"><stop  offset="0" style="stop-color:' . $color1 . '"/><stop  offset="1" style="stop-color:' . $color2 . '"/></linearGradient>';
		$source .= '<polygon class="st6" points="-133.3,211.1 -110.8,206.6 -115.3,184.2 -137.8,188.7"/>';
		$source .= '<linearGradient id="SVGID_7_" gradientUnits="userSpaceOnUse" x1="442.4363" y1="887.3397" x2="442.4363" y2="537.4772" gradientTransform="matrix(0.9805 -0.1964 0.1964 0.9805 -567.5302 -509.0906)"><stop  offset="0" style="stop-color:' . $color1 . '"/><stop  offset="1" style="stop-color:' . $color2 . '"/></linearGradient>';
		$source .= '<polygon class="st7" points="73.8,182.8 -25.3,202.7 -20.8,225.1 78.3,205.3"/>';
		$source .= '<linearGradient id="SVGID_8_" gradientUnits="userSpaceOnUse" x1="367.5056" y1="887.3397" x2="367.5056" y2="537.4772" gradientTransform="matrix(0.9805 -0.1964 0.1964 0.9805 -567.5302 -509.0906)"><stop  offset="0" style="stop-color:' . $color1 . '"/><stop  offset="1" style="stop-color:' . $color2 . '"/></linearGradient>';
		$source .= '<polygon class="st8" points="-60.4,209.7 -55.9,232.2 -33.5,227.7 -38,205.2"/>';
		$source .= '<linearGradient id="SVGID_9_" gradientUnits="userSpaceOnUse" x1="331.6549" y1="887.3397" x2="331.6549" y2="537.4772" gradientTransform="matrix(0.9805 -0.1964 0.1964 0.9805 -567.5302 -509.0906)"><stop  offset="0" style="stop-color:' . $color1 . '"/><stop  offset="1" style="stop-color:' . $color2 . '"/></linearGradient>';
		$source .= '<polygon class="st9" points="-98.1,204.1 -75.7,199.6 -80.2,177.1 -102.6,181.6"/>';
		$source .= '<linearGradient id="SVGID_10_" gradientUnits="userSpaceOnUse" x1="289.1909" y1="880.5443" x2="289.1909" y2="548.7296" gradientTransform="matrix(0.9805 -0.1964 0.1964 0.9805 -567.5302 -509.0906)"><stop  offset="0" style="stop-color:' . $color1 . '"/><stop  offset="1" style="stop-color:' . $color2 . '"/></linearGradient>';
		$source .= '<polygon class="st10" points="-140.3,176 -130.8,174.1 -166.8,-5.6 -176.3,-3.7"/>';
		$source .= '<linearGradient id="SVGID_11_" gradientUnits="userSpaceOnUse" x1="302.4872" y1="889.7463" x2="302.4872" y2="533.4922" gradientTransform="matrix(0.9805 -0.1964 0.1964 0.9805 -567.5302 -509.0906)"><stop  offset="0" style="stop-color:' . $color1 . '"/><stop  offset="1" style="stop-color:' . $color2 . '"/></linearGradient>';
		$source .= '<polygon class="st11" points="-127.3,173.4 -117.8,171.5 -156.4,-21.4 -165.9,-19.5"/>';
		$source .= '<linearGradient id="SVGID_12_" gradientUnits="userSpaceOnUse" x1="325.1889" y1="908.8145" x2="325.1889" y2="501.9178" gradientTransform="matrix(0.9805 -0.1964 0.1964 0.9805 -567.5302 -509.0906)"><stop  offset="0" style="stop-color:' . $color1 . '"/><stop  offset="1" style="stop-color:' . $color2 . '"/></linearGradient>';
		$source .= '<polygon class="st12" points="-105,168.9 -95.5,167 -139.7,-53.3 -149.2,-51.4"/>';
		$source .= '<linearGradient id="SVGID_13_" gradientUnits="userSpaceOnUse" x1="338.4852" y1="896.2529" x2="338.4852" y2="522.7181" gradientTransform="matrix(0.9805 -0.1964 0.1964 0.9805 -567.5302 -509.0906)"><stop  offset="0" style="stop-color:' . $color1 . '"/><stop  offset="1" style="stop-color:' . $color2 . '"/></linearGradient>';
		$source .= '<polygon class="st13" points="-92,166.3 -82.5,164.4 -123,-37.9 -132.5,-36"/>';
		$source .= '<linearGradient id="SVGID_14_" gradientUnits="userSpaceOnUse" x1="360.8988" y1="870.7903" x2="360.8988" y2="564.8808" gradientTransform="matrix(0.9805 -0.1964 0.1964 0.9805 -567.5302 -509.0906)"><stop  offset="0" style="stop-color:' . $color1 . '"/><stop  offset="1" style="stop-color:' . $color2 . '"/></linearGradient>';
		$source .= '<polygon class="st14" points="-70,161.9 -60.5,160 -93.7,-5.7 -103.2,-3.8"/>';
		$source .= '<linearGradient id="SVGID_15_" gradientUnits="userSpaceOnUse" x1="374.1951" y1="875.2039" x2="374.1951" y2="557.5726" gradientTransform="matrix(0.9805 -0.1964 0.1964 0.9805 -567.5302 -509.0906)"><stop  offset="0" style="stop-color:' . $color1 . '"/><stop  offset="1" style="stop-color:' . $color2 . '"/></linearGradient>';
		$source .= '<polygon class="st15" points="-57,159.3 -47.5,157.4 -81.9,-14.6 -91.4,-12.7"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Grafana icon.
	 *
	 * @param string $color1 Optional. Color of the icon.
	 * @param string $color2 Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 2.4.0
	 */
	private function get_base64_grafana_icon( $color1 = '#FFF100', $color2 = '#F05A28' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="256px" height="256px" viewBox="0 0 256 256">';
		$source .= '<style type="text/css">.st5{fill:url(#SVGID_5_);}</style>';
		$source .= '<g transform="translate(26,26) scale(2.2,2.2)">';
		$source .= '<linearGradient id="SVGID_5_" gradientUnits="userSpaceOnUse" x1="42.7746" y1="113.8217" x2="42.7746" y2="28.9256"><stop  offset="0" style="stop-color:' . $color1 . '"/><stop  offset="1" style="stop-color:' . $color2 . '"/></linearGradient>';
		$source .= '<path class="st5" d="M85.43,41c-0.14-1.56-0.41-3.36-0.93-5.35c-0.52-1.98-1.29-4.15-2.4-6.41C80.97,27,79.5,24.66,77.6,22.4 c-0.74-0.89-1.55-1.76-2.43-2.62c1.31-5.2-1.59-9.7-1.59-9.7c-5-0.31-8.18,1.55-9.36,2.41c-0.2-0.08-0.39-0.17-0.59-0.25 c-0.85-0.35-1.73-0.67-2.63-0.95c-0.9-0.28-1.82-0.54-2.76-0.77c-0.94-0.22-1.9-0.41-2.87-0.56c-0.17-0.03-0.34-0.05-0.51-0.07 C52.68,2.91,46.41,0,46.41,0c-6.98,4.43-8.31,10.63-8.31,10.63s-0.03,0.14-0.07,0.37c-0.39,0.11-0.77,0.22-1.16,0.34 c-0.54,0.16-1.06,0.36-1.6,0.55c-0.53,0.21-1.06,0.41-1.59,0.64c-1.05,0.45-2.1,0.96-3.12,1.53c-0.99,0.56-1.96,1.17-2.91,1.83 c-0.14-0.06-0.24-0.11-0.24-0.11c-9.67-3.69-18.26,0.75-18.26,0.75c-0.78,10.29,3.86,16.77,4.78,17.94 c-0.23,0.64-0.44,1.28-0.64,1.93c-0.72,2.33-1.25,4.72-1.58,7.2c-0.05,0.35-0.09,0.71-0.13,1.07C2.65,49.08,0,58.13,0,58.13 c7.46,8.58,16.15,9.11,16.15,9.11c0.01-0.01,0.02-0.01,0.02-0.02c1.11,1.97,2.39,3.85,3.82,5.6c0.6,0.73,1.24,1.44,1.89,2.12 c-2.72,7.77,0.38,14.25,0.38,14.25c8.3,0.31,13.76-3.63,14.9-4.54c0.83,0.28,1.66,0.53,2.51,0.75c2.55,0.66,5.16,1.04,7.77,1.16 c0.65,0.03,1.3,0.04,1.96,0.04l0.32,0l0.21-0.01l0.41-0.01l0.41-0.02l0.01,0.01c3.91,5.58,10.79,6.37,10.79,6.37 c4.89-5.16,5.17-10.27,5.17-11.38l0,0c0,0,0-0.04,0-0.07c0-0.09,0-0.16,0-0.16s0,0,0,0c0-0.08-0.01-0.15-0.01-0.24 c1.03-0.72,2.01-1.49,2.93-2.32c1.96-1.77,3.67-3.79,5.09-5.96c0.13-0.2,0.26-0.41,0.39-0.62c5.54,0.32,9.44-3.43,9.44-3.43 c-0.92-5.77-4.21-8.58-4.89-9.12l0,0c0,0-0.03-0.02-0.07-0.05c-0.04-0.03-0.06-0.05-0.06-0.05c0,0,0,0,0,0 c-0.04-0.02-0.08-0.05-0.12-0.08c0.03-0.35,0.06-0.69,0.08-1.04c0.04-0.62,0.06-1.24,0.06-1.86l0-0.46l0-0.23l0-0.12 c0-0.16,0-0.1,0-0.16l-0.02-0.39l-0.03-0.52c-0.01-0.18-0.02-0.34-0.04-0.5c-0.01-0.16-0.03-0.32-0.05-0.48l-0.06-0.48l-0.07-0.48 c-0.09-0.63-0.22-1.26-0.36-1.89c-0.58-2.49-1.55-4.85-2.83-6.97c-1.28-2.12-2.88-4-4.67-5.58c-1.8-1.59-3.81-2.86-5.92-3.81 c-2.11-0.95-4.33-1.56-6.54-1.84c-1.1-0.14-2.21-0.2-3.3-0.19l-0.41,0.01l-0.1,0c-0.03,0-0.15,0-0.14,0l-0.17,0.01l-0.4,0.03 c-0.15,0.01-0.31,0.02-0.45,0.04c-0.56,0.05-1.12,0.13-1.66,0.24c-2.19,0.41-4.26,1.2-6.09,2.29c-1.82,1.09-3.41,2.46-4.7,4 c-1.29,1.55-2.29,3.26-2.98,5.03c-0.69,1.77-1.07,3.6-1.18,5.38c-0.03,0.44-0.04,0.89-0.03,1.32c0,0.11,0,0.22,0.01,0.33l0.01,0.36 c0.02,0.21,0.03,0.43,0.05,0.64c0.09,0.9,0.25,1.76,0.49,2.6c0.48,1.66,1.25,3.17,2.21,4.45c0.95,1.28,2.09,2.34,3.3,3.17 c1.21,0.83,2.5,1.42,3.78,1.79c1.28,0.38,2.55,0.54,3.75,0.54c0.15,0,0.3,0,0.45-0.01c0.08,0,0.16-0.01,0.24-0.01 c0.08,0,0.16-0.01,0.24-0.01c0.13-0.01,0.25-0.03,0.38-0.04c0.03,0,0.07-0.01,0.11-0.01l0.12-0.02c0.08-0.01,0.15-0.02,0.23-0.03 c0.16-0.02,0.29-0.05,0.44-0.08c0.14-0.03,0.28-0.05,0.42-0.09c0.28-0.06,0.54-0.14,0.8-0.23c0.52-0.17,1.01-0.38,1.47-0.61 c0.46-0.24,0.88-0.5,1.27-0.77c0.11-0.08,0.22-0.16,0.33-0.25c0.42-0.33,0.49-0.94,0.15-1.35c-0.29-0.36-0.8-0.45-1.2-0.23 c-0.1,0.05-0.2,0.11-0.3,0.16c-0.35,0.17-0.71,0.32-1.1,0.45c-0.39,0.12-0.79,0.22-1.21,0.3c-0.21,0.03-0.42,0.06-0.64,0.08 c-0.11,0.01-0.22,0.02-0.32,0.02c-0.11,0-0.22,0.01-0.32,0.01c-0.1,0-0.21,0-0.31-0.01c-0.13-0.01-0.26-0.01-0.39-0.02 c0,0-0.07,0-0.01,0l-0.04,0l-0.09-0.01c-0.06-0.01-0.12-0.01-0.17-0.02c-0.12-0.01-0.23-0.03-0.35-0.04 c-0.94-0.13-1.89-0.4-2.8-0.82c-0.92-0.41-1.8-0.99-2.59-1.7c-0.79-0.71-1.48-1.57-2.02-2.54c-0.54-0.97-0.92-2.04-1.1-3.17 c-0.09-0.56-0.13-1.15-0.11-1.72c0.01-0.16,0.01-0.31,0.02-0.47c0,0.04,0-0.02,0-0.03l0-0.06l0.01-0.12 c0.01-0.08,0.01-0.15,0.02-0.23c0.03-0.31,0.08-0.62,0.13-0.93c0.43-2.46,1.66-4.86,3.57-6.68c0.48-0.45,0.99-0.88,1.54-1.25 c0.55-0.38,1.13-0.71,1.73-0.99c0.61-0.28,1.24-0.51,1.89-0.68c0.65-0.17,1.32-0.29,1.99-0.35c0.34-0.03,0.68-0.04,1.02-0.04 c0.09,0,0.16,0,0.23,0l0.28,0.01l0.17,0.01c0.07,0,0,0,0.03,0l0.07,0l0.28,0.02c0.73,0.06,1.46,0.16,2.18,0.33 c1.44,0.32,2.84,0.85,4.15,1.57c2.61,1.45,4.84,3.71,6.2,6.44c0.69,1.36,1.17,2.82,1.41,4.33c0.06,0.38,0.1,0.76,0.13,1.14 l0.02,0.29l0.01,0.29c0.01,0.1,0.01,0.19,0.01,0.29c0,0.1,0.01,0.2,0,0.27l0,0.25l-0.01,0.28c-0.01,0.19-0.02,0.49-0.03,0.68 c-0.03,0.42-0.07,0.83-0.12,1.25c-0.05,0.41-0.12,0.82-0.19,1.23c-0.08,0.41-0.17,0.81-0.27,1.21c-0.2,0.8-0.46,1.6-0.77,2.37 c-0.61,1.55-1.43,3.02-2.41,4.38c-1.97,2.71-4.66,4.92-7.72,6.32c-1.53,0.69-3.15,1.2-4.8,1.47c-0.83,0.14-1.67,0.22-2.51,0.25 l-0.16,0.01l-0.13,0l-0.27,0l-0.41,0l-0.21,0c0.11,0-0.02,0-0.01,0l-0.08,0c-0.45-0.01-0.9-0.03-1.35-0.07 c-1.8-0.13-3.57-0.45-5.29-0.95c-1.72-0.5-3.39-1.17-4.97-2.01c-3.16-1.69-5.98-4-8.19-6.79c-1.11-1.39-2.08-2.88-2.88-4.45 c-0.8-1.57-1.43-3.22-1.9-4.9c-0.46-1.69-0.75-3.41-0.86-5.15l-0.02-0.33l-0.01-0.08l0-0.07l0-0.14l-0.01-0.29l0-0.07l0-0.1l0-0.2 l-0.01-0.4l0-0.08c0,0.01,0,0.01,0-0.03l0-0.16c0-0.21,0.01-0.42,0.01-0.64c0.03-0.86,0.1-1.74,0.22-2.62 c0.11-0.88,0.26-1.77,0.44-2.65c0.18-0.88,0.4-1.75,0.64-2.6c0.49-1.71,1.1-3.37,1.83-4.94c1.45-3.14,3.35-5.91,5.64-8.13 c0.57-0.56,1.16-1.09,1.78-1.58c0.61-0.49,1.25-0.95,1.91-1.38c0.65-0.43,1.33-0.83,2.03-1.19c0.34-0.19,0.7-0.35,1.05-0.52 c0.18-0.08,0.36-0.16,0.54-0.24c0.18-0.08,0.36-0.16,0.54-0.23c0.72-0.31,1.47-0.56,2.22-0.8c0.19-0.06,0.38-0.11,0.57-0.17 c0.19-0.06,0.38-0.1,0.57-0.16c0.38-0.11,0.77-0.2,1.15-0.29c0.19-0.05,0.39-0.09,0.58-0.13c0.19-0.04,0.39-0.08,0.58-0.12 c0.2-0.04,0.39-0.07,0.59-0.11l0.29-0.05l0.29-0.04c0.2-0.03,0.39-0.06,0.59-0.09c0.22-0.04,0.44-0.05,0.66-0.09 c0.18-0.02,0.48-0.06,0.66-0.08c0.14-0.01,0.28-0.03,0.42-0.04l0.28-0.03l0.14-0.01l0.16-0.01c0.22-0.01,0.44-0.03,0.67-0.04 l0.33-0.02c0,0,0.12,0,0.02,0l0.07,0l0.14-0.01c0.19-0.01,0.38-0.02,0.57-0.03c0.75-0.02,1.5-0.02,2.25,0 c1.49,0.06,2.95,0.22,4.37,0.49c2.84,0.53,5.51,1.44,7.93,2.64c2.42,1.18,4.59,2.64,6.47,4.22c0.12,0.1,0.23,0.2,0.35,0.3 c0.11,0.1,0.23,0.2,0.34,0.3c0.23,0.2,0.45,0.41,0.67,0.61c0.22,0.2,0.43,0.41,0.64,0.62c0.21,0.21,0.42,0.42,0.61,0.63 c0.8,0.85,1.54,1.7,2.2,2.56c1.34,1.72,2.41,3.46,3.26,5.1c0.05,0.1,0.11,0.2,0.16,0.31c0.05,0.1,0.1,0.2,0.15,0.31 c0.1,0.2,0.2,0.4,0.29,0.6c0.09,0.2,0.19,0.4,0.27,0.59c0.09,0.2,0.17,0.39,0.25,0.58c0.32,0.77,0.61,1.5,0.84,2.19 c0.39,1.11,0.68,2.12,0.9,3c0.09,0.35,0.42,0.58,0.78,0.55c0.37-0.03,0.66-0.34,0.67-0.71C85.56,43.36,85.54,42.26,85.43,41z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Prometheus icon.
	 *
	 * @param string $color1 Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 3.0.0
	 */
	private function get_base64_prometheus_icon( $color1 = '#DA4E31' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="256px" height="256px" viewBox="0 0 256 256">';
		$source .= '<style type="text/css">.st5{fill:url(#SVGID_5_);}</style>';
		$source .= '<g transform="translate(23,23) scale(0.81,0.81)">';
		$source .= '<path d="M128.001.667C57.311.667 0 57.971 0 128.664c0 70.69 57.311 127.998 128.001 127.998S256 199.354 256 128.664C256 57.97 198.689.667 128.001.667zm0 239.56c-20.112 0-36.419-13.435-36.419-30.004h72.838c0 16.566-16.306 30.004-36.419 30.004zm60.153-39.94H67.842V178.47h120.314v21.816h-.002zm-.432-33.045H68.185c-.398-.458-.804-.91-1.188-1.375-12.315-14.954-15.216-22.76-18.032-30.716-.048-.262 14.933 3.06 25.556 5.45 0 0 5.466 1.265 13.458 2.722-7.673-8.994-12.23-20.428-12.23-32.116 0-25.658 19.68-48.079 12.58-66.201 6.91.562 14.3 14.583 14.8 36.505 7.346-10.152 10.42-28.69 10.42-40.056 0-11.769 7.755-25.44 15.512-25.907-6.915 11.396 1.79 21.165 9.53 45.4 2.902 9.103 2.532 24.423 4.772 34.138.744-20.178 4.213-49.62 17.014-59.784-5.647 12.8.836 28.818 5.27 36.518 7.154 12.424 11.49 21.836 11.49 39.638 0 11.936-4.407 23.173-11.84 31.958 8.452-1.586 14.289-3.016 14.289-3.016l27.45-5.355c.002-.002-3.987 16.401-19.314 32.197z" fill="' . $color1 . '"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the influxDB icon.
	 *
	 * @param string $color1 Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 3.0.0
	 */
	private function get_base64_infuxdb_icon( $color1 = '#22ADF6' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="256px" height="256px" viewBox="0 0 900 900">';
		$source .= '<style type="text/css">.st0{fill:none;}.st1{fill:' . $color1 . ';}</style>';
		$source .= '<g transform="translate(220,200) scale(0.84,0.84)">';
		$source .= '<path class="st1" d="M694.1,394.9l-81-352.7C608.5,22.9,591,3.6,571.7-2L201.5-116.2c-4.6-1.8-10.1-1.8-15.7-1.8 c-15.7,0-32.2,6.4-43.3,15.7l-265.2,246.8c-14.7,12.9-22.1,38.7-17.5,57.1l86.6,377.6c4.6,19.3,22.1,38.7,41.4,44.2l346.2,106.8 c4.6,1.8,10.1,1.8,15.7,1.8c15.7,0,32.2-6.4,43.3-15.7L676.6,453C691.4,439.2,698.7,414.3,694.1,394.9z M240.2-32.4l254.1,78.3 c10.1,2.8,10.1,7.4,0,10.1L360.8,86.4c-10.1,2.8-23.9-1.8-31.3-9.2l-93-100.4C228.2-31.4,230-35.1,240.2-32.4z M398.5,423.5 c2.8,10.1-3.7,15.7-13.8,12.9l-274.4-84.7c-10.1-2.8-12-11.1-4.6-18.4L315.7,138c7.4-7.4,15.7-4.6,18.4,5.5L398.5,423.5z M-53.6,174.8L169.3-32.4c7.4-7.4,19.3-6.4,26.7,0.9L307.4,89.2c7.4,7.4,6.4,19.3-0.9,26.7L83.6,323.1c-7.4,7.4-19.3,6.4-26.7-0.9 L-54.5,201.6C-61.9,193.3-60.9,181.3-53.6,174.8z M0.8,503.6l-58.9-258.8c-2.8-10.1,1.8-12,8.3-4.6l93,100.4 c7.4,7.4,10.1,22.1,7.4,32.2L10,503.6C7.2,513.7,2.6,513.7,0.8,503.6z M326.7,654.6l-291-89.3c-10.1-2.8-15.7-13.8-12.9-23.9 l48.8-156.6c2.8-10.1,13.8-15.7,23.9-12.9l291,89.3c10.1,2.8,15.7,13.8,12.9,23.9l-48.8,156.6C347,651.9,336.9,657.4,326.7,654.6z M584.5,442.8L390.3,623.3c-7.4,7.4-11,4.6-8.3-5.5L422.5,487c2.8-10.1,13.8-20.3,23.9-22.1l133.5-30.4 C590.1,431.8,591.9,436.4,584.5,442.8z M605.7,404.2L445.5,441c-10.1,2.8-20.3-3.7-23-13.8l-68.1-296.5c-2.8-10.1,3.7-20.3,13.8-23 l160.2-36.8c10.1-2.8,20.3,3.7,23,13.8l68.1,296.5C622.3,392.2,615.9,402.3,605.7,404.2z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Tempo icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @param string $color2 Optional. Color 2 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 3.0.0
	 */
	private function get_base64_tempo_icon( $color1 = '#fff100', $color2 = '#f05a28' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="256px" height="256px" viewBox="0 0 256 256">';
		$source .= '<defs>';
		$source .= '<style>.cls-1{fill:url(#linear-gradient);}</style>';
		$source .= '<linearGradient id="linear-gradient" x1="168.55" y1="13.4" x2="27.2" y2="57" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="' . $color1 . '"/><stop offset="1" stop-color="' . $color2 . '"/></linearGradient>';
		$source .= '</defs>';
		$source .= '<g transform="translate(20,50) scale(1.6,1.6)">';
		$source .= '<path class="cls-1" d="M4.66,25H2.37a2.37,2.37,0,0,0,0,4.74H4.66a2.37,2.37,0,1,0,0-4.74ZM48.48,59.57H46.19a2.37,2.37,0,1,0,0,4.74h2.29a2.37,2.37,0,1,0,0-4.74ZM46.56,37a2.37,2.37,0,0,0-2.37-2.37H36.27a2.37,2.37,0,0,0,0,4.74h7.92A2.37,2.37,0,0,0,46.56,37ZM121.73,22.1,119.32,8.56A9.88,9.88,0,0,0,109.07,0H16.24A6.28,6.28,0,0,0,9.9,7.7l2.54,14.4a3.38,3.38,0,0,0,.08.34v0c.3,1.76-.59,2.47-1.39,2.73h0a2.37,2.37,0,0,0,.79,4.6H115.39A6.28,6.28,0,0,0,121.73,22.1ZM90.15,76.42c-1-5.25-4-7.2-7.39-7.2H58.24a2.39,2.39,0,0,0-2.37,2.4A2.37,2.37,0,0,0,58,74h0c.78.14,1.62,1.16,2.15,3.68l2.52,14a9.59,9.59,0,0,0,9.19,7.54l14.63-.07a6.28,6.28,0,0,0,6.44-7.61ZM57.73,64.48H84.34a2.27,2.27,0,0,0,.59-.09c2.46-.52,2.58-2.52,2.26-4.51L83.8,41.27c-.93-4.84-3.74-6.75-7.43-6.75H52a2.37,2.37,0,0,0-.28,4.72h0c.81.15,1.7,1.24,2.22,4l2.57,14.24v0A1.92,1.92,0,0,1,55,59.87h0a2.36,2.36,0,0,0,.79,4.59h1.9Z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Zipkin icon.
	 *
	 * @param string $color1 Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 3.0.0
	 */
	private function get_base64_zipkin_icon( $color1 = '#EC7849' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="256px" height="256px" viewBox="0 0 256 256">';
		$source .= '<g transform="translate(30,230) scale(0.42,-0.42)">';
		$source .= '<path fill="' . $color1 . '" d="M 234.211449 488.107623 C 239.634903 482.710159 401.677949 218.057731 412.854954 197.300455 C 418.997748 185.892389 468.280990 109.385719 466.890808 106.605395 C 465.196477 103.216691 418.127666 82.892278 410.949111 79.026913 C 366.194146 54.928075 318.589380 36.193670 273.841501 12.098648 C 266.575754 8.186333 258.530663 5.703510 250.467055 1.000000 L 249.345979 1.000000 C 249.345979 64.434230 249.345979 126.747384 249.345979 189.060538 C 249.345979 217.880629 248.000525 247.072987 249.906517 276.784753 C 266.869879 243.418568 286.603606 212.116360 303.998445 179.811659 C 306.099312 175.910048 321.306539 150.535207 320.422205 148.589679 C 319.499178 146.559013 280.974769 128.000953 274.570194 124.318386 L 274.570194 92.367713 C 285.622304 97.333230 366.916871 131.416054 368.124019 134.071763 C 369.063292 136.138180 340.039976 180.702500 336.565696 187.154695 C 328.562239 202.018296 239.419598 347.961619 234.211449 354.699552 C 233.114905 354.699552 233.041977 355.491962 231.969297 354.699552 C 230.027907 346.933992 198.612516 300.798969 192.563474 289.565016 C 185.119859 275.741178 103.324846 141.217561 98.561225 134.968610 C 98.973614 134.968610 98.775434 135.034670 99.121763 134.408072 C 108.488844 131.256651 186.186518 95.362249 190.489476 92.367713 C 190.489476 92.780102 190.423416 92.581922 191.050014 92.928251 C 191.050014 103.248175 190.218068 114.824680 192.171091 124.878924 C 184.895875 132.392687 155.962373 140.419258 146.767503 147.860987 C 146.767503 150.418632 183.890379 218.965209 187.686786 222.973094 C 190.570024 229.500653 213.606392 270.798639 217.955844 277.345291 L 218.516382 277.345291 C 218.516382 239.695815 218.516382 203.167414 218.516382 165.237668 C 216.429930 159.943687 217.395306 19.458654 217.395306 1.000000 C 164.380046 26.947092 109.612928 49.313485 58.594856 76.784760 C 51.315327 80.704500 2.696352 101.642303 1.027593 104.979821 C 0.036551 106.961903 26.050578 146.966774 29.278715 152.961870 C 36.003914 165.451540 119.973805 304.150251 123.785440 308.174888 C 125.941644 312.703104 194.105594 423.982925 200.018624 432.614350 C 203.329323 440.317426 227.912459 477.451361 234.211449 488.107623 Z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Jaeger icon.
	 *
	 * @param string $color1 Optional. Color of the icon.
	 * @param string $color2 Optional. Color of the icon.
	 * @param string $color3 Optional. Color of the icon.
	 * @param string $color4 Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 3.0.0
	 */
	private function get_base64_jaeger_icon( $color1 = '#231f20', $color2 = '#67cfe3', $color3 = '#dfcaa3', $color4 = '#648c1a' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="256px" height="256px" viewBox="0 0 256 256">';
		$source .= '<defs>';
		$source .= '<style>.st0{fill:none}.st1{fill:' . $color1 . '}.st2{fill:' . $color2 . '}.st3{fill:' . $color3 . '}</style>';
		$source .= '</defs>';
		$source .= '<g transform="translate(24,20) scale(0.42,0.42)">';
		$source .= '<path d="M274.853 195.985c-23.21 0-42.027 18.817-42.027 42.028 0 23.21 18.816 42.027 42.027 42.027 23.21 0 42.027-18.816 42.027-42.027 0-23.211-18.816-42.028-42.027-42.028zm-5.68 69.289c-7.318 0-13.251-5.933-13.251-13.252 0-7.319 5.933-13.252 13.252-13.252 7.318 0 13.252 5.933 13.252 13.252 0 7.319-5.934 13.252-13.252 13.252zm-1.832 30.332c-6.783-2.773-15.704-4.225-26.515-4.316l-4.746-.04c-2.79-5.183-11.735-8.03-21.926-6.765 10.19-1.265 19.136 1.582 21.925 6.764a7.319 7.319 0 0 1 .896 3.616c-.087 6.223-8.003 12.439-18.768 14.654a40.55 40.55 0 0 1-.804.157c-.243.045-.485.091-.73.132-11.605 1.927-21.962-1.483-24.075-7.684l-3.892 1.315c-2.416.816-23.664 8.403-25.649 23.284-.66 4.949.675 9.488 3.86 13.125 3.833 4.379 10.096 6.993 16.753 6.993 3.602 0 6.946-.796 9.668-2.3 1.699-.939 3.513-2.076 5.434-3.28 6.576-4.12 14.76-9.247 24.674-10.552.887-.117 2.365-.176 4.392-.176 2.209 0 4.861.068 7.669.14 3.241.084 6.914.179 10.477.179 10.495 0 16.8-.816 21.08-2.728 9.065-4.051 13.566-11.942 12.038-21.107-.763-4.57-4.94-8.623-11.761-11.411zm-2.893 26.664c-3.937 1.758-11.067 2.17-18.464 2.17-6.508 0-13.221-.319-18.146-.319-2.132 0-3.929.06-5.229.231-14.387 1.893-25.178 10.601-32.372 14.577-1.815 1.003-4.135 1.5-6.567 1.5-7.208 0-15.39-4.366-14.257-12.859 1.514-11.358 21.345-18.057 21.345-18.057 2.547 3.147 6.879 6.77 17.889 6.77 2.9 0 6.262-.251 10.176-.829 20.692-3.05 21.949-17.753 21.949-17.753 22.338.19 31.376 6.594 32.005 10.37.568 3.408.568 10.223-8.33 14.199z" class="st0"/>';
		$source .= '<path d="M216.328 337.591c-5.387 2.072-10.163 5.023-14.296 7.61l-.186.117c.952 4.416 4.141 14.599 12.995 11.961 4.58-1.364 4.671-6.963 3.681-12.077-.587-3.038-1.556-5.905-2.194-7.61zm23.091-2.301a959.6 959.6 0 0 1-2.474-.062c-2.792-.072-5.427-.14-7.6-.14-2.38 0-3.512.083-4.043.153-1.08.142-2.14.34-3.181.574.375 2.361 1.048 5.996 2.058 9.387 1.19 3.998 2.851 7.656 5.05 8.48 2.31.867 11.159 1.642 11.03-8.48a18.821 18.821 0 0 0-.05-1.174c-.304-4.057-.571-6.829-.79-8.738zm-65.029-37.338c7.566-7.6 12.244-18.078 12.244-29.65 0-23.21-18.816-42.027-42.027-42.027s-42.028 18.817-42.028 42.028 18.816 42.027 42.028 42.027a41.82 41.82 0 0 0 21.27-5.784 42.186 42.186 0 0 0 8.512-6.594zm-33.57-1.631c-7.319 0-13.252-5.933-13.252-13.252 0-7.319 5.933-13.252 13.252-13.252 7.319 0 13.252 5.933 13.252 13.252 0 7.319-5.933 13.252-13.252 13.252z" class="st0"/>';
		$source .= '<path d="M142.819 451.05c-7.194 1.645-28.364 13.155-46.657 16.649-18.292 3.494-34.119 5.344-39.668 7.399s-9.866 6.166 1.028 10.277c10.893 4.11 63.716 5.55 68.443 0 4.727-5.55-3.26-7.955-14.799-7.81-8.221.102-7.213-2.763-7.193-3.084 0 0 .514-3.596 13.154-5.344 12.62-1.744 68.443-6.988 67.416-12.948-1.028-5.96-34.53-6.783-41.724-5.139z" class="st1"/><ellipse cx="106.593" cy="495.035" class="st1" rx="10.945" ry="3.186" transform="rotate(-12.278 106.593 495.035)"/><ellipse cx="81.261" cy="493.596" class="st1" rx="11.51" ry="3.083" transform="rotate(-4.551 81.26 493.596)"/><ellipse cx="61.016" cy="491.078" class="st1" rx="7.708" ry="2.415" transform="rotate(-7.758 61.016 491.078)"/><ellipse cx="46.063" cy="488.766" class="st1" rx="7.451" ry="1.953" transform="rotate(-8.26 46.063 488.766)"/><ellipse cx="42.672" cy="484.553" class="st1" rx="7.451" ry="1.953" transform="rotate(-8.26 42.672 484.552)"/><ellipse cx="41.785" cy="479.788" class="st1" rx="6.477" ry="1.697" transform="rotate(-8.261 41.785 479.788)"/>';
		$source .= '<path d="M252.575 456.805c-12.538.617-40.902-.822-44.807 5.755-3.905 6.577 11.51 9.044 35.764 6.988 24.253-2.055 58.577-4.727 60.016-12.126 1.439-7.4-6.372-5.96-7.194-10.688-.822-4.727 8.633-4.727 10.482-11.716 1.85-6.988-12.743-6.166-25.692-6.166-12.948 0-34.53 1.234-36.585 8.016-2.055 6.783 9.866 6.783 18.293 8.427 8.427 1.645 5.96 4.728 5.96 4.728-.41 4.316-3.7 6.166-16.237 6.782z" class="st1"/><ellipse cx="219.047" cy="474.841" class="st1" rx="10.868" ry="3.545"/><ellipse cx="247.026" cy="474.07" class="st1" rx="7.245" ry="3.237"/><ellipse cx="269.609" cy="471.758" class="st1" rx="7.939" ry="2.621"/><ellipse cx="287.028" cy="468.829" class="st1" rx="5.781" ry="2.775"/><ellipse cx="302.058" cy="465.669" class="st1" rx="5.858" ry="2.698"/>';
		$source .= '<path d="M46.47 389.879c0 3.504 7.645 6.818 21.222 9.767-.967-7.032-.37-14.125.537-19.65-13.913 2.978-21.758 6.332-21.758 9.883zm400.637-9.081a31.8 31.8 0 0 1-.66 1.683 30.59 30.59 0 0 1-7.122 10.25c-6.133 5.808-11.51 9.289-16.312 10.558 26.534-3.73 42.285-8.374 42.285-13.41 0-3.235-6.515-6.308-18.19-9.081z" class="st1"/>';
		$source .= '<path d="M401.72 373.427c-1.687-12.369-6.963-17.917-11.617-22.813-4.334-4.56-8.429-8.865-7.11-16.771 1.218-7.309 7.033-11.806 16.533-12.975-5.77-39.002-14.225-88.207-25.095-130.183-5.428-20.964-14.013-35.525-25.526-45.206-13.176 6.74-29.025 14.15-46.717 21.821a1223.516 1223.516 0 0 1-38.532 15.908c-10.104 10.84-21.144 21.524-27.91 24.569-4.817 2.167-9.098 3.03-12.818 3.03-8.055 0-13.493-4.04-16.126-7.634-3.052.807-5.49 1.33-7.147 1.505-1.983.208-4.906.456-8.56.72 1.404 3.677.845 7.982-1.848 11.226-2.962 3.569-7.735 5.361-14.5 5.361-9.734 0-23.592-3.712-42.122-11.197-2.726-1.1-5.23-2.08-7.528-2.951-1.42.007-2.838.012-4.256.012h-.013c-17.703 0-31.538-.557-42.384-1.445-11.879 24.59-6.717 51.863-2.369 74.393 4.167 21.591 18.623 60.008 32.032 93.016a2069.253 2069.253 0 0 0 13.91 33.364c36.3 3.211 82.84 5.14 133.585 5.14 60.39 0 114.785-2.736 153.09-7.108-2.79-11.486-6.326-27.467-6.934-31.532l-.038-.25zM102.58 268.303c0-23.211 18.815-42.028 42.027-42.028s42.027 18.817 42.027 42.028c0 11.571-4.678 22.05-12.245 29.65a42.186 42.186 0 0 1-8.512 6.593 41.82 41.82 0 0 1-21.27 5.784c-23.212 0-42.028-18.816-42.028-42.027zm112.261 88.976c-8.854 2.638-12.043-7.545-12.995-11.96.06-.04.125-.08.186-.117 4.133-2.588 8.909-5.54 14.296-7.61.638 1.705 1.607 4.572 2.194 7.61.99 5.114.899 10.713-3.68 12.077zm14.388-3.597c-2.199-.824-3.86-4.482-5.05-8.48-1.01-3.39-1.683-7.026-2.058-9.387 1.041-.235 2.1-.432 3.18-.574.532-.07 1.664-.153 4.043-.153 2.174 0 4.81.068 7.6.14.801.02 1.63.042 2.475.062.219 1.91.486 4.681.79 8.738.03.408.045.796.05 1.174.129 10.122-8.72 9.347-11.03 8.48zm37.834-25.558c-4.28 1.912-10.584 2.728-21.08 2.728-3.562 0-7.235-.095-10.476-.178-2.808-.073-5.46-.14-7.669-.14-2.027 0-3.505.058-4.392.175-9.914 1.305-18.098 6.432-24.674 10.553-1.92 1.203-3.735 2.34-5.434 3.279-2.722 1.504-6.066 2.3-9.668 2.3-6.657 0-12.92-2.614-16.753-6.993-3.185-3.637-4.52-8.176-3.86-13.125 1.985-14.88 23.233-22.468 25.65-23.284l3.89-1.315a7.292 7.292 0 0 1-.295-1.16c-1.15-6.927 7.896-14.2 20.205-16.244a41.82 41.82 0 0 1 1.647-.235c10.19-1.265 19.137 1.582 21.926 6.764l4.746.04c10.81.092 19.732 1.544 26.515 4.317 6.822 2.788 10.998 6.84 11.76 11.411 1.529 9.165-2.972 17.056-12.038 21.107zm7.79-48.084c-23.21 0-42.027-18.816-42.027-42.027 0-23.211 18.816-42.028 42.027-42.028 23.21 0 42.027 18.817 42.027 42.028 0 23.21-18.816 42.027-42.027 42.027z" class="st2"/>';
		$source .= '<path d="M328.68 96.41l-.314-.548-.415-.526c-.39-.626-1.93-1.593-2.05-1.603l-.157-.084-.08-.042-.3-.13-.026-.01-.053-.02-.427-.15-.859-.294c-.565-.21-1.21-.283-1.82-.41-.62-.11-1.277-.106-1.917-.147-.645.023-1.301.067-1.942.159-2.582.455-4.86 1.705-6.595 3.192-1.762 1.484-3.083 3.184-4.155 4.87-2.13 3.39-3.327 6.77-4.119 9.685a40.4 40.4 0 0 0-1.242 7.155c-.09.867-.083 1.565-.102 2.031-.007.47.007.723.007.723l1.316-2.39c.836-1.504 2.008-3.639 3.453-6.096 1.456-2.44 3.194-5.238 5.302-7.774 1.056-1.257 2.215-2.429 3.438-3.335 1.233-.895 2.494-1.509 3.75-1.71.317-.04.632-.07.952-.097.325.035.644-.002.976.058.332.081.662.077 1.004.188l.806.218.255.116c-.012-.01-.025-.019-.034-.031a.128.128 0 0 0 .043.008c.007.015.018.027.028.04l.034.016.073.035-.035-.004a.248.248 0 0 1-.049-.016c.04.025.076.085.11.165.174.31.301.924.363 1.603.08 1.402-.09 3.02-.353 4.552a67.319 67.319 0 0 1-.906 4.422c-.656 2.776-1.282 5.138-1.695 6.812-.43 1.668-.657 2.652-.657 2.652s.72-.71 1.804-2.081a42.869 42.869 0 0 0 4.026-6.03 36.571 36.571 0 0 0 2.1-4.476c.631-1.675 1.211-3.521 1.392-5.76.07-1.135.037-2.396-.448-3.842a5.392 5.392 0 0 0-.482-1.094z"/>';
		$source .= '<path d="M323.403 99.457l-.037-.018c.015.014.026.033.045.043l.015.005-.008-.009c-.006-.006-.01-.014-.015-.021z"/>';
		$source .= '<path d="M68.017 176.726h.577a97.188 97.188 0 0 1-.577-1.04v1.04zm5.148-16.286a33.069 33.069 0 0 0 3.542 5.618c.474.64.911 1.103 1.188 1.428.286.32.452.481.452.481s-.168-.908-.484-2.443c-.298-1.539-.754-3.712-1.168-6.241-.204-1.262-.391-2.618-.484-3.99-.075-1.35-.075-2.793.221-3.854.148-.526.342-.934.53-1.14.182-.186.33-.247.595-.341l.197-.04.32-.065c.092-.014-.025.052-.034.06a.213.213 0 0 1-.108.026.185.185 0 0 1-.052-.009l.04.023c.057.025.11.029.175.036.31.116.784.367 1.281.744 1.036.75 2.108 1.916 3.135 3.098a79.387 79.387 0 0 1 2.951 3.757c1.878 2.554 3.615 5.051 5.141 7.195a179.068 179.068 0 0 0 3.808 5.195 57.627 57.627 0 0 0 1.542 1.965l-.074-.656c-.067-.42-.133-1.054-.297-1.828a48.738 48.738 0 0 0-1.643-6.35 53.163 53.163 0 0 0-3.646-8.567c-.824-1.517-1.749-3.07-2.88-4.583-1.13-1.524-2.415-3.022-4.198-4.394-.925-.66-1.991-1.298-3.36-1.71a14.285 14.285 0 0 0-1.116-.214l-.282-.038-.38-.005c-.254.001-.51.013-.763.034-.225.02-.564.057-.685.088l-.315.085-.315.086-.417.122-.122.054-.245.11c-1.295.531-2.563 1.597-3.283 2.834-.738 1.228-1.026 2.466-1.128 3.565-.21 2.216.224 3.994.662 5.613a27.17 27.17 0 0 0 1.699 4.25z" class="st1"/>';
		$source .= '<path d="M92.545 418.614c9.853 0 16.79-5.363 21.66-12.324-5.572-12.92-14.741-34.693-23.434-57.607-2.354 2.002-5.073 5.025-8.015 9.585-7.324 11.352-13.356 37.765-5.759 51.706 3.167 5.815 8.254 8.64 15.548 8.64zm347.847-65.447c-5.776-14.944-18.059-25.552-29.869-25.799l-.423-.036c-.018-.004-2.288-.342-5.337-.342-6.433 0-14.329 1.373-15.419 7.911-.756 4.537 1.228 6.861 5.425 11.276 4.797 5.044 11.364 11.952 13.331 26.38 2.253 16.523 6.288 24.903 11.99 24.904 2.663 0 7.519-1.596 15.546-9.2 9.886-9.366 8.939-24.269 4.756-35.094z" class="st3"/><circle cx="140.82" cy="283.069" r="13.252" class="st1"/><circle cx="269.174" cy="252.022" r="13.252" class="st1"/>';
		$source .= '<path fill="' . $color4 . '" d="M104.872 135.026c10.341 2.791 22.033 4.206 34.752 4.206 20.436 0 63.826-3.97 124.812-30.561l1.373-.599c-.191-3.097.06-6.493.84-10.048 1.354-6.15 4.033-11.363 7.322-14.816-.635-2.359-1.157-4.082-1.52-4.957-7.038-3.752-65.056-25.363-95.313-25.363-3.023 0-5.663.225-7.846.67-26.817 5.474-67.563 52.092-69.026 58.701-.44 2.86-.306 11.836.015 21.528l4.591 1.24zm-.522 66.202c-1.466-.306-2.287-.4-2.624-.428-5.591.345-10.717-3.565-11.75-9.236a11.274 11.274 0 0 1-.164-1.717c-3.042 1.726-6.086 3.592-8.935 5.564l-5.066 3.508-3.728-4.907c-1.123-1.48-6.309-8.476-11.088-17.68-8.976 4.235-15.988 8.864-17.7 13.25-.324.832-.193 1.28.143 1.772 1.465 2.145 10.155 8.701 60.911 9.874zm105.388-14.372c4.457-2.837 10.15-2.15 13.813 1.336.565-.089 1.524-.33 2.946-.97 5.557-2.64 25.598-24.037 40.824-42.36a11.102 11.102 0 0 1 1.811-1.731c-34.795 15.035-83.414 31.467-129.707 31.467-7.44 0-14.505-.434-21.063-1.283l1.016 2.993-5.789 2.217c-.042.016-1.037.4-2.702 1.092 7.452 1.741 17.525 5.163 30.177 10.273 23.158 9.352 32.014 9.71 34.23 9.577 3.15-1.668 6.954-1.733 10.211-.142 6.007-.385 10.68-.758 13.475-1.052 1.271-.134 3.16-.53 5.563-1.15a11.265 11.265 0 0 1 5.195-10.267zm134.793-68.77c-1.686 10.05-4.927 21.082-7.076 24.092l-2.017 2.82c9.132-4.446 17.278-8.663 24.193-12.54 24.043-13.481 27.032-19.384 27.376-20.873.225-.975-.014-1.276-.117-1.404-.261-.329-1.95-1.967-9.544-1.967-8.298 0-19.916 1.898-32.04 4.529a117.98 117.98 0 0 1-.775 5.343zm-9.374 27.049l-3.467-.282c-.063-.005-6.429-.516-15.061-.516-8.968 0-20.793.542-30.496 2.839 1.853 3.851 1.433 8.59-1.477 12.091a631.95 631.95 0 0 1-11.206 13.104 1201.72 1201.72 0 0 0 26.176-10.979c12.961-5.62 24.91-11.092 35.53-16.257z"/>';
		$source .= '<path d="M267.01 114.574c-24.37 10.625-77.765 31.098-127.386 31.098-12.563 0-24.883-1.312-36.43-4.429a103.396 103.396 0 0 1 3.58 5.843c4.291 7.539 7.43 14.986 9.14 19.381 6 .938 13.83 1.691 23.511 1.691 28.503 0 73.052-6.524 134.181-34.037.96-3.015 2.238-6.79 3.822-10.985-.124.006-.247.027-.371.027-.65 0-1.3-.07-1.93-.208-3.794-.834-6.602-3.928-8.117-8.381z" class="st3"/>';
		$source .= '<path d="M68.594 176.726c4.402 7.838 8.618 13.39 8.618 13.39 14.766-10.222 34.076-17.605 34.076-17.605s-.496-1.457-1.426-3.837a145.917 145.917 0 0 0-1.76-4.311 161.363 161.363 0 0 0-2.005-4.504 150.289 150.289 0 0 0-1.055-2.222 135.584 135.584 0 0 0-2.482-4.863c-1.49-2.772-3.144-5.594-4.946-8.295-5.927-8.887-13.44-16.448-21.853-16.446-2.652 0-5.396.753-8.204 2.45-15.458 9.347-7.48 30.75.46 45.204.192.35.385.697.577 1.04zm2.21-26.15c.102-1.099.39-2.337 1.128-3.565.72-1.237 1.988-2.303 3.283-2.834l.245-.11.122-.054.417-.122.315-.086.315-.085c.12-.03.46-.068.685-.088.254-.021.51-.033.763-.034l.38.005.282.038c.373.058.753.128 1.116.214 1.369.412 2.435 1.05 3.36 1.71 1.783 1.372 3.067 2.87 4.197 4.394 1.132 1.513 2.057 3.066 2.881 4.583a53.163 53.163 0 0 1 3.646 8.566 48.738 48.738 0 0 1 1.643 6.35c.164.775.23 1.408.297 1.829l.074.656s-.592-.71-1.542-1.965a179.068 179.068 0 0 1-3.808-5.195c-1.526-2.144-3.263-4.64-5.141-7.195a79.387 79.387 0 0 0-2.951-3.757c-1.027-1.182-2.1-2.347-3.135-3.098-.497-.377-.97-.628-1.28-.744-.066-.007-.119-.01-.175-.036l-.04-.023a.185.185 0 0 0 .051.01.213.213 0 0 0 .108-.028c.009-.007.126-.073.034-.059l-.32.065-.197.04c-.264.094-.413.155-.595.34-.188.207-.382.615-.53 1.141-.296 1.06-.296 2.505-.22 3.853.092 1.373.279 2.729.483 3.99.414 2.53.87 4.703 1.168 6.242.316 1.535.484 2.443.484 2.443s-.166-.16-.452-.48c-.277-.326-.714-.79-1.188-1.429a33.069 33.069 0 0 1-3.542-5.618 27.216 27.216 0 0 1-1.699-4.251c-.438-1.62-.873-3.397-.662-5.613zm4.96-25.763v3.22z" class="st2"/>';
		$source .= '<path d="M282.872 78.738a9 9 0 0 1 1.93.208c4.725 1.04 7.918 5.588 8.992 11.897 9.009-12.652 18.833-19.05 29.334-19.05 4.426 0 8.311 1.044 11.57 3.059 5.54-6.738 9.065-11.385 9.065-11.385-4.165.757-12.874 1.893-12.874 1.893 32.94-12.116 30.29-45.056 30.29-45.056 0 5.68-17.038 19.31-17.038 19.31.758-4.922-1.893-11.359-1.893-11.359.758 6.437-19.688 23.096-19.688 23.096 1.514-3.029.378-8.33.378-8.33-1.136 6.058-10.222 8.33-10.222 8.33 5.679-6.815 16.28-42.784 8.33-45.056-7.952-2.272-18.932 23.475-18.932 23.475-.379-6.058-3.786-6.058-3.786-6.058 1.514 18.93-13.63 34.076-13.63 34.076-1.136-3.786-7.195-5.68-7.195-5.68 1.945 4.214-.27 23.395-.95 28.836 2.01-1.42 4.158-2.206 6.319-2.206z" class="st3"/>';
		$source .= '<path d="M274.777 115.57l.026.025c.162.177.33.336.503.473.063.05.13.083.194.128.125.087.25.176.38.241.205.103.414.181.629.228h.001a2.998 2.998 0 0 0 1.057.02c.992-.153 2.083-.77 3.175-1.787a173.277 173.277 0 0 1 3.22-7.09 144.189 144.189 0 0 1 3.213-6.276c1.617-8.56-.56-15.593-3.757-16.296a2.48 2.48 0 0 0-.546-.059c-3.286 0-8.061 5.718-9.933 14.23-1.357 6.178-.73 11.668.871 14.736l.006.012c.29.554.613 1.03.96 1.415z"/>';
		$source .= '<path d="M323.375 99.416a.128.128 0 0 1-.043-.008c.009.012.022.02.034.031l.037.018c-.01-.014-.02-.026-.028-.041zm.135.091l-.073-.035-.034-.015c.005.007.009.015.015.021l.008.009a.248.248 0 0 0 .05.016l.034.004z" class="st2"/>';
		$source .= '<path d="M323.128 78.234c-18.752 0-32.548 28.226-39.758 47.402a215.436 215.436 0 0 0-3.516 10.104 189.136 189.136 0 0 0-1.614 5.282c.906.184 1.792.492 2.645.904 10.926-3.27 25.066-4.028 35.744-4.028 9.036 0 15.586.537 15.586.537 2.84-3.976 19.31-60.201-9.087-60.201zm6.482 23.111c-.18 2.24-.76 4.086-1.393 5.76a36.571 36.571 0 0 1-2.099 4.477 42.869 42.869 0 0 1-4.026 6.03c-1.085 1.37-1.804 2.08-1.804 2.08s.228-.983.657-2.651c.413-1.674 1.04-4.036 1.695-6.812.325-1.386.652-2.88.906-4.422.263-1.532.432-3.15.353-4.552-.062-.679-.19-1.293-.363-1.603-.034-.08-.07-.14-.11-.165l-.015-.005c-.02-.01-.03-.029-.045-.043l-.255-.116-.806-.218c-.342-.111-.672-.107-1.004-.188-.332-.06-.651-.023-.976-.058-.32.028-.635.057-.952.096-1.256.202-2.517.816-3.75 1.711-1.223.906-2.382 2.078-3.438 3.335-2.108 2.536-3.846 5.333-5.302 7.774-1.445 2.457-2.617 4.592-3.453 6.096l-1.316 2.39s-.014-.253-.007-.723c.019-.466.013-1.164.102-2.031a40.4 40.4 0 0 1 1.242-7.155c.792-2.916 1.99-6.296 4.119-9.686 1.072-1.685 2.393-3.385 4.155-4.869 1.735-1.487 4.013-2.737 6.595-3.192a18.468 18.468 0 0 1 1.942-.159c.64.04 1.297.038 1.916.147.61.127 1.256.2 1.821.41l.86.294.426.15.053.02.026.01.3.13.08.042.158.084c.12.01 1.659.977 2.05 1.603l.414.526.314.548c.22.373.366.736.482 1.094.485 1.446.518 2.707.448 3.841z" class="st2"/>';
		$source .= '<path d="M222.928 203.992c3.066 0 6.438-.817 10.022-2.43 7.47-3.362 25.709-21.674 46.465-46.65a4.422 4.422 0 0 0 1.009-3.257 4.424 4.424 0 0 0-1.588-3.016 4.397 4.397 0 0 0-2.84-1.029 4.449 4.449 0 0 0-3.434 1.608c-9.036 10.874-34.4 40.007-43.138 44.16l-.13.06c-1.7.765-3.277 1.265-4.685 1.486l-3.32.522-2.435-2.317a4.501 4.501 0 0 0-3.078-1.221 4.43 4.43 0 0 0-2.38.699 4.423 4.423 0 0 0-1.957 2.79 4.428 4.428 0 0 0 .592 3.358c.34.536 3.544 5.237 10.897 5.237zm-42.348.98c-.731 0-1.455.18-2.094.518l-1.308.693-1.479.087c-.285.017-.637.03-1.065.03-4.158 0-14.381-1.31-36.122-10.09-23.975-9.683-33.472-11.13-37-11.13-.67 0-1.065.055-1.245.087a4.427 4.427 0 0 0-2.865 1.847 4.42 4.42 0 0 0-.72 3.329c.38 2.088 2.254 3.663 4.358 3.663.088 0 .176-.003.266-.008l.493-.031.491.04c2.644.22 11.14 1.68 32.887 10.462 17.58 7.1 30.894 10.701 39.57 10.701 4.545 0 7.659-.975 9.256-2.9a4.422 4.422 0 0 0 1.007-3.255 4.424 4.424 0 0 0-1.588-3.015 4.406 4.406 0 0 0-2.842-1.028z"/>';
		$source .= '<path d="M240.772 297.701s-1.257 14.703-21.949 17.753c-3.914.578-7.277.829-10.176.829-11.01 0-15.342-3.623-17.889-6.77 0 0-19.83 6.699-21.345 18.057-1.133 8.493 7.05 12.859 14.257 12.859 2.432 0 4.752-.497 6.567-1.5 7.194-3.976 17.985-12.684 32.372-14.577 1.3-.17 3.097-.23 5.23-.23 4.924 0 11.637.318 18.145.318 7.397 0 14.527-.412 18.464-2.17 8.897-3.976 8.897-10.791 8.33-14.199-.63-3.776-9.668-10.18-32.006-10.37z" class="st3"/>';
		$source .= '<path d="M216.672 309.808c.246-.04.488-.087.73-.132.27-.05.539-.102.805-.157 10.765-2.215 18.681-8.431 18.768-14.654a7.319 7.319 0 0 0-.896-3.616c-2.789-5.182-11.734-8.03-21.925-6.764a42.09 42.09 0 0 0-1.647.235c-12.31 2.044-21.355 9.317-20.205 16.245.066.397.167.783.296 1.16 2.113 6.2 12.47 9.61 24.074 7.683z" class="st1"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Zipkin icon.
	 *
	 * @param string $color1 Optional. Color of the icon.
	 * @return string The svg resource as a base64.
	 * @since 3.0.0
	 */
	private function get_base64_datadog_icon( $color1 = '#632CA6' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="256px" height="256px" viewBox="0 0 256 256">';
		$source .= '<defs>';
		$source .= '<style>.st0{fill-rule:evenodd;clip-rule:evenodd;fill:' . $color1 . '}</style>';
		$source .= '</defs>';
		$source .= '<g transform="translate(28,24) scale(0.25,0.25)">';
		$source .= '<path class="st0" d="M670.38,608.27l-71.24-46.99l-59.43,99.27l-69.12-20.21l-60.86,92.89l3.12,29.24l330.9-60.97l-19.22-206.75 L670.38,608.27z M361.79,519.13l53.09-7.3c8.59,3.86,14.57,5.33,24.87,7.95c16.04,4.18,34.61,8.19,62.11-5.67 c6.4-3.17,19.73-15.36,25.12-22.31l217.52-39.46l22.19,268.56l-372.65,67.16L361.79,519.13z M765.85,422.36l-21.47,4.09L703.13,0.27 L0.27,81.77l86.59,702.68l82.27-11.94c-6.57-9.38-16.8-20.73-34.27-35.26c-24.23-20.13-15.66-54.32-1.37-75.91 c18.91-36.48,116.34-82.84,110.82-141.15c-1.98-21.2-5.35-48.8-25.03-67.71c-0.74,7.85,0.59,15.41,0.59,15.41 s-8.08-10.31-12.11-24.37c-4-5.39-7.14-7.11-11.39-14.31c-3.03,8.33-2.63,17.99-2.63,17.99s-6.61-15.62-7.68-28.8 c-3.92,5.9-4.91,17.11-4.91,17.11s-8.59-24.62-6.63-37.88c-3.92-11.54-15.54-34.44-12.25-86.49c21.45,15.03,68.67,11.46,87.07-15.66 c6.11-8.98,10.29-33.5-3.05-81.81c-8.57-30.98-29.79-77.11-38.06-94.61l-0.99,0.71c4.36,14.1,13.35,43.66,16.8,57.99 c10.44,43.47,13.24,58.6,8.34,78.64c-4.17,17.42-14.17,28.82-39.52,41.56c-25.35,12.78-58.99-18.32-61.12-20.04 c-24.63-19.62-43.68-51.63-45.81-67.18c-2.21-17.02,9.81-27.24,15.87-41.16c-8.67,2.48-18.34,6.88-18.34,6.88 s11.54-11.94,25.77-22.27c5.89-3.9,9.35-6.38,15.56-11.54c-8.99-0.15-16.29,0.11-16.29,0.11s14.99-8.1,30.53-14 c-11.37-0.5-22.25-0.08-22.25-0.08s33.45-14.96,59.87-25.94c18.17-7.45,35.92-5.25,45.89,9.17c13.09,18.89,26.84,29.15,55.98,35.51 c17.89-7.93,23.33-12.01,45.81-18.13c19.79-21.76,35.33-24.58,35.33-24.58s-7.71,7.07-9.77,18.18 c11.22-8.84,23.52-16.22,23.52-16.22s-4.76,5.88-9.2,15.22l1.03,1.53c13.09-7.85,28.48-14.04,28.48-14.04s-4.4,5.56-9.56,12.76 c9.87-0.08,29.89,0.42,37.66,1.3c45.87,1.01,55.39-48.99,72.99-55.26c22.04-7.87,31.89-12.63,69.45,24.26 c32.23,31.67,57.41,88.36,44.91,101.06c-10.48,10.54-31.16-4.11-54.08-32.68c-12.11-15.13-21.27-33.01-25.56-55.74 c-3.62-19.18-17.71-30.31-17.71-30.31S520,92.95,520,109.01c0,8.77,1.1,41.56,15.16,59.96c-1.39,2.69-2.04,13.31-3.58,15.34 c-16.36-19.77-51.49-33.92-57.22-38.09c19.39,15.89,63.96,52.39,81.08,87.37c16.19,33.08,6.65,63.4,14.84,71.25 c2.33,2.25,34.82,42.73,41.07,63.07c10.9,35.45,0.65,72.7-13.62,95.81l-39.85,6.21c-5.83-1.62-9.76-2.43-14.99-5.46 c2.88-5.1,8.61-17.82,8.67-20.44l-2.25-3.95c-12.4,17.57-33.18,34.63-50.44,44.43c-22.59,12.8-48.63,10.83-65.58,5.58 c-48.11-14.84-93.6-47.35-104.57-55.89c0,0-0.34,6.82,1.73,8.35c12.13,13.68,39.92,38.43,66.78,55.68l-57.26,6.3l27.07,210.78 c-12,1.72-13.87,2.56-27.01,4.43c-11.58-40.91-33.73-67.62-57.94-83.18c-21.35-13.72-50.8-16.81-78.99-11.23l-1.81,2.1 c19.6-2.04,42.74,0.8,66.51,15.85c23.33,14.75,42.13,52.85,49.05,75.79c8.86,29.32,14.99,60.68-8.86,93.92 c-16.97,23.63-66.51,36.69-106.53,8.44c10.69,17.19,25.14,31.25,44.59,33.9c28.88,3.92,56.29-1.09,75.16-20.46 c16.11-16.56,24.65-51.19,22.4-87.66l25.49-3.7l9.2,65.46l421.98-50.81L765.85,422.36z M509.12,244.59 c-1.18,2.69-3.03,4.45-0.25,13.2l0.17,0.5l0.44,1.13l1.16,2.62c5.01,10.24,10.51,19.9,19.7,24.83c2.38-0.4,4.84-0.67,7.39-0.8 c8.63-0.38,14.08,0.99,17.54,2.85c0.31-1.72,0.38-4.24,0.19-7.95c-0.67-12.97,2.57-35.03-22.36-46.64 c-9.41-4.37-22.61-3.02-27.01,2.43c0.8,0.1,1.52,0.27,2.08,0.46C514.82,239.55,510.31,241.84,509.12,244.59 M578.99,365.61 c-3.27-1.8-18.55-1.09-29.29,0.19c-20.46,2.41-42.55,9.51-47.39,13.29c-8.8,6.8-4.8,18.66,1.7,23.53 c18.23,13.62,34.21,22.75,51.08,20.53c10.36-1.36,19.49-17.76,25.96-32.64C585.48,380.26,585.48,369.2,578.99,365.61 M397.85,260.65 c5.77-5.48-28.74-12.68-55.52,5.58c-19.75,13.47-20.38,42.35-1.47,58.72c1.89,1.62,3.45,2.77,4.91,3.71 c5.52-2.6,11.81-5.23,19.05-7.58c12.23-3.97,22.4-6.02,30.76-7.11c4-4.47,8.65-12.34,7.49-26.59 C401.49,268.05,386.84,271.12,397.85,260.65"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the New Relic icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @param string $color2 Optional. Color 2 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 2.5.0
	 */
	private function get_base64_newrelic_icon( $color1 = '#008c99', $color2 = '#70ccd3' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="256px" height="256px" viewBox="0 0 256 256">';
		$source .= '<g transform="translate(24,48) scale(1.26,1.26)">';
		$source .= '<path fill="' . $color1 . '" d="M168.72,55.82C161.07,20.67,118.92,0,74.56,9.64S.45,55.6,8.09,90.74s49.8,55.83,94.15,46.18S176.36,91,168.72,55.82ZM88.41,105.68a32.4,32.4,0,1,1,32.4-32.4A32.4,32.4,0,0,1,88.41,105.68Z" transform="translate(-6.9 -7.27)"/>';
		$source .= '<path fill="' . $color2 . '" d="M95.57,27.92A46.52,46.52,0,1,0,142.1,74.44,46.52,46.52,0,0,0,95.57,27.92Zm-7.17,73.66a28.3,28.3,0,1,1,28.3-28.3A28.3,28.3,0,0,1,88.41,101.58Z" transform="translate(-6.9 -7.27)"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the files icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @param string $color2 Optional. Color 2 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 2.5.0
	 */
	private function get_base64_files_icon( $color1 = '#cd377c', $color2 = '#cd375c' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" viewBox="0 0 512 512">';
		$source .= '<g transform="translate(64,64) scale(0.74,0.74)">';
		$source .= '<path fill="' . $color1 . '" d="M153.6,460.8a64,64,0,0,1-64-64V78.09A64,64,0,0,0,38.4,140.8V448a64,64,0,0,0,64,64H332.8a64,64,0,0,0,62.71-51.2Z"/>';
		$source .= '<path fill="' . $color2 . '" d="M320,0H179.2a64,64,0,0,0-64,64V371.2a64,64,0,0,0,64,64H409.6a64,64,0,0,0,64-64V153.6ZM230.4,204.8h76.8a12.8,12.8,0,0,1,0,25.6H230.4a12.8,12.8,0,1,1,0-25.6ZM358,332.8H230.4a12.8,12.8,0,1,1,0-25.6H358a12.8,12.8,0,0,1,0,25.6Zm.38-51.2h-128a12.8,12.8,0,1,1,0-25.6h128a12.8,12.8,0,1,1,0,25.6Zm12.8-115.2a64,64,0,0,1-64-64V25.6L448,166.4Z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Tracy icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @param string $color2 Optional. Color 2 of the icon.
	 * @param string $color3 Optional. Color 3 of the icon.
	 * @param string $color4 Optional. Color 4 of the icon.
	 * @param string $color5 Optional. Color 5 of the icon.
	 * @param string $color6 Optional. Color 6 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 2.5.0
	 */
	private function get_base64_tracy_icon( $color1 = '#cc1817', $color2 = '#ebacac', $color3 = '#fbfaf9', $color4 = '#5fade9', $color5 = '#9da09f', $color6 = '#faf4ce' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="500" height="500" viewBox="0 0 500 500">';
		$source .= '<g transform="scale(0.8,-0.8) translate(110,-530)">';
		$source .= '<path d="M 1.000000 128.192982 L 501.000000 128.192982 L 501.000000 51.438596 L 25.122807 51.438596 L 25.122807 25.122807 L 501.000000 25.122807 L 501.000000 1.000000 L 1.000000 1.000000 Z M 25.122807 104.070175 L 25.122807 82.140351 L 108.456140 82.140351 L 108.456140 104.070175 L 25.122807 104.070175 Z" transform="scale(0.8,0.8) translate(-1.000000,371.807018)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 1.000000 22.929825 L 84.333333 22.929825 L 84.333333 1.000000 L 1.000000 1.000000 L 1.000000 22.929825 Z" transform="scale(0.8,0.8) translate(23.122807,452.947368)" fill="' . $color2 . '"></path>';
		$source .= '<path d="M 1.000000 27.315789 L 476.877193 27.315789 L 476.877193 1.000000 L 1.000000 1.000000 Z" transform="scale(0.8,0.8) translate(23.122807,395.929825)" fill="' . $color2 . '"></path>';
		$source .= '<path d="M 1.000000 373.807018 L 501.000000 373.807018 L 501.000000 267.447368 L 499.903509 267.447368 L 498.807018 267.447368 L 497.710526 267.447368 L 496.614035 267.447368 L 495.517544 267.447368 L 494.421053 267.447368 L 493.324561 267.447368 L 492.228070 267.447368 L 491.131579 267.447368 L 490.035088 267.447368 L 488.938596 267.447368 L 488.938596 268.543860 L 487.842105 268.543860 L 486.745614 268.543860 L 485.649123 268.543860 L 484.552632 268.543860 L 483.456140 268.543860 L 482.359649 268.543860 L 481.263158 268.543860 L 480.166667 268.543860 L 479.070175 268.543860 L 477.973684 268.543860 L 476.877193 268.543860 L 475.780702 268.543860 L 474.684211 268.543860 L 473.587719 268.543860 L 472.491228 268.543860 L 471.394737 268.543860 L 470.298246 268.543860 L 469.201754 268.543860 L 468.105263 268.543860 L 467.008772 268.543860 L 465.912281 268.543860 L 464.815789 268.543860 L 463.719298 268.543860 L 462.622807 268.543860 L 461.526316 268.543860 L 460.429825 268.543860 L 459.333333 268.543860 L 458.236842 268.543860 L 457.140351 268.543860 L 456.043860 268.543860 L 454.947368 268.543860 L 453.850877 268.543860 L 452.754386 268.543860 L 451.657895 268.543860 L 450.561404 268.543860 L 449.464912 268.543860 L 448.368421 268.543860 L 447.271930 268.543860 L 446.175439 268.543860 L 445.078947 268.543860 L 443.982456 268.543860 L 442.885965 268.543860 L 441.789474 268.543860 L 440.692982 268.543860 L 439.596491 268.543860 L 438.500000 268.543860 L 437.403509 268.543860 L 436.307018 268.543860 L 435.210526 268.543860 L 434.114035 268.543860 L 433.017544 268.543860 L 431.921053 268.543860 L 430.824561 268.543860 L 429.728070 268.543860 L 428.631579 268.543860 L 427.535088 268.543860 L 426.438596 268.543860 L 425.342105 268.543860 L 424.245614 268.543860 L 423.149123 268.543860 L 422.052632 268.543860 L 420.956140 268.543860 L 419.859649 268.543860 L 418.763158 268.543860 L 417.666667 268.543860 L 416.570175 268.543860 L 415.473684 268.543860 L 414.377193 268.543860 L 413.280702 268.543860 L 412.184211 268.543860 L 411.087719 268.543860 L 409.991228 268.543860 L 408.894737 268.543860 L 407.798246 268.543860 L 406.701754 268.543860 L 405.605263 268.543860 L 404.508772 268.543860 L 403.412281 268.543860 L 402.315789 268.543860 L 401.219298 268.543860 L 400.122807 268.543860 L 399.026316 268.543860 L 397.929825 268.543860 L 396.833333 268.543860 L 395.736842 268.543860 L 394.640351 268.543860 L 393.543860 268.543860 L 392.447368 268.543860 L 391.350877 268.543860 L 390.254386 268.543860 L 389.157895 268.543860 L 388.061404 268.543860 L 386.964912 268.543860 L 385.868421 268.543860 L 384.771930 268.543860 L 383.675439 268.543860 L 382.578947 268.543860 L 381.482456 268.543860 L 380.385965 268.543860 L 379.289474 268.543860 L 378.192982 268.543860 L 377.096491 268.543860 L 376.000000 268.543860 L 374.903509 268.543860 L 373.807018 268.543860 L 372.710526 268.543860 L 371.614035 268.543860 L 370.517544 268.543860 L 369.421053 268.543860 L 368.324561 268.543860 L 367.228070 268.543860 L 366.131579 268.543860 L 365.035088 268.543860 L 363.938596 268.543860 L 362.842105 268.543860 L 361.745614 268.543860 L 360.649123 268.543860 L 359.552632 268.543860 L 358.456140 268.543860 L 357.359649 268.543860 L 356.263158 268.543860 L 355.166667 268.543860 L 354.070175 268.543860 L 352.973684 268.543860 L 351.877193 268.543860 L 350.780702 268.543860 L 349.684211 268.543860 L 348.587719 268.543860 L 347.491228 268.543860 L 346.394737 268.543860 L 345.298246 268.543860 L 344.201754 268.543860 L 343.105263 268.543860 L 342.008772 268.543860 L 340.912281 268.543860 L 339.815789 268.543860 L 338.719298 268.543860 L 337.622807 268.543860 L 336.526316 268.543860 L 335.429825 268.543860 L 334.333333 268.543860 L 333.236842 268.543860 L 332.140351 268.543860 L 331.043860 268.543860 L 329.947368 268.543860 L 328.850877 268.543860 L 327.754386 268.543860 L 326.657895 268.543860 L 325.561404 268.543860 L 324.464912 268.543860 L 323.368421 268.543860 L 322.271930 268.543860 L 321.175439 268.543860 L 320.078947 268.543860 L 318.982456 268.543860 L 317.885965 268.543860 L 316.789474 268.543860 L 315.692982 268.543860 L 314.596491 268.543860 L 313.500000 268.543860 L 312.403509 268.543860 L 311.307018 268.543860 L 310.210526 268.543860 L 309.114035 268.543860 L 308.017544 268.543860 L 306.921053 268.543860 L 305.824561 268.543860 L 304.728070 268.543860 L 303.631579 268.543860 L 302.535088 268.543860 L 301.438596 268.543860 L 300.342105 268.543860 L 299.245614 268.543860 L 298.149123 268.543860 L 297.052632 268.543860 L 295.956140 268.543860 L 294.859649 268.543860 L 293.763158 268.543860 L 292.666667 268.543860 L 291.570175 268.543860 L 290.473684 268.543860 L 289.377193 268.543860 L 288.280702 268.543860 L 287.184211 268.543860 L 286.087719 268.543860 L 284.991228 268.543860 L 283.894737 268.543860 L 282.798246 268.543860 L 281.701754 268.543860 L 280.605263 268.543860 L 279.508772 268.543860 L 278.412281 268.543860 L 277.315789 268.543860 L 276.219298 268.543860 L 275.122807 268.543860 L 274.026316 268.543860 L 272.929825 268.543860 L 271.833333 268.543860 L 270.736842 268.543860 L 269.640351 268.543860 L 268.543860 268.543860 L 267.447368 268.543860 L 266.350877 268.543860 L 265.254386 268.543860 L 264.157895 268.543860 L 263.061404 268.543860 L 261.964912 268.543860 L 260.868421 268.543860 L 259.771930 268.543860 L 258.675439 268.543860 L 257.578947 268.543860 L 256.482456 268.543860 L 255.385965 268.543860 L 254.289474 268.543860 L 253.192982 268.543860 L 252.096491 268.543860 L 251.000000 268.543860 L 249.903509 268.543860 L 248.807018 268.543860 L 247.710526 268.543860 L 246.614035 268.543860 L 245.517544 268.543860 L 244.421053 268.543860 L 243.324561 268.543860 L 242.228070 268.543860 L 241.131579 268.543860 L 240.035088 268.543860 L 238.938596 268.543860 L 237.842105 268.543860 L 236.745614 268.543860 L 235.649123 268.543860 L 234.552632 268.543860 L 233.456140 268.543860 L 232.359649 268.543860 L 231.263158 268.543860 L 230.166667 268.543860 L 229.070175 268.543860 L 227.973684 268.543860 L 226.877193 268.543860 L 225.780702 268.543860 L 224.684211 268.543860 L 223.587719 268.543860 L 222.491228 268.543860 L 221.394737 268.543860 L 220.298246 268.543860 L 219.201754 268.543860 L 218.105263 268.543860 L 217.008772 268.543860 L 215.912281 268.543860 L 214.815789 268.543860 L 213.719298 268.543860 L 212.622807 268.543860 L 211.526316 268.543860 L 210.429825 268.543860 L 209.333333 268.543860 L 208.236842 268.543860 L 207.140351 268.543860 L 206.043860 268.543860 L 204.947368 268.543860 L 203.850877 268.543860 L 202.754386 268.543860 L 201.657895 268.543860 L 200.561404 268.543860 L 199.464912 268.543860 L 198.368421 268.543860 L 197.271930 268.543860 L 196.175439 268.543860 L 195.078947 268.543860 L 193.982456 268.543860 L 192.885965 268.543860 L 191.789474 268.543860 L 190.692982 268.543860 L 189.596491 268.543860 L 188.500000 268.543860 L 187.403509 268.543860 L 186.307018 268.543860 L 185.210526 268.543860 L 184.114035 268.543860 L 183.017544 268.543860 L 181.921053 268.543860 L 180.824561 268.543860 L 179.728070 268.543860 L 178.631579 268.543860 L 177.535088 268.543860 L 176.438596 268.543860 L 175.342105 268.543860 L 174.245614 268.543860 L 173.149123 268.543860 L 172.052632 268.543860 L 170.956140 268.543860 L 169.859649 268.543860 L 168.763158 268.543860 L 167.666667 268.543860 L 166.570175 268.543860 L 165.473684 268.543860 L 164.377193 268.543860 L 163.280702 268.543860 L 162.184211 268.543860 L 161.087719 268.543860 L 159.991228 268.543860 L 158.894737 268.543860 L 157.798246 268.543860 L 156.701754 268.543860 L 155.605263 268.543860 L 154.508772 268.543860 L 153.412281 268.543860 L 152.315789 268.543860 L 151.219298 268.543860 L 150.122807 268.543860 L 149.026316 268.543860 L 147.929825 268.543860 L 146.833333 268.543860 L 145.736842 268.543860 L 144.640351 268.543860 L 143.543860 268.543860 L 142.447368 268.543860 L 141.350877 268.543860 L 140.254386 268.543860 L 139.157895 268.543860 L 138.061404 268.543860 L 136.964912 268.543860 L 135.868421 268.543860 L 134.771930 268.543860 L 133.675439 268.543860 L 132.578947 268.543860 L 131.482456 268.543860 L 130.385965 268.543860 L 129.289474 268.543860 L 128.192982 268.543860 L 127.096491 268.543860 L 126.000000 268.543860 L 124.903509 268.543860 L 123.807018 268.543860 L 122.710526 268.543860 L 121.614035 268.543860 L 120.517544 268.543860 L 119.421053 268.543860 L 118.324561 268.543860 L 117.228070 268.543860 L 116.131579 268.543860 L 115.035088 268.543860 L 113.938596 268.543860 L 112.842105 268.543860 L 111.745614 268.543860 L 110.649123 268.543860 L 109.552632 268.543860 L 108.456140 268.543860 L 107.359649 268.543860 L 106.263158 268.543860 L 105.166667 268.543860 L 104.070175 268.543860 L 102.973684 268.543860 L 101.877193 268.543860 L 100.780702 268.543860 L 99.684211 268.543860 L 98.587719 268.543860 L 97.491228 268.543860 L 96.394737 268.543860 L 95.298246 268.543860 L 94.201754 268.543860 L 93.105263 268.543860 L 92.008772 268.543860 L 90.912281 268.543860 L 89.815789 268.543860 L 88.719298 268.543860 L 87.622807 268.543860 L 86.526316 268.543860 L 85.429825 268.543860 L 84.333333 268.543860 L 83.236842 268.543860 L 82.140351 268.543860 L 81.043860 268.543860 L 79.947368 268.543860 L 78.850877 268.543860 L 77.754386 268.543860 L 76.657895 268.543860 L 75.561404 268.543860 L 74.464912 268.543860 L 73.368421 268.543860 L 72.271930 268.543860 L 71.175439 268.543860 L 70.078947 268.543860 L 68.982456 268.543860 L 67.885965 268.543860 L 66.789474 268.543860 L 65.692982 268.543860 L 64.596491 268.543860 L 63.500000 268.543860 L 62.403509 268.543860 L 61.307018 268.543860 L 60.210526 268.543860 L 59.114035 268.543860 L 58.017544 268.543860 L 56.921053 268.543860 L 55.824561 268.543860 L 54.728070 268.543860 L 53.631579 268.543860 L 52.535088 268.543860 L 51.438596 268.543860 L 50.342105 268.543860 L 49.245614 268.543860 L 48.149123 268.543860 L 47.052632 268.543860 L 45.956140 268.543860 L 44.859649 268.543860 L 43.763158 268.543860 L 43.763158 267.447368 L 42.666667 267.447368 L 41.570175 267.447368 L 40.473684 267.447368 L 40.473684 266.350877 L 39.377193 266.350877 L 39.377193 265.254386 L 38.280702 265.254386 L 38.280702 264.157895 L 38.280702 263.061404 L 37.184211 263.061404 L 37.184211 261.964912 L 37.184211 260.868421 L 37.184211 259.771930 L 37.184211 258.675439 L 37.184211 257.578947 L 36.087719 257.578947 L 36.087719 256.482456 L 36.087719 255.385965 L 36.087719 254.289474 L 36.087719 253.192982 L 37.184211 253.192982 L 37.184211 252.096491 L 37.184211 251.000000 L 37.184211 249.903509 L 37.184211 248.807018 L 37.184211 247.710526 L 37.184211 246.614035 L 37.184211 245.517544 L 37.184211 244.421053 L 37.184211 243.324561 L 37.184211 242.228070 L 37.184211 241.131579 L 37.184211 240.035088 L 37.184211 238.938596 L 37.184211 237.842105 L 37.184211 236.745614 L 37.184211 235.649123 L 37.184211 234.552632 L 37.184211 233.456140 L 37.184211 232.359649 L 37.184211 231.263158 L 37.184211 230.166667 L 37.184211 229.070175 L 37.184211 227.973684 L 37.184211 226.877193 L 37.184211 225.780702 L 37.184211 224.684211 L 37.184211 223.587719 L 37.184211 222.491228 L 37.184211 221.394737 L 37.184211 220.298246 L 37.184211 219.201754 L 37.184211 218.105263 L 37.184211 217.008772 L 37.184211 215.912281 L 37.184211 214.815789 L 37.184211 213.719298 L 37.184211 212.622807 L 37.184211 211.526316 L 37.184211 210.429825 L 37.184211 209.333333 L 37.184211 208.236842 L 37.184211 207.140351 L 37.184211 206.043860 L 37.184211 204.947368 L 37.184211 203.850877 L 37.184211 202.754386 L 37.184211 201.657895 L 37.184211 200.561404 L 37.184211 199.464912 L 37.184211 198.368421 L 37.184211 197.271930 L 37.184211 196.175439 L 37.184211 195.078947 L 37.184211 193.982456 L 37.184211 192.885965 L 37.184211 191.789474 L 37.184211 190.692982 L 37.184211 189.596491 L 37.184211 188.500000 L 37.184211 187.403509 L 37.184211 186.307018 L 37.184211 185.210526 L 37.184211 184.114035 L 37.184211 183.017544 L 37.184211 181.921053 L 37.184211 180.824561 L 37.184211 179.728070 L 37.184211 178.631579 L 37.184211 177.535088 L 37.184211 176.438596 L 37.184211 175.342105 L 37.184211 174.245614 L 37.184211 173.149123 L 37.184211 172.052632 L 37.184211 170.956140 L 37.184211 169.859649 L 37.184211 168.763158 L 36.087719 168.763158 L 36.087719 167.666667 L 37.184211 167.666667 L 37.184211 166.570175 L 37.184211 165.473684 L 37.184211 164.377193 L 37.184211 163.280702 L 37.184211 162.184211 L 37.184211 161.087719 L 37.184211 159.991228 L 37.184211 158.894737 L 37.184211 157.798246 L 37.184211 156.701754 L 37.184211 155.605263 L 37.184211 154.508772 L 37.184211 153.412281 L 37.184211 152.315789 L 37.184211 151.219298 L 38.280702 151.219298 L 38.280702 150.122807 L 38.280702 149.026316 L 38.280702 147.929825 L 38.280702 146.833333 L 38.280702 145.736842 L 38.280702 144.640351 L 38.280702 143.543860 L 38.280702 142.447368 L 38.280702 141.350877 L 38.280702 140.254386 L 38.280702 139.157895 L 38.280702 138.061404 L 38.280702 136.964912 L 38.280702 135.868421 L 38.280702 134.771930 L 38.280702 133.675439 L 38.280702 132.578947 L 38.280702 131.482456 L 38.280702 130.385965 L 38.280702 129.289474 L 38.280702 128.192982 L 38.280702 127.096491 L 38.280702 126.000000 L 37.184211 126.000000 L 37.184211 124.903509 L 37.184211 123.807018 L 37.184211 122.710526 L 37.184211 121.614035 L 37.184211 120.517544 L 37.184211 119.421053 L 37.184211 118.324561 L 37.184211 117.228070 L 37.184211 116.131579 L 37.184211 115.035088 L 37.184211 113.938596 L 37.184211 112.842105 L 37.184211 111.745614 L 37.184211 110.649123 L 37.184211 109.552632 L 37.184211 108.456140 L 37.184211 107.359649 L 37.184211 106.263158 L 37.184211 105.166667 L 37.184211 104.070175 L 37.184211 102.973684 L 37.184211 101.877193 L 37.184211 100.780702 L 37.184211 99.684211 L 37.184211 98.587719 L 37.184211 97.491228 L 37.184211 96.394737 L 37.184211 95.298246 L 37.184211 94.201754 L 37.184211 93.105263 L 37.184211 92.008772 L 37.184211 90.912281 L 37.184211 89.815789 L 37.184211 88.719298 L 37.184211 87.622807 L 37.184211 86.526316 L 37.184211 85.429825 L 37.184211 84.333333 L 37.184211 83.236842 L 37.184211 82.140351 L 37.184211 81.043860 L 37.184211 79.947368 L 37.184211 78.850877 L 37.184211 77.754386 L 37.184211 76.657895 L 37.184211 75.561404 L 37.184211 74.464912 L 37.184211 73.368421 L 37.184211 72.271930 L 37.184211 71.175439 L 37.184211 70.078947 L 37.184211 68.982456 L 37.184211 67.885965 L 37.184211 66.789474 L 37.184211 65.692982 L 37.184211 64.596491 L 37.184211 63.500000 L 37.184211 62.403509 L 37.184211 61.307018 L 37.184211 60.210526 L 37.184211 59.114035 L 37.184211 58.017544 L 37.184211 56.921053 L 37.184211 55.824561 L 37.184211 54.728070 L 37.184211 53.631579 L 37.184211 52.535088 L 37.184211 51.438596 L 37.184211 50.342105 L 37.184211 49.245614 L 37.184211 48.149123 L 37.184211 47.052632 L 37.184211 45.956140 L 37.184211 44.859649 L 36.087719 44.859649 L 36.087719 43.763158 L 36.087719 42.666667 L 37.184211 42.666667 L 37.184211 41.570175 L 37.184211 40.473684 L 37.184211 39.377193 L 37.184211 38.280702 L 37.184211 37.184211 L 37.184211 36.087719 L 37.184211 34.991228 L 38.280702 34.991228 L 38.280702 33.894737 L 38.280702 32.798246 L 39.377193 32.798246 L 39.377193 31.701754 L 40.473684 31.701754 L 40.473684 30.605263 L 41.570175 30.605263 L 42.666667 30.605263 L 42.666667 29.508772 L 43.763158 29.508772 L 44.859649 29.508772 L 45.956140 29.508772 L 47.052632 29.508772 L 48.149123 29.508772 L 49.245614 29.508772 L 50.342105 29.508772 L 51.438596 29.508772 L 52.535088 29.508772 L 53.631579 29.508772 L 54.728070 29.508772 L 55.824561 29.508772 L 56.921053 29.508772 L 58.017544 29.508772 L 59.114035 29.508772 L 60.210526 29.508772 L 61.307018 29.508772 L 62.403509 29.508772 L 63.500000 29.508772 L 64.596491 29.508772 L 65.692982 29.508772 L 66.789474 29.508772 L 67.885965 29.508772 L 68.982456 29.508772 L 70.078947 29.508772 L 71.175439 29.508772 L 72.271930 29.508772 L 73.368421 29.508772 L 74.464912 29.508772 L 75.561404 29.508772 L 76.657895 29.508772 L 77.754386 29.508772 L 78.850877 29.508772 L 79.947368 29.508772 L 81.043860 29.508772 L 82.140351 29.508772 L 83.236842 29.508772 L 84.333333 29.508772 L 85.429825 29.508772 L 86.526316 29.508772 L 87.622807 29.508772 L 88.719298 29.508772 L 89.815789 29.508772 L 90.912281 29.508772 L 92.008772 29.508772 L 93.105263 29.508772 L 94.201754 29.508772 L 95.298246 29.508772 L 96.394737 29.508772 L 97.491228 29.508772 L 98.587719 29.508772 L 99.684211 29.508772 L 100.780702 29.508772 L 101.877193 29.508772 L 102.973684 29.508772 L 104.070175 29.508772 L 105.166667 29.508772 L 106.263158 29.508772 L 107.359649 29.508772 L 108.456140 29.508772 L 109.552632 29.508772 L 110.649123 29.508772 L 111.745614 29.508772 L 112.842105 29.508772 L 113.938596 29.508772 L 115.035088 29.508772 L 116.131579 29.508772 L 117.228070 29.508772 L 118.324561 29.508772 L 119.421053 29.508772 L 120.517544 29.508772 L 121.614035 29.508772 L 122.710526 29.508772 L 123.807018 29.508772 L 124.903509 29.508772 L 126.000000 29.508772 L 127.096491 29.508772 L 128.192982 29.508772 L 129.289474 29.508772 L 130.385965 29.508772 L 131.482456 29.508772 L 132.578947 29.508772 L 133.675439 29.508772 L 134.771930 29.508772 L 135.868421 29.508772 L 136.964912 29.508772 L 138.061404 29.508772 L 139.157895 29.508772 L 140.254386 29.508772 L 141.350877 29.508772 L 142.447368 29.508772 L 143.543860 29.508772 L 144.640351 29.508772 L 145.736842 29.508772 L 146.833333 29.508772 L 147.929825 29.508772 L 149.026316 29.508772 L 150.122807 29.508772 L 151.219298 29.508772 L 152.315789 29.508772 L 153.412281 29.508772 L 154.508772 29.508772 L 155.605263 29.508772 L 156.701754 29.508772 L 157.798246 29.508772 L 158.894737 29.508772 L 159.991228 29.508772 L 161.087719 29.508772 L 162.184211 29.508772 L 163.280702 29.508772 L 164.377193 29.508772 L 165.473684 29.508772 L 166.570175 29.508772 L 167.666667 29.508772 L 168.763158 29.508772 L 169.859649 29.508772 L 170.956140 29.508772 L 172.052632 29.508772 L 173.149123 29.508772 L 174.245614 29.508772 L 175.342105 29.508772 L 176.438596 29.508772 L 177.535088 29.508772 L 178.631579 29.508772 L 179.728070 29.508772 L 180.824561 29.508772 L 181.921053 29.508772 L 183.017544 29.508772 L 184.114035 29.508772 L 185.210526 29.508772 L 186.307018 29.508772 L 187.403509 29.508772 L 188.500000 29.508772 L 189.596491 29.508772 L 190.692982 29.508772 L 191.789474 29.508772 L 192.885965 29.508772 L 193.982456 29.508772 L 195.078947 29.508772 L 196.175439 29.508772 L 197.271930 29.508772 L 198.368421 29.508772 L 199.464912 29.508772 L 200.561404 29.508772 L 201.657895 29.508772 L 202.754386 29.508772 L 203.850877 29.508772 L 204.947368 29.508772 L 206.043860 29.508772 L 207.140351 29.508772 L 208.236842 29.508772 L 209.333333 29.508772 L 210.429825 29.508772 L 211.526316 29.508772 L 212.622807 29.508772 L 213.719298 29.508772 L 214.815789 29.508772 L 215.912281 29.508772 L 217.008772 29.508772 L 218.105263 29.508772 L 219.201754 29.508772 L 220.298246 29.508772 L 221.394737 29.508772 L 222.491228 29.508772 L 223.587719 29.508772 L 224.684211 29.508772 L 225.780702 29.508772 L 226.877193 29.508772 L 227.973684 29.508772 L 229.070175 29.508772 L 230.166667 29.508772 L 231.263158 29.508772 L 232.359649 29.508772 L 233.456140 29.508772 L 234.552632 29.508772 L 235.649123 29.508772 L 236.745614 29.508772 L 237.842105 29.508772 L 238.938596 29.508772 L 240.035088 29.508772 L 241.131579 29.508772 L 242.228070 29.508772 L 243.324561 29.508772 L 244.421053 29.508772 L 245.517544 29.508772 L 246.614035 29.508772 L 247.710526 29.508772 L 248.807018 29.508772 L 249.903509 29.508772 L 251.000000 29.508772 L 252.096491 29.508772 L 253.192982 29.508772 L 254.289474 29.508772 L 255.385965 29.508772 L 256.482456 29.508772 L 257.578947 29.508772 L 258.675439 29.508772 L 259.771930 29.508772 L 260.868421 29.508772 L 261.964912 29.508772 L 263.061404 29.508772 L 264.157895 29.508772 L 265.254386 29.508772 L 266.350877 29.508772 L 267.447368 29.508772 L 268.543860 29.508772 L 269.640351 29.508772 L 270.736842 29.508772 L 271.833333 29.508772 L 272.929825 29.508772 L 274.026316 29.508772 L 275.122807 29.508772 L 276.219298 29.508772 L 277.315789 29.508772 L 278.412281 29.508772 L 279.508772 29.508772 L 280.605263 29.508772 L 281.701754 29.508772 L 282.798246 29.508772 L 283.894737 29.508772 L 284.991228 29.508772 L 286.087719 29.508772 L 287.184211 29.508772 L 288.280702 29.508772 L 289.377193 29.508772 L 290.473684 29.508772 L 291.570175 29.508772 L 292.666667 29.508772 L 293.763158 29.508772 L 294.859649 29.508772 L 295.956140 29.508772 L 297.052632 29.508772 L 298.149123 29.508772 L 299.245614 29.508772 L 300.342105 29.508772 L 301.438596 29.508772 L 302.535088 29.508772 L 303.631579 29.508772 L 304.728070 29.508772 L 305.824561 29.508772 L 306.921053 29.508772 L 308.017544 29.508772 L 309.114035 29.508772 L 310.210526 29.508772 L 311.307018 29.508772 L 312.403509 29.508772 L 313.500000 29.508772 L 314.596491 29.508772 L 315.692982 29.508772 L 316.789474 29.508772 L 317.885965 29.508772 L 318.982456 29.508772 L 320.078947 29.508772 L 321.175439 29.508772 L 322.271930 29.508772 L 323.368421 29.508772 L 324.464912 29.508772 L 325.561404 29.508772 L 326.657895 29.508772 L 327.754386 29.508772 L 328.850877 29.508772 L 329.947368 29.508772 L 331.043860 29.508772 L 332.140351 29.508772 L 333.236842 29.508772 L 334.333333 29.508772 L 335.429825 29.508772 L 336.526316 29.508772 L 337.622807 29.508772 L 338.719298 29.508772 L 339.815789 29.508772 L 340.912281 29.508772 L 342.008772 29.508772 L 343.105263 29.508772 L 344.201754 29.508772 L 345.298246 29.508772 L 346.394737 29.508772 L 347.491228 29.508772 L 348.587719 29.508772 L 349.684211 29.508772 L 350.780702 29.508772 L 351.877193 29.508772 L 352.973684 29.508772 L 354.070175 29.508772 L 355.166667 29.508772 L 356.263158 29.508772 L 357.359649 29.508772 L 358.456140 29.508772 L 359.552632 29.508772 L 360.649123 29.508772 L 361.745614 29.508772 L 362.842105 29.508772 L 363.938596 29.508772 L 365.035088 29.508772 L 366.131579 29.508772 L 367.228070 29.508772 L 368.324561 29.508772 L 369.421053 29.508772 L 370.517544 29.508772 L 371.614035 29.508772 L 372.710526 29.508772 L 373.807018 29.508772 L 374.903509 29.508772 L 376.000000 29.508772 L 377.096491 29.508772 L 378.192982 29.508772 L 379.289474 29.508772 L 380.385965 29.508772 L 381.482456 29.508772 L 382.578947 29.508772 L 383.675439 29.508772 L 384.771930 29.508772 L 385.868421 29.508772 L 386.964912 29.508772 L 388.061404 29.508772 L 389.157895 29.508772 L 390.254386 29.508772 L 391.350877 29.508772 L 392.447368 29.508772 L 393.543860 29.508772 L 394.640351 29.508772 L 395.736842 29.508772 L 396.833333 29.508772 L 397.929825 29.508772 L 399.026316 29.508772 L 400.122807 29.508772 L 401.219298 29.508772 L 402.315789 29.508772 L 403.412281 29.508772 L 404.508772 29.508772 L 405.605263 29.508772 L 406.701754 29.508772 L 407.798246 29.508772 L 408.894737 29.508772 L 409.991228 29.508772 L 411.087719 29.508772 L 412.184211 29.508772 L 413.280702 29.508772 L 414.377193 29.508772 L 415.473684 29.508772 L 416.570175 29.508772 L 417.666667 29.508772 L 418.763158 29.508772 L 419.859649 29.508772 L 420.956140 29.508772 L 422.052632 29.508772 L 423.149123 29.508772 L 424.245614 29.508772 L 425.342105 29.508772 L 426.438596 29.508772 L 427.535088 29.508772 L 428.631579 29.508772 L 429.728070 29.508772 L 430.824561 29.508772 L 431.921053 29.508772 L 433.017544 29.508772 L 434.114035 29.508772 L 435.210526 29.508772 L 436.307018 29.508772 L 437.403509 29.508772 L 438.500000 29.508772 L 439.596491 29.508772 L 440.692982 29.508772 L 441.789474 29.508772 L 442.885965 29.508772 L 443.982456 29.508772 L 445.078947 29.508772 L 446.175439 29.508772 L 447.271930 29.508772 L 448.368421 29.508772 L 449.464912 29.508772 L 450.561404 29.508772 L 451.657895 29.508772 L 452.754386 29.508772 L 453.850877 29.508772 L 454.947368 29.508772 L 456.043860 29.508772 L 457.140351 29.508772 L 458.236842 29.508772 L 459.333333 29.508772 L 460.429825 29.508772 L 461.526316 29.508772 L 462.622807 29.508772 L 463.719298 29.508772 L 464.815789 29.508772 L 465.912281 29.508772 L 467.008772 29.508772 L 468.105263 29.508772 L 469.201754 29.508772 L 470.298246 29.508772 L 471.394737 29.508772 L 472.491228 29.508772 L 473.587719 29.508772 L 474.684211 29.508772 L 475.780702 29.508772 L 476.877193 29.508772 L 477.973684 29.508772 L 479.070175 29.508772 L 480.166667 29.508772 L 481.263158 29.508772 L 482.359649 29.508772 L 483.456140 29.508772 L 484.552632 29.508772 L 485.649123 29.508772 L 486.745614 29.508772 L 487.842105 29.508772 L 488.938596 29.508772 L 490.035088 29.508772 L 491.131579 29.508772 L 492.228070 29.508772 L 493.324561 29.508772 L 494.421053 29.508772 L 495.517544 29.508772 L 496.614035 29.508772 L 497.710526 29.508772 L 498.807018 29.508772 L 499.903509 29.508772 L 501.000000 29.508772 L 501.000000 1.000000 L 1.000000 1.000000 Z M 29.508772 354.070175 L 29.508772 327.754386 L 169.859649 327.754386 L 169.859649 354.070175 L 29.508772 354.070175 Z M 38.280702 303.631579 L 38.280702 277.315789 L 485.649123 277.315789 L 485.649123 303.631579 L 38.280702 303.631579 Z" transform="scale(0.8,0.8) translate(-1.000000,-1.000000)" fill="' . $color3 . '"></path>';
		$source .= '<path d="M 1.000000 27.315789 L 141.350877 27.315789 L 141.350877 1.000000 L 1.000000 1.000000 L 1.000000 27.315789 Z" transform="scale(0.8,0.8) translate(27.508772,325.754386)" fill="' . $color4 . '"></path>';
		$source .= '<path d="M 1.000000 27.315789 L 448.368421 27.315789 L 448.368421 1.000000 L 1.000000 1.000000 L 1.000000 27.315789 Z" transform="scale(0.8,0.8) translate(36.280702,275.315789)" fill="' . $color5 . '"></path>';
		$source .= '<path d="M 8.675439 240.035088 L 9.771930 240.035088 L 10.868421 240.035088 L 11.964912 240.035088 L 13.061404 240.035088 L 14.157895 240.035088 L 15.254386 240.035088 L 16.350877 240.035088 L 17.447368 240.035088 L 18.543860 240.035088 L 19.640351 240.035088 L 20.736842 240.035088 L 21.833333 240.035088 L 22.929825 240.035088 L 24.026316 240.035088 L 25.122807 240.035088 L 26.219298 240.035088 L 27.315789 240.035088 L 28.412281 240.035088 L 29.508772 240.035088 L 30.605263 240.035088 L 31.701754 240.035088 L 32.798246 240.035088 L 33.894737 240.035088 L 34.991228 240.035088 L 36.087719 240.035088 L 37.184211 240.035088 L 38.280702 240.035088 L 39.377193 240.035088 L 40.473684 240.035088 L 41.570175 240.035088 L 42.666667 240.035088 L 43.763158 240.035088 L 44.859649 240.035088 L 45.956140 240.035088 L 47.052632 240.035088 L 48.149123 240.035088 L 49.245614 240.035088 L 50.342105 240.035088 L 51.438596 240.035088 L 52.535088 240.035088 L 53.631579 240.035088 L 54.728070 240.035088 L 55.824561 240.035088 L 56.921053 240.035088 L 58.017544 240.035088 L 59.114035 240.035088 L 60.210526 240.035088 L 61.307018 240.035088 L 62.403509 240.035088 L 63.500000 240.035088 L 64.596491 240.035088 L 65.692982 240.035088 L 66.789474 240.035088 L 67.885965 240.035088 L 68.982456 240.035088 L 70.078947 240.035088 L 71.175439 240.035088 L 72.271930 240.035088 L 73.368421 240.035088 L 74.464912 240.035088 L 75.561404 240.035088 L 76.657895 240.035088 L 77.754386 240.035088 L 78.850877 240.035088 L 79.947368 240.035088 L 81.043860 240.035088 L 82.140351 240.035088 L 83.236842 240.035088 L 84.333333 240.035088 L 85.429825 240.035088 L 86.526316 240.035088 L 87.622807 240.035088 L 88.719298 240.035088 L 89.815789 240.035088 L 90.912281 240.035088 L 92.008772 240.035088 L 93.105263 240.035088 L 94.201754 240.035088 L 95.298246 240.035088 L 96.394737 240.035088 L 97.491228 240.035088 L 98.587719 240.035088 L 99.684211 240.035088 L 100.780702 240.035088 L 101.877193 240.035088 L 102.973684 240.035088 L 104.070175 240.035088 L 105.166667 240.035088 L 106.263158 240.035088 L 107.359649 240.035088 L 108.456140 240.035088 L 109.552632 240.035088 L 110.649123 240.035088 L 111.745614 240.035088 L 112.842105 240.035088 L 113.938596 240.035088 L 115.035088 240.035088 L 116.131579 240.035088 L 117.228070 240.035088 L 118.324561 240.035088 L 119.421053 240.035088 L 120.517544 240.035088 L 121.614035 240.035088 L 122.710526 240.035088 L 123.807018 240.035088 L 124.903509 240.035088 L 126.000000 240.035088 L 127.096491 240.035088 L 128.192982 240.035088 L 129.289474 240.035088 L 130.385965 240.035088 L 131.482456 240.035088 L 132.578947 240.035088 L 133.675439 240.035088 L 134.771930 240.035088 L 135.868421 240.035088 L 136.964912 240.035088 L 138.061404 240.035088 L 139.157895 240.035088 L 140.254386 240.035088 L 141.350877 240.035088 L 142.447368 240.035088 L 143.543860 240.035088 L 144.640351 240.035088 L 145.736842 240.035088 L 146.833333 240.035088 L 147.929825 240.035088 L 149.026316 240.035088 L 150.122807 240.035088 L 151.219298 240.035088 L 152.315789 240.035088 L 153.412281 240.035088 L 154.508772 240.035088 L 155.605263 240.035088 L 156.701754 240.035088 L 157.798246 240.035088 L 158.894737 240.035088 L 159.991228 240.035088 L 161.087719 240.035088 L 162.184211 240.035088 L 163.280702 240.035088 L 164.377193 240.035088 L 165.473684 240.035088 L 166.570175 240.035088 L 167.666667 240.035088 L 168.763158 240.035088 L 169.859649 240.035088 L 170.956140 240.035088 L 172.052632 240.035088 L 173.149123 240.035088 L 174.245614 240.035088 L 175.342105 240.035088 L 176.438596 240.035088 L 177.535088 240.035088 L 178.631579 240.035088 L 179.728070 240.035088 L 180.824561 240.035088 L 181.921053 240.035088 L 183.017544 240.035088 L 184.114035 240.035088 L 185.210526 240.035088 L 186.307018 240.035088 L 187.403509 240.035088 L 188.500000 240.035088 L 189.596491 240.035088 L 190.692982 240.035088 L 191.789474 240.035088 L 192.885965 240.035088 L 193.982456 240.035088 L 195.078947 240.035088 L 196.175439 240.035088 L 197.271930 240.035088 L 198.368421 240.035088 L 199.464912 240.035088 L 200.561404 240.035088 L 201.657895 240.035088 L 202.754386 240.035088 L 203.850877 240.035088 L 204.947368 240.035088 L 206.043860 240.035088 L 207.140351 240.035088 L 208.236842 240.035088 L 209.333333 240.035088 L 210.429825 240.035088 L 211.526316 240.035088 L 212.622807 240.035088 L 213.719298 240.035088 L 214.815789 240.035088 L 215.912281 240.035088 L 217.008772 240.035088 L 218.105263 240.035088 L 219.201754 240.035088 L 220.298246 240.035088 L 221.394737 240.035088 L 222.491228 240.035088 L 223.587719 240.035088 L 224.684211 240.035088 L 225.780702 240.035088 L 226.877193 240.035088 L 227.973684 240.035088 L 229.070175 240.035088 L 230.166667 240.035088 L 231.263158 240.035088 L 232.359649 240.035088 L 233.456140 240.035088 L 234.552632 240.035088 L 235.649123 240.035088 L 236.745614 240.035088 L 237.842105 240.035088 L 238.938596 240.035088 L 240.035088 240.035088 L 241.131579 240.035088 L 242.228070 240.035088 L 243.324561 240.035088 L 244.421053 240.035088 L 245.517544 240.035088 L 246.614035 240.035088 L 247.710526 240.035088 L 248.807018 240.035088 L 249.903509 240.035088 L 251.000000 240.035088 L 252.096491 240.035088 L 253.192982 240.035088 L 254.289474 240.035088 L 255.385965 240.035088 L 256.482456 240.035088 L 257.578947 240.035088 L 258.675439 240.035088 L 259.771930 240.035088 L 260.868421 240.035088 L 261.964912 240.035088 L 263.061404 240.035088 L 264.157895 240.035088 L 265.254386 240.035088 L 266.350877 240.035088 L 267.447368 240.035088 L 268.543860 240.035088 L 269.640351 240.035088 L 270.736842 240.035088 L 271.833333 240.035088 L 272.929825 240.035088 L 274.026316 240.035088 L 275.122807 240.035088 L 276.219298 240.035088 L 277.315789 240.035088 L 278.412281 240.035088 L 279.508772 240.035088 L 280.605263 240.035088 L 281.701754 240.035088 L 282.798246 240.035088 L 283.894737 240.035088 L 284.991228 240.035088 L 286.087719 240.035088 L 287.184211 240.035088 L 288.280702 240.035088 L 289.377193 240.035088 L 290.473684 240.035088 L 291.570175 240.035088 L 292.666667 240.035088 L 293.763158 240.035088 L 294.859649 240.035088 L 295.956140 240.035088 L 297.052632 240.035088 L 298.149123 240.035088 L 299.245614 240.035088 L 300.342105 240.035088 L 301.438596 240.035088 L 302.535088 240.035088 L 303.631579 240.035088 L 304.728070 240.035088 L 305.824561 240.035088 L 306.921053 240.035088 L 308.017544 240.035088 L 309.114035 240.035088 L 310.210526 240.035088 L 311.307018 240.035088 L 312.403509 240.035088 L 313.500000 240.035088 L 314.596491 240.035088 L 315.692982 240.035088 L 316.789474 240.035088 L 317.885965 240.035088 L 318.982456 240.035088 L 320.078947 240.035088 L 321.175439 240.035088 L 322.271930 240.035088 L 323.368421 240.035088 L 324.464912 240.035088 L 325.561404 240.035088 L 326.657895 240.035088 L 327.754386 240.035088 L 328.850877 240.035088 L 329.947368 240.035088 L 331.043860 240.035088 L 332.140351 240.035088 L 333.236842 240.035088 L 334.333333 240.035088 L 335.429825 240.035088 L 336.526316 240.035088 L 337.622807 240.035088 L 338.719298 240.035088 L 339.815789 240.035088 L 340.912281 240.035088 L 342.008772 240.035088 L 343.105263 240.035088 L 344.201754 240.035088 L 345.298246 240.035088 L 346.394737 240.035088 L 347.491228 240.035088 L 348.587719 240.035088 L 349.684211 240.035088 L 350.780702 240.035088 L 351.877193 240.035088 L 352.973684 240.035088 L 354.070175 240.035088 L 355.166667 240.035088 L 356.263158 240.035088 L 357.359649 240.035088 L 358.456140 240.035088 L 359.552632 240.035088 L 360.649123 240.035088 L 361.745614 240.035088 L 362.842105 240.035088 L 363.938596 240.035088 L 365.035088 240.035088 L 366.131579 240.035088 L 367.228070 240.035088 L 368.324561 240.035088 L 369.421053 240.035088 L 370.517544 240.035088 L 371.614035 240.035088 L 372.710526 240.035088 L 373.807018 240.035088 L 374.903509 240.035088 L 376.000000 240.035088 L 377.096491 240.035088 L 378.192982 240.035088 L 379.289474 240.035088 L 380.385965 240.035088 L 381.482456 240.035088 L 382.578947 240.035088 L 383.675439 240.035088 L 384.771930 240.035088 L 385.868421 240.035088 L 386.964912 240.035088 L 388.061404 240.035088 L 389.157895 240.035088 L 390.254386 240.035088 L 391.350877 240.035088 L 392.447368 240.035088 L 393.543860 240.035088 L 394.640351 240.035088 L 395.736842 240.035088 L 396.833333 240.035088 L 397.929825 240.035088 L 399.026316 240.035088 L 400.122807 240.035088 L 401.219298 240.035088 L 402.315789 240.035088 L 403.412281 240.035088 L 404.508772 240.035088 L 405.605263 240.035088 L 406.701754 240.035088 L 407.798246 240.035088 L 408.894737 240.035088 L 409.991228 240.035088 L 411.087719 240.035088 L 412.184211 240.035088 L 413.280702 240.035088 L 414.377193 240.035088 L 415.473684 240.035088 L 416.570175 240.035088 L 417.666667 240.035088 L 418.763158 240.035088 L 419.859649 240.035088 L 420.956140 240.035088 L 422.052632 240.035088 L 423.149123 240.035088 L 424.245614 240.035088 L 425.342105 240.035088 L 426.438596 240.035088 L 427.535088 240.035088 L 428.631579 240.035088 L 429.728070 240.035088 L 430.824561 240.035088 L 431.921053 240.035088 L 433.017544 240.035088 L 434.114035 240.035088 L 435.210526 240.035088 L 436.307018 240.035088 L 437.403509 240.035088 L 438.500000 240.035088 L 439.596491 240.035088 L 440.692982 240.035088 L 441.789474 240.035088 L 442.885965 240.035088 L 443.982456 240.035088 L 445.078947 240.035088 L 446.175439 240.035088 L 447.271930 240.035088 L 448.368421 240.035088 L 449.464912 240.035088 L 450.561404 240.035088 L 451.657895 240.035088 L 452.754386 240.035088 L 453.850877 240.035088 L 453.850877 238.938596 L 454.947368 238.938596 L 456.043860 238.938596 L 457.140351 238.938596 L 458.236842 238.938596 L 459.333333 238.938596 L 460.429825 238.938596 L 461.526316 238.938596 L 462.622807 238.938596 L 463.719298 238.938596 L 464.815789 238.938596 L 465.912281 238.938596 L 465.912281 126.000000 L 11.964912 126.000000 L 11.964912 95.298246 L 465.912281 95.298246 L 465.912281 1.000000 L 464.815789 1.000000 L 463.719298 1.000000 L 462.622807 1.000000 L 461.526316 1.000000 L 460.429825 1.000000 L 459.333333 1.000000 L 458.236842 1.000000 L 457.140351 1.000000 L 456.043860 1.000000 L 454.947368 1.000000 L 453.850877 1.000000 L 452.754386 1.000000 L 451.657895 1.000000 L 450.561404 1.000000 L 449.464912 1.000000 L 448.368421 1.000000 L 447.271930 1.000000 L 446.175439 1.000000 L 445.078947 1.000000 L 443.982456 1.000000 L 442.885965 1.000000 L 441.789474 1.000000 L 440.692982 1.000000 L 439.596491 1.000000 L 438.500000 1.000000 L 437.403509 1.000000 L 436.307018 1.000000 L 435.210526 1.000000 L 434.114035 1.000000 L 433.017544 1.000000 L 431.921053 1.000000 L 430.824561 1.000000 L 429.728070 1.000000 L 428.631579 1.000000 L 427.535088 1.000000 L 426.438596 1.000000 L 425.342105 1.000000 L 424.245614 1.000000 L 423.149123 1.000000 L 422.052632 1.000000 L 420.956140 1.000000 L 419.859649 1.000000 L 418.763158 1.000000 L 417.666667 1.000000 L 416.570175 1.000000 L 415.473684 1.000000 L 414.377193 1.000000 L 413.280702 1.000000 L 412.184211 1.000000 L 411.087719 1.000000 L 409.991228 1.000000 L 408.894737 1.000000 L 407.798246 1.000000 L 406.701754 1.000000 L 405.605263 1.000000 L 404.508772 1.000000 L 403.412281 1.000000 L 402.315789 1.000000 L 401.219298 1.000000 L 400.122807 1.000000 L 399.026316 1.000000 L 397.929825 1.000000 L 396.833333 1.000000 L 395.736842 1.000000 L 394.640351 1.000000 L 393.543860 1.000000 L 392.447368 1.000000 L 391.350877 1.000000 L 390.254386 1.000000 L 389.157895 1.000000 L 388.061404 1.000000 L 386.964912 1.000000 L 385.868421 1.000000 L 384.771930 1.000000 L 383.675439 1.000000 L 382.578947 1.000000 L 381.482456 1.000000 L 380.385965 1.000000 L 379.289474 1.000000 L 378.192982 1.000000 L 377.096491 1.000000 L 376.000000 1.000000 L 374.903509 1.000000 L 373.807018 1.000000 L 372.710526 1.000000 L 371.614035 1.000000 L 370.517544 1.000000 L 369.421053 1.000000 L 368.324561 1.000000 L 367.228070 1.000000 L 366.131579 1.000000 L 365.035088 1.000000 L 363.938596 1.000000 L 362.842105 1.000000 L 361.745614 1.000000 L 360.649123 1.000000 L 359.552632 1.000000 L 358.456140 1.000000 L 357.359649 1.000000 L 356.263158 1.000000 L 355.166667 1.000000 L 354.070175 1.000000 L 352.973684 1.000000 L 351.877193 1.000000 L 350.780702 1.000000 L 349.684211 1.000000 L 348.587719 1.000000 L 347.491228 1.000000 L 346.394737 1.000000 L 345.298246 1.000000 L 344.201754 1.000000 L 343.105263 1.000000 L 342.008772 1.000000 L 340.912281 1.000000 L 339.815789 1.000000 L 338.719298 1.000000 L 337.622807 1.000000 L 336.526316 1.000000 L 335.429825 1.000000 L 334.333333 1.000000 L 333.236842 1.000000 L 332.140351 1.000000 L 331.043860 1.000000 L 329.947368 1.000000 L 328.850877 1.000000 L 327.754386 1.000000 L 326.657895 1.000000 L 325.561404 1.000000 L 324.464912 1.000000 L 323.368421 1.000000 L 322.271930 1.000000 L 321.175439 1.000000 L 320.078947 1.000000 L 318.982456 1.000000 L 317.885965 1.000000 L 316.789474 1.000000 L 315.692982 1.000000 L 314.596491 1.000000 L 313.500000 1.000000 L 312.403509 1.000000 L 311.307018 1.000000 L 310.210526 1.000000 L 309.114035 1.000000 L 308.017544 1.000000 L 306.921053 1.000000 L 305.824561 1.000000 L 304.728070 1.000000 L 303.631579 1.000000 L 302.535088 1.000000 L 301.438596 1.000000 L 300.342105 1.000000 L 299.245614 1.000000 L 298.149123 1.000000 L 297.052632 1.000000 L 295.956140 1.000000 L 294.859649 1.000000 L 293.763158 1.000000 L 292.666667 1.000000 L 291.570175 1.000000 L 290.473684 1.000000 L 289.377193 1.000000 L 288.280702 1.000000 L 287.184211 1.000000 L 286.087719 1.000000 L 284.991228 1.000000 L 283.894737 1.000000 L 282.798246 1.000000 L 281.701754 1.000000 L 280.605263 1.000000 L 279.508772 1.000000 L 278.412281 1.000000 L 277.315789 1.000000 L 276.219298 1.000000 L 275.122807 1.000000 L 274.026316 1.000000 L 272.929825 1.000000 L 271.833333 1.000000 L 270.736842 1.000000 L 269.640351 1.000000 L 268.543860 1.000000 L 267.447368 1.000000 L 266.350877 1.000000 L 265.254386 1.000000 L 264.157895 1.000000 L 263.061404 1.000000 L 261.964912 1.000000 L 260.868421 1.000000 L 259.771930 1.000000 L 258.675439 1.000000 L 257.578947 1.000000 L 256.482456 1.000000 L 255.385965 1.000000 L 254.289474 1.000000 L 253.192982 1.000000 L 252.096491 1.000000 L 251.000000 1.000000 L 249.903509 1.000000 L 248.807018 1.000000 L 247.710526 1.000000 L 246.614035 1.000000 L 245.517544 1.000000 L 244.421053 1.000000 L 243.324561 1.000000 L 242.228070 1.000000 L 241.131579 1.000000 L 240.035088 1.000000 L 238.938596 1.000000 L 237.842105 1.000000 L 236.745614 1.000000 L 235.649123 1.000000 L 234.552632 1.000000 L 233.456140 1.000000 L 232.359649 1.000000 L 231.263158 1.000000 L 230.166667 1.000000 L 229.070175 1.000000 L 227.973684 1.000000 L 226.877193 1.000000 L 225.780702 1.000000 L 224.684211 1.000000 L 223.587719 1.000000 L 222.491228 1.000000 L 221.394737 1.000000 L 220.298246 1.000000 L 219.201754 1.000000 L 218.105263 1.000000 L 217.008772 1.000000 L 215.912281 1.000000 L 214.815789 1.000000 L 213.719298 1.000000 L 212.622807 1.000000 L 211.526316 1.000000 L 210.429825 1.000000 L 209.333333 1.000000 L 208.236842 1.000000 L 207.140351 1.000000 L 206.043860 1.000000 L 204.947368 1.000000 L 203.850877 1.000000 L 202.754386 1.000000 L 201.657895 1.000000 L 200.561404 1.000000 L 199.464912 1.000000 L 198.368421 1.000000 L 197.271930 1.000000 L 196.175439 1.000000 L 195.078947 1.000000 L 193.982456 1.000000 L 192.885965 1.000000 L 191.789474 1.000000 L 190.692982 1.000000 L 189.596491 1.000000 L 188.500000 1.000000 L 187.403509 1.000000 L 186.307018 1.000000 L 185.210526 1.000000 L 184.114035 1.000000 L 183.017544 1.000000 L 181.921053 1.000000 L 180.824561 1.000000 L 179.728070 1.000000 L 178.631579 1.000000 L 177.535088 1.000000 L 176.438596 1.000000 L 175.342105 1.000000 L 174.245614 1.000000 L 173.149123 1.000000 L 172.052632 1.000000 L 170.956140 1.000000 L 169.859649 1.000000 L 168.763158 1.000000 L 167.666667 1.000000 L 166.570175 1.000000 L 165.473684 1.000000 L 164.377193 1.000000 L 163.280702 1.000000 L 162.184211 1.000000 L 161.087719 1.000000 L 159.991228 1.000000 L 158.894737 1.000000 L 157.798246 1.000000 L 156.701754 1.000000 L 155.605263 1.000000 L 154.508772 1.000000 L 153.412281 1.000000 L 152.315789 1.000000 L 151.219298 1.000000 L 150.122807 1.000000 L 149.026316 1.000000 L 147.929825 1.000000 L 146.833333 1.000000 L 145.736842 1.000000 L 144.640351 1.000000 L 143.543860 1.000000 L 142.447368 1.000000 L 141.350877 1.000000 L 140.254386 1.000000 L 139.157895 1.000000 L 138.061404 1.000000 L 136.964912 1.000000 L 135.868421 1.000000 L 134.771930 1.000000 L 133.675439 1.000000 L 132.578947 1.000000 L 131.482456 1.000000 L 130.385965 1.000000 L 129.289474 1.000000 L 128.192982 1.000000 L 127.096491 1.000000 L 126.000000 1.000000 L 124.903509 1.000000 L 123.807018 1.000000 L 122.710526 1.000000 L 121.614035 1.000000 L 120.517544 1.000000 L 119.421053 1.000000 L 118.324561 1.000000 L 117.228070 1.000000 L 116.131579 1.000000 L 115.035088 1.000000 L 113.938596 1.000000 L 112.842105 1.000000 L 111.745614 1.000000 L 110.649123 1.000000 L 109.552632 1.000000 L 108.456140 1.000000 L 107.359649 1.000000 L 106.263158 1.000000 L 105.166667 1.000000 L 104.070175 1.000000 L 102.973684 1.000000 L 101.877193 1.000000 L 100.780702 1.000000 L 99.684211 1.000000 L 98.587719 1.000000 L 97.491228 1.000000 L 96.394737 1.000000 L 95.298246 1.000000 L 94.201754 1.000000 L 93.105263 1.000000 L 92.008772 1.000000 L 90.912281 1.000000 L 89.815789 1.000000 L 88.719298 1.000000 L 87.622807 1.000000 L 86.526316 1.000000 L 85.429825 1.000000 L 84.333333 1.000000 L 83.236842 1.000000 L 82.140351 1.000000 L 81.043860 1.000000 L 79.947368 1.000000 L 78.850877 1.000000 L 77.754386 1.000000 L 76.657895 1.000000 L 75.561404 1.000000 L 74.464912 1.000000 L 73.368421 1.000000 L 72.271930 1.000000 L 71.175439 1.000000 L 70.078947 1.000000 L 68.982456 1.000000 L 67.885965 1.000000 L 66.789474 1.000000 L 65.692982 1.000000 L 64.596491 1.000000 L 63.500000 1.000000 L 62.403509 1.000000 L 61.307018 1.000000 L 60.210526 1.000000 L 59.114035 1.000000 L 58.017544 1.000000 L 56.921053 1.000000 L 55.824561 1.000000 L 54.728070 1.000000 L 53.631579 1.000000 L 52.535088 1.000000 L 51.438596 1.000000 L 50.342105 1.000000 L 49.245614 1.000000 L 48.149123 1.000000 L 47.052632 1.000000 L 45.956140 1.000000 L 44.859649 1.000000 L 43.763158 1.000000 L 42.666667 1.000000 L 41.570175 1.000000 L 40.473684 1.000000 L 39.377193 1.000000 L 38.280702 1.000000 L 37.184211 1.000000 L 36.087719 1.000000 L 34.991228 1.000000 L 33.894737 1.000000 L 32.798246 1.000000 L 31.701754 1.000000 L 30.605263 1.000000 L 29.508772 1.000000 L 28.412281 1.000000 L 27.315789 1.000000 L 26.219298 1.000000 L 25.122807 1.000000 L 24.026316 1.000000 L 22.929825 1.000000 L 21.833333 1.000000 L 20.736842 1.000000 L 19.640351 1.000000 L 18.543860 1.000000 L 17.447368 1.000000 L 16.350877 1.000000 L 15.254386 1.000000 L 14.157895 1.000000 L 13.061404 1.000000 L 11.964912 1.000000 L 10.868421 1.000000 L 9.771930 1.000000 L 8.675439 1.000000 L 7.578947 1.000000 L 7.578947 2.096491 L 6.482456 2.096491 L 5.385965 2.096491 L 5.385965 3.192982 L 4.289474 3.192982 L 4.289474 4.289474 L 3.192982 4.289474 L 3.192982 5.385965 L 3.192982 6.482456 L 2.096491 6.482456 L 2.096491 7.578947 L 2.096491 8.675439 L 2.096491 9.771930 L 2.096491 10.868421 L 2.096491 11.964912 L 2.096491 13.061404 L 2.096491 14.157895 L 1.000000 14.157895 L 1.000000 15.254386 L 1.000000 16.350877 L 2.096491 16.350877 L 2.096491 17.447368 L 2.096491 18.543860 L 2.096491 19.640351 L 2.096491 20.736842 L 2.096491 21.833333 L 2.096491 22.929825 L 2.096491 24.026316 L 2.096491 25.122807 L 2.096491 26.219298 L 2.096491 27.315789 L 2.096491 28.412281 L 2.096491 29.508772 L 2.096491 30.605263 L 2.096491 31.701754 L 2.096491 32.798246 L 2.096491 33.894737 L 2.096491 34.991228 L 2.096491 36.087719 L 2.096491 37.184211 L 2.096491 38.280702 L 2.096491 39.377193 L 2.096491 40.473684 L 2.096491 41.570175 L 2.096491 42.666667 L 2.096491 43.763158 L 2.096491 44.859649 L 2.096491 45.956140 L 2.096491 47.052632 L 2.096491 48.149123 L 2.096491 49.245614 L 2.096491 50.342105 L 2.096491 51.438596 L 2.096491 52.535088 L 2.096491 53.631579 L 2.096491 54.728070 L 2.096491 55.824561 L 2.096491 56.921053 L 2.096491 58.017544 L 2.096491 59.114035 L 2.096491 60.210526 L 2.096491 61.307018 L 2.096491 62.403509 L 2.096491 63.500000 L 2.096491 64.596491 L 2.096491 65.692982 L 2.096491 66.789474 L 2.096491 67.885965 L 2.096491 68.982456 L 2.096491 70.078947 L 2.096491 71.175439 L 2.096491 72.271930 L 2.096491 73.368421 L 2.096491 74.464912 L 2.096491 75.561404 L 2.096491 76.657895 L 2.096491 77.754386 L 2.096491 78.850877 L 2.096491 79.947368 L 2.096491 81.043860 L 2.096491 82.140351 L 2.096491 83.236842 L 2.096491 84.333333 L 2.096491 85.429825 L 2.096491 86.526316 L 2.096491 87.622807 L 2.096491 88.719298 L 2.096491 89.815789 L 2.096491 90.912281 L 2.096491 92.008772 L 2.096491 93.105263 L 2.096491 94.201754 L 2.096491 95.298246 L 2.096491 96.394737 L 2.096491 97.491228 L 3.192982 97.491228 L 3.192982 98.587719 L 3.192982 99.684211 L 3.192982 100.780702 L 3.192982 101.877193 L 3.192982 102.973684 L 3.192982 104.070175 L 3.192982 105.166667 L 3.192982 106.263158 L 3.192982 107.359649 L 3.192982 108.456140 L 3.192982 109.552632 L 3.192982 110.649123 L 3.192982 111.745614 L 3.192982 112.842105 L 3.192982 113.938596 L 3.192982 115.035088 L 3.192982 116.131579 L 3.192982 117.228070 L 3.192982 118.324561 L 3.192982 119.421053 L 3.192982 120.517544 L 3.192982 121.614035 L 3.192982 122.710526 L 2.096491 122.710526 L 2.096491 123.807018 L 2.096491 124.903509 L 2.096491 126.000000 L 2.096491 127.096491 L 2.096491 128.192982 L 2.096491 129.289474 L 2.096491 130.385965 L 2.096491 131.482456 L 2.096491 132.578947 L 2.096491 133.675439 L 2.096491 134.771930 L 2.096491 135.868421 L 2.096491 136.964912 L 2.096491 138.061404 L 2.096491 139.157895 L 1.000000 139.157895 L 1.000000 140.254386 L 2.096491 140.254386 L 2.096491 141.350877 L 2.096491 142.447368 L 2.096491 143.543860 L 2.096491 144.640351 L 2.096491 145.736842 L 2.096491 146.833333 L 2.096491 147.929825 L 2.096491 149.026316 L 2.096491 150.122807 L 2.096491 151.219298 L 2.096491 152.315789 L 2.096491 153.412281 L 2.096491 154.508772 L 2.096491 155.605263 L 2.096491 156.701754 L 2.096491 157.798246 L 2.096491 158.894737 L 2.096491 159.991228 L 2.096491 161.087719 L 2.096491 162.184211 L 2.096491 163.280702 L 2.096491 164.377193 L 2.096491 165.473684 L 2.096491 166.570175 L 2.096491 167.666667 L 2.096491 168.763158 L 2.096491 169.859649 L 2.096491 170.956140 L 2.096491 172.052632 L 2.096491 173.149123 L 2.096491 174.245614 L 2.096491 175.342105 L 2.096491 176.438596 L 2.096491 177.535088 L 2.096491 178.631579 L 2.096491 179.728070 L 2.096491 180.824561 L 2.096491 181.921053 L 2.096491 183.017544 L 2.096491 184.114035 L 2.096491 185.210526 L 2.096491 186.307018 L 2.096491 187.403509 L 2.096491 188.500000 L 2.096491 189.596491 L 2.096491 190.692982 L 2.096491 191.789474 L 2.096491 192.885965 L 2.096491 193.982456 L 2.096491 195.078947 L 2.096491 196.175439 L 2.096491 197.271930 L 2.096491 198.368421 L 2.096491 199.464912 L 2.096491 200.561404 L 2.096491 201.657895 L 2.096491 202.754386 L 2.096491 203.850877 L 2.096491 204.947368 L 2.096491 206.043860 L 2.096491 207.140351 L 2.096491 208.236842 L 2.096491 209.333333 L 2.096491 210.429825 L 2.096491 211.526316 L 2.096491 212.622807 L 2.096491 213.719298 L 2.096491 214.815789 L 2.096491 215.912281 L 2.096491 217.008772 L 2.096491 218.105263 L 2.096491 219.201754 L 2.096491 220.298246 L 2.096491 221.394737 L 2.096491 222.491228 L 2.096491 223.587719 L 2.096491 224.684211 L 1.000000 224.684211 L 1.000000 225.780702 L 1.000000 226.877193 L 1.000000 227.973684 L 1.000000 229.070175 L 2.096491 229.070175 L 2.096491 230.166667 L 2.096491 231.263158 L 2.096491 232.359649 L 2.096491 233.456140 L 2.096491 234.552632 L 3.192982 234.552632 L 3.192982 235.649123 L 3.192982 236.745614 L 4.289474 236.745614 L 4.289474 237.842105 L 5.385965 237.842105 L 5.385965 238.938596 L 6.482456 238.938596 L 7.578947 238.938596 L 8.675439 238.938596 Z" transform="scale(0.8,0.8) translate(34.087719,27.508772)" fill="' . $color6 . '"></path>';
		$source .= '<path d="M 1.000000 31.701754 L 454.947368 31.701754 L 454.947368 1.000000 L 1.000000 1.000000 Z" transform="scale(0.8,0.8) translate(45.052632,121.807018)" fill="' . $color1 . '"></path>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Litmus icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @param string $color2 Optional. Color 2 of the icon.
	 * @param string $color3 Optional. Color 3 of the icon.
	 * @param string $color4 Optional. Color 4 of the icon.
	 * @param string $color5 Optional. Color 5 of the icon.
	 * @param string $color6 Optional. Color 6 of the icon.
	 * @param string $color7 Optional. Color 7 of the icon.
	 * @param string $color8 Optional. Color 8 of the icon.
	 * @param string $color9 Optional. Color 9 of the icon.
	 * @param string $color10 Optional. Color 10 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 2.5.0
	 */
	private function get_base64_litmus_icon( $color1 = '#6A7A55', $color2 = '#A09440', $color3 = '#AFA148', $color4 = '#4C5C59', $color5 = '#48555D', $color6 = '#E3B23B', $color7 = '#903024', $color8 = '#D65841', $color9 = '#E6703E', $color10 = '#F4A83A' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="500" height="500" viewBox="0 0 500 500" preserveAspectRatio="xMidYMid">';
		$source .= '<g transform="scale(1.5,1.5) translate(38,38)">';
		$source .= '<path d="M0 127.94c0 12.487 1.799 24.552 5.133 35.961l70.62-23.131a53.012 53.012 0 0 1 .465-25.778L5.095 92.109A127.964 127.964 0 0 0 0 127.94" fill="' . $color1 . '"/>';
		$source .= '<path d="M93.597 87.948L48.946 27.277C30.192 42.024 15.664 61.898 7.466 84.785l71.289 22.937a53.225 53.225 0 0 1 14.842-19.774" fill="' . $color2 . '"/>';
		$source .= '<path d="M124.211 75.836V0C98.583.745 74.848 9.027 55.136 22.7l44.725 60.772a52.746 52.746 0 0 1 24.35-7.636" fill="' . $color3 . '"/>';
		$source .= '<path d="M78.044 148.12L7.512 171.222c8.316 23.145 23.104 43.203 42.191 57.981l43.531-59.892a53.18 53.18 0 0 1-15.19-21.191" fill="' . $color4 . '"/>';
		$source .= '<path d="M55.934 233.725c19.554 13.346 42.993 21.419 68.277 22.155v-74.152a52.752 52.752 0 0 1-24.752-7.888l-43.525 59.885" fill="' . $color5 . '"/>';
		$source .= '<path d="M200.148 22.212C180.609 8.854 157.182.763 131.909.005v75.926a52.764 52.764 0 0 1 23.527 7.798l44.712-61.517" fill="' . $color6 . '"/>';
		$source .= '<path d="M131.909 181.633v74.242c25.471-.764 49.068-8.976 68.699-22.522l-44.245-60.119a52.75 52.75 0 0 1-24.454 8.399" fill="' . $color7 . '"/>';
		$source .= '<path d="M162.495 168.577l44.317 60.217c19.033-14.892 33.734-35.058 41.928-58.294l-71.649-23.052a53.147 53.147 0 0 1-14.596 21.129" fill="' . $color8 . '"/>';
		$source .= '<path d="M256 127.94c0-12.642-1.84-24.852-5.254-36.386l-72.044 23.597a52.997 52.997 0 0 1 .579 24.914l71.796 23.1A128.022 128.022 0 0 0 256 127.94" fill="' . $color9 . '"/>';
		$source .= '<path d="M176.188 107.874l72.154-23.634c-8.332-22.937-23.032-42.818-41.967-57.501l-44.718 61.526a53.203 53.203 0 0 1 14.531 19.609" fill="' . $color10 . '"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Email On Acid icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 2.5.0
	 */
	private function get_base64_eoa_icon( $color1 = '#a2cd39' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="500" height="500" viewBox="0 0 500 500" preserveAspectRatio="xMidYMid">';
		$source .= '<g transform="scale(0.84,-0.84) translate(60,-550)">';
		$source .= '<path d="M 6.127030 102.250000 C 31.456193 102.250000 32.790391 43.771132 33.627030 27.875000 C 33.939354 21.940845 36.127030 16.329227 36.127030 7.250000 L 32.377030 7.250000 C 32.377030 4.809441 32.618703 2.370837 29.877030 1.000000 L 31.127030 6.000000 L 26.127030 6.000000 L 17.377030 38.500000 L 14.877030 38.500000 C 12.140752 53.160805 -0.968609 80.166380 1.252037 92.750000 C 1.832681 96.040359 3.760161 97.516263 6.127030 102.250000 Z" transform="scale(1,1) translate(178.872970,395.250000)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 1.000000 67.250000 L 3.500000 67.250000 L 12.250000 34.750000 L 17.250000 34.750000 L 16.000000 29.750000 C 18.741674 31.120837 18.500000 33.559441 18.500000 36.000000 L 22.250000 36.000000 L 24.750000 12.250000 C 18.409353 21.760971 18.541134 7.118370 19.750000 1.000000 Z" transform="scale(1,1) translate(192.750000,366.500000)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 24.750000 23.500000 L 22.250000 23.500000 L 22.250000 14.750000 L 13.500000 14.750000 C 13.500000 12.670286 14.319676 11.000000 17.250000 11.000000 L 11.000000 1.000000 L 4.750000 1.000000 L 3.500000 8.500000 C 5.258261 11.402298 6.138661 13.104560 9.750000 14.750000 L 9.750000 17.250000 L 1.000000 18.500000 L 1.000000 21.000000 C 8.231914 25.821276 16.122827 24.868859 24.750000 23.500000 Z" transform="scale(1,1) translate(267.750000,387.750000)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 275.985371 409.947699 L 278.485371 409.947699 C 294.671398 393.235096 264.870910 360.678125 281.485363 329.822691 C 283.741773 325.632237 286.972618 320.704075 293.485371 317.447699 L 293.485371 314.947699 C 301.179936 314.947699 306.115698 315.032808 311.110378 316.697691 C 319.590754 319.524493 359.620821 336.140424 369.735371 341.197699 C 373.393631 343.617427 377.366707 343.904699 379.735371 344.322699 C 383.231199 344.939610 386.610344 346.285936 390.235363 346.072701 C 401.641036 345.401777 415.603699 336.743224 413.235355 323.322695 C 409.937574 304.635187 388.181692 305.657440 377.235371 298.697699 L 378.485371 304.947699 L 343.485371 292.447699 L 343.485371 288.697699 L 337.235371 289.947699 L 379.735371 306.197699 L 379.735371 308.697699 C 366.782406 308.697699 346.503967 299.505789 337.110363 294.447706 C 334.183324 292.871597 332.316843 290.008803 328.485371 286.197699 L 337.235371 289.947699 L 337.235371 287.447699 L 328.485371 284.947699 L 328.485371 282.447699 L 333.485371 282.447699 C 330.443353 277.884672 327.178051 275.823391 325.360371 272.447699 C 317.359954 257.589782 335.848223 250.014313 342.235371 238.697699 C 323.858287 238.697699 321.224494 232.567260 308.485371 226.197699 C 294.578557 212.466737 304.122599 191.642239 315.360371 182.447699 C 342.186004 160.499453 389.384385 156.973903 410.985371 130.572699 C 434.413599 101.938197 422.297353 81.256644 389.735371 88.697699 L 387.235371 94.947699 C 379.540367 96.871450 370.073354 109.069212 362.235371 113.697699 L 362.235371 111.197699 C 357.889341 118.639757 329.992487 151.385058 324.110371 156.197699 C 319.472656 159.992193 314.655901 163.596259 309.360386 166.447714 C 288.195277 177.844288 280.768278 154.675475 288.485371 141.197699 C 273.806842 145.967164 267.262963 155.273712 249.985367 158.322714 C 239.822080 160.116221 229.593278 158.284649 220.610367 153.447683 C 186.781483 135.232144 183.161798 94.278407 174.735371 60.572699 C 167.927337 33.340565 154.523923 1.197699 121.610371 1.197699 C 116.540603 1.197699 110.254182 0.197185 105.610367 2.697714 C 99.303029 6.093959 96.966733 14.240442 96.235367 20.822714 C 90.637297 71.205358 150.878541 82.502210 164.485375 123.322714 C 167.984332 133.819583 170.935330 158.967192 163.485371 168.072699 C 154.669171 178.848054 132.807151 175.676411 119.735371 169.947699 C 93.465753 163.380294 54.891031 128.467815 29.985372 124.072683 C 25.229378 123.233406 20.174758 123.697699 12.235371 123.697699 C 5.356708 128.283474 1.934895 134.526984 1.235370 140.822714 C -1.359133 164.173229 17.977626 167.736784 35.485369 173.572691 C 43.693688 176.308805 52.083760 175.840062 60.485369 177.322691 C 64.503309 178.031747 68.037753 180.126060 71.985375 180.822695 C 86.209789 183.332890 119.215381 187.107003 126.735369 201.072691 C 128.899148 205.091142 128.485371 209.931597 128.485371 214.322699 C 128.485371 223.279560 126.250038 230.356153 119.110371 236.197699 C 109.426565 244.120812 96.550230 244.524054 85.985371 251.197699 L 85.985371 253.697699 C 94.031703 253.697699 112.795458 258.662745 117.735373 261.322706 C 125.382550 265.440411 133.301904 268.149862 140.235373 273.822706 C 145.552122 278.172768 145.985371 284.151941 145.985371 290.572699 C 145.985371 334.341526 70.862886 332.527957 50.985371 356.822699 C 42.496033 367.198555 37.244228 379.696292 44.110371 392.447699 C 45.425839 394.890712 46.908256 398.145968 49.110371 399.947699 C 90.766586 434.030057 119.900134 361.621321 139.985367 337.072695 C 151.438083 323.074939 170.301812 303.863801 180.985371 333.697699 C 184.872711 331.040738 188.475433 329.035565 191.360375 329.447706 C 200.939164 330.816098 198.485371 356.181877 198.485371 366.197699 L 199.735371 357.447699 L 202.235371 361.197699 L 205.985371 361.197699 L 203.485371 377.447699 C 207.492373 371.437196 206.871651 363.413859 208.610378 358.197697 C 211.492497 349.551321 215.388285 341.230462 220.985371 331.197699 L 220.985371 328.697699 C 229.101950 330.726843 230.581074 335.627972 234.735371 342.447699 C 244.173901 344.072993 241.829595 365.337138 239.735371 373.697699 C 241.526408 376.029774 244.922064 373.511006 247.235371 372.447699 L 244.735371 381.197699 L 245.985371 382.447699 L 249.735371 382.447699 L 249.735371 386.197699 L 257.235371 394.947699 L 258.485371 387.447699 L 264.735371 387.447699 L 270.985371 397.447699 C 268.055047 397.447699 267.235371 399.117984 267.235371 401.197699 L 275.985371 401.197699 Z M 253.485371 314.947699 L 207.860371 292.447699 L 162.235371 269.947699 C 156.399463 266.820768 140.919815 262.315477 144.735371 254.947699 L 144.735371 251.197699 L 140.985371 251.197699 C 143.537967 246.139843 144.221800 241.883616 145.735375 239.072703 C 148.340122 234.235303 158.617930 209.969739 162.985378 209.572695 C 165.911500 209.306687 168.719773 211.045562 171.610371 211.197699 C 184.699079 211.886578 183.876675 211.295525 198.485371 214.947699 L 205.985371 231.197699 L 208.485371 231.197699 L 203.485371 214.947699 L 204.735371 213.697699 C 215.554528 213.697699 220.601231 218.863350 228.110367 219.697703 C 248.474649 221.960396 235.355448 204.632737 290.985371 231.197699 L 304.735371 236.197699 C 305.058381 243.297266 304.387679 248.143083 300.985371 253.697699 L 270.985371 312.447699 L 267.235371 313.697699 L 265.985371 312.447699 L 263.485371 306.197699 L 259.735371 307.447699 L 263.485371 316.197699 Z" transform="scale(1,1) translate(14.014629,1.302301)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 34.750000 79.750000 L 34.750000 77.250000 C 25.800636 68.807870 19.750000 63.869031 19.750000 46.000000 C 21.844224 37.639439 24.188530 16.375294 14.750000 14.750000 C 10.595703 7.930273 9.116579 3.029145 1.000000 1.000000 L 1.000000 3.500000 C 29.178642 16.339321 6.318993 65.534496 34.750000 79.750000 Z" transform="scale(1,1) translate(234.000000,329.000000)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 16.000000 33.500000 C 11.621899 26.932849 7.743313 22.965918 11.000000 14.750000 L 11.000000 11.000000 L 7.250000 11.000000 L 6.000000 9.750000 L 8.500000 1.000000 C 6.186693 2.063307 2.791038 4.582075 1.000000 2.250000 C 1.000000 20.119031 7.050636 25.057870 16.000000 33.500000 Z" transform="scale(1,1) translate(252.750000,372.750000)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 31.000000 28.163034 C 32.177578 23.452724 32.180321 20.519458 31.000000 15.663034 L 31.000000 19.413034 L 29.750000 19.413034 L 29.750000 15.663034 C 24.788581 19.223026 19.340298 19.843630 14.750000 14.413034 C 13.068645 11.379349 11.728434 10.109420 7.250000 9.413034 L 12.250000 4.413034 L 7.250000 4.413034 L 7.250000 1.913034 L 16.000000 1.913034 C 10.937143 0.658545 6.467834 0.375137 1.000000 3.163034 C 5.902846 15.468726 15.573688 28.163034 31.000000 28.163034 Z" transform="scale(1,1) translate(456.500000,361.836966)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 6.653128 21.000000 L 9.153128 4.750000 L 5.403128 4.750000 L 2.903128 1.000000 L 1.653128 9.750000 C 0.444262 15.868370 0.312480 30.510971 6.653128 21.000000 Z" transform="scale(1,1) translate(210.846872,357.750000)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 23.500000 14.750000 L 23.500000 18.500000 L 24.750000 18.500000 L 24.750000 14.750000 C 23.365725 9.380743 21.482862 7.546721 19.293762 6.050144 C 17.104636 4.553577 14.609274 3.394455 9.750000 1.000000 L 1.000000 1.000000 L 1.000000 3.500000 L 6.000000 3.500000 L 1.000000 8.500000 C 5.478434 9.196386 6.818645 10.466314 8.500000 13.500000 C 13.090298 18.930595 18.538581 18.309992 23.500000 14.750000 Z" transform="scale(1,1) translate(462.750000,362.750000)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 6.000000 13.500000 L 18.500000 12.250000 C 14.569999 6.354999 8.786499 2.611529 1.000000 1.000000 C 1.000000 6.202938 2.565211 8.846732 6.000000 13.500000 Z" transform="scale(1,1) translate(434.000000,351.500000)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 26.000000 51.000000 L 29.750000 49.750000 L 6.000000 2.250000 L 3.500000 4.750000 L 3.500000 1.000000 L 1.000000 1.000000 Z" transform="scale(1,1) translate(247.750000,257.750000)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 51.000000 23.500000 L 49.750000 17.250000 C 34.034601 13.321150 20.653375 8.326687 6.000000 1.000000 L 1.000000 1.000000 L 1.000000 3.500000 L 9.750000 6.000000 L 9.750000 8.500000 L 16.000000 7.250000 L 16.000000 11.000000 Z" transform="scale(1,1) translate(341.500000,282.750000)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 59.750000 24.750000 L 62.250000 22.250000 C 54.405576 10.124101 51.791568 1.000000 34.750000 1.000000 L 6.000000 7.250000 L 1.000000 11.000000 L 4.750000 11.000000 C 11.139859 9.706294 15.051950 9.880538 18.625008 9.250008 C 35.038753 6.353455 46.070877 4.231315 57.250000 21.000000 L 59.750000 21.000000 Z" transform="scale(1,1) translate(191.500000,237.750000)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 1.000000 8.500000 L 32.250000 3.500000 L 31.000000 1.000000 C 26.215543 2.234604 23.146091 3.748378 19.874998 3.999992 C 15.148634 4.363567 10.391257 1.000000 2.250000 1.000000 L 1.000000 6.000000 Z" transform="scale(1,1) translate(54.000000,251.500000)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 1.000000 9.583158 L 2.250000 4.583158 C 10.391257 4.583158 15.148634 7.946724 19.874998 7.583150 C 23.146091 7.331535 26.215543 5.817762 31.000000 4.583158 C 23.358971 -0.510862 2.796118 -1.254224 1.000000 9.583158 Z" transform="scale(1,1) translate(54.000000,247.916842)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 1.000000 12.250000 L 38.500000 4.750000 L 34.750000 4.750000 L 39.750000 1.000000 L 1.000000 8.500000 Z" transform="scale(1,1) translate(157.750000,244.000000)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 56.000000 23.500000 C 59.402308 17.945384 60.073010 13.099567 59.750000 6.000000 L 46.000000 1.000000 L 46.000000 3.500000 L 56.000000 6.000000 L 56.000000 8.500000 L 1.000000 17.250000 L 1.000000 21.000000 L 57.250000 12.250000 L 58.500000 13.500000 Z" transform="scale(1,1) translate(259.000000,231.500000)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 1.000000 27.981168 C 4.242422 26.061256 6.163116 25.177254 7.625004 23.981152 C 29.465181 6.111928 16.294579 -6.940909 8.999992 6.606160 C 5.942976 12.283499 4.942644 18.845879 1.000000 27.981168 Z" transform="scale(1,1) translate(304.000000,109.518832)" fill="' . $color1 . '"></path>';
		$source .= '<path d="M 1.000000 26.000000 C 8.837983 21.371513 18.304996 9.173751 26.000000 7.250000 L 28.500000 1.000000 C 23.572788 4.530061 19.099966 6.134118 15.749985 8.874985 C 11.025768 12.740280 7.100604 17.987047 1.000000 23.500000 Z" transform="scale(1,1) translate(375.250000,89.000000)" fill="' . $color1 . '"></path>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Litmus icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @param string $color2 Optional. Color 2 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 2.5.0
	 */
	private function get_base64_mailercheck_icon( $color1 = '#47AE7D', $color2 = '#5ED69D' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="500" height="500" viewBox="0 0 500 500" preserveAspectRatio="xMidYMid">';
		$source .= '<g transform="scale(13,13) translate(3,4)">';
		$source .= '<path d="M5.73338394,12.289647 L12.9220887,12.2811531 L15.8354142,16.34256 C12.7938089,18.6822768 10.9336776,20.0266856 10.2550204,20.3757864 C9.5763632,20.7248872 8.85725395,20.9371537 8.09769265,21.012586 L5.86759381,21.0077206 C2.22189424,20.8833734 1.20024661,17.8426226 1.29733652,16.6285146 C1.26414909,15.1219682 2.40554081,12.4878636 5.73338394,12.289647 Z" id="Rectangle" fill="' . $color1 . '" transform="translate(8.563275, 16.646870) rotate(44.000000) translate(-8.563275, -16.646870) "></path>';
		$source .= '<path d="M9.60566159,8.0802605 L27.6828642,8.0802605 C30.2397134,8.10086632 32.4780711,10.1173763 32.4901961,12.9322082 L32.5505842,16.8370236 L32.6435176,20.0622137 C31.8518245,17.9867939 30.4638771,16.877343 28.4796753,16.7338611 C27.4418755,16.5713035 21.2226412,16.4310142 9.82197249,16.3129933 C6.7856776,16.3281542 5.44074836,13.801684 5.44240625,12.163585 C5.35084825,10.5345429 6.66555818,8.0264458 9.60566159,8.0802605 Z" id="Rectangle" fill="' . $color2 . '" transform="translate(19.040730, 14.070811) rotate(135.000000) translate(-19.040730, -14.070811) "></path>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Imap icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 2.5.0
	 */
	private function get_base64_imap_icon( $color1 = '#51575D' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="500" height="500" viewBox="0 0 500 500" preserveAspectRatio="xMidYMid">';
		$source .= '<g transform="scale(5,5) translate(0,0)">';
		$source .= '<g transform="matrix(0.075,0,0,0.075,12,14)">';
		$source .= '<path d="M537.9,603.6L537.9,706.5C558,712.4 576.4,723.6 590.9,738.6C605.4,752.8 616.3,770.2 622.5,790.3L952.3,790.3C973.2,790.3 990,806.6 990,828C990,848.6 973.2,865.5 952.3,865.5L623.5,865.5C617.3,886.6 606.4,905.6 590.8,920.4C567.8,944.2 535.7,958.4 499.8,958.4C464.5,958.4 431.8,944.2 408.8,920.4C393.8,905.6 382.6,886.7 376.1,865.5L47.5,865.5C26.6,865.5 10,848.7 10,828C10,806.6 26.6,790.3 47.5,790.3L377.3,790.3C383.7,770.2 394.9,752.8 408.9,738.6C423.6,723.6 441.8,712.4 462.1,706.5L462.1,603.7L537.9,603.6ZM558.8,770.2C543.8,755.2 523.5,745.8 499.9,745.8C476.9,745.8 456,755.2 441,770.2C426,785.5 416.4,806.1 416.4,829.4C416.4,852.7 426,873.6 441,888.5C456,903.5 476.9,912.9 499.9,912.9C523.5,912.9 543.8,903.5 558.8,888.5C574.3,873.5 583.4,852.7 583.4,829.4C583.4,806.1 574.3,785.5 558.8,770.2Z" fill="' . $color1 . '"/>';
		$source .= '<g transform="matrix(0.465104,0,0,0.360754,81.0077,3.48146)">';
		$source .= '<path d="M1792,710L1792,1504C1792,1548 1776.33,1585.67 1745,1617C1713.67,1648.33 1676,1664 1632,1664L160,1664C116,1664 78.333,1648.33 47,1617C15.667,1585.67 0,1548 0,1504L0,710C29.333,742.667 63,771.667 101,797C342.333,961 508,1076 598,1142C636,1170 666.833,1191.83 690.5,1207.5C714.167,1223.17 745.667,1239.17 785,1255.5C824.333,1271.83 861,1280 895,1280L897,1280C931,1280 967.667,1271.83 1007,1255.5C1046.33,1239.17 1077.83,1223.17 1101.5,1207.5C1125.17,1191.83 1156,1170 1194,1142C1307.33,1060 1473.33,945 1692,797C1730,771 1763.33,742 1792,710ZM1792,416C1792,468.667 1775.67,519 1743,567C1710.33,615 1669.67,656 1621,690C1370.33,864 1214.33,972.333 1153,1015C1146.33,1019.67 1132.17,1029.83 1110.5,1045.5C1088.83,1061.17 1070.83,1073.83 1056.5,1083.5C1042.17,1093.17 1024.83,1104 1004.5,1116C984.167,1128 965,1137 947,1143C929,1149 912.333,1152 897,1152L895,1152C879.667,1152 863,1149 845,1143C827,1137 807.833,1128 787.5,1116C767.167,1104 749.833,1093.17 735.5,1083.5C721.167,1073.83 703.167,1061.17 681.5,1045.5C659.833,1029.83 645.667,1019.67 639,1015C578.333,972.333 491,911.5 377,832.5C263,753.5 194.667,706 172,690C130.667,662 91.667,623.5 55,574.5C18.333,525.5 0,480 0,438C0,386 13.833,342.667 41.5,308C69.167,273.333 108.667,256 160,256L1632,256C1675.33,256 1712.83,271.667 1744.5,303C1776.17,334.333 1792,372 1792,416Z" fill="' . $color1 . '"/>';
		$source .= '</g>';
		$source .= '</g>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Litmus icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @param string $color2 Optional. Color 2 of the icon.
	 * @param string $color3 Optional. Color 3 of the icon.
	 * @param string $color4 Optional. Color 4 of the icon.
	 * @param string $color5 Optional. Color 5 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 2.5.0
	 */
	private function get_base64_gmail_icon( $color1 = '#4285f4', $color2 = '#34a853', $color3 = '#fbbc04', $color4 = '#ea4335', $color5 = '#c5221f' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="500" height="500" viewBox="0 0 500 500" preserveAspectRatio="xMidYMid">';
		$source .= '<g transform="scale(4.4,4.4) translate(-39,-19)">';
		$source .= '<path fill="' . $color1 . '" d="M58 108h14V74L52 59v43c0 3.32 2.69 6 6 6"/>';
		$source .= '<path fill="' . $color2 . '" d="M120 108h14c3.32 0 6-2.69 6-6V59l-20 15"/>';
		$source .= '<path fill="' . $color3 . '" d="M120 48v26l20-15v-8c0-7.42-8.47-11.65-14.4-7.2"/>';
		$source .= '<path fill="' . $color4 . '" d="M72 74V48l24 18 24-18v26L96 92"/>';
		$source .= '<path fill="' . $color5 . '" d="M52 51v8l20 15V48l-5.6-4.2c-5.94-4.45-14.4-.22-14.4 7.2"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Litmus icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @param string $color2 Optional. Color 2 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 2.5.0
	 */
	private function get_base64_ovh_icon( $color1 = '#123F6D', $color2 = '#FFFFFF' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="500" height="500" viewBox="0 0 500 500" preserveAspectRatio="xMidYMid">';
		$source .= '<g transform="scale(0.39,0.39) translate(130,130)">';
		$source .= '<circle fill="' . $color1 . '" cx="512" cy="512" r="512"/>';
		$source .= '<path fill="' . $color2 . '" d="M700.2,466.5l61.2-106.3c23.6,41.6,37.2,89.8,37.2,141.1c0,68.8-24.3,131.9-64.7,181.4H575.8l48.7-84.6h-64.4 l75.8-131.7L700.2,466.5z M644.8,341.3L448.3,682.5l0.1,0.2H290.1c-40.5-49.5-64.7-112.6-64.7-181.4c0-51.4,13.6-99.6,37.3-141.3 l102.5,178.2l113.3-197H644.8z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Gandi icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 2.5.0
	 */
	private function get_base64_gandi_icon( $color1 = '#4FA7A9' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="500" height="500" viewBox="0 0 500 500" preserveAspectRatio="xMidYMid">';
		$source .= '<g transform="scale(2,2) translate(50,26)">';
		$source .= '<path fill="' . $color1 . '" d="M125.657,51.037C120.536,47.412 113.447,48.625 109.823,53.745C105.374,60.035 100.449,64.698 94.844,67.918C94.327,68.215 93.803,68.499 93.274,68.773C92.786,69.026 92.291,69.268 91.793,69.498C86.56,71.923 80.732,73.197 73.994,73.399C67.255,73.197 61.424,71.922 56.189,69.497C49.295,66.302 43.396,61.15 38.155,53.745C34.531,48.625 27.442,47.412 22.323,51.037C17.202,54.659 15.988,61.748 19.613,66.869C27.101,77.45 36.194,85.27 46.64,90.109C48.691,91.06 50.784,91.896 52.93,92.619C44.057,99.026 34.464,108.006 29.869,120.317C25.099,133.093 25.159,146.925 30.035,159.268C34.714,171.109 43.374,180.739 54.422,186.387C66.73,192.68 82.398,192.694 96.332,186.418C110.955,179.837 120.492,167.786 121.848,154.176C123.271,139.851 116.403,127.096 104.348,121.682C91.861,116.074 77.775,120.077 68.464,131.877C64.577,136.8 65.42,143.944 70.343,147.83C75.268,151.717 82.411,150.874 86.296,145.95C89.063,142.445 92.252,141.152 95.045,142.406C97.425,143.476 99.777,146.54 99.242,151.93C98.709,157.266 94.022,162.547 87.008,165.705C79.419,169.118 70.894,169.293 64.76,166.159C58.638,163.028 53.809,157.616 51.163,150.919C49.176,145.893 47.561,137.877 51.152,128.262C53.845,121.05 60.633,114.245 72.515,106.852C77.559,103.711 82.969,100.785 88.198,97.955C94.229,94.69 99.945,91.596 104.965,88.282C113.899,83.397 121.756,76.208 128.366,66.869C131.991,61.748 130.777,54.659 125.657,51.037L125.657,51.037ZM67.916,29.653C69.37,28.198 71.305,27.396 73.361,27.396C75.42,27.396 77.355,28.198 78.809,29.652C80.264,31.109 81.066,33.042 81.066,35.099C81.066,37.158 80.264,39.092 78.81,40.545C77.355,42.002 75.42,42.803 73.361,42.803C71.305,42.803 69.37,42.002 67.916,40.548C66.461,39.092 65.659,37.158 65.659,35.099C65.659,33.042 66.461,31.109 67.916,29.653ZM73.361,61.316C80.364,61.316 86.949,58.588 91.9,53.636C96.85,48.686 99.577,42.104 99.577,35.099C99.577,28.097 96.85,21.514 91.898,16.562C86.949,11.612 80.364,8.884 73.361,8.884C66.36,8.884 59.776,11.612 54.825,16.564C49.873,21.514 47.147,28.097 47.147,35.099C47.147,42.102 49.873,48.686 54.825,53.636C59.776,58.588 66.36,61.316 73.361,61.316Z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Hosterra icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 2.5.0
	 */
	private function get_base64_hosterra_icon( $color1 = '#00667A' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="500" height="500" viewBox="0 0 500 500" preserveAspectRatio="xMidYMid">';
		$source .= '<g transform="scale(3.5,3.5) translate(16,20)">';
		$source .= '<path fill="' . $color1 . '" d="M102.38,4.16C99.11,1.48 80.45,-0.23 78.74,0.02C75.99,0.42 74.94,2.46 73.57,6.59C72.69,9.24 70.95,16.17 69.56,21.84L62.28,21.73L66.79,30.25C65.93,31.67 65.02,33.06 64.07,34.42L54.34,33.39L58,42.11C57.04,43.25 56.12,44.4 55.19,45.54L46.73,43.12L48.94,52.81C47.65,54.2 46.38,55.65 45.11,57.1L37.3,54.87L39.26,63.46C37.71,65 36.11,66.43 34.43,67.67L27.59,63.59L27.45,71.35C26.82,71.55 26.17,71.72 25.51,71.86C17.16,73.59 0.04,55.85 0,55.83C0,55.83 7.79,90.91 35.73,88.01L30.85,97.38L31.34,98.7L34.04,106.6C34.09,106.75 34.25,106.83 34.4,106.78L37.61,105.68C37.76,105.63 37.84,105.47 37.79,105.32L36.47,101.47L48.15,87.73C49.46,87.51 50.78,87.24 52.11,86.9C53.97,86.43 55.75,85.73 57.45,84.85L68.23,97.08L59.65,97.08C59.02,94.8 56.96,93.1 54.47,93.05C51.43,92.99 48.92,95.39 48.86,98.43C48.8,101.47 51.2,103.98 54.24,104.04C56.72,104.09 58.85,102.49 59.58,100.25L85.78,100.25C85.83,100.25 86.27,100.22 86.63,99.99C87,99.77 87.16,99.49 87.24,99.35L92.51,86.8L93.97,94.24C92.73,95.27 91.96,96.84 92,98.57C92.06,101.61 94.58,104.01 97.61,103.95C100.65,103.88 103.05,101.37 102.99,98.34C102.93,95.3 100.41,92.9 97.38,92.96C97.2,92.96 97.02,92.98 96.84,93L90.56,61.12L92.88,60.79C95.32,62.04 97.66,60.55 98.41,59.98C99.1,59.76 99.56,59.07 99.46,58.32C99.35,57.48 98.58,56.9 97.74,57.01L97.19,57.09C95.49,55.32 91.66,51.51 90.7,50.52C87.6,47.32 84.28,44.32 80.61,41.79C80.38,41.63 80.14,41.47 79.89,41.32C80.58,38.31 82.06,32.54 83.28,27.87C84.36,24.73 85.42,22.45 86.33,21.89C87.72,21.04 91.43,22.84 95.67,22.59C99.91,22.35 101.78,21.74 102.93,20.63C105.88,17.77 95.67,17.69 95.67,17.69C87.01,17.08 87.31,13.32 87.31,13.32C89.64,13.94 95.98,14.55 98.94,14.3C101.82,14.06 104.05,13.55 104.77,12.54C105.38,11.5 105.67,6.83 102.4,4.15L102.38,4.16ZM91.42,81.29L84.31,97.08L80.12,97.08L80.12,93.8C80.12,93.65 80,93.52 79.84,93.52L74.54,93.52L66.13,78.05C67.85,76.15 69.36,74.06 70.64,71.87C73.34,67.25 75.12,62.16 76.38,56.98C76.51,56.43 76.65,55.87 76.79,55.31L79.61,59.58L78.45,59.74C77.61,59.85 77.03,60.62 77.14,61.46C77.25,62.3 78.02,62.88 78.86,62.77L80.99,62.47C83.3,63.96 85.44,62.11 85.77,61.8L87.53,61.55L91.42,81.3L91.42,81.29ZM90.71,58.01L88.69,58.29L88.52,58.29C88.47,58.29 88.43,58.31 88.38,58.33L85.36,58.76L81.79,51.48C83.59,52.7 85.38,53.92 87.18,55.15C88.44,56 89.69,56.89 90.71,58.01Z"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

	/**
	 * Returns a base64 svg resource for the Litmus icon.
	 *
	 * @param string $color1 Optional. Color 1 of the icon.
	 * @param string $color2 Optional. Color 2 of the icon.
	 * @param string $color3 Optional. Color 3 of the icon.
	 * @param string $color4 Optional. Color 4 of the icon.
	 * @return string The svg resource as a base64.
	 * @since 2.5.0
	 */
	private function get_base64_microsoft_icon( $color1 = '#4285f4', $color2 = '#34a853', $color3 = '#fbbc04', $color4 = '#ea4335' ) {
		$source  = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="500" height="500" viewBox="0 0 500 500" preserveAspectRatio="xMidYMid">';
		$source .= '<g transform="scale(5.4,5.4) translate(15,15)">';
		$source .= '<rect fill="' . $color1 . '" width="30" height="30"/>';
		$source .= '<rect fill="' . $color2 . '" x="34" width="30" height="30"/>';
		$source .= '<rect fill="' . $color3 . '" x="34" y="34" width="30" height="30"/>';
		$source .= '<rect fill="' . $color4 . '" y="34"  width="30" height="30"/>';
		$source .= '</g>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

}
