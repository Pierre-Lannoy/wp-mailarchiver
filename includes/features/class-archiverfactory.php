<?php
/**
 * Archiver consistency handling
 *
 * Handles all archiver consistency operations.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Plugin\Feature;

use MAMonolog\Logger;

/**
 * Define the archiver consistency functionality.
 *
 * Handles all archiver consistency operations.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class ArchiverFactory {

	/**
	 * The HandlerTypes instance.
	 *
	 * @since  1.0.0
	 * @var    HandlerTypes    $handler_types    The handlers types.
	 */
	private $handler_types;

	/**
	 * The processorTypes instance.
	 *
	 * @since  1.0.0
	 * @var    processorTypes    $processor_types    The processors types.
	 */
	private $processor_types;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->handler_types   = new HandlerTypes();
		$this->processor_types = new ProcessorTypes();
	}

	/**
	 * Create an instance of $class_name.
	 *
	 * @param   string $class_name The class name.
	 * @param   array  $args   The param of the constructor for $class_name class.
	 * @return  boolean|object The instance of the class if creation was possible, null otherwise.
	 * @since    1.0.0
	 */
	private function create_instance( $class_name, $args = [] ) {
		if ( class_exists( $class_name ) ) {
			try {
				$reflection = new \ReflectionClass( $class_name );
				return $reflection->newInstanceArgs( $args );
			} catch ( \Exception $ex ) {
				return false;
			}
		}
		return false;
	}

	/**
	 * Create an instance of archiver.
	 *
	 * @param   array $archiver   The archiver parameters.
	 * @return  null|object The instance of archiver if creation was possible, null otherwise.
	 * @since    1.0.0
	 */
	public function create_archiver( $archiver ) {
		$archiver  = $this->check( $archiver );
		$handler = null;
		if ( $archiver['running'] ) {
			$handler_def = $this->handler_types->get( $archiver['handler'] );
			if ( isset( $handler_def ) ) {
				$classname = $handler_def['namespace'] . '\\' . $handler_def['id'];
				if ( class_exists( $classname ) ) {
					$args = [];
					foreach ( $handler_def['init'] as $p ) {
						switch ( $p['type'] ) {
							case 'level':
								$args[] = (int) $archiver['level'];
								break;
							case 'literal':
								$args[] = $p['value'];
								break;
							case 'configuration':
								$args[] = $archiver['configuration'][ $p['value'] ];
								break;
							case 'compute':
								switch ( $p['value'] ) {
									case 'tablename':
										global $wpdb;
										$args[] = $wpdb->base_prefix . 'mailarchiver_' . str_replace( '-', '', $archiver['uuid'] );
										break;
								}
								break;
						}
					}
					$handler = $this->create_instance( $classname, $args );
				}
			}
			if ( $handler ) {
				foreach ( array_reverse( $archiver['processors'] ) as $processor ) {
					$p_instance    = null;
					$processor_def = $this->processor_types->get( $processor );
					if ( $processor_def ) {
						$classname = $processor_def['namespace'] . '\\' . $processor_def['id'];
						if ( class_exists( $classname ) ) {
							$args = [];
							foreach ( $processor_def['init'] as $p ) {
								switch ( $p['type'] ) {
									case 'level':
										$args[] = (int) $archiver['level'];
										break;
									case 'privacy':
										if ( 'encryption' === $p['value'] ) {
											$args[] = (string) $archiver['privacy'][ $p['value'] ];
										} else {
											$args[] = (bool) $archiver['privacy'][ $p['value'] ];
										}
										break;
									case 'security':
										$args[] = (bool) $archiver['security'][ $p['value'] ];
										break;
									case 'literal':
										$args[] = $p['value'];
										break;
								}
							}
							$p_instance = $this->create_instance( $classname, $args );
						}
					}
					if ( $p_instance ) {
						$handler->pushProcessor( $p_instance );
					}
				}
			}
		}
		return $handler;
	}

	/**
	 * Check if archiver definition is compliant.
	 *
	 * @param   array   $archiver  The archiver definition.
	 * @param   boolean $init_handler   Optional. Init handlers needing it.
	 * @return  array   The checked archiver definition.
	 * @since    1.0.0
	 */
	public function check( $archiver, $init_handler = false ) {
		$archiver = $this->standard_check( $archiver );
		$handler  = $this->handler_types->get( $archiver['handler'] );
		if ( $handler && in_array( 'privacy', $handler['params'], true ) ) {
			$archiver = $this->privacy_check( $archiver );
		}
		if ( $handler && in_array( 'security', $handler['params'], true ) ) {
			$archiver = $this->security_check( $archiver );
		}
		if ( $handler && in_array( 'processors', $handler['params'], true ) ) {
			$archiver = $this->processor_check( $archiver );
		}
		if ( $handler && array_key_exists( 'configuration', $handler ) ) {
			$archiver = $this->configuration_check( $archiver, $handler['configuration'] );
		}
		if ( $init_handler && array_key_exists( 'uuid', $archiver ) ) {
			$classname = 'Mailarchiver\Plugin\Feature\\' . $archiver['handler'];
			if ( class_exists( $classname ) ) {
				$instance = $this->create_instance( $classname );
				$instance->set_archiver( $archiver );
				$instance->initialize();
			}
		}
		return $archiver;
	}

	/**
	 * Clean the archiver.
	 *
	 * @param   array $archiver  The archiver definition.
	 * @since    1.0.0
	 */
	public function destroy( $archiver ) {
		if ( array_key_exists( 'uuid', $archiver ) ) {
			$classname = 'Mailarchiver\Plugin\Feature\\' . $archiver['handler'];
			if ( class_exists( $classname ) ) {
				$instance = $this->create_instance( $classname );
				$instance->set_archiver( $archiver );
				$instance->finalize();
			}
		}
	}

	/**
	 * Force purge the $archiver.
	 *
	 * @param   array $archiver  The $archiver definition.
	 * @since    2.0.0
	 */
	public function purge( $archiver ) {
		if ( array_key_exists( 'uuid', $archiver ) ) {
			$classname = 'Mailarchiver\Plugin\Feature\\' . $archiver['handler'];
			if ( class_exists( $classname ) ) {
				$instance = $this->create_instance( $classname );
				$instance->set_archiver( $archiver );
				$instance->force_purge();
			}
		}
	}

	/**
	 * Clean the $archiver.
	 *
	 * @param   array $logger  The $archiver definition.
	 * @return  integer     The number of deleted records.
	 * @since    2.0.0
	 */
	public function clean( $archiver ) {
		if ( array_key_exists( 'uuid', $archiver ) ) {
			$classname = 'Mailarchiver\Plugin\Feature\\' . $archiver['handler'];
			if ( class_exists( $classname ) ) {
				$instance = $this->create_instance( $classname );
				$instance->set_archiver( $archiver );
				return $instance->cron_clean();
			}
		}
	}

	/**
	 * Check the standard part of the archiver.
	 *
	 * @param   array $archiver  The archiver definition.
	 * @return  array   The checked archiver definition.
	 * @since    1.0.0
	 */
	private function standard_check( $archiver ) {
		if ( ! array_key_exists( 'name', $archiver ) ) {
			$archiver['name'] = esc_html__( 'Unnamed archiver', 'mailarchiver' );
		}
		if ( ! array_key_exists( 'running', $archiver ) ) {
			$archiver['running'] = false;
		}
		if ( ! array_key_exists( 'handler', $archiver ) ) {
			$archiver['handler'] = 'NullHandler';
		} elseif ( ! in_array( $archiver['handler'], $this->handler_types->get_list(), true ) ) {
			$archiver['handler'] = 'NullHandler';
		}
		if ( ! array_key_exists( 'level', $archiver ) ) {
			$archiver['level'] = Logger::DEBUG;
		} elseif ( ! in_array( $archiver['level'], EventTypes::$level_values, false ) ) {
			$archiver['level'] = Logger::DEBUG;
		}
		return $archiver;
	}

	/**
	 * Check the privacy part of the archiver.
	 *
	 * @param   array $archiver  The archiver definition.
	 * @return  array   The checked archiver definition.
	 * @since    1.0.0
	 */
	private function privacy_check( $archiver ) {
		if ( array_key_exists( 'privacy', $archiver ) ) {
			if ( ! array_key_exists( 'obfuscation', $archiver['privacy'] ) ) {
				$archiver['privacy']['obfuscation'] = false;
			}
			if ( ! array_key_exists( 'pseudonymization', $archiver['privacy'] ) ) {
				$archiver['privacy']['pseudonymization'] = false;
			}
			if ( ! array_key_exists( 'mailanonymization', $archiver['privacy'] ) ) {
				$archiver['privacy']['mailanonymization'] = false;
			}
			if ( ! array_key_exists( 'encryption', $archiver['privacy'] ) ) {
				$archiver['privacy']['encryption'] = '';
			}
		} else {
			$archiver['privacy']['obfuscation']       = false;
			$archiver['privacy']['pseudonymization']  = false;
			$archiver['privacy']['mailanonymization'] = false;
			$archiver['privacy']['encryption']        = '';
		}
		return $archiver;
	}

	/**
	 * Check the security part of the archiver.
	 *
	 * @param   array $archiver  The archiver definition.
	 * @return  array   The checked archiver definition.
	 * @since    2.11.0
	 */
	private function security_check( $archiver ) {
		if ( array_key_exists( 'security', $archiver ) ) {
			if ( ! array_key_exists( 'xss', $archiver['security'] ) ) {
				$archiver['security']['xss'] = true;
			}
		} else {
			$archiver['security']['xss'] = true;
		}
		return $archiver;
	}

	/**
	 * Check the processor part of the archiver.
	 *
	 * @param   array $archiver  The archiver definition.
	 * @return  array   The checked archiver definition.
	 * @since    1.0.0
	 */
	private function processor_check( $archiver ) {
		if ( ! array_key_exists( 'processors', $archiver ) ) {
			$archiver['processors'] = [];
		}
		if ( 'WordpressHandler' === $archiver['handler'] ) {
			$archiver['processors'] = array_merge( [ 'WordpressProcessor' ], $archiver['processors'] );
		} else {
			$processors = [];
			foreach ( $archiver['processors'] as $processor ) {
				if ( in_array( $processor, $this->processor_types->get_list( true ), true ) ) {
					$processors[] = $processor;
				}
			}
			$archiver['processors'] = $processors;
		}
		return $archiver;
	}

	/**
	 * Check the configuration part of the archiver.
	 *
	 * @param   array $archiver  The archiver definition.
	 * @param   array $configuration   The configuration definition.
	 * @return  array   The checked archiver definition.
	 * @since    1.0.0
	 */
	private function configuration_check( $archiver, $configuration ) {
		if ( ! array_key_exists( 'configuration', $archiver ) ) {
			$archiver['configuration'] = [];
		}
		foreach ( $configuration as $key => $conf ) {
			if ( ! array_key_exists( $key, $archiver['configuration'] ) ) {
				$archiver['configuration'][ $key ] = $conf['default'];
			}
		}
		return $archiver;
	}

}
