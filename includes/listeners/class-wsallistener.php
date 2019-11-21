<?php
/**
 * WP Security Audit Log listener for MailArchiver.
 *
 * Defines class for WP Security Audit Log listener.
 *
 * @package Listeners
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.6.0
 */

namespace Decalog\Listener;

use Decalog\System\Option;
use Decalog\Plugin\Feature\EventTypes;

/**
 * WP Security Audit Log listener for MailArchiver.
 *
 * Defines methods and properties for WP Security Audit Log class.
 *
 * @package Listeners
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.6.0
 */
class WsalListener extends AbstractListener {

	/**
	 * Sets the listener properties.
	 *
	 * @since    1.0.0
	 */
	protected function init() {
		$this->id      = 'wsal';
		$this->class   = 'plugin';
		$this->product = 'WP Security Audit Log';
		$this->name    = 'WP Security Audit Log';
		if ( defined( 'WSAL_VERSION' ) ) {
			$this->version = WSAL_VERSION;
		} else {
			$this->version = 'x';
		}
		if ( function_exists( 'wsal_freemius' ) && wsal_freemius()->is_premium() ) {
			$this->name .= ' Premium';
		} elseif ( function_exists( 'wsal_freemius' ) && wsal_freemius()->is_trial() ) {
			$this->name .= ' Trial';
		} elseif ( function_exists( 'wsal_freemius' ) && wsal_freemius()->is_free_plan() ) {
			$this->name .= ' Free';
		}
	}

	/**
	 * Verify if this listener is needed, mainly by verifying if the listen plugin/theme is loaded.
	 *
	 * @return  boolean     True if listener is needed, false otherwise.
	 * @since    1.6.0
	 */
	protected function is_available() {
		return class_exists( 'WpSecurityAuditLog' );
	}

	/**
	 * "Launch" the listener.
	 *
	 * @return  boolean     True if listener was launched, false otherwise.
	 * @since    1.6.0
	 */
	protected function launch() {
		add_action( 'wsal_logged_alert', [ $this, 'wsal_logged_alert' ], 10, 6 );
		return true;
	}

	/**
	 * "wsal_logged_alert" event.
	 *
	 * @since    1.6.0
	 */
	public function wsal_logged_alert( $occ, $type, $data, $date, $siteid, $migrated ) {
		if ( $occ instanceof \WSAL_Models_Occurrence ) {
			$message = $occ->GetMessage();
		} else {
			$message = '<unable to retrieve WP Security Audit Log message>';
		}
		$code     = (int) $type;
		$severity = 3;
		if ( array_key_exists( 'Severity', $data ) ) {
			$severity = (int) $data['Severity'];
		}
		switch ( $severity ) {
			case 2:
				$severity = 4;
				break;
			case 4:
			case 5:
				$severity = $severity + 1;
				break;
		}
		$this->logger->log( EventTypes::$wsal_levels[ $severity ], $message, $code );
	}
}
