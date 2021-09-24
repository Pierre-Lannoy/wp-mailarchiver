<?php declare(strict_types=1);
/**
 * Fluentd formatter for Monolog
 *
 * Handles all features of Fluentd formatter for Monolog.
 *
 * @package Formatters
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */

namespace Mailarchiver\Formatter;

use MAMonolog\Formatter\FormatterInterface;
use Mailarchiver\Plugin\Feature\EventTypes;

/**
 * Define the Monolog Fluentd formatter.
 *
 * Handles all features of Fluentd formatter for Monolog.
 *
 * @package Formatters
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */
class EmlFileFormatter implements FormatterInterface {

	/**
	 * {@inheritDoc}
	 */
	public function format( array $record ): array {
		if ( ! array_key_exists( 'headers', $record['context'] ) ) {
			$record['context']['headers'] = [];
		}
		$record['context']['headers'][] = 'X-Simulator: ' . MAILARCHIVER_PRODUCT_SHORTNAME . ' ' . MAILARCHIVER_VERSION;
		$message                        = [];
		$now                            = new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
		$message['filename']            = $now->format( 'Y-m-d-H-i-s-u-' ) . ( isset( $record['level'] ) && 200 === (int) $record['level'] ? 'OK' : 'KO' ) . '.eml';
		$message['content']             = $this->simulate_wp_mail(
			array_key_exists( 'to', $record['context'] ) ? $record['context']['to'] : '',
			array_key_exists( 'subject', $record['context'] ) ? $record['context']['subject'] : '',
			array_key_exists( 'body', $record['context'] ) && array_key_exists( 'raw', $record['context']['body'] ) ? $record['context']['body']['raw'] : '',
			array_key_exists( 'headers', $record['context'] ) ? $record['context']['headers'] : '',
			array_key_exists( 'attachments', $record['context'] ) ? $record['context']['attachments'] : ''
		);
		return $message;
	}

	/**
	 * Simulate an email, similar to PHP's mail function.
	 *
	 * It doesn't send mail!
	 *
	 * The default content type is `text/plain` which does not allow using HTML.
	 * However, you can set the content type of the email by using the
	 * {@see 'wp_mail_content_type'} filter.
	 *
	 * The default charset is based on the charset used on the blog. The charset can
	 * be set using the {@see 'wp_mail_charset'} filter.
	 *
	 * @param string|string[] $to          Array or comma-separated list of email addresses to send message.
	 * @param string          $subject     Email subject.
	 * @param string          $message     Message contents.
	 * @param string|string[] $headers     Optional. Additional headers.
	 * @param string|string[] $attachments Optional. Paths to files to attach.
	 * @return bool Whether the email was sent successfully.
	 *
	 * @since 2.5.0
	 *
	 */
	private function simulate_wp_mail( $to, $subject, $message, $headers = '', $attachments = [] ) {
		$phpmailer = mailarchiver_wp_mail( $to, $subject, $message, $headers, $attachments );
		$phpmailer->preSend();
		return $phpmailer->getSentMIMEMessage();
	}

	/**
	 * {@inheritDoc}
	 */
	public function formatBatch( array $records ): array {
		return [];
	}
}
