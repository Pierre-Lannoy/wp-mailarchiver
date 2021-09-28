<?php
/**
 * Gandi handler for Monolog
 *
 * Handles all features of Gandi handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */

namespace Mailarchiver\Handler;

use InfluxDB2\Model\ImportDeclaration;
use MAMonolog\Logger;
use Mailarchiver\System\Imap;
use Mailarchiver\Handler\ImapHandler;

/**
 * Define the Monolog Gandi handler.
 *
 * Handles all features of Gandi handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */
class GandiHandler extends ImapHandler {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param   string  $user           The mailbox's user.
	 * @param   string  $pwd            The mailbox's password.
	 * @param   integer $level          Optional. The min level to log.
	 * @param   boolean $bubble         Optional. Has the record to bubble?.
	 * @since    1.0.0
	 */
	public function __construct( $user, $pwd, $level = Logger::INFO, bool $bubble = true ) {
		parent::__construct( 'mail.gandi.net:993', 'GANDI', 'ssl', 'validate-cert', $user, $pwd, $level, $bubble );
	}

}
