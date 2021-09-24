<?php declare(strict_types=1);
/**
 * Mail records processing
 *
 * Adds mail specific records with respect to privacy settings.
 *
 * @package Processors
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Processor;

use MAMonolog\Processor\ProcessorInterface;
use Mailarchiver\System\Hash;
use Mailarchiver\System\PwdProtect;


/**
 * Define the Mail processor functionality.
 *
 * Adds mail specific records with respect to privacy settings.
 *
 * @package Processors
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class MailProcessor implements ProcessorInterface {

	/**
	 * Mailanonymization switch.
	 *
	 * @since  1.0.0
	 * @var    boolean    $mailanonymization    Is mailanonymization activated?
	 */
	private $mailanonymization = false;

	/**
	 * Encryption key.
	 *
	 * @since  1.0.0
	 * @var    string    $encryption    Encryption key
	 */
	private $encryption = '';

	/**
	 * Initializes the class and set its properties.
	 *
	 * @since   1.0.0
	 * @param   boolean $mailanonymize  Optional. Is mailanonymization activated?
	 * @param   string  $encryption     Optional. Encryption key.
	 */
	public function __construct( $mailanonymize = true, $encryption = '' ) {
		$this->mailanonymization = $mailanonymize;
		$this->encryption        = $encryption;
	}

	/**
	 * Invocation of the processor.
	 *
	 * @since   1.0.0
	 * @param   array $record  Array or added records.
	 * @@return array   The modified records.
	 */
	public function __invoke( array $record ): array {
		if ( $this->mailanonymization ) {
			$record['context']['from'] = Hash::simple_hash( (string) $record['context']['from'] );
			$tos                       = [];
			foreach ( $record['context']['to'] as $to ) {
				$tos[] = Hash::simple_hash( (string) $to );
			}
			$record['context']['to'] = $tos;
		}
		if ( 0 < strlen( $this->encryption ) ) {
			if ( PwdProtect::is_available() ) {
				$pwd                               = new PwdProtect( $this->encryption );
				$record['context']['body']['type'] = 'encrypted';
				$record['context']['body']['raw']  = $pwd->encrypt( (string) $record['context']['body']['raw'] );
				$record['context']['body']['text'] = $pwd->encrypt( (string) $record['context']['body']['text'] );
			} else {
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->warning( 'OpenSSL is not available to encrypt mail body.', [ 'code' => 503 ] );
			}
		}
		return $record;
	}
}
