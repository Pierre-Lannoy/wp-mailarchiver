<?php
/**
 * Conversion handling
 *
 * Handles all conversion operations.
 *
 * @package System
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\System;

/**
 * Define the conversion functionality.
 *
 * Handles all conversion operations.
 *
 * @package System
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class Conversion {

	/**
	 * Initializes the class and set its properties.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Get a shortened number.
	 *
	 * @param   float   $number    The number to shorten.
	 * @param   integer $precision Optional. The decimal numbers.
	 * @param   boolean $detail    Optional. Give the detail of the shortening.
	 * @param   string  $separator Optional. Unit separator.
	 * @return  string|array  The shortened number.
	 * @since   1.0.0
	 */
	public static function number_shorten( $number, $precision = 2, $detail = false, $separator = '' ) {
		$divisors = [
			pow( 1000, 0 ) => '',
			pow( 1000, 1 ) => esc_html_x( 'K', 'Abbreviation - Stands for "thousand".', 'mailarchiver' ),
			pow( 1000, 2 ) => esc_html_x( 'M', 'Abbreviation - Stands for "million".', 'mailarchiver' ),
			pow( 1000, 3 ) => esc_html_x( 'B', 'Abbreviation - Stands for "billion".', 'mailarchiver' ),
			pow( 1000, 4 ) => esc_html_x( 'T', 'Abbreviation - Stands for "trillion".', 'mailarchiver' ),
			pow( 1000, 5 ) => esc_html_x( 'Qa', 'Abbreviation - Stands for "quadrillion".', 'mailarchiver' ),
			pow( 1000, 6 ) => esc_html_x( 'Qi', 'Abbreviation - Stands for "quintillion".', 'mailarchiver' ),
		];
		foreach ( $divisors as $divisor => $shorthand ) {
			if ( abs( $number ) < ( $divisor * 1000 ) ) {
				break;
			}
		}
		if ( $detail ) {
			return [
				'value'        => number_format( $number / $divisor, $precision, '.', '' ),
				'divisor'      => $divisor,
				'abbreviation' => $shorthand,
				'base'         => 1000,
			];
		} else {
			return 0 + number_format( $number / $divisor, $precision, '.', '' ) . $separator . $shorthand;
		}
	}

	/**
	 * Get a shortened data.
	 *
	 * @param   float   $number    The data to shorten.
	 * @param   integer $precision Optional. The decimal numbers.
	 * @param   boolean $detail    Optional. Give the detail of the shortening.
	 * @param   string  $separator Optional. Unit separator.
	 * @return  string|array  The shortened data.
	 * @since   1.0.0
	 */
	public static function data_shorten( $number, $precision = 2, $detail = false, $separator = '' ) {
		$divisors = [
			pow( 1024, 0 ) => esc_html_x( 'B', 'Abbreviation - Stands for "byte".', 'mailarchiver' ),
			pow( 1024, 1 ) => esc_html_x( 'KB', 'Abbreviation - Stands for "kilobytes".', 'mailarchiver' ),
			pow( 1024, 2 ) => esc_html_x( 'MB', 'Abbreviation - Stands for "megabytes".', 'mailarchiver' ),
			pow( 1024, 3 ) => esc_html_x( 'GB', 'Abbreviation - Stands for "gigabytes".', 'mailarchiver' ),
			pow( 1024, 4 ) => esc_html_x( 'TB', 'Abbreviation - Stands for "terabytes".', 'mailarchiver' ),
			pow( 1024, 5 ) => esc_html_x( 'PB', 'Abbreviation - Stands for "petabytes".', 'mailarchiver' ),
			pow( 1024, 6 ) => esc_html_x( 'EB', 'Abbreviation - Stands for "exabytes".', 'mailarchiver' ),
		];
		foreach ( $divisors as $divisor => $shorthand ) {
			if ( abs( $number ) < ( $divisor * 1024 ) ) {
				break;
			}
		}
		if ( $detail ) {
			return [
				'value'        => number_format( $number / $divisor, $precision, '.', '' ),
				'divisor'      => $divisor,
				'abbreviation' => $shorthand,
				'base'         => 1024,
			];
		} else {
			return 0 + number_format( $number / $divisor, $precision, '.', '' ) . $separator . $shorthand;
		}
	}

}