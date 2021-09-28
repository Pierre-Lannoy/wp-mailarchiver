<?php
/**
 * Imap handler for Monolog
 *
 * Handles all features of Imap handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */

namespace Mailarchiver\Handler;

use InfluxDB2\Model\ImportDeclaration;
use MAMonolog\Logger;
use Mailarchiver\System\Imap;

/**
 * Define the Monolog Imap handler.
 *
 * Handles all features of Imap handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */
class ImapHandler extends AbstractBufferedMailHandler {

	/**
	 * The imap stream.
	 *
	 * @since  2.5.0
	 * @var    resource|false    $imap    The imap stream.
	 */
	private $imap = false;

	/**
	 * The server connexion string.
	 *
	 * @since  2.5.0
	 * @var    string    $server    The server connexion string.
	 */
	private $server = '';

	/**
	 * The mailbox root.
	 *
	 * @since  2.5.0
	 * @var    string    $root    The mailbox root.
	 */
	private $root = '';

	/**
	 * The user's name.
	 *
	 * @since  2.5.0
	 * @var    string    $user_name    The user's name.
	 */
	private $user_name = '';

	/**
	 * The user's password.
	 *
	 * @since  2.5.0
	 * @var    string    $user_password    The user's password.
	 */
	private $user_password = '';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param   string  $url            The host and port.
	 * @param   string  $root           The root mailbox.
	 * @param   string  $encryption     The encryption type.
	 * @param   string  $validation     The validation type.
	 * @param   string  $user           The mailbox's user.
	 * @param   string  $pwd            The mailbox's password.
	 * @param   integer $level          Optional. The min level to log.
	 * @param   boolean $bubble         Optional. Has the record to bubble?.
	 * @since    1.0.0
	 */
	public function __construct( $url, $root, $encryption, $validation, $user, $pwd, $level = Logger::INFO, bool $bubble = true ) {
		parent::__construct( $level, true, $bubble );
		$this->server = $url . '/imap/' . $encryption . '/norsh';
		if ( 'notls' !== $encryption ) {
			$this->server .= '/' . $validation;
		}
		$this->server        = '{' . $this->server . '}';
		$this->root          = $root;
		$this->user_name     = $user;
		$this->user_password = $pwd;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function prepare( array $record ): array {
		$message                = [];
		$message['to']          = array_key_exists( 'to', $record['context'] ) ? $record['context']['to'] : '';
		$message['subject']     = array_key_exists( 'subject', $record['context'] ) ? $record['context']['subject'] : '';
		$message['message']     = array_key_exists( 'body', $record['context'] ) && array_key_exists( 'raw', $record['context']['body'] ) ? $record['context']['body']['raw'] : '';
		$message['headers']     = array_key_exists( 'headers', $record['context'] ) ? $record['context']['headers'] : '';
		$message['attachments'] = array_key_exists( 'attachments', $record['context'] ) ? $record['context']['attachments'] : '';
		return $message;
	}

	/**
	 * {@inheritdoc}
	 */
	public function handleBatch( array $records ): void {
		if ( 0 < $records ) {
			$this->imap = Imap::open_mailbox( $this->server, $this->root, $this->user_name, $this->user_password );
			if ( $this->imap ) {
				foreach ( $records as $record ) {
					$this->write( $record );
				}
			} else {
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->error( sprintf( 'Unable to store archives in "%s" imap mailbox.', $this->server . Imap::get_mailbox_name( $this->root, '/' ) ) );
			}
			Imap::close_mailbox( $this->imap );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function write( array $record ): void {
		if ( ! $record['headers'] ) {
			$record['headers'] = [];
		}
		$record['headers'][] = 'X-Archiver: ' . MAILARCHIVER_PRODUCT_SHORTNAME . ' ' . MAILARCHIVER_VERSION;
		$phpmailer           = mailarchiver_wp_mail( $record['to'], $record['subject'], $record['message'], $record['headers'], $record['attachments'] );
		$phpmailer->preSend();
		if ( Imap::save_mail( $this->imap, $this->server, $this->root, $phpmailer->getSentMIMEMessage() ) && $this->error_control ) {
			\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->debug( sprintf( 'Archive successfully stored in "%s" imap mailbox.', $this->server . Imap::get_mailbox_name( $this->root, '/' ) ) );
		} elseif ( $this->error_control ) {
			\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->error( sprintf( 'Unable to store archives in "%s" imap mailbox.', $this->server . Imap::get_mailbox_name( $this->root, '/' ) ) );
		}
	}

}
