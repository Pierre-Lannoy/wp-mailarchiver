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
