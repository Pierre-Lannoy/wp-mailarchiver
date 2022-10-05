<?php
/**
 * WP-CLI for MailArchiver.
 *
 * Adds WP-CLI commands to MailArchiver
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.0.0
 */

namespace Mailarchiver\Plugin\Feature;

use Mailarchiver\Listener\ListenerFactory;
use Mailarchiver\Plugin\Feature\Archive;
use Mailarchiver\System\Cache;
use Mailarchiver\System\Date;
use Mailarchiver\System\Environment;
use Mailarchiver\System\Markdown;
use Mailarchiver\System\Option;
use Mailarchiver\System\Timezone;
use Mailarchiver\System\UUID;
use Mailarchiver\Plugin\Feature\EventTypes;
use Mailarchiver\System\PwdProtect;
use Mailarchiver\Plugin\Feature\ArchiverFactory;
use MAMonolog\Logger as Mnlg;
use Spyc;

/**
 * Manages MailArchiver.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.0.0
 */
class Wpcli {

	/**
	 * List of exit codes.
	 *
	 * @since    2.0.0
	 * @var array $exit_codes Exit codes.
	 */
	private $exit_codes = [
		0   => 'operation successful.',
		1   => 'invalid archiver type supplied.',
		2   => 'invalid archiver uuid supplied.',
		3   => 'system archivers can\'t be managed.',
		4   => 'unable to create a new archiver.',
		5   => 'unable to modify this archiver.',
		6   => 'unrecognized setting.',
		7   => 'unrecognized action.',
		8   => 'encryption features are not available.',
		9   => 'wrong password.',
		255 => 'unknown error.',
	];

	/**
	 * Flush output without warnings.
	 *
	 * @since    2.0.2
	 */
	private function flush() {
		// phpcs:ignore
		set_error_handler( null );
		// phpcs:ignore
		@ob_flush();
		// phpcs:ignore
		restore_error_handler();
	}

	/**
	 * Write ids as clean stdout.
	 *
	 * @param   array   $ids   The ids.
	 * @param   string  $field  Optional. The field to output.
	 * @since   2.0.0
	 */
	private function write_ids( $ids, $field = '' ) {
		$result = '';
		$last   = end( $ids );
		foreach ( $ids as $key => $id ) {
			if ( '' === $field ) {
				$result .= $key;
			} else {
				$result .= $id[ $field ];
			}
			if ( $id !== $last ) {
				$result .= ' ';
			}
		}
		// phpcs:ignore
		fwrite( STDOUT, $result );
	}

	/**
	 * Write an error.
	 *
	 * @param   integer  $code      Optional. The error code.
	 * @param   boolean  $stdout    Optional. Clean stdout output.
	 * @since   2.0.0
	 */
	private function error( $code = 255, $stdout = false ) {
		$msg = '[' . MAILARCHIVER_PRODUCT_NAME . '] ' . ucfirst( $this->exit_codes[ $code ] );
		if ( \WP_CLI\Utils\isPiped() ) {
			// phpcs:ignore
			fwrite( STDOUT, '' );
			// phpcs:ignore
			exit( $code );
		} elseif ( $stdout ) {
			// phpcs:ignore
			fwrite( STDERR, $msg );
			// phpcs:ignore
			exit( $code );
		} else {
			\WP_CLI::error( $msg );
		}
	}

	/**
	 * Write a warning.
	 *
	 * @param   string   $msg       The message.
	 * @param   string   $result    Optional. The result.
	 * @param   boolean  $stdout    Optional. Clean stdout output.
	 * @since   2.0.0
	 */
	private function warning( $msg, $result = '', $stdout = false ) {
		$msg = '[' . MAILARCHIVER_PRODUCT_NAME . '] ' . ucfirst( $msg );
		if ( \WP_CLI\Utils\isPiped() || $stdout ) {
			// phpcs:ignore
			fwrite( STDOUT, $result );
		} else {
			\WP_CLI::warning( $msg );
		}
	}

	/**
	 * Write a success.
	 *
	 * @param   string   $msg       The message.
	 * @param   string   $result    Optional. The result.
	 * @param   boolean  $stdout    Optional. Clean stdout output.
	 * @since   2.0.0
	 */
	private function success( $msg, $result = '', $stdout = false ) {
		$msg = '[' . MAILARCHIVER_PRODUCT_NAME . '] ' . ucfirst( $msg );
		if ( \WP_CLI\Utils\isPiped() || $stdout ) {
			// phpcs:ignore
			fwrite( STDOUT, $result );
		} else {
			\WP_CLI::success( $msg );
		}
	}

