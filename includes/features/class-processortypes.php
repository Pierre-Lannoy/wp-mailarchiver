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
	 * The array of mandatory processors.
	 *
	 * @since  1.0.0
	 * @var    array    $mandatory_processors    The mandatory processors.
	 */
	private $mandatory_processors = [];

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->processors[]           = [
			'id'        => 'WordpressProcessor',
			'namespace' => 'Mailarchiver\\Processor',
			'name'      => esc_html__( 'WordPress', 'mailarchiver' ),
			'help'      => esc_html__( 'Allows to record site, user and remote IP of the current request.', 'mailarchiver' ),
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
		$this->mandatory_processors[] = [
			'id'        => 'MailProcessor',
			'namespace' => 'Mailarchiver\\Processor',
			'name'      => esc_html__( 'Mail', 'mailarchiver' ),
			'help'      => esc_html__( 'Allows to record email fields and metadata.', 'mailarchiver' ),
			'init'      => [
				[
					'type'  => 'privacy',
					'value' => 'mailanonymization',
				],
				[
					'type'  => 'security',
					'value' => 'xss',
				],
				[
					'type'  => 'privacy',
					'value' => 'encryption',
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
	 * @param boolean $full Optional. Get mandatory processors too.
	 * @return  array   A list of all available processors.
	 * @since    1.0.0
	 */
	public function get_list( $full = false ) {
		$result = [];
		foreach ( $this->processors as $processor ) {
			$result[] = $processor['id'];
		}
		if ( $full ) {
			foreach ( $this->mandatory_processors as $processor ) {
				$result[] = $processor['id'];
			}
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
		foreach ( array_merge( $this->processors, $this->mandatory_processors ) as $processor ) {
			if ( $processor['id'] === $id ) {
				return $processor;
			}
		}
		return null;
	}

}
