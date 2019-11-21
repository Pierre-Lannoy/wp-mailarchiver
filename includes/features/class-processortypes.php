<?php
/**
 * Processor types handling
 *
 * Handles all available processor types.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Plugin\Feature;

/**
 * Define the processor types functionality.
 *
 * Handles all available processor types.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class ProcessorTypes {

	/**
	 * The array of available processors.
	 *
	 * @since  1.0.0
	 * @var    array    $processors    The available processors.
	 */
	private $processors = [];

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->processors[] = [
			'id'        => 'BacktraceProcessor',
			'namespace' => 'Mailarchiver\\Processor',
			'name'      => esc_html__( 'Backtrace', 'mailarchiver' ),
			'help'      => esc_html__( 'Allows to log the full PHP and WordPress call stack.', 'mailarchiver' ),
			'init'      => [
				[ 'type' => 'level' ],
			],
		];
		$this->processors[] = [
			'id'        => 'IntrospectionProcessor',
			'namespace' => 'Monolog\\Processor',
			'name'      => esc_html__( 'PHP introspection', 'mailarchiver' ),
			'help'      => esc_html__( 'Allows to log line, file, class and function generating the event.', 'mailarchiver' ),
			'init'      => [
				[ 'type' => 'level' ],
				[
					'type'  => 'literal',
					'value' => [ 'Mailarchiver\\' ],
				],
			],
		];
		$this->processors[] = [
			'id'        => 'WWWProcessor',
			'namespace' => 'Mailarchiver\\Processor',
			'name'      => esc_html__( 'HTTP request', 'mailarchiver' ),
			'help'      => esc_html__( 'Allows to log url, method, referrer and remote IP of the current web request.', 'mailarchiver' ),
			'init'      => [
				[
					'type'  => 'literal',
					'value' => null,
				],
				[
					'type'  => 'literal',
					'value' => null,
				],
				[
					'type'  => 'privacy',
					'value' => 'obfuscation',
				],
			],
		];
		$this->processors[] = [
			'id'        => 'WordpressProcessor',
			'namespace' => 'Mailarchiver\\Processor',
			'name'      => esc_html__( 'WordPress ', 'mailarchiver' ),
			'help'      => esc_html__( 'Allows to log site, user and remote IP of the current request.', 'mailarchiver' ),
			'init'      => [
				[
					'type'  => 'privacy',
					'value' => 'pseudonymization',
				],
				[
					'type'  => 'privacy',
					'value' => 'obfuscation',
				],
			],
		];
	}

	/**
	 * Get the processors definition.
	 *
	 * @return  array   A list of all available processors definitions.
	 * @since    1.0.0
	 */
	public function get_all() {
		return $this->processors;
	}

	/**
	 * Get the processors list.
	 *
	 * @return  array   A list of all available processors.
	 * @since    1.0.0
	 */
	public function get_list() {
		$result = [];
		foreach ( $this->processors as $processor ) {
			$result[] = $processor['id'];
		}
		return $result;
	}

	/**
	 * Get a specific processor.
	 *
	 * @param   string $id The processor id.
	 * @return  null|array   The detail of the processor, null if not found.
	 * @since    1.0.0
	 */
	public function get( $id ) {
		foreach ( $this->processors as $processor ) {
			if ( $processor['id'] === $id ) {
				return $processor;
			}
		}
		return null;
	}

}