	/**
	 * Write a wimple line.
	 *
	 * @param   string   $msg       The message.
	 * @param   string   $result    Optional. The result.
	 * @param   boolean  $stdout    Optional. Clean stdout output.
	 * @since   2.0.0
	 */
	private function line( $msg, $result = '', $stdout = false ) {
		if ( \WP_CLI\Utils\isPiped() || $stdout ) {
			// phpcs:ignore
			fwrite( STDOUT, $result );
		} else {
			\WP_CLI::line( $msg );
		}
	}

	/**
	 * Write a wimple log line.
	 *
	 * @param   string   $msg       The message.
	 * @param   boolean  $stdout    Optional. Clean stdout output.
	 * @since   2.0.0
	 */
	private function log( $msg, $stdout = false ) {
		if ( ! \WP_CLI\Utils\isPiped() && ! $stdout ) {
			\WP_CLI::log( $msg );
		}
	}

	/**
	 * Get params from command line.
	 *
	 * @param   array   $args   The command line parameters.
	 * @return  array The true parameters.
	 * @since   2.0.0
	 */
	private function get_params( $args ) {
		$result = '';
		if ( array_key_exists( 'settings', $args ) ) {
			$result = \json_decode( $args['settings'], true );
		}
		if ( ! $result || ! is_array( $result ) ) {
			$result = [];
		}
		return $result;
	}

	/**
	 * Update processors.
	 *
	 * @param   array   $processors     The current processors.
	 * @param   string  $proc           The processor to set.
	 * @param   boolean $value          The value to set.
	 * @return  array The updated processors.
	 * @since   2.0.0
	 */
	private function updated_proc( $processors, $proc, $value ) {
		$key = '';
		switch ( $proc ) {
			case 'proc_wp':
				$key = 'WordpressProcessor';
				break;
		}
		if ( '' !== $key ) {
			if ( $value && ! in_array( $key, $processors, true ) ) {
				$processors[] = $key;
			}
			if ( ! $value && in_array( $key, $processors, true ) ) {
				$processors = array_diff( $processors, [ $key ] );
			}
		}
		return $processors;
	}

	/**
	 * Modify a archiver.
	 *
	 * @param   string  $uuid   The archiver uuid.
	 * @param   array   $args   The command line parameters.
	 * @param   boolean $start  Optional. Force running mode.
	 * @return  string The archiver uuid.
	 * @since   2.0.0
	 */
	private function archiver_modify( $uuid, $args, $start = false ) {
		$params        = $this->get_params( $args );
		$loggers       = Option::network_get( 'archivers' );
		$logger        = $loggers[ $uuid ];
		$handler_types = new HandlerTypes();
		$handler       = $handler_types->get( $logger['handler'] );
		unset( $loggers[ $uuid ] );
		foreach ( $params as $param => $value ) {
			switch ( $param ) {
				case 'obfuscation':
				case 'pseudonymization':
				case 'mailanonymization':
					$logger['privacy'][ $param ] = (bool) $value;
					break;
				case 'encryption':
					$logger['privacy'][ $param ] = (string) $value;
					break;
				case 'proc_wp':
					$logger['processors'] = $this->updated_proc( $logger['processors'], $param, (bool) $value );
					break;
				case 'level':
					if ( array_key_exists( strtolower( $value ), EventTypes::$levels ) ) {
						$logger['level'] = EventTypes::$levels[ strtolower( $value ) ];
					} else {
						$logger['level'] = $handler['minimal'];
					}
					break;
				case 'name':
					$logger['name'] = esc_html( (string) $value );
					break;
				default:
					if ( array_key_exists( $param, $handler['configuration'] ) ) {
						switch ( $handler['configuration'][ $param ]['control']['cast'] ) {
							case 'boolean':
								$logger['configuration'][ $param ] = (bool) $value;
								break;
							case 'integer':
								$logger['configuration'][ $param ] = (int) $value;
								break;
							case 'string':
								$logger['configuration'][ $param ] = (string) $value;
								break;
						}
					}
					break;
			}
		}
		if ( $start ) {
			$logger['running'] = true;
		}
		$loggers[ $uuid ] = $logger;
		Option::network_set( 'archivers', $loggers );
		return $uuid;
	}

	/**
	 * Add a archiver.
	 *
	 * @param   string  $uuid   The archiver uuid.
	 * @param   array   $args   The command line parameters.
	 * @return  string The archiver uuid.
	 * @since   2.0.0
	 */
	private function archiver_add( $handler, $args ) {
		$uuid             = UUID::generate_v4();
		$logger           = [
			'uuid'    => $uuid,
			'name'    => esc_html__( 'New archiver', 'mailarchiver' ),
			'handler' => $handler,
			'running' => false,
		];
		$loggers          = Option::network_get( 'archivers' );
		$factory          = new ArchiverFactory();
		$loggers[ $uuid ] = $factory->check( $logger, true );
		Option::network_set( 'archivers', $loggers );
		if ( $this->archiver_modify( $uuid, $args, Option::network_get( 'archiver_autostart' ) ) === $uuid ) {
			return $uuid;
		}
		return '';
	}

	/**
	 * Get MailArchiver details and operation modes.
	 *
	 * ## EXAMPLES
	 *
	 * wp mail archive status
	 *
	 *
	 *   === For other examples and recipes, visit https://github.com/Pierre-Lannoy/wp-mailarchiver/blob/master/WP-CLI.md ===
	 *
	 */
	public function status( $args, $assoc_args ) {
		$run = 0;
		foreach ( Option::network_get( 'archivers' ) as $key => $logger ) {
			if ( $logger['running'] ) {
				$run++;
			}
		}
		if ( 0 === $run ) {
			\WP_CLI::line( sprintf( '%s running.', Environment::plugin_version_text() ) );
		} elseif ( 1 === $run ) {
			\WP_CLI::line( sprintf( '%s running 1 archiver.', Environment::plugin_version_text() ) );
		} else {
			\WP_CLI::line( sprintf( '%s running %d archivers.', Environment::plugin_version_text(), $run ) );
		}
		if ( Option::network_get( 'archiver_autostart' ) ) {
			\WP_CLI::line( 'Auto-Start: enabled.' );
		} else {
			\WP_CLI::line( 'Auto-Start: disabled.' );
		}
		if ( \DecaLog\Engine::isDecalogActivated() ) {
			\WP_CLI::line( 'Logging support: ' . \DecaLog\Engine::getVersionString() . '.' );
		} else {
			\WP_CLI::line( 'Logging support: no.' );
		}
		if ( PwdProtect::is_available() ) {
			\WP_CLI::line( 'Encryption support: ' . PwdProtect::get_openssl_version() . '.' );
			\WP_CLI::line( 'Encryption type: ' . PwdProtect::get_encryption_details() . '.' );
		} else {
			\WP_CLI::line( 'Encryption support: no.' );
		}
	}

