<?php
/**
 * WordPress handler utility
 *
 * Handles all features of WordPress handler.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Decalog\Plugin\Feature;

use Decalog\Plugin\Feature\DLogger;
use Decalog\Plugin\Feature\Log;
use Decalog\System\Database;
use Decalog\System\Http;

/**
 * Define the WordPress handler functionality.
 *
 * Handles all features of WordPress handler.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class WordpressHandler {

	/**
	 * The logger definition.
	 *
	 * @since  1.0.0
	 * @var    array    $logger    The logger definition.
	 */
	private $logger = [];

	/**
	 * An instance of DLogger to log internal events.
	 *
	 * @since  1.0.0
	 * @var    DLogger    $log    An instance of DLogger to log internal events.
	 */
	private $log = null;

	/**
	 * The full table name.
	 *
	 * @since  1.0.0
	 * @var    string    $table    The full table name.
	 */
	private $table = '';

	/**
	 * The simple table name.
	 *
	 * @since  1.0.0
	 * @var    string    $table_name    The simple table name.
	 */
	private $table_name = '';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param   array $logger    The logger definition.
	 * @since    1.0.0
	 */
	public function __construct( $logger = [] ) {
		if ( [] !== $logger ) {
			$this->set_logger( $logger );
		}
		$this->log = Log::bootstrap( 'plugin', MAILARCHIVER_PRODUCT_SHORTNAME, MAILARCHIVER_VERSION );
	}

	/**
	 * Set the internal logger.
	 *
	 * @param   array $logger    The logger definition.
	 * @since    1.0.0
	 */
	public function set_logger( $logger ) {
		global $wpdb;
		$this->logger     = $logger;
		$this->table_name = 'mailarchiver_' . str_replace( '-', '', $logger['uuid'] );
		$this->table      = $wpdb->base_prefix . $this->table_name;
	}

	/**
	 * Initialize the logger.
	 *
	 * @since    1.0.0
	 */
	public function initialize() {
		global $wpdb;
		$cl = [];
		foreach ( ClassTypes::$classes as $c ) {
			$cl[] = "'" . $c . "'";
		}
		$classes = implode( ',', $cl );
		$cl      = [];
		foreach ( Http::$verbs as $c ) {
			$cl[] = "'" . $c . "'";
		}
		$verbs = implode( ',', $cl );
		if ( '' !== $this->table ) {
			$charset_collate = 'DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci';
			$sql             = 'CREATE TABLE IF NOT EXISTS ' . $this->table;
			$sql            .= ' (`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,';
			$sql            .= " `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',";
			$sql            .= " `level` enum('emergency','alert','critical','error','warning','notice','info','debug','unknown') NOT NULL DEFAULT 'unknown',";
			$sql            .= " `channel` enum('cli','cron','ajax','xmlrpc','api','feed','wback','wfront','unknown') NOT NULL DEFAULT 'unknown',";
			$sql            .= ' `class` enum(' . $classes . ") NOT NULL DEFAULT 'unknown',";
			$sql            .= " `component` varchar(26) NOT NULL DEFAULT 'Unknown',";
			$sql            .= " `version` varchar(13) NOT NULL DEFAULT 'N/A',";
			$sql            .= " `code` int(11) UNSIGNED NOT NULL DEFAULT '0',";
			$sql            .= " `message` varchar(7500) NOT NULL DEFAULT '-',";
			$sql            .= " `site_id` int(11) UNSIGNED NOT NULL DEFAULT '0',";
			$sql            .= " `site_name` varchar(250) NOT NULL DEFAULT 'Unknown',";
			$sql            .= " `user_id` varchar(66) NOT NULL DEFAULT '0',";  // Needed by SHA-256 pseudonymization.
			$sql            .= " `user_name` varchar(250) NOT NULL DEFAULT 'Unknown',";
			$sql            .= " `remote_ip` varchar(66) NOT NULL DEFAULT '127.0.0.1',";  // Needed by SHA-256 obfuscation.
			$sql            .= " `url` varchar(2083) NOT NULL DEFAULT '-',";
			$sql            .= ' `verb` enum(' . $verbs . ") NOT NULL DEFAULT 'unknown',";
			$sql            .= " `server` varchar(250) NOT NULL DEFAULT 'unknown',";
			$sql            .= " `referrer` varchar(250) NOT NULL DEFAULT '-',";
			$sql            .= " `file` varchar(250) NOT NULL DEFAULT 'unknown',";
			$sql            .= " `line` int(11) UNSIGNED NOT NULL DEFAULT '0',";
			$sql            .= " `classname` varchar(100) NOT NULL DEFAULT 'unknown',";
			$sql            .= " `function` varchar(100) NOT NULL DEFAULT 'unknown',";
			$sql            .= ' `trace` varchar(7500),';
			$sql            .= ' PRIMARY KEY (`id`)';
			$sql            .= ") $charset_collate;";
			// phpcs:ignore
			$wpdb->query( $sql );
			$this->log->debug( sprintf( 'Table "%s" updated or created.', $this->table ) );
		}
	}

	/**
	 * Update the logger.
	 *
	 * @param   string $from   The version from which the plugin is updated.
	 * @since    1.0.0
	 */
	public function update( $from ) {
		global $wpdb;
		// Starting 1.6.0, WordpressHandler is utf8_unicode_ci and "message" and "trace" fields are varchar(7500).
		$sql = 'ALTER TABLE ' . $this->table . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;';
		// phpcs:ignore
		$wpdb->query( $sql );
		$sql = 'ALTER TABLE ' . $this->table . " MODIFY COLUMN message varchar(7500) NOT NULL DEFAULT '-';";
		// phpcs:ignore
		$wpdb->query( $sql );
		$sql = 'ALTER TABLE ' . $this->table . " MODIFY COLUMN trace varchar(7500);";
		// phpcs:ignore
		$wpdb->query( $sql );
		$this->log->debug( sprintf( 'Table "%s" updated.', $this->table ) );
	}

	/**
	 * Finalize the logger.
	 *
	 * @since    1.0.0
	 */
	public function finalize() {
		global $wpdb;
		if ( '' !== $this->table ) {
			$this->log->debug( sprintf( 'Table "%s" dropped.', $this->table ) );
			$sql = 'DROP TABLE IF EXISTS ' . $this->table;
			// phpcs:ignore
			$wpdb->query( $sql );
		}
	}

	/**
	 * Rotate and purge.
	 *
	 * @since    1.0.0
	 */
	public function cron_clean() {
		if ( '' !== $this->table_name ) {
			$count    = 0;
			$database = new Database();
			if ( $hour_done = $database->purge( $this->table_name, 'timestamp', 24 * (int) $this->logger['configuration']['purge'] ) ) {
				$count += $hour_done;
			}
			$limit = $database->count_lines( $this->table_name ) - (int) $this->logger['configuration']['rotate'];
			if ( $limit > 0 ) {
				if ( $max_done = $database->rotate( $this->table_name, 'id', $limit ) ) {
					$count += $max_done;
				}
			}
			if ( 0 === $count ) {
				$this->log->info( sprintf( 'No old records to delete for logger "%s".', $this->logger['name'] ) );
			} elseif ( 1 === $count ) {
				$this->log->info( sprintf( '1 old record deleted for logger "%s".', $this->logger['name'] ) );
			} else {
				$this->log->info( sprintf( '%1$s old records deleted for logger "%2$s".', $count, $this->logger['name'] ) );
			}
		}
	}

}
