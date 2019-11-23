<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package Plugin
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Plugin;

use Mailarchiver\System\Loader;
use Mailarchiver\Plugin\Initializer;
use Mailarchiver\System\I18n;
use Mailarchiver\System\Assets;
use Mailarchiver\Library\Libraries;
use Mailarchiver\System\Nag;
use Mailarchiver\Plugin\Feature\ArchiverMaintainer;
use Mailarchiver\Listener\ListenerFactory;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @package Plugin
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class Core {


	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since  1.0.0
	 * @var    Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->loader = new Loader();
		$this->set_locale();
		$this->define_global_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since  1.0.0
	 */
	private function set_locale() {
		$plugin_i18n = new I18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the features of the plugin.
	 *
	 * @since  1.0.0
	 */
	private function define_global_hooks() {
		$bootstrap = new Initializer();
		$assets    = new Assets();
		$updater   = new Updater();
		$libraries = new Libraries();
		$listeners = new ListenerFactory();
		$this->loader->add_action( 'plugins_loaded', $bootstrap, 'initialize', 0 );
		$this->loader->add_action( 'plugins_loaded', $listeners, 'launch', 1 );
		$this->loader->add_action( 'plugins_loaded', $listeners, 'launch_late_init', PHP_INT_MAX );
		$this->loader->add_action( 'wp_head', $assets, 'prefetch' );
		$this->loader->add_action( 'auto_update_plugin', $updater, 'auto_update_plugin', 10, 2 );
		add_shortcode( 'mailarchiver-changelog', [ $updater, 'sc_get_changelog' ] );
		add_shortcode( 'mailarchiver-libraries', [ $libraries, 'sc_get_list' ] );
		add_shortcode( 'mailarchiver-statistics', [ 'Mailarchiver\System\Statistics', 'sc_get_raw' ] );
		if ( ! wp_next_scheduled( MAILARCHIVER_CRON_NAME ) ) {
			wp_schedule_event( time(), 'twicedaily', MAILARCHIVER_CRON_NAME );
		}
		$maintainer = new ArchiverMaintainer();
		$this->loader->add_action( MAILARCHIVER_CRON_NAME, $maintainer, 'cron_clean' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Mailarchiver_Admin();
		$nag          = new Nag();
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'register_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'register_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'init_admin_menus' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'init_settings_sections' );
		$this->loader->add_filter( 'plugin_action_links_' . plugin_basename( MAILARCHIVER_PLUGIN_DIR . MAILARCHIVER_SLUG . '.php' ), $plugin_admin, 'add_actions_links', 10, 4 );
		$this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'add_row_meta', 10, 2 );
		$this->loader->add_action( 'admin_notices', $nag, 'display' );
		$this->loader->add_action( 'wp_ajax_hide_mailarchiver_nag', $nag, 'hide_callback' );
		$this->loader->add_filter( 'myblogs_blog_actions', $plugin_admin, 'blog_action', 10, 2 );
		$this->loader->add_filter( 'manage_sites_action_links', $plugin_admin, 'site_action', 10, 3 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 */
	private function define_public_hooks() {
		$plugin_public = new Mailarchiver_Public();
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'register_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'register_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  1.0.0
	 * @return Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Returns a base64 svg resource for the plugin logo.
	 *
	 * @return string The svg resource as a base64.
	 * @since 1.5.0
	 */
	public static function get_base64_logo() {
		$source  = '<svg width="100%" height="100%" viewBox="0 0 1001 1001" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-miterlimit:10;">';
		$source .= '<g id="MailArchiver" transform="matrix(10.0067,0,0,10.0067,0,0)">';
		$source .= '<rect x="0" y="0" width="100" height="100" style="fill:none;"/>';
		$source .= '<clipPath id="_clip1"><rect x="0" y="0" width="100" height="100"/></clipPath>';
		$source .= '<g clip-path="url(#_clip1)">';
		$source .= '<g id="Icon" transform="matrix(0.964549,0,0,0.964549,-0.63865,1.78035)">';
		$source .= '<g transform="matrix(0,1,1,0,60.0193,87.9577)"><path d="M-7.5,-7.5L7.5,-7.5" style="fill:none;fill-rule:nonzero;stroke:rgb(87,159,244);stroke-width:1.73px;"/></g>';
		$source .= '<g transform="matrix(1,0,0,1,7.51934,95.4577)"><path d="M0,0L90,0" style="fill:none;fill-rule:nonzero;stroke:rgb(87,159,244);stroke-width:1.73px;"/></g>';
		$source .= '<g transform="matrix(-1,0,0,1,103.001,83.2866)"><rect x="44" y="9" width="13" height="6" style="fill:rgb(171,207,249);stroke:rgb(171,207,249);stroke-width:1.73px;stroke-linecap:round;stroke-linejoin:round;"/></g>';
		$source .= '<g transform="matrix(-74.0061,0,0,69.5617,89.7095,37.9131)"><path d="M0.932,-0.161C0.969,-0.161 1,-0.13 1,-0.093L1,0.093C1,0.13 0.969,0.161 0.932,0.161L0.079,0.161C0.041,0.161 0.01,0.13 0.01,0.093L0.01,-0.093C0.01,-0.13 0.041,-0.161 0.079,-0.161L0.932,-0.161Z" style="fill:url(#_Linear2);fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(0,-1.72045,-1.83038,0,27.617,35.3325)"><path d="M-1.5,-1.5C-2.329,-1.5 -3,-0.829 -3,0C-3,0.829 -2.329,1.5 -1.5,1.5C-0.671,1.5 0,0.829 0,0C0,-0.829 -0.671,-1.5 -1.5,-1.5" style="fill:white;fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(1.83038,0,0,1.72045,80.0045,37.0529)"><path d="M0,2L-15.242,2C-15.728,2 -16.121,1.606 -16.121,1.121L-16.121,0.879C-16.121,0.394 -15.728,0 -15.242,0L0,0C0.485,0 0.879,0.394 0.879,0.879L0.879,1.121C0.879,1.606 0.485,2 0,2" style="fill:white;fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(-74.0061,0,0,69.5617,89.7095,13.8268)"><path d="M0.932,-0.161C0.969,-0.161 1,-0.13 1,-0.093L1,0.093C1,0.13 0.969,0.161 0.932,0.161L0.079,0.161C0.041,0.161 0.01,0.13 0.01,0.093L0.01,-0.093C0.01,-0.13 0.041,-0.161 0.079,-0.161L0.932,-0.161Z" style="fill:url(#_Linear3);fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(0,-1.72045,-1.83038,0,27.617,11.2461)"><path d="M-1.5,-1.5C-2.329,-1.5 -3,-0.829 -3,0C-3,0.829 -2.329,1.5 -1.5,1.5C-0.671,1.5 0,0.829 0,0C0,-0.829 -0.671,-1.5 -1.5,-1.5" style="fill:white;fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(1.83038,0,0,1.72045,80.0045,11.2461)"><path d="M0,2L-15.242,2C-15.728,2 -16.121,1.606 -16.121,1.121L-16.121,0.879C-16.121,0.394 -15.728,0 -15.242,0L0,0C0.485,0 0.879,0.394 0.879,0.879L0.879,1.121C0.879,1.606 0.485,2 0,2" style="fill:white;fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(-74.0061,0,0,69.5617,89.7095,61.9995)"><path d="M0.932,-0.161C0.969,-0.161 1,-0.13 1,-0.093L1,0.093C1,0.13 0.969,0.161 0.932,0.161L0.079,0.161C0.041,0.161 0.01,0.13 0.01,0.093L0.01,-0.093C0.01,-0.13 0.041,-0.161 0.079,-0.161L0.932,-0.161Z" style="fill:url(#_Linear4);fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(0,-1.72045,-1.83038,0,27.617,61.1393)"><path d="M-1.5,-1.5C-2.329,-1.5 -3,-0.829 -3,0C-3,0.829 -2.329,1.5 -1.5,1.5C-0.671,1.5 0,0.829 0,0C0,-0.829 -0.671,-1.5 -1.5,-1.5" style="fill:white;fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(1.83038,0,0,1.72045,80.0045,61.1393)"><path d="M0,2L-15.242,2C-15.728,2 -16.121,1.606 -16.121,1.121L-16.121,0.879C-16.121,0.394 -15.728,0 -15.242,0L0,0C0.485,0 0.879,0.394 0.879,0.879L0.879,1.121C0.879,1.606 0.485,2 0,2" style="fill:white;fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(0,102.342,95.773,0,52.6879,3.58289)"><path d="M0.217,-0.035L0.661,-0.295C0.674,-0.303 0.69,-0.303 0.703,-0.296C0.716,-0.289 0.724,-0.275 0.724,-0.26L0.724,0.26C0.724,0.275 0.716,0.289 0.703,0.296C0.69,0.303 0.674,0.303 0.661,0.295L0.216,0.035C0.204,0.027 0.197,0.014 0.197,0C0.197,-0.014 0.204,-0.027 0.217,-0.035Z" style="fill:url(#_Linear5);fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(0,-66.7731,-62.4871,0,52.6897,87.2624)"><path d="M0.906,0.03L0.225,0.428C0.201,0.443 0.17,0.426 0.17,0.398L0.17,-0.398C0.17,-0.426 0.201,-0.443 0.225,-0.428L0.906,-0.03C0.917,-0.023 0.922,-0.012 0.922,0C0.922,0.012 0.917,0.023 0.906,0.03Z" style="fill:url(#_Linear6);fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(1.6838,0,0,1.7993,54.7887,65.0716)"><path d="M0,-11.978L-0.395,-3.716C-0.415,-3.26 -0.685,-2.928 -1.267,-2.928C-1.889,-2.928 -2.159,-3.26 -2.18,-3.716L-2.553,-11.978C-2.595,-12.787 -2.097,-13.14 -1.267,-13.14C-0.457,-13.14 0.042,-12.787 0,-11.978M-2.636,-0.187C-2.636,-1.122 -2.138,-1.579 -1.267,-1.579C-0.395,-1.579 0.083,-1.122 0.083,-0.187C0.083,0.726 -0.395,1.162 -1.267,1.162C-2.138,1.162 -2.636,0.726 -2.636,-0.187" style="fill:white;fill-rule:nonzero;"/></g>';
		$source .= '</g>';
		$source .= '</g>';
		$source .= '</g>';
		$source .= '<defs>';
		$source .= '<linearGradient id="_Linear2" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(1,0,0,-1,0,-5.55112e-17)"><stop offset="0" style="stop-color:rgb(25,39,131);stop-opacity:1"/><stop offset="1" style="stop-color:rgb(65,172,255);stop-opacity:1"/></linearGradient>';
		$source .= '<linearGradient id="_Linear3" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(1,0,0,-1,0,0)"><stop offset="0" style="stop-color:rgb(25,39,131);stop-opacity:1"/><stop offset="1" style="stop-color:rgb(65,172,255);stop-opacity:1"/></linearGradient>';
		$source .= '<linearGradient id="_Linear4" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(1,0,0,-1,0,0)"><stop offset="0" style="stop-color:rgb(25,39,131);stop-opacity:1"/><stop offset="1" style="stop-color:rgb(65,172,255);stop-opacity:1"/></linearGradient>';
		$source .= '<linearGradient id="_Linear5" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(1,0,0,-1,0,1.85025e-05)"><stop offset="0" style="stop-color:rgb(248,247,252);stop-opacity:1"/><stop offset="0.08" style="stop-color:rgb(248,247,252);stop-opacity:1"/><stop offset="1" style="stop-color:rgb(65,172,255);stop-opacity:1"/></linearGradient>';
		$source .= '<linearGradient id="_Linear6" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(1,0,0,-1,0,2.88974e-05)"><stop offset="0" style="stop-color:rgb(25,39,131);stop-opacity:1"/><stop offset="1" style="stop-color:rgb(65,172,255);stop-opacity:1"/></linearGradient>';
		$source .= '</defs>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

}
