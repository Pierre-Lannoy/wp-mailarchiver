<?php
/**
 * Events list
 *
 * Lists all events.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Plugin\Feature;

use Mailarchiver\System\Date;

use Mailarchiver\System\Option;
use Mailarchiver\System\Role;
use Mailarchiver\System\Timezone;
use Mailarchiver\System\User;
use Feather\Icons;


if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Define the events list functionality.
 *
 * Lists all events.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class Events extends \WP_List_Table {

	/**
	 * The available events logs.
	 *
	 * @since    1.0.0
	 * @var      array    $logs    The archivers list.
	 */
	private static $logs = [];

	/**
	 * The columns always shown.
	 *
	 * @since    1.0.0
	 * @var      array    $standard_columns    The columns always shown.
	 */
	private static $standard_columns = [];

	/**
	 * The columns which may be shown.
	 *
	 * @since    1.0.0
	 * @var      array    $extra_columns    The columns which may be shown.
	 */
	private static $extra_columns = [];

	/**
	 * The columns which must be shown to the current user.
	 *
	 * @since    1.0.0
	 * @var      array    $extra_columns    The columns which must be shown to the current user.
	 */
	private static $user_columns = [];

	/**
	 * The order of the columns.
	 *
	 * @since    1.0.0
	 * @var      array    $columns_order    The order of the columns.
	 */
	private static $columns_order = [];

	/**
	 * The events types icons.
	 *
	 * @since    1.0.0
	 * @var      array    $icons    The icons list.
	 */
	private $icons = [];

	/**
	 * The number of lines to display.
	 *
	 * @since    1.0.0
	 * @var      integer    $limit    The number of lines to display.
	 */
	private $limit = 25;

	/**
	 * The archiver ID.
	 *
	 * @since    1.0.0
	 * @var      string    $archiver    The archiver ID.
	 */
	private $archiver = null;

	/**
	 * The main filter.
	 *
	 * @since    1.0.0
	 * @var      array    $filters    The main filter.
	 */
	private $filters = [];

	/**
	 * Forces the site_id filter if set.
	 *
	 * @since    1.0.0
	 * @var      integer    $force_siteid    Forces the site_id filter if set.
	 */
	private $force_siteid = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		parent::__construct(
			[
				'singular' => 'email',
				'plural'   => 'emails',
				'ajax'     => true,
			]
		);
	}

	/**
	 * Default column formatter.
	 *
	 * @param   object $item   The current item to render.
	 * @param   string $column_name    The name of the current rendered column.
	 * @return  string  The cell formatted, ready to print.
	 * @since   1.0.0
	 */
	protected function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * "mail" column formatter.
	 *
	 * @param   object $item   The current item to render.
	 * @return  string  The cell formatted, ready to print.
	 * @since   1.0.0
	 */
	protected function column_mail( $item ) {
		$args            = [];
		$args['page']    = 'mailarchiver-viewer';
		$args['logid']   = $this->archiver;
		$args['eventid'] = $item['id'];
		$url             = add_query_arg( $args, admin_url( 'admin.php' ) );
		$icon            = '<img style="width:18px;float:left;padding-right:6px;" src="' . EventTypes::$icons[ $item['level'] ] . '" />';
		$name            = '<a href="' . $url . '">' . mailarchiver_strip_script_tags( $item['subject'], true ) . '</a>';
		$result          = $icon . $name;
		return $result;
	}

	/**
	 * "to" column formatter.
	 *
	 * @param   object $item   The current item to render.
	 * @return  string  The cell formatted, ready to print.
	 * @since   1.0.0
	 */
	protected function column_to( $item ) {
		$items = \json_decode( $item['to'], true );
		$tos   = [];
		foreach ( $items as $item ) {
			if ( 0 === strpos( $item, '{' ) ) {
				$email = '<em>' . esc_html__( 'Masked address', 'mailarchiver' ) . '</em>';
			} else {
				// phpcs:ignore
				$email = $item;
			}
			$tos[] = $email . $this->get_filter( 'to', $item );
		}
		$result = implode( '<br/>', $tos );
		if ( 1 === count( $items ) && ! ( 0 === strpos( $items[0], '{' ) ) ) {
			$user = get_user_by( 'email', $items[0] );
			if ( false !== $user ) {
				// phpcs:ignore
				$result = $result . '<br /><span style="color:silver">' . User::get_user_string( $user->ID ) . '</span>';
			}
		}
		return $result;
	}

	/**
	 * "time" column formatter.
	 *
	 * @param   object $item   The current item to render.
	 * @return  string  The cell formatted, ready to print.
	 * @since   1.0.0
	 */
	protected function column_time( $item ) {
		$result  = Date::get_date_from_mysql_utc( $item['timestamp'], Timezone::network_get()->getName(), 'Y-m-d H:i:s' );
		$result .= '<br /><span style="color:silver">' . Date::get_positive_time_diff_from_mysql_utc( $item['timestamp'] ) . '</span>';
		return $result;
	}

	/**
	 * "attachments" column formatter.
	 *
	 * @param   object $item   The current item to render.
	 * @return  string  The cell formatted, ready to print.
	 * @since   1.0.0
	 */
	protected function column_attachments( $item ) {
		$result = '-';
		$att    = \json_decode( $item['attachments'] );
		if ( is_array( $att ) ) {
			$result = count ( $att );
		}
		return (string) $result;
	}

	/**
	 * "site" column formatter.
	 *
	 * @param   object $item   The current item to render.
	 * @return  string  The cell formatted, ready to print.
	 * @since   1.0.0
	 */
	protected function column_site( $item ) {
		$name = $item['site_name'] . $this->get_filter( 'site_id', $item['site_id'] );
		// phpcs:ignore
		$result = $name . '<br /><span style="color:silver">' . sprintf(esc_html__('Site ID %s', 'mailarchiver'), $item['site_id']) . '</span>';
		return $result;
	}

	/**
	 * "user" column formatter.
	 *
	 * @param   object $item   The current item to render.
	 * @return  string  The cell formatted, ready to print.
	 * @since   1.0.0
	 */
	protected function column_user( $item ) {
		$user = $item['user_name'];
		if ( 'anonymous' === $user ) {
			$user = '<em>' . esc_html__( 'Anonymous user', 'mailarchiver' ) . '</em>';
		}
		$id = '';
		if ( 0 === strpos( $item['user_name'], '{' ) ) {
			$user = '<em>' . esc_html__( 'Pseudonymized user', 'mailarchiver' ) . '</em>';
		} elseif ( 0 !== (int) $item['user_id'] ) {
			// phpcs:ignore
			$id = '<br /><span style="color:silver">' . sprintf( esc_html__( 'User ID %s', 'mailarchiver' ), $item[ 'user_id' ] ) . '</span>';
		}
		$result = $user . $this->get_filter( 'user_id', $item['user_id'] ) . $id;
		return $result;
	}

	/**
	 * "ip" column formatter.
	 *
	 * @param   object $item   The current item to render.
	 * @return  string  The cell formatted, ready to print.
	 * @since   1.0.0
	 */
	protected function column_ip( $item ) {
		$ip = $item['remote_ip'];
		if ( 0 === strpos( $ip, '{' ) ) {
			$ip = '<em>' . esc_html__( 'Obfuscated', 'mailarchiver' ) . '</em>';
		}
		$result = $ip . $this->get_filter( 'remote_ip', $item['remote_ip'] );
		return $result;
	}

	/**
	 * Initialize the list view.
	 *
	 * @return  array   The columns to render.
	 * @since 1.0.0
	 */
	public function get_columns() {
		$columns = [];
		foreach ( self::$columns_order as $column ) {
			if ( array_key_exists( $column, self::$standard_columns ) ) {
				$columns[ $column ] = self::$standard_columns[ $column ];
				// phpcs:ignore
			} elseif ( array_key_exists( $column, self::$extra_columns ) && in_array( $column, self::$user_columns, true ) ) {
				$columns[ $column ] = self::$extra_columns[ $column ];
			}
		}
		return $columns;
	}

	/**
	 * Initialize values and filter.
	 *
	 * @since 1.0.0
	 */
	protected function init_values() {
		$this->limit = filter_input( INPUT_GET, 'limit', FILTER_SANITIZE_NUMBER_INT );
		if ( ! $this->limit ) {
			$this->limit = 25;
		}
		$this->force_siteid = null;
		$this->archiver     = filter_input( INPUT_GET, 'archiver_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( $this->archiver ) {
			$this->set_level_access();
		} else {
			$this->set_first_available();
		}
		$this->filters = [];
		$level         = filter_input( INPUT_GET, 'level', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( $level && array_key_exists( strtolower( $level ), EventTypes::$levels ) && 'debug' !== strtolower( $level ) ) {
			$this->filters['level'] = strtolower( $level );
		}
		foreach ( [ 'to', 'site_id', 'user_id', 'remote_ip' ] as $f ) {
			$v = filter_input( INPUT_GET, $f, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			if ( $v ) {
				$this->filters[ $f ] = strtolower( $v );
			}
		}
		if ( $this->force_siteid ) {
			$this->filters['site_id'] = $this->force_siteid;
		}
	}

	/**
	 * Get the filter image.
	 *
	 * @param   string  $filter     The filter name.
	 * @param   string  $value      The filter value.
	 * @param   boolean $soft       Optional. The image must be softened.
	 * @return  string  The filter image, ready to print.
	 * @since   1.0.0
	 */
	protected function get_filter( $filter, $value, $soft = false ) {
		$filters = $this->filters;
		if ( array_key_exists( $filter, $this->filters ) && $value === $this->filters[ $filter ] ) {
			unset( $this->filters[ $filter ] );
			$url    = $this->get_page_url();
			$alt    = esc_html__( 'Remove this filter', 'mailarchiver' );
			$fill   = '#9999FF';
			$stroke = '#0000AA';
		} else {
			$this->filters[ $filter ] = $value;
			$url                      = $this->get_page_url();
			$alt                      = esc_html__( 'Add as filter', 'mailarchiver' );
			$fill                     = 'none';
			if ( $soft ) {
				$stroke = '#C0C0FF';
			} else {
				$stroke = '#3333AA';
			}
		}
		$this->filters = $filters;
		return '&nbsp;<a href="' . $url . '"><img title="' . $alt . '" style="width:11px;vertical-align:baseline;" src="' . Icons::get_base64( 'filter', $fill, $stroke ) . '" /></a>';
	}

	/**
	 * Initialize the list view.
	 *
	 * @since 1.0.0
	 */
	public function prepare_items() {
		$this->init_values();
		$columns               = $this->get_columns();
		$hidden                = [];
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = [ $columns, $hidden, $sortable ];
		$current_page          = $this->get_pagenum();
		$total_items           = $this->get_count();
		$this->items           = $this->get_list( ( $current_page - 1 ) * $this->limit, $this->limit );
		$this->set_pagination_args(
			[
				'total_items' => $total_items,
				'per_page'    => $this->limit,
				'total_pages' => ceil( $total_items / $this->limit ),
			]
		);
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @param string $which Position of extra control.
	 * @since 1.0.0
	 */
	protected function display_tablenav( $which ) {
		echo '<div class="tablenav ' . esc_attr( $which ) . '">';
		$this->extra_tablenav( $which );
		$this->pagination( $which );
		echo '<br class="clear" />';
		echo '</div>';
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination.
	 *
	 * @param string $which Position of extra control.
	 * @since 1.0.0
	 */
	public function extra_tablenav( $which ) {
		$list = $this;
		$args = compact( 'list' );
		foreach ( $args as $key => $val ) {
			$$key = $val;
		}
		if ( 'top' === $which ) {
			include MAILARCHIVER_ADMIN_DIR . 'partials/mailarchiver-admin-view-events-top.php';
		}
		if ( 'bottom' === $which ) {
			include MAILARCHIVER_ADMIN_DIR . 'partials/mailarchiver-admin-view-events-bottom.php';
		}
	}

	/**
	 * Get the page url with args.
	 *
	 * @return  string  The url.
	 * @since 1.0.0
	 */
	public function get_page_url() {
		$args                = [];
		$args['page']        = 'mailarchiver-viewer';
		$args['archiver_id'] = $this->archiver;
		if ( count( $this->filters ) > 0 ) {
			foreach ( $this->filters as $key => $filter ) {
				if ( '' !== $filter ) {
					$args[ $key ] = $filter;
				}
			}
		}
		if ( 25 !== $this->limit ) {
			$args['limit'] = $this->limit;
		}
		$url = add_query_arg( $args, admin_url( 'admin.php' ) );
		return $url;
	}

	/**
	 * Get available views line.
	 *
	 * @since 1.0.0
	 */
	public function get_views() {
		$filters = $this->filters;
		$level   = array_key_exists( 'level', $this->filters ) ? $this->filters['level'] : '';
		unset( $this->filters['level'] );
		$s1                     = '<a href="' . $this->get_page_url() . '"' . ( '' === $level ? ' class="current"' : '' ) . '>' . esc_html__( 'All', 'mailarchiver' ) . ' <span class="count">(' . $this->get_count() . ')</span></a>';
		$this->filters['level'] = 'error';
		$s2                     = '<a href="' . $this->get_page_url() . '"' . ( 'error' === $level ? ' class="current"' : '' ) . '>' . esc_html__( 'Errors', 'mailarchiver' ) . ' <span class="count">(' . $this->get_count() . ')</span></a>';
		$status_links           = [
			'all'   => $s1,
			'error' => $s2,
		];
		$this->filters          = $filters;
		return $status_links;
	}

	/**
	 * Get the available events logs.
	 *
	 * @return  array   The list of available events logs.
	 * @since    1.0.0
	 */
	public function get_archivers() {
		return self::$logs;
	}

	/**
	 * Get the available events logs.
	 *
	 * @return  array   The list of available events logs.
	 * @since    1.0.0
	 */
	public static function get() {
		return self::$logs;
	}

	/**
	 * Get the current events log id.
	 *
	 * @return  string   The current events log id.
	 * @since    1.0.0
	 */
	public function get_current_Log_id() {
		return $this->archiver;
	}

	/**
	 * Get available lines breakdowns.
	 *
	 * @since 1.0.0
	 */
	public function get_line_number_select() {
		$_disp  = [ 25, 50, 100, 250, 500 ];
		$result = [];
		foreach ( $_disp as $d ) {
			$l          = [];
			$l['value'] = $d;
			// phpcs:ignore
			$l['text']     = sprintf( esc_html__( 'Show %d lines per page', 'mailarchiver' ), $d );
			$l['selected'] = ( $d === (int) $this->limit ? 'selected="selected" ' : '' );
			$result[]      = $l;
		}
		return $result;
	}

	/**
	 * Set the level access to an events log.
	 *
	 * @since    1.0.0
	 */
	private function set_level_access() {
		$this->force_siteid = null;
		$id                 = $this->archiver;
		$this->archiver     = null;
		foreach ( self::$logs as $log ) {
			if ( $id === $log['id'] ) {
				$this->archiver = $id;
				if ( array_key_exists( 'limit', $log ) ) {
					$this->force_siteid = $log['limit'];
				}
			}
		}
	}

	/**
	 * Set the level access to an events log.
	 *
	 * @since    1.0.0
	 */
	private function set_first_available() {
		$this->force_siteid = null;
		$this->archiver     = null;
		foreach ( self::$logs as $log ) {
			if ( array_key_exists( 'limit', $log ) ) {
				$this->force_siteid = $log['limit'];
			}
			$this->archiver = $log['id'];
			break;
		}
	}

	/**
	 * Get list of logged errors.
	 *
	 * @param integer $offset The offset to record.
	 * @param integer $rowcount Optional. The number of rows to return.
	 * @return array An array containing the filtered logged errors.
	 * @since 3.0.0
	 */
	protected function get_list( $offset = null, $rowcount = null ) {
		$result = [];
		$limit  = '';
		if ( ! is_null( $offset ) && ! is_null( $rowcount ) ) {
			$limit = 'LIMIT ' . $offset . ',' . $rowcount;
		}
		global $wpdb;
		$table_name = $wpdb->base_prefix . 'mailarchiver_' . str_replace( '-', '', $this->archiver );
		$sql        = 'SELECT * FROM ' . $table_name . ' ' . $this->get_where_clause() . ' ORDER BY id DESC ' . $limit;
		// phpcs:ignore
		$query = $wpdb->get_results( $sql, ARRAY_A );
		foreach ( $query as $val ) {
			$result[] = (array) $val;
		}
		return $result;
	}

	/**
	 * Count logged errors.
	 *
	 * @return integer The count of the filtered logged errors.
	 * @since 3.0.0
	 */
	protected function get_count() {
		$result = 0;
		if ( $this->archiver ) {
			global $wpdb;
			$table_name = $wpdb->base_prefix . 'mailarchiver_' . str_replace( '-', '', $this->archiver );
			$sql        = 'SELECT COUNT(*) as CNT FROM ' . $table_name . ' ' . $this->get_where_clause();
			// phpcs:ignore
			$cnt = $wpdb->get_results( $sql, ARRAY_A );
			if ( count( $cnt ) > 0 ) {
				if ( array_key_exists( 'CNT', $cnt[0] ) ) {
					$result = $cnt[0]['CNT'];
				}
			}
		}
		return $result;
	}

	/**
	 * Get "where" clause for log table.
	 *
	 * @return string The "where" clause.
	 * @since 1.0.0
	 */
	private function get_where_clause() {
		$result = '';
		$w      = [];
		foreach ( $this->filters as $key => $filter ) {
			if ( $filter ) {
				if ( 'level' === $key ) {
					$l = [];
					foreach ( EventTypes::$levels as $str => $val ) {
						if ( EventTypes::$levels[ $filter ] <= $val ) {
							$l[] = "'" . $str . "'";
						}
					}
					$w[] = $key . ' IN (' . implode( ',', $l ) . ')';
				} elseif ( 'to' === $key ) {
					$w[] = '`' . $key . '` like "%' . $filter . '%"';
				} else {
					$w[] = '`' . $key . '`="' . $filter . '"';
				}
			}
		}
		if ( count( $w ) > 0 ) {
			$result = 'WHERE (' . implode( ' AND ', $w ) . ')';
		}
		return $result;
	}

	/**
	 * Initialize the meta class and set its columns properties.
	 *
	 * @since    1.0.0
	 */
	private static function load_columns() {
		self::$standard_columns             = [];
		self::$standard_columns['mail']     = esc_html__( 'Email', 'mailarchiver' );
		self::$standard_columns['to']       = esc_html__( 'To', 'mailarchiver' );
		self::$standard_columns['time']     = esc_html__( 'Time', 'mailarchiver' );
		self::$extra_columns                = [];
		self::$extra_columns['attachments'] = esc_html__( 'Attachments', 'mailarchiver' );
		self::$extra_columns['site']        = esc_html__( 'Site', 'mailarchiver' );
		self::$extra_columns['user']        = esc_html__( 'User', 'mailarchiver' );
		self::$extra_columns['ip']          = esc_html__( 'Remote IP', 'mailarchiver' );
		self::$columns_order                = [ 'mail', 'to', 'time', 'attachments', 'site', 'user', 'ip' ];
		self::$user_columns                 = [];
		foreach ( self::$extra_columns as $key => $extra_column ) {
			if ( 'site' !== $key || ( 'site' === $key && is_multisite() ) ) {
				self::$user_columns[] = $key;
			}
		}
	}

	/**
	 * Initialize the meta class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public static function init() {
		self::$logs = [];
		foreach ( Option::network_get( 'archivers' ) as $key => $archiver ) {
			if ( 'WordpressHandler' === $archiver['handler'] ) {
				if ( array_key_exists( 'configuration', $archiver ) ) {
					if ( array_key_exists( 'local', $archiver['configuration'] ) ) {
						$local = $archiver['configuration']['local'];
					} else {
						$local = false;
					}
					if ( Role::SUPER_ADMIN === Role::admin_type() || Role::SINGLE_ADMIN === Role::admin_type() || ( Role::LOCAL_ADMIN === Role::admin_type() && $local ) || Role::override_privileges() ) {
						$log = [
							'name'    => $archiver['name'],
							'running' => $archiver['running'],
							'id'      => $key,
						];
						if ( Role::LOCAL_ADMIN === Role::admin_type() ) {
							$log['limit'] = get_current_blog_id();
						}
						self::$logs[] = $log;
					}
				}
			}
		}
		uasort(
			self::$logs,
			function ( $a, $b ) {
				if ( $a['running'] === $b['running'] ) {
					return strcasecmp( str_replace( ' ', '', $a['name'] ), str_replace( ' ', '', $b['name'] ) );
				} return $a['running'] ? -1 : 1;
			}
		);
		self::load_columns();
	}

	/**
	 * Get the number of available logs.
	 *
	 * @return  integer     The number of logs.
	 * @since    1.0.0
	 */
	public static function archivers_count() {
		return count( self::$logs );
	}
}

Events::init();
