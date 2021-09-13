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
		$this->define_global_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
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
		$this->loader->add_filter( 'perfopsone_plugin_info', self::class, 'perfopsone_plugin_info' );
		$this->loader->add_action( 'init', $bootstrap, 'initialize', 0 );
		$this->loader->add_action( 'init', $bootstrap, 'late_initialize', PHP_INT_MAX );
		$this->loader->add_action( 'plugins_loaded', $listeners, 'launch', 1 );
		$this->loader->add_action( 'plugins_loaded', $listeners, 'launch_late_init', PHP_INT_MAX );
		$this->loader->add_action( 'wp_head', $assets, 'prefetch' );
		$this->loader->add_action( 'shutdown', 'Mailarchiver\Plugin\Feature\Capture', 'store_archives', MAILARCHIVER_MAX_SHUTDOWN_PRIORITY, 0 );
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
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'finalize_admin_menus', 100 );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'normalize_admin_menus', 110 );
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
	 * Adds full plugin identification.
	 *
	 * @param array $plugin The already set identification information.
	 * @return array The extended identification information.
	 * @since 1.0.0
	 */
	public static function perfopsone_plugin_info( $plugin ) {
		$plugin[ MAILARCHIVER_SLUG ] = [
			'name'    => MAILARCHIVER_PRODUCT_NAME,
			'code'    => MAILARCHIVER_CODENAME,
			'version' => MAILARCHIVER_VERSION,
			'url'     => MAILARCHIVER_PRODUCT_URL,
			'icon'    => self::get_base64_logo(),
		];
		return $plugin;
	}

	/**
	 * Returns a base64 svg resource for the plugin logo.
	 *
	 * @return string The svg resource as a base64.
	 * @since 1.5.0
	 */
	public static function get_base64_logo() {
		$source  = '<svg width="100%" height="100%" viewBox="0 0 1001 1001" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">';
		$source .= '<g id="MailArchiver" transform="matrix(10.0067,0,0,10.0067,0,0)">';
		$source .= '<rect x="0" y="0" width="100" height="100" style="fill:none;"/>';
		$source .= '<g id="Icon" transform="matrix(0.964549,0,0,0.964549,-0.63865,1.78035)">';
		$source .= '<g transform="matrix(-74.0061,0,0,69.5617,89.7095,37.9131)"><path d="M0.932,-0.161C0.969,-0.161 1,-0.13 1,-0.093L1,0.093C1,0.13 0.969,0.161 0.932,0.161L0.079,0.161C0.041,0.161 0.01,0.13 0.01,0.093L0.01,-0.093C0.01,-0.13 0.041,-0.161 0.079,-0.161L0.932,-0.161Z" style="fill:url(#_Linear1);fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(0,-1.72045,-1.83038,0,26.7882,37.0938)"><path d="M-1.5,-1.5C-2.329,-1.5 -3,-0.829 -3,0C-3,0.829 -2.329,1.5 -1.5,1.5C-0.671,1.5 0,0.829 0,0C0,-0.829 -0.671,-1.5 -1.5,-1.5" style="fill:white;fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(1.83038,0,0,1.72045,80.0045,37.0529)"><path d="M0,2L-15.242,2C-15.728,2 -16.121,1.606 -16.121,1.121L-16.121,0.879C-16.121,0.394 -15.728,0 -15.242,0L0,0C0.485,0 0.879,0.394 0.879,0.879L0.879,1.121C0.879,1.606 0.485,2 0,2" style="fill:white;fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(-74.0061,0,0,69.5617,89.7095,13.8268)"><path d="M0.932,-0.161C0.969,-0.161 1,-0.13 1,-0.093L1,0.093C1,0.13 0.969,0.161 0.932,0.161L0.079,0.161C0.041,0.161 0.01,0.13 0.01,0.093L0.01,-0.093C0.01,-0.13 0.041,-0.161 0.079,-0.161L0.932,-0.161Z" style="fill:url(#_Linear2);fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(0,-1.72045,-1.83038,0,27.617,11.2461)"><path d="M-1.5,-1.5C-2.329,-1.5 -3,-0.829 -3,0C-3,0.829 -2.329,1.5 -1.5,1.5C-0.671,1.5 0,0.829 0,0C0,-0.829 -0.671,-1.5 -1.5,-1.5" style="fill:white;fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(1.83038,0,0,1.72045,80.0045,11.2461)"><path d="M0,2L-15.242,2C-15.728,2 -16.121,1.606 -16.121,1.121L-16.121,0.879C-16.121,0.394 -15.728,0 -15.242,0L0,0C0.485,0 0.879,0.394 0.879,0.879L0.879,1.121C0.879,1.606 0.485,2 0,2" style="fill:white;fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(-74.0061,0,0,69.5617,89.7095,61.9995)"><path d="M0.932,-0.161C0.969,-0.161 1,-0.13 1,-0.093L1,0.093C1,0.13 0.969,0.161 0.932,0.161L0.079,0.161C0.041,0.161 0.01,0.13 0.01,0.093L0.01,-0.093C0.01,-0.13 0.041,-0.161 0.079,-0.161L0.932,-0.161Z" style="fill:url(#_Linear3);fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(0,-1.72045,-1.83038,0,26.7882,62.9006)"><path d="M-1.5,-1.5C-2.329,-1.5 -3,-0.829 -3,0C-3,0.829 -2.329,1.5 -1.5,1.5C-0.671,1.5 0,0.829 0,0C0,-0.829 -0.671,-1.5 -1.5,-1.5" style="fill:white;fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(1.83038,0,0,1.72045,80.0045,61.1393)"><path d="M0,2L-15.242,2C-15.728,2 -16.121,1.606 -16.121,1.121L-16.121,0.879C-16.121,0.394 -15.728,0 -15.242,0L0,0C0.485,0 0.879,0.394 0.879,0.879L0.879,1.121C0.879,1.606 0.485,2 0,2" style="fill:white;fill-rule:nonzero;"/></g>';
		$source .= '</g>';
		$source .= '<g id="Icon1" serif:id="Icon" transform="matrix(2.05703,0,0,2.05703,-23.7858,-22.5367)">';
		$source .= '<g transform="matrix(15.8005,0,0,-15.4118,37.2789,49.9564)"><rect x="-0.038" y="-0.403" width="1.288" height="0.805" style="fill:url(#_Linear4);"/></g>';
		$source .= '<g transform="matrix(0,-8.53333,-8.74855,0,46.7135,51.2038)"><path d="M0.873,1.147L0.176,0.009L0.873,-1.147L0.873,1.147Z" style="fill:url(#_Linear5);fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(0,9.69104,9.93546,0,47.282,44.0298)"><path d="M0.057,-0.811L0.168,-0.811L0.615,-0.028L0.13,0.811L0.057,0.811L0.057,-0.811Z" style="fill:url(#_Linear6);fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(7.51692e-18,8.33083,8.66371,7.332e-18,46.8578,36.3)"><path d="M0.894,-1.175L0.894,1.175L0.181,0.062L0.894,-1.175Z" style="fill:url(#_Linear7);fill-rule:nonzero;"/></g>';
		$source .= '<g transform="matrix(0,-19.1174,-19.5995,0,47.282,51.1076)"><path d="M0.515,0.177L0.515,0.108L0.115,0.108L0.059,0.019L0.131,-0.108L0.515,-0.108L0.515,-0.177L0.722,0L0.515,0.177Z" style="fill:url(#_Linear8);fill-rule:nonzero;"/></g>';
		$source .= '</g>';
		$source .= '</g>';
		$source .= '<defs>';
		$source .= '<linearGradient id="_Linear1" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(1,0,0,-1,0,-5.55112e-17)"><stop offset="0" style="stop-color:rgb(25,39,131);stop-opacity:1"/><stop offset="1" style="stop-color:rgb(65,172,255);stop-opacity:1"/></linearGradient>';
		$source .= '<linearGradient id="_Linear2" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(1,0,0,-1,0,0)"><stop offset="0" style="stop-color:rgb(25,39,131);stop-opacity:1"/><stop offset="1" style="stop-color:rgb(65,172,255);stop-opacity:1"/></linearGradient>';
		$source .= '<linearGradient id="_Linear3" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(1,0,0,-1,0,0)"><stop offset="0" style="stop-color:rgb(25,39,131);stop-opacity:1"/><stop offset="1" style="stop-color:rgb(65,172,255);stop-opacity:1"/></linearGradient>';
		$source .= '<linearGradient id="_Linear4" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(0,1,1,0,0.606249,-0.606249)"><stop offset="0" style="stop-color:rgb(25,39,131);stop-opacity:1"/><stop offset="1" style="stop-color:rgb(65,172,255);stop-opacity:1"/></linearGradient>';
		$source .= '<linearGradient id="_Linear5" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(1,0,0,-1,0,-4.91737e-05)"><stop offset="0" style="stop-color:rgb(25,39,131);stop-opacity:1"/><stop offset="1" style="stop-color:rgb(65,172,255);stop-opacity:1"/></linearGradient>';
		$source .= '<linearGradient id="_Linear6" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(1,0,0,-1,0,0)"><stop offset="0" style="stop-color:rgb(248,247,252);stop-opacity:1"/><stop offset="1" style="stop-color:rgb(65,172,255);stop-opacity:1"/></linearGradient>';
		$source .= '<linearGradient id="_Linear7" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(1,0,0,-1,0,5.0369e-05)"><stop offset="0" style="stop-color:rgb(25,39,131);stop-opacity:1"/><stop offset="1" style="stop-color:rgb(65,172,255);stop-opacity:1"/></linearGradient>';
		$source .= '<linearGradient id="_Linear8" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(1,0,0,-1,0,2.22045e-16)"><stop offset="0" style="stop-color:rgb(255,216,111);stop-opacity:1"/><stop offset="1" style="stop-color:rgb(255,147,8);stop-opacity:1"/></linearGradient>';
		$source .= '</defs>';
		$source .= '</svg>';
		// phpcs:ignore
		return 'data:image/svg+xml;base64,' . base64_encode( $source );
	}

}
