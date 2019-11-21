<?php
/**
 * Loggers list
 *
 * Lists all available loggers.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Decalog\Plugin\Feature;

use Decalog\System\Option;
use Decalog\Plugin\Feature\Log;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Define the loggers list functionality.
 *
 * Lists all available loggers.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class Loggers extends \WP_List_Table {

	/**
	 * The loggers options handler.
	 *
	 * @since    1.0.0
	 * @var      array    $loggers    The loggers list.
	 */
	private $loggers = [];

	/**
	 * The HandlerTypes instance.
	 *
	 * @since  1.0.0
	 * @var    HandlerTypes    $handler_types    The handlers types.
	 */
	private $handler_types;

	/**
	 * The ProcessorTypes instance.
	 *
	 * @since  1.0.0
	 * @var    HandlerTypes    $processor_types    The processors types.
	 */
	private $processor_types;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		parent::__construct(
			[
				'singular' => 'logger',
				'plural'   => 'loggers',
				'ajax'     => true,
			]
		);
		global $wp_version;
		if ( version_compare( $wp_version, '4.2-z', '>=' ) && $this->compat_fields && is_array( $this->compat_fields ) ) {
			array_push( $this->compat_fields, 'all_items' );
		}
		$this->loggers = [];
		foreach ( Option::network_get( 'loggers' ) as $key => $logger ) {
			$logger['uuid']  = $key;
			$this->loggers[] = $logger;
		}
		$this->handler_types   = new HandlerTypes();
		$this->processor_types = new ProcessorTypes();
	}

	/**
	 * Default column formatter.
	 *
	 * @param   array  $item   The current item.
	 * @param   string $column_name The current column name.
	 * @return  string  The cell formatted, ready to print.
	 * @since    1.0.0
	 */
	protected function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * "name" column formatter.
	 *
	 * @param   array $item   The current item.
	 * @return  string  The cell formatted, ready to print.
	 * @since    1.0.0
	 */
	protected function column_name( $item ) {
		$edit              = esc_url(
			add_query_arg(
				[
					'page'   => 'mailarchiver-settings',
					'action' => 'form-edit',
					'tab'    => 'loggers',
					'uuid'   => $item['uuid'],
				],
				admin_url( 'options-general.php' )
			)
		);
		$delete            = esc_url(
			add_query_arg(
				[
					'page'   => 'mailarchiver-settings',
					'action' => 'form-delete',
					'tab'    => 'loggers',
					'uuid'   => $item['uuid'],
				],
				admin_url( 'options-general.php' )
			)
		);
		$pause             = esc_url(
			add_query_arg(
				[
					'page'   => 'mailarchiver-settings',
					'action' => 'pause',
					'tab'    => 'loggers',
					'uuid'   => $item['uuid'],
					'nonce'  => wp_create_nonce( 'mailarchiver-logger-pause-' . $item['uuid'] ),
				],
				admin_url( 'options-general.php' )
			)
		);
		$test              = esc_url(
			add_query_arg(
				[
					'page'   => 'mailarchiver-settings',
					'action' => 'test',
					'tab'    => 'loggers',
					'uuid'   => $item['uuid'],
					'nonce'  => wp_create_nonce( 'mailarchiver-logger-test-' . $item['uuid'] ),
				],
				admin_url( 'options-general.php' )
			)
		);
		$start             = esc_url(
			add_query_arg(
				[
					'page'   => 'mailarchiver-settings',
					'action' => 'start',
					'tab'    => 'loggers',
					'uuid'   => $item['uuid'],
					'nonce'  => wp_create_nonce( 'mailarchiver-logger-start-' . $item['uuid'] ),
				],
				admin_url( 'options-general.php' )
			)
		);
		$view              = esc_url(
			add_query_arg(
				[
					'page'      => 'mailarchiver-viewer',
					'logger_id' => $item['uuid'],
				],
				admin_url( 'tools.php' )
			)
		);
		$handler           = $this->handler_types->get( $item['handler'] );
		$icon              = '<img style="width:34px;float:left;padding-right:6px;" src="' . $handler['icon'] . '" />';
		$actions['edit']   = sprintf( '<a href="%s">' . esc_html__( 'Edit', 'mailarchiver' ) . '</a>', $edit );
		$actions['delete'] = sprintf( '<a href="%s">' . esc_html__( 'Remove', 'mailarchiver' ) . '</a>', $delete );
		if ( $item['running'] ) {
			$actions['pause'] = sprintf( '<a href="%s">' . esc_html__( 'Pause', 'mailarchiver' ) . '</a>', $pause );
		} else {
			$actions['start'] = sprintf( '<a href="%s">' . esc_html__( 'Start', 'mailarchiver' ) . '</a>', $start );
		}
		if ( 'WordpressHandler' === $handler['id'] ) {
			$actions['view'] = sprintf( '<a href="%s">' . esc_html__( 'View', 'mailarchiver' ) . '</a>', $view );
		}
		if ( $item['running'] ) {
			$actions['test'] = sprintf( '<a href="%s">' . esc_html__( 'Send Test', 'mailarchiver' ) . '</a>', $test );
		}
		return $icon . '&nbsp;' . sprintf( '<a href="%1$s">%2$s</a><br /><span style="color:silver">&nbsp;%3$s</span>%4$s', $edit, $item['name'], $handler['name'], $this->row_actions( $actions ) );
	}

	/**
	 * "status" column formatter.
	 *
	 * @param   array $item   The current item.
	 * @return  string  The cell formatted, ready to print.
	 * @since    1.0.0
	 */
	protected function column_status( $item ) {
		$status = ( $item['running'] ? '▶&nbsp;' . esc_html__( 'Running', 'mailarchiver' ) : '❙❙&nbsp;' . esc_html__( 'Paused', 'mailarchiver' ) );
		return $status;
	}

	/**
	 * "details" column formatter.
	 *
	 * @param   array $item   The current item.
	 * @return  string  The cell formatted, ready to print.
	 * @since    1.0.0
	 */
	protected function column_details( $item ) {
		$list = [ esc_html__( 'Standard', 'mailarchiver' ) ];
		foreach ( $item['processors'] as $processor ) {
			$list[] = $this->processor_types->get( $processor )['name'];
		}
		return implode( ', ', $list );
	}

	/**
	 * "minimal level" column formatter.
	 *
	 * @param   array $item   The current item.
	 * @return  string  The cell formatted, ready to print.
	 * @since    1.0.0
	 */
	protected function column_level( $item ) {
		$name = ucfirst( strtolower( Log::level_name( $item['level'] ) ) );
		return $name;
	}

	/**
	 * Enumerates columns.
	 *
	 * @return      array   The columns.
	 * @since    1.0.0
	 */
	public function get_columns() {
		$columns = [
			'name'    => esc_html__( 'Logger', 'mailarchiver' ),
			'status'  => esc_html__( 'Status', 'mailarchiver' ),
			'level'   => esc_html__( 'Minimal level', 'mailarchiver' ),
			'details' => esc_html__( 'Reported details', 'mailarchiver' ),
		];
		return $columns;
	}

	/**
	 * Enumerates hidden columns.
	 *
	 * @return      array   The hidden columns.
	 * @since    1.0.0
	 */
	protected function get_hidden_columns() {
		return [];
	}

	/**
	 * Enumerates sortable columns.
	 *
	 * @return      array   The sortable columns.
	 * @since    1.0.0
	 */
	protected function get_sortable_columns() {
		$sortable_columns = [
			'name' => [ 'name', true ],
		];
		return $sortable_columns;
	}

	/**
	 * Enumerates bulk actions.
	 *
	 * @return      array   The bulk actions.
	 * @since    1.0.0
	 */
	public function get_bulk_actions() {
		return [];
	}

	/**
	 * Prepares the list to be displayed.
	 *
	 * @since    1.0.0
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = $this->get_hidden_columns();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = [ $columns, $hidden, $sortable ];
		$data                  = $this->loggers;
		usort(
			$data,
			function ( $a, $b ) {
				//phpcs:ignore
				$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'name';
				//phpcs:ignore
				$order  = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc';
				$result = strcmp( strtolower( $a[ $orderby ] ), strtolower( $b[ $orderby ] ) );
				return ( 'asc' === $order ) ? -$result : $result;
			}
		);
		$this->items = $data;
	}

}