	/**
	 * Get information on archiver types.
	 *
	 * ## OPTIONS
	 *
	 * <list|describe>
	 * : The action to take.
	 * ---
	 * options:
	 *  - list
	 *  - describe
	 * ---
	 *
	 * [<archiver_type>]
	 * : The type of the archiver to describe.
	 *
	 * [--format=<format>]
	 * : Allows overriding the output of the command when listing types.
	 * ---
	 * default: table
	 * options:
	 *  - table
	 *  - json
	 *  - csv
	 *  - yaml
	 *  - ids
	 *  - count
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 * Lists available types:
	 * + wp m-archive type list
	 * + wp m-archive type list --format=json
	 *
	 * Details the WordpressHandler archiver type:
	 * + wp log type describe WordpressHandler
	 *
	 *
	 *   === For other examples and recipes, visit https://github.com/Pierre-Lannoy/wp-mailarchiver/blob/master/WP-CLI.md ===
	 *
	 */
	public function type( $args, $assoc_args ) {
		$stdout        = \WP_CLI\Utils\get_flag_value( $assoc_args, 'stdout', false );
		$format        = \WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'table' );
		$handler_types = new HandlerTypes();
		$handlers      = [];
		foreach ( $handler_types->get_all() as $key => $handler ) {
			if ( 'system' !== $handler['class'] ) {
				$handler['type']                          = $handler['id'];
				$handlers[ strtolower( $handler['id'] ) ] = $handler;
			}
		}
		uasort(
			$handlers,
			function ( $a, $b ) {
				return strcmp( strtolower( $a['name'] ), strtolower( $b['name'] ) );
			}
		);
		$uuid   = '';
		$action = isset( $args[0] ) ? $args[0] : 'list';
		if ( isset( $args[1] ) ) {
			$uuid = strtolower( $args[1] );
			if ( ! array_key_exists( $uuid, $handlers ) ) {
				$uuid = '';
			}
		}
		if ( 'list' !== $action && '' === $uuid ) {
			$this->error( 1, $stdout );
		}
		switch ( $action ) {
			case 'list':
				$details = [];
				foreach ( $handlers as $key => $handler ) {
					$item = [];
					foreach ( $handler as $i => $h ) {
						if ( in_array( $i, [ 'type', 'class', 'name', 'version' ], true ) ) {
							$item[ $i ] = $h;
						}
					}
					$details[ $handler['type'] ] = $item;
				}
				if ( 'ids' === $format ) {
					$this->write_ids( $handlers, 'type' );
				} elseif ( 'yaml' === $format ) {
					$details = Spyc::YAMLDump( $details, true, true, true );
					$this->line( $details, $details, $stdout );
				} elseif ( 'json' === $format ) {
					$details = wp_json_encode( $details );
					$this->line( $details, $details, $stdout );
				} else {
					\WP_CLI\Utils\format_items( $format, $details, [ 'type', 'class', 'name', 'version' ] );
				}
				break;
			case 'describe':
				$example = [];
				$handler = $handlers[ $uuid ];
				\WP_CLI::line( '' );
				\WP_CLI::line( \WP_CLI::colorize( '%8' . $handler['name'] . ' - ' . $handler['id'] . '%n' ) );
				\WP_CLI::line( $handler['help'] );
				\WP_CLI::line( '' );
				\WP_CLI::line( \WP_CLI::colorize( '%UMinimal Level%n' ) );
				\WP_CLI::line( '' );
				\WP_CLI::line( '  ' . strtolower( Archive::level_name( $handler['minimal'] ) ) );
				\WP_CLI::line( '' );
				\WP_CLI::line( \WP_CLI::colorize( '%UParameters%n' ) );
				\WP_CLI::line( '' );
				$param = '  * ';
				$elem  = '    - ';
				$list  = '       ';
				\WP_CLI::line( $param . 'Name - Used only in admin dashboard.' );
				\WP_CLI::line( $elem . 'field name: name' );
				\WP_CLI::line( $elem . 'field type: string' );
				\WP_CLI::line( $elem . 'default value: "New Archiver"' );
				\WP_CLI::line( '' );
				\WP_CLI::line( $param . 'Archived emails - Archived emails level.' );
				\WP_CLI::line( $elem . 'field name: level' );
				\WP_CLI::line( $elem . 'field type: string' );
				\WP_CLI::line( $elem . 'default value: "' . ( Mnlg::INFO === $handler['minimal'] ? 'info' : 'error' ) . '"' );
				\WP_CLI::line( $elem . 'available values:' );
				foreach ( Archive::get_levels( EventTypes::$levels[ strtolower( $handler['minimal'] ) ] ) as $level ) {
					\WP_CLI::line( $list . '"' . strtolower( EventTypes::$level_names[ $level[0] ] ) . '": ' . $level[1] . '.' );
				}
				\WP_CLI::line( '' );
				foreach ( $handler['configuration'] as $key => $conf ) {
					if ( ! $conf['show'] || ! $conf['control']['enabled'] ) {
						continue;
					}
					\WP_CLI::line( $param . $conf['name'] . ' - ' . $conf['help'] );
					\WP_CLI::line( $elem . 'field name: ' . $key );
					\WP_CLI::line( $elem . 'field type: ' . $conf['type'] );
					switch ( $conf['control']['type'] ) {
						case 'field_input_integer':
							\WP_CLI::line( $elem . 'default value: ' . $conf['default'] );
							\WP_CLI::line( $elem . 'range: [' . $conf['control']['min'] . '-' . $conf['control']['max'] . ']' );
							$example[] = '"' . $key . '": ' . $conf['default'];
							break;
						case 'field_checkbox':
							\WP_CLI::line( $elem . 'default value: ' . ( $conf['default'] ? 'true' : 'false' ) );
							$example[] = '"' . $key . '": ' . ( $conf['default'] ? 'true' : 'false' );
							break;
						case 'field_input_text':
							\WP_CLI::line( $elem . 'default value: "' . $conf['default'] . '"' );
							$example[] = '"' . $key . '": "' . $conf['default'] . '"';
							break;
						case 'field_select':
							switch ( $conf['control']['cast'] ) {
								case 'integer':
									\WP_CLI::line( $elem . 'default value: ' . $conf['default'] );
									$example[] = '"' . $key . '": ' . $conf['default'];
									break;
								case 'string':
									\WP_CLI::line( $elem . 'default value: "' . $conf['default'] . '"' );
									$example[] = '"' . $key . '": "' . $conf['default'] . '"';
									break;
							}
							\WP_CLI::line( $elem . 'available values:' );
							foreach ( $conf['control']['list'] as $point ) {
								switch ( $conf['control']['cast'] ) {
									case 'integer':
										\WP_CLI::line( $list . $point[0] . ': ' . $point[1] );
										break;
									case 'string':
										\WP_CLI::line( $list . '"' . $point[0] . '": ' . $point[1] );
										break;
								}
							}
							break;
					}
					\WP_CLI::line( '' );
				}
				\WP_CLI::line( $param . 'IP obfuscation - Recorded fields will contain hashes instead of real IPs.' );
				\WP_CLI::line( $elem . 'field name: obfuscation' );
				\WP_CLI::line( $elem . 'field type: boolean' );
				\WP_CLI::line( $elem . 'default value: false' );
				\WP_CLI::line( '' );
				\WP_CLI::line( $param . 'User pseudonymization - Recorded fields will contain hashes instead of user IDs & names.' );
				\WP_CLI::line( $elem . 'field name: pseudonymization' );
				\WP_CLI::line( $elem . 'field type: boolean' );
				\WP_CLI::line( $elem . 'default value: false' );
				\WP_CLI::line( '' );
				\WP_CLI::line( $param . 'Email masking - Recorded fields will contain hashes instead of email adresses.' );
				\WP_CLI::line( $elem . 'field name: mailanonymization' );
				\WP_CLI::line( $elem . 'field type: boolean' );
				\WP_CLI::line( $elem . 'default value: false' );
				\WP_CLI::line( '' );
				\WP_CLI::line( $param . 'Reported details: WordPress - Allows to record site, user and remote IP of the current request.' );
				\WP_CLI::line( $elem . 'field name: proc_wp' );
				\WP_CLI::line( $elem . 'field type: boolean' );
				\WP_CLI::line( $elem . 'default value: true' );
				\WP_CLI::line( '' );
				\WP_CLI::line( \WP_CLI::colorize( '%UExample%n' ) );
				\WP_CLI::line( '' );
				\WP_CLI::line( '  {' . implode( ', ', $example ) . '}' );
				\WP_CLI::line( '' );
				break;
		}

	}

	/**
	 * Decrypt a previously encrypted mail body.
	 *
	 * ## OPTIONS
	 *
	 * <password>
	 * : The password to use.
	 *
	 * <encrypted-content>
	 * : The encrypted content to decrypt.
	 *
	 * [--stdout]
	 * : Use clean STDOUT output to use results in scripts. Unnecessary when piping commands because piping is detected by MailArchiver.
	 *
	 * ## NOTES
	 *
	 *   <password> and <encrypted-content> must be surrounded by double-quotes.
	 *
	 * ## EXAMPLES
	 *
	 * Decrypt a content protected by the password "password":
	 * + wp m-archive decrypt "password" "IBP50CCSNgUIMVf99HKZ5n6FpaMY8WVUJNZvF5PZW1vofcqotHX/IZeCT1BmFCA9+qpR1vsZKRyNyWacEeQl/sNpww4tZnq/Yoh4dMzqkETfUQv0/LmvhuV258dMRqRGHzYhcbvzxUXX1vhVNRLv3g=="
	 *
	 *
	 *   === For other examples and recipes, visit https://github.com/Pierre-Lannoy/wp-mailarchiver/blob/master/WP-CLI.md ===
	 *
	 */
	public function decrypt( $args, $assoc_args ) {
		$stdout = \WP_CLI\Utils\get_flag_value( $assoc_args, 'stdout', false );
		if ( PwdProtect::is_available() ) {
			$pwp = new \Mailarchiver\System\PwdProtect( isset( $args[0] ) ? $args[0] : '' );
			$txt = $pwp->decrypt( isset( $args[1] ) ? $args[1] : '' );
			if ( $txt ) {
				$this->success( sprintf( 'decrypted content is "%s".', $txt ), $txt, $stdout );
			} else {
				$this->error( 9, $stdout );
			}
		} else {
			$this->error( 8, $stdout );
		}
	}

	/**
	 * Manage MailArchiver archivers.
	 *
	 * ## OPTIONS
	 *
	 * <list|start|pause|clean|purge|remove|add|set>
	 * : The action to take.
	 * ---
	 * options:
	 *  - list
	 *  - start
	 *  - pause
	 *  - clean
	 *  - purge
	 *  - remove
	 *  - add
	 *  - set
	 * ---
	 *
	 * [<uuid_or_type>]
	 * : The uuid of the archiver to perform an action on or the type of the archiver to add.
	 *
	 * [--settings=<settings>]
	 * : The settings needed by "add" and "modify" actions.
	 * MUST be a string containing a json configuration.
	 * ---
	 * default: '{}'
	 * example: '{"host": "syslog.collection.eu.sumologic.com", "timeout": 800, "ident": "MailArchiver", "format": 1}'
	 * ---
	 *
	 * [--detail=<detail>]
	 * : The details of the output when listing archivers.
	 * ---
	 * default: short
	 * options:
	 *  - short
	 *  - full
	 * ---
	 *
	 * [--format=<format>]
	 * : Allows overriding the output of the command when listing archivers. Note if json or yaml is chosen: full metadata is outputted too.
	 * ---
	 * default: table
	 * options:
	 *  - table
	 *  - json
	 *  - csv
	 *  - yaml
	 *  - ids
	 *  - count
	 * ---
	 *
	 * [--yes]
	 * : Answer yes to the confirmation message, if any.
	 *
	 * [--stdout]
	 * : Use clean STDOUT output to use results in scripts. Unnecessary when piping commands because piping is detected by MailArchiver.
	 *
	 * ## EXAMPLES
	 *
	 * Lists configured archivers:
	 * + wp m-archive archiver list
	 * + wp m-archive archiver list --detail=full
	 * + wp m-archive archiver list --format=json
	 *
	 * Starts an archiver:
	 * + wp mail archiver start 37cf1c00-d67d-4e7d-9518-e579f01407a7
	 *
	 * Pauses an archiver:
	 * + wp m-archive archiver pause 37cf1c00-d67d-4e7d-9518-e579f01407a7
	 *
	 * Deletes old records of an archiver:
	 * + wp m-archive archiver clean 37cf1c00-d67d-4e7d-9518-e579f01407a7
	 *
	 * Deletes all records of an archiver:
	 * + wp m-archive archiver purge 37cf1c00-d67d-4e7d-9518-e579f01407a7
	 * + wp m-archive archiver purge 37cf1c00-d67d-4e7d-9518-e579f01407a7 --yes
	 *
	 * Permanently deletes an archiver:
	 * + wp m-archive archiver remove 37cf1c00-d67d-4e7d-9518-e579f01407a7
	 * + wp m-archive archiver remove 37cf1c00-d67d-4e7d-9518-e579f01407a7 --yes
	 *
	 * Adds an new archiver:
	 * + wp m-archive archiver add WordpressHandler --settings='{"rotate": 8000, "purge": 5, "level":"error", "proc_wp": true}'
	 *
	 * Change the settings of an archiver
	 * + wp m-archive archiver set 37cf1c00-d67d-4e7d-9518-e579f01407a7 --settings='{"level":"info"}'
	 *
	 *
	 *   === For other examples and recipes, visit https://github.com/Pierre-Lannoy/wp-mailarchiver/blob/master/WP-CLI.md ===
	 *
	 */
	public function archiver( $args, $assoc_args ) {
		$stdout       = \WP_CLI\Utils\get_flag_value( $assoc_args, 'stdout', false );
		$format       = \WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'table' );
		$detail       = \WP_CLI\Utils\get_flag_value( $assoc_args, 'detail', 'short' );
		$uuid         = '';
		$action       = isset( $args[0] ) ? $args[0] : 'list';
		$loggers_list = Option::network_get( 'archivers' );
		if ( isset( $args[1] ) ) {
			$uuid = $args[1];
			if ( 'add' === $action ) {
				$handler_types = new HandlerTypes();
				$t             = '';
				foreach ( $handler_types->get_all() as $handler ) {
					if ( 'system' !== $handler['class'] && strtolower( $uuid ) === strtolower( $handler['id'] ) ) {
						$t = $uuid;
					}
					if ( 'system' === $handler['class'] && strtolower( $uuid ) === strtolower( $handler['id'] ) ) {
						$t = 'system';
					}
				}
				$uuid = $t;
			} else {
				if ( ! array_key_exists( $uuid, $loggers_list ) ) {
					$uuid = '';
				} else {
					$handler_types = new HandlerTypes();
					foreach ( $handler_types->get_all() as $handler ) {
						if ( 'system' === $handler['class'] && $loggers_list[ $uuid ]['handler'] === $handler['id'] ) {
							$uuid = 'system';
						}
					}
				}
			}
		}
		if ( 'add' === $action && '' === $uuid ) {
			$this->error( 1, $stdout );
		} elseif ( 'system' === $uuid ) {
			$this->error( 3, $stdout );
		} elseif ( 'list' !== $action && '' === $uuid ) {
			$this->error( 2, $stdout );
		}
		switch ( $action ) {
			case 'list':
				$handler_types   = new HandlerTypes();
				$processor_types = new ProcessorTypes();
				$loggers         = [];
				foreach ( $loggers_list as $key => $logger ) {
					$handler           = $handler_types->get( $logger['handler'] );
					$logger['type']    = $handler['name'];
					$logger['uuid']    = $key;
					$logger['level']   = strtolower( Archive::level_name( $logger['level'] ) );
					$logger['running'] = $logger['running'] ? 'yes' : 'no';
					$list              = [];
					foreach ( $logger['processors'] as $processor ) {
						$name = $processor_types->get( $processor )['name'];
						if ( 'Mail' === $name ) {
							$name = 'Standard';
						}
						$list[] = $name;
					}
					$logger['processors'] = implode( ', ', $list );
					$loggers[ $key ]      = $logger;
				}
				usort(
					$loggers,
					function ( $a, $b ) {
						return strcmp( strtolower( $a['name'] ), strtolower( $b['name'] ) );
					}
				);
				if ( 'full' === $detail ) {
					$detail = [ 'uuid', 'type', 'name', 'running', 'level', 'processors' ];
				} else {
					$detail = [ 'uuid', 'type', 'name', 'running' ];
				}
				if ( 'ids' === $format ) {
					$this->write_ids( $loggers, 'uuid' );
				} elseif ( 'yaml' === $format ) {
					$details = Spyc::YAMLDump( $loggers_list, true, true, true );
					$this->line( $details, $details, $stdout );
				} elseif ( 'json' === $format ) {
					$details = wp_json_encode( $loggers_list );
					$this->line( $details, $details, $stdout );
				} else {
					\WP_CLI\Utils\format_items( $format, $loggers, $detail );
				}
				break;
			case 'start':
				if ( $loggers_list[ $uuid ]['running'] ) {
					$this->line( sprintf( 'The archiver %s is already running.', $uuid ), $uuid, $stdout );
				} else {
					$loggers_list[ $uuid ]['running'] = true;
					Option::network_set( 'archivers', $loggers_list );
					\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->info( sprintf( 'Archiver "%s" has started.', $loggers_list[ $uuid ]['name'] ) );
					$this->success( sprintf( 'archiver %s is now running.', $uuid ), $uuid, $stdout );
				}
				break;
			case 'pause':
				if ( ! $loggers_list[ $uuid ]['running'] ) {
					$this->line( sprintf( 'The archiver %s is already paused.', $uuid ), $uuid, $stdout );
				} else {
					$loggers_list[ $uuid ]['running'] = false;
					\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->info( sprintf( 'Archiver "%s" has been paused.', $loggers_list[ $uuid ]['name'] ) );
					Option::network_set( 'archivers', $loggers_list );
					$this->success( sprintf( 'archiver %s is now paused.', $uuid ), $uuid, $stdout );
				}
				break;
			case 'purge':
				$loggers_list[ $uuid ]['uuid'] = $uuid;
				if ( 'WordpressHandler' !== $loggers_list[ $uuid ]['handler'] ) {
					$this->warning( sprintf( 'archiver %s can\'t be purged.', $uuid ), $uuid, $stdout );
				} else {
					\WP_CLI::confirm( sprintf( 'Are you sure you want to purge archiver %s?', $uuid ), $assoc_args );
					$factory = new ArchiverFactory();
					$factory->purge( $loggers_list[ $uuid ] );
					\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->notice( sprintf( 'Archiver "%s" has been purged.', $loggers_list[ $uuid ]['name'] ) );
					$this->success( sprintf( 'archiver %s successfully purged.', $uuid ), $uuid, $stdout );
				}
				break;
			case 'clean':
				$loggers_list[ $uuid ]['uuid'] = $uuid;
				if ( 'WordpressHandler' !== $loggers_list[ $uuid ]['handler'] ) {
					$this->warning( sprintf( 'archiver %s can\'t be cleaned.', $uuid ), $uuid, $stdout );
				} else {
					$factory = new ArchiverFactory();
					$count   = $factory->clean( $loggers_list[ $uuid ] );
					$this->log( sprintf( '%d record(s) deleted.', $count ), $stdout );
					$this->success( sprintf( 'archiver %s successfully cleaned.', $uuid ), $uuid, $stdout );
				}
				break;
			case 'remove':
				$loggers_list[ $uuid ]['uuid'] = $uuid;
				\WP_CLI::confirm( sprintf( 'Are you sure you want to remove archiver %s?', $uuid ), $assoc_args );
				$factory = new ArchiverFactory();
				$factory->destroy( $loggers_list[ $uuid ] );
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->notice( sprintf( 'Archiver "%s" has been removed.', $loggers_list[ $uuid ]['name'] ) );
				unset( $loggers_list[ $uuid ] );
				Option::network_set( 'archivers', $loggers_list );
				$this->success( sprintf( 'archiver %s successfully removed.', $uuid ), $uuid, $stdout );
				break;
			case 'add':
				$result = $this->archiver_add( $uuid, $assoc_args );
				if ( '' === $result ) {
					\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->error( 'Unable to add a archiver.', [ 'code' => 1 ] );
					$this->error( 4, $stdout );
				} else {
					$loggers_list = Option::network_get( 'archivers' );
					\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->notice( sprintf( 'Archiver "%s" has been saved.', $loggers_list[ $result ]['name'] ) );
					$this->success( sprintf( 'archiver %s successfully created.', $result ), $result, $stdout );
				}
				break;
			case 'set':
				$result = $this->archiver_modify( $uuid, $assoc_args );
				if ( '' === $result ) {
					\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->error( 'Unable to modify a archiver.', [ 'code' => 1 ] );
					$this->error( 5, $stdout );
				} else {
					$loggers_list = Option::network_get( 'archivers' );
					\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->notice( sprintf( 'Archiver "%s" has been saved.', $loggers_list[ $result ]['name'] ) );
					$this->success( sprintf( 'archiver %s successfully saved.', $result ), $result, $stdout );
				}
				break;
		}
	}

	/**
	 * Modify MailArchiver main settings.
	 *
	 * ## OPTIONS
	 *
	 * <enable|disable>
	 * : The action to take.
	 *
	 * <auto-start>
	 * : The setting to change.
	 *
	 * [--yes]
	 * : Answer yes to the confirmation message, if any.
	 *
	 * [--stdout]
	 * : Use clean STDOUT output to use results in scripts. Unnecessary when piping commands because piping is detected by MailArchiver.
	 *
	 * ## EXAMPLES
	 *
	 * wp m-archive settings enable auto-start
	 * wp m-archive settings disable auto-start --yes
	 *
	 *
	 *   === For other examples and recipes, visit https://github.com/Pierre-Lannoy/wp-mailarchiver/blob/master/WP-CLI.md ===
	 *
	 */
	public function settings( $args, $assoc_args ) {
		$stdout  = \WP_CLI\Utils\get_flag_value( $assoc_args, 'stdout', false );
		$action  = isset( $args[0] ) ? (string) $args[0] : '';
		$setting = isset( $args[1] ) ? (string) $args[1] : '';
		switch ( $action ) {
			case 'enable':
				switch ( $setting ) {
					case 'auto-start':
						Option::network_set( 'archiver_autostart', true );
						$this->success( 'auto-start is now activated.', '', $stdout );
						break;
					default:
						$this->error( 6, $stdout );
				}
				break;
			case 'disable':
				switch ( $setting ) {
					case 'auto-start':
						\WP_CLI::confirm( 'Are you sure you want to deactivate auto-start?', $assoc_args );
						Option::network_set( 'archiver_autostart', false );
						$this->success( 'auto-start is now deactivated.', '', $stdout );
						break;
					default:
						$this->error( 6, $stdout );
				}
				break;
			default:
				$this->error( 7, $stdout );
		}
	}

	/**
	 * Get information on exit codes.
	 *
	 * ## OPTIONS
	 *
	 * <list>
	 * : The action to take.
	 * ---
	 * options:
	 *  - list
	 * ---
	 *
	 * [--format=<format>]
	 * : Allows overriding the output of the command when listing exit codes.
	 * ---
	 * default: table
	 * options:
	 *  - table
	 *  - json
	 *  - csv
	 *  - yaml
	 *  - ids
	 *  - count
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 * Lists available exit codes:
	 * + wp m-archive exitcode list
	 * + wp m-archive exitcode list --format=json
	 *
	 *
	 *   === For other examples and recipes, visit https://github.com/Pierre-Lannoy/wp-mailarchiver/blob/master/WP-CLI.md ===
	 *
	 */
	public function exitcode( $args, $assoc_args ) {
		$stdout = \WP_CLI\Utils\get_flag_value( $assoc_args, 'stdout', false );
		$format = \WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'table' );
		$action = isset( $args[0] ) ? $args[0] : 'list';
		$codes  = [];
		foreach ( $this->exit_codes as $key => $msg ) {
			$codes[ $key ] = [
				'code'    => $key,
				'meaning' => ucfirst( $msg ),
			];
		}
		switch ( $action ) {
			case 'list':
				if ( 'ids' === $format ) {
					$this->write_ids( $codes );
				} else {
					\WP_CLI\Utils\format_items( $format, $codes, [ 'code', 'meaning' ] );
				}
				break;
		}
	}

	/**
	 * Get the WP-CLI help file.
	 *
	 * @param   array $attributes  'style' => 'markdown', 'html'.
	 *                             'mode'  => 'raw', 'clean'.
	 * @return  string  The output of the shortcode, ready to print.
	 * @since 1.0.0
	 */
	public static function sc_get_helpfile( $attributes ) {
		$md = new Markdown();
		return $md->get_shortcode( 'WP-CLI.md', $attributes );
	}

}

add_shortcode( 'mailarchiver-wpcli', [ 'Mailarchiver\Plugin\Feature\Wpcli', 'sc_get_helpfile' ] );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	\WP_CLI::add_command( 'm-archive', 'Mailarchiver\Plugin\Feature\Wpcli' );
}
