<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package Plugin
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Plugin;

use Mailarchiver\System\Assets;

/**
 * The class responsible for the public-facing functionality of the plugin.
 *
 * @package Plugin
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class Mailarchiver_Public {


	/**
	 * The assets manager that's responsible for handling all assets of the plugin.
	 *
	 * @since  1.0.0
	 * @var    Assets    $assets    The plugin assets manager.
	 */
	protected $assets;

	/**
	 * Initializes the class and set its properties.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->assets = new Assets();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function register_styles() {
		//$this->assets->register_style( MAILARCHIVER_ASSETS_ID, MAILARCHIVER_PUBLIC_URL, 'css/mailarchiver.min.css' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function register_scripts() {
		//$this->assets->register_script( MAILARCHIVER_ASSETS_ID, MAILARCHIVER_PUBLIC_URL, 'js/mailarchiver.min.js', [ 'jquery' ] );
	}

}
