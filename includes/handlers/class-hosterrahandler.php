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
use MAMonolog\Logger;
use Mailarchiver\System\Imap;
use Mailarchiver\Handler\ImapHandler;

/**
 * Define the Monolog Hosterra Email handler.
 *
 * Handles all features of Hosterra Email handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.14.0
 */
class HosterraHandler extends ImapHandler {

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
		parent::__construct( 'hosterra.email:993', 'HOSTERRA', 'ssl', 'validate-cert', $user, $pwd, $level, $bubble );
	}

}
