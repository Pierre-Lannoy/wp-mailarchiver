<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Plugin
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Plugin;

use Mailarchiver\Plugin\Feature\Archive;
use Mailarchiver\Plugin\Feature\EventViewer;
use Mailarchiver\Plugin\Feature\HandlerTypes;
use Mailarchiver\Plugin\Feature\ProcessorTypes;
use Mailarchiver\Plugin\Feature\ArchiverFactory;
use Mailarchiver\Plugin\Feature\Events;
use Mailarchiver\Plugin\Feature\InlineHelp;
use Mailarchiver\Listener\ListenerFactory;
use Mailarchiver\System\Assets;
use Mailarchiver\System\UUID;
use Mailarchiver\System\Option;
use Mailarchiver\System\Form;
use Mailarchiver\System\Role;
use Mailarchiver\System\Logger;
use Mailarchiver\System\Secret;
use Mailarchiver\System\Environment;
use PerfOpsOne\AdminMenus;

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Plugin
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class Mailarchiver_Admin {

	/**
	 * The assets manager that's responsible for handling all assets of the plugin.
	 *
	 * @since  1.0.0
	 * @var    Assets    $assets    The plugin assets manager.
	 */
	protected $assets;

	/**
	 * The current archiver.
	 *
	 * @since  1.0.0
	 * @var    array    $current_archiver    The current archiver.
	 */
	protected $current_archiver;

	/**
	 * The current handler.
	 *
	 * @since  1.0.0
	 * @var    array    $current_handler    The current handler.
	 */
	protected $current_handler;

	/**
	 * The current view.
	 *
	 * @since  1.0.0
	 * @var    object    $current_view    The current view.
	 */
	protected $current_view = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->assets = new Assets();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function register_styles() {
		$this->assets->register_style( MAILARCHIVER_ASSETS_ID, MAILARCHIVER_ADMIN_URL, 'css/mailarchiver.min.css' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function register_scripts() {
		$this->assets->register_script( MAILARCHIVER_ASSETS_ID, MAILARCHIVER_ADMIN_URL, 'js/mailarchiver.min.js', [ 'jquery' ] );
	}

	/**
	 * Sets the help action for the settings page.
	 *
	 * @param string $hook_suffix    The hook suffix.
	 * @since 1.0.0
	 */
	public function set_settings_help( $hook_suffix ) {
		add_action( 'load-' . $hook_suffix, [ new InlineHelp(), 'set_contextual_settings' ] );
	}

	/**
	 * Sets the help action (and boxes settings) for the viewer.
	 *
	 * @param string $hook_suffix    The hook suffix.
	 * @since 1.0.0
	 */
	public function set_viewer_help( $hook_suffix ) {
		$this->current_view = null;
		add_action( 'load-' . $hook_suffix, [ new InlineHelp(), 'set_contextual_viewer' ] );
		$logid   = filter_input( INPUT_GET, 'logid', FILTER_SANITIZE_STRING );
		$eventid = filter_input( INPUT_GET, 'eventid', FILTER_SANITIZE_NUMBER_INT );
		if ( 'mailarchiver-viewer' === filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) ) {
			if ( isset( $logid ) && isset( $eventid ) && 0 !== $eventid ) {
				$this->current_view = new EventViewer( $logid, $eventid );
				add_action( 'load-' . $hook_suffix, [ $this->current_view, 'add_metaboxes_options' ] );
				add_action( 'admin_footer-' . $hook_suffix, [ $this->current_view, 'add_footer' ] );
				add_filter( 'screen_settings', [ $this->current_view, 'display_screen_settings' ], 10, 2 );
			} else {
				add_action( 'load-' . $hook_suffix, [ 'Mailarchiver\Plugin\Feature\Events', 'add_column_options' ] );
				add_filter( 'screen_settings', [ 'Mailarchiver\Plugin\Feature\Events', 'display_screen_settings' ], 10, 2 );
			}
		}
	}

	/**
	 * Init PerfOps admin menus.
	 *
	 * @param array $perfops    The already declared menus.
	 * @return array    The completed menus array.
	 * @since 1.0.0
	 */
	public function init_perfops_admin_menus( $perfops ) {
		if ( Role::SUPER_ADMIN === Role::admin_type() || Role::SINGLE_ADMIN === Role::admin_type() ) {
			$perfops['settings'][] = [
				'name'          => MAILARCHIVER_PRODUCT_NAME,
				'description'   => '',
				'icon_callback' => [ \Mailarchiver\Plugin\Core::class, 'get_base64_logo' ],
				'slug'          => 'mailarchiver-settings',
				/* translators: as in the sentence "Mailarchiver Settings" or "WordPress Settings" */
				'page_title'    => sprintf( esc_html__( '%s Settings', 'mailarchiver' ), MAILARCHIVER_PRODUCT_NAME ),
				'menu_title'    => MAILARCHIVER_PRODUCT_NAME,
				'capability'    => 'manage_options',
				'callback'      => [ $this, 'get_settings_page' ],
				'position'      => 50,
				'plugin'        => MAILARCHIVER_SLUG,
				'version'       => MAILARCHIVER_VERSION,
				'activated'     => true,
				'remedy'        => '',
				'statistics'    => [ '\Mailarchiver\System\Statistics', 'sc_get_raw' ],
				'post_callback' => [ $this, 'set_settings_help' ],
			];
		}
		if ( Role::SUPER_ADMIN === Role::admin_type() || Role::SINGLE_ADMIN === Role::admin_type() || Role::LOCAL_ADMIN === Role::admin_type() ) {
			if ( Events::archivers_count() > 0 ) {
				$perfops['records'][] = [
					'name'          => esc_html__( 'Archived Emails', 'mailarchiver' ),
					/* translators: as in the sentence "Access the emails sent from your network." or "Access the emails sent from your website." */
					'description'   => sprintf( esc_html__( 'Access the emails sent from your %s.', 'mailarchiver' ), Environment::is_wordpress_multisite() ? esc_html__( 'network', 'mailarchiver' ) : esc_html__( 'website', 'mailarchiver' ) ),
					'icon_callback' => [ \Mailarchiver\Plugin\Core::class, 'get_base64_logo' ],
					'slug'          => 'mailarchiver-viewer',
					/* translators: as in the sentence "Mailarchiver Viewer" */
					'page_title'    => sprintf( esc_html__( '%s Viewer', 'mailarchiver' ), MAILARCHIVER_PRODUCT_NAME ),
					'menu_title'    => esc_html__( 'Archived Mails', 'mailarchiver' ),
					'capability'    => 'manage_options',
					'callback'      => [ $this, 'get_tools_page' ],
					'position'      => 50,
					'plugin'        => MAILARCHIVER_SLUG,
					'activated'     => true,
					'remedy'        => '',
					'post_callback' => [ $this, 'set_viewer_help' ],
				];
			}
		}
		return $perfops;
	}

	/**
	 * Set the items in the settings menu.
	 *
	 * @since 1.0.0
	 */
	public function init_admin_menus() {
		if ( 'mailarchiver-settings' === filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) ) {
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
		}
		add_filter( 'init_perfops_admin_menus', [ $this, 'init_perfops_admin_menus' ] );
		AdminMenus::initialize();
	}

	/**
	 * Get actions links for myblogs_blog_actions hook.
	 *
	 * @param string $actions   The HTML site link markup.
	 * @param object $user_blog An object containing the site data.
	 * @return string   The action string.
	 * @since 1.2.0
	 */
	public function blog_action( $actions, $user_blog ) {
		if ( Role::SUPER_ADMIN === Role::admin_type() || Role::LOCAL_ADMIN === Role::admin_type() && Events::archivers_count() > 0 ) {
			$actions .= " | <a href='" . esc_url( admin_url( 'tools.php?page=mailarchiver-viewer&site_id=' . $user_blog->userblog_id ) ) . "'>" . __( 'Archived emails', 'mailarchiver' ) . '</a>';
		}
		return $actions;
	}

	/**
	 * Get actions for manage_sites_action_links hook.
	 *
	 * @param string[] $actions  An array of action links to be displayed.
	 * @param int      $blog_id  The site ID.
	 * @param string   $blogname Site path, formatted depending on whether it is a sub-domain
	 *                           or subdirectory multisite installation.
	 * @return array   The actions.
	 * @since 1.2.0
	 */
	public function site_action( $actions, $blog_id, $blogname ) {
		if ( Role::SUPER_ADMIN === Role::admin_type() || Role::LOCAL_ADMIN === Role::admin_type() && Events::archivers_count() > 0 ) {
			$actions['mail_archive'] = "<a href='" . esc_url( admin_url( 'tools.php?page=mailarchiver-viewer&site_id=' . $blog_id ) ) . "' rel='bookmark'>" . __( 'Archived emails', 'mailarchiver' ) . '</a>';
		}
		return $actions;
	}

	/**
	 * Initializes settings sections.
	 *
	 * @since 1.0.0
	 */
	public function init_settings_sections() {
		add_settings_section( 'mailarchiver_archivers_options_section', esc_html__( 'Archivers options', 'mailarchiver' ), [ $this, 'archivers_options_section_callback' ], 'mailarchiver_archivers_options_section' );
		add_settings_section( 'mailarchiver_plugin_options_section', esc_html__( 'Plugin options', 'mailarchiver' ), [ $this, 'plugin_options_section_callback' ], 'mailarchiver_plugin_options_section' );
		add_settings_section( 'mailarchiver_listeners_options_section', null, [ $this, 'listeners_options_section_callback' ], 'mailarchiver_listeners_options_section' );
		add_settings_section( 'mailarchiver_listeners_settings_section', null, [ $this, 'listeners_settings_section_callback' ], 'mailarchiver_listeners_settings_section' );
		add_settings_section( 'mailarchiver_archiver_misc_section', null, [ $this, 'archiver_misc_section_callback' ], 'mailarchiver_archiver_misc_section' );
		add_settings_section( 'mailarchiver_archiver_delete_section', null, [ $this, 'archiver_delete_section_callback' ], 'mailarchiver_archiver_delete_section' );
		add_settings_section( 'mailarchiver_archiver_specific_section', null, [ $this, 'archiver_specific_section_callback' ], 'mailarchiver_archiver_specific_section' );
		add_settings_section( 'mailarchiver_archiver_privacy_section', esc_html__( 'Privacy options', 'mailarchiver' ), [ $this, 'archiver_privacy_section_callback' ], 'mailarchiver_archiver_privacy_section' );
		add_settings_section( 'mailarchiver_archiver_details_section', esc_html__( 'Reported details', 'mailarchiver' ), [ $this, 'archiver_details_section_callback' ], 'mailarchiver_archiver_details_section' );
	}

	/**
	 * Add links in the "Actions" column on the plugins view page.
	 *
	 * @param string[] $actions     An array of plugin action links. By default this can include 'activate',
	 *                              'deactivate', and 'delete'.
	 * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
	 * @param array    $plugin_data An array of plugin data. See `get_plugin_data()`.
	 * @param string   $context     The plugin context. By default this can include 'all', 'active', 'inactive',
	 *                              'recently_activated', 'upgrade', 'mustuse', 'dropins', and 'search'.
	 * @return array Extended list of links to print in the "Actions" column on the Plugins page.
	 * @since 1.0.0
	 */
	public function add_actions_links( $actions, $plugin_file, $plugin_data, $context ) {
		$actions[] = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=mailarchiver-settings' ), esc_html__( 'Settings', 'mailarchiver' ) );
		if ( Events::archivers_count() > 0 ) {
			$actions[] = sprintf( '<a href="%s">%s</a>', admin_url( 'tools.php?page=mailarchiver-viewer' ), esc_html__( 'Archived Emails', 'mailarchiver' ) );
		}
		return $actions;
	}

	/**
	 * Add links in the "Description" column on the Plugins page.
	 *
	 * @param array  $links List of links to print in the "Description" column on the Plugins page.
	 * @param string $file Path to the plugin file relative to the plugins directory.
	 * @return array Extended list of links to print in the "Description" column on the Plugins page.
	 * @since 1.3.0
	 */
	public function add_row_meta( $links, $file ) {
		if ( 0 === strpos( $file, MAILARCHIVER_SLUG . '/' ) ) {
			$links[] = '<a href="https://wordpress.org/support/plugin/' . MAILARCHIVER_SLUG . '/">' . __( 'Support', 'mailarchiver' ) . '</a>';
			$links[] = '<a href="https://github.com/Pierre-Lannoy/wp-mailarchiver">' . __( 'GitHub repository', 'mailarchiver' ) . '</a>';
		}
		return $links;
	}

	/**
	 * Get the content of the tools page.
	 *
	 * @since 1.0.0
	 */
	public function get_tools_page() {
		if ( isset( $this->current_view ) ) {
			$this->current_view->get();
		} else {
			include MAILARCHIVER_ADMIN_DIR . 'partials/mailarchiver-admin-view-events.php';
		}
	}

	/**
	 * Get the content of the settings page.
	 *
	 * @since 1.0.0
	 */
	public function get_settings_page() {
		$this->current_handler  = null;
		$this->current_archiver = null;
		if ( ! ( $action = filter_input( INPUT_GET, 'action' ) ) ) {
			$action = filter_input( INPUT_POST, 'action' );
		}
		if ( ! ( $tab = filter_input( INPUT_GET, 'tab' ) ) ) {
			$tab = filter_input( INPUT_POST, 'tab' );
		}
		if ( ! ( $handler = filter_input( INPUT_GET, 'handler' ) ) ) {
			$handler = filter_input( INPUT_POST, 'handler' );
		}
		if ( ! ( $uuid = filter_input( INPUT_GET, 'uuid' ) ) ) {
			$uuid = filter_input( INPUT_POST, 'uuid' );
		}
		$nonce = filter_input( INPUT_GET, 'nonce' );
		if ( $uuid ) {
			$archivers = Option::network_get( 'archivers' );
			if ( array_key_exists( $uuid, $archivers ) ) {
				$this->current_archiver         = $archivers[ $uuid ];
				$this->current_archiver['uuid'] = $uuid;
			}
		}
		if ( $handler ) {
			$handlers              = new HandlerTypes();
			$this->current_handler = $handlers->get( $handler );
		} elseif ( $this->current_archiver ) {
			$handlers              = new HandlerTypes();
			$this->current_handler = $handlers->get( $this->current_archiver['handler'] );
		}
		if ( $this->current_handler && ! $this->current_archiver ) {
			$this->current_archiver = [
				'uuid'    => $uuid = UUID::generate_v4(),
				'name'    => esc_html__( 'New archiver', 'mailarchiver' ),
				'handler' => $this->current_handler['id'],
				'running' => Option::network_get( 'archiver_autostart' ),
			];
		}
		if ( $this->current_archiver ) {
			$factory                = new ArchiverFactory();
			$this->current_archiver = $factory->check( $this->current_archiver );
		}
		$view = 'mailarchiver-admin-settings-main';
		if ( $action && $tab ) {
			switch ( $tab ) {
				case 'archivers':
					switch ( $action ) {
						case 'form-edit':
							if ( Role::SUPER_ADMIN === Role::admin_type() || Role::SINGLE_ADMIN === Role::admin_type() ) {
								$current_archiver = $this->current_archiver;
								$current_handler  = $this->current_handler;
								$args             = compact( 'current_archiver', 'current_handler' );
								$view             = 'mailarchiver-admin-settings-archiver-edit';
							}
							break;
						case 'form-delete':
							if ( Role::SUPER_ADMIN === Role::admin_type() || Role::SINGLE_ADMIN === Role::admin_type() ) {
								$current_archiver = $this->current_archiver;
								$current_handler  = $this->current_handler;
								$args             = compact( 'current_archiver', 'current_handler' );
								$view             = 'mailarchiver-admin-settings-archiver-delete';
							}
							break;
						case 'do-edit':
							if ( Role::SUPER_ADMIN === Role::admin_type() || Role::SINGLE_ADMIN === Role::admin_type() ) {
								$this->save_current();
							}
							break;
						case 'do-delete':
							if ( Role::SUPER_ADMIN === Role::admin_type() || Role::SINGLE_ADMIN === Role::admin_type() ) {
								$this->delete_current();
							}
							break;
						case 'start':
							if ( Role::SUPER_ADMIN === Role::admin_type() || Role::SINGLE_ADMIN === Role::admin_type() ) {
								if ( $nonce && $uuid && wp_verify_nonce( $nonce, 'mailarchiver-archiver-start-' . $uuid ) ) {
									$archivers = Option::network_get( 'archivers' );
									if ( array_key_exists( $uuid, $archivers ) ) {
										$archivers[ $uuid ]['running'] = true;
										Option::network_set( 'archivers', $archivers );
										$message = sprintf( esc_html__( 'Archiver %s has started.', 'mailarchiver' ), '<em>' . $archivers[ $uuid ]['name'] . '</em>' );
										$code    = 0;
										add_settings_error( 'mailarchiver_no_error', $code, $message, 'updated' );
										Logger::info( sprintf( 'Archiver "%s" has started.', $archivers[ $uuid ]['name'] ), $code );
									}
								}
							}
							break;
						case 'pause':
							if ( Role::SUPER_ADMIN === Role::admin_type() || Role::SINGLE_ADMIN === Role::admin_type() ) {
								if ( $nonce && $uuid && wp_verify_nonce( $nonce, 'mailarchiver-archiver-pause-' . $uuid ) ) {
									$archivers = Option::network_get( 'archivers' );
									if ( array_key_exists( $uuid, $archivers ) ) {
										$message = sprintf( esc_html__( 'Archiver %s has been paused.', 'mailarchiver' ), '<em>' . $archivers[ $uuid ]['name'] . '</em>' );
										$code    = 0;
										Logger::notice( sprintf( 'Archiver "%s" has been paused.', $archivers[ $uuid ]['name'] ), $code );
										$archivers[ $uuid ]['running'] = false;
										Option::network_set( 'archivers', $archivers );
										add_settings_error( 'mailarchiver_no_error', $code, $message, 'updated' );
									}
								}
							}
					}
					break;
				case 'misc':
					switch ( $action ) {
						case 'do-save':
							if ( Role::SUPER_ADMIN === Role::admin_type() || Role::SINGLE_ADMIN === Role::admin_type() ) {
								if ( ! empty( $_POST ) && array_key_exists( 'submit', $_POST ) ) {
									$this->save_options();
								} elseif ( ! empty( $_POST ) && array_key_exists( 'reset-to-defaults', $_POST ) ) {
									$this->reset_options();
								}
							}
							break;
					}
					break;
				case 'listeners':
					switch ( $action ) {
						case 'do-save':
							if ( Role::SUPER_ADMIN === Role::admin_type() || Role::SINGLE_ADMIN === Role::admin_type() ) {
								if ( ! empty( $_POST ) && array_key_exists( 'submit', $_POST ) ) {
									$this->save_listeners();
								} elseif ( ! empty( $_POST ) && array_key_exists( 'reset-to-defaults', $_POST ) ) {
									$this->reset_listeners();
								}
							}
							break;
					}
					break;
			}
		}
		include MAILARCHIVER_ADMIN_DIR . 'partials/' . $view . '.php';
	}

	/**
	 * Save the listeners options.
	 *
	 * @since 1.0.0
	 */
	private function save_listeners() {
		if ( ! empty( $_POST ) ) {
			if ( array_key_exists( '_wpnonce', $_POST ) && wp_verify_nonce( $_POST['_wpnonce'], 'mailarchiver-listeners-options' ) ) {
				Option::network_set( 'autolisteners', 'auto' === filter_input( INPUT_POST, 'mailarchiver_listeners_options_auto' ) );
				$list      = [];
				$listeners = ListenerFactory::$infos;
				foreach ( $listeners as $listener ) {
					if ( array_key_exists( 'mailarchiver_listeners_settings_' . $listener['id'], $_POST ) ) {
						$list[] = $listener['id'];
					}
				}
				Option::network_set( 'listeners', $list );
				$message = esc_html__( 'Listeners settings have been saved.', 'mailarchiver' );
				$code    = 0;
				add_settings_error( 'mailarchiver_no_error', $code, $message, 'updated' );
				Logger::info( 'Listeners settings updated.', $code );
			} else {
				$message = esc_html__( 'Listeners settings have not been saved. Please try again.', 'mailarchiver' );
				$code    = 2;
				add_settings_error( 'mailarchiver_nonce_error', $code, $message, 'error' );
				Logger::warning( 'Listeners settings not updated.', $code );
			}
		}
	}

	/**
	 * Reset the listeners options.
	 *
	 * @since 1.0.0
	 */
	private function reset_listeners() {
		if ( ! empty( $_POST ) ) {
			if ( array_key_exists( '_wpnonce', $_POST ) && wp_verify_nonce( $_POST['_wpnonce'], 'mailarchiver-listeners-options' ) ) {
				Option::network_set( 'autolisteners', true );
				$message = esc_html__( 'Listeners settings have been reset to defaults.', 'mailarchiver' );
				$code    = 0;
				add_settings_error( 'mailarchiver_no_error', $code, $message, 'updated' );
				Logger::info( 'Listeners settings reset to defaults.', $code );
			} else {
				$message = esc_html__( 'Listeners settings have not been reset to defaults. Please try again.', 'mailarchiver' );
				$code    = 2;
				add_settings_error( 'mailarchiver_nonce_error', $code, $message, 'error' );
				Logger::warning( 'Listeners settings not reset to defaults.', $code );
			}
		}
	}

	/**
	 * Save the plugin options.
	 *
	 * @since 1.0.0
	 */
	private function save_options() {
		if ( ! empty( $_POST ) ) {
			if ( array_key_exists( '_wpnonce', $_POST ) && wp_verify_nonce( $_POST['_wpnonce'], 'mailarchiver-plugin-options' ) ) {
				Option::network_set( 'use_cdn', array_key_exists( 'mailarchiver_plugin_options_usecdn', $_POST ) );
				Option::network_set( 'display_nag', array_key_exists( 'mailarchiver_plugin_options_nag', $_POST ) );
				Option::network_set( 'archiver_autostart', array_key_exists( 'mailarchiver_archivers_options_autostart', $_POST ) );
				$message = esc_html__( 'Plugin settings have been saved.', 'mailarchiver' );
				$code    = 0;
				add_settings_error( 'mailarchiver_no_error', $code, $message, 'updated' );
				Logger::info( 'Plugin settings updated.', $code );
			} else {
				$message = esc_html__( 'Plugin settings have not been saved. Please try again.', 'mailarchiver' );
				$code    = 2;
				add_settings_error( 'mailarchiver_nonce_error', $code, $message, 'error' );
				Logger::warning( 'Plugin settings not updated.', $code );
			}
		}
	}

	/**
	 * Reset the plugin options.
	 *
	 * @since 1.0.0
	 */
	private function reset_options() {
		if ( ! empty( $_POST ) ) {
			if ( array_key_exists( '_wpnonce', $_POST ) && wp_verify_nonce( $_POST['_wpnonce'], 'mailarchiver-plugin-options' ) ) {
				Option::reset_to_defaults();
				$message = esc_html__( 'Plugin settings have been reset to defaults.', 'mailarchiver' );
				$code    = 0;
				add_settings_error( 'mailarchiver_no_error', $code, $message, 'updated' );
				Logger::info( 'Plugin settings reset to defaults.', $code );
			} else {
				$message = esc_html__( 'Plugin settings have not been reset to defaults. Please try again.', 'mailarchiver' );
				$code    = 2;
				add_settings_error( 'mailarchiver_nonce_error', $code, $message, 'error' );
				Logger::warning( 'Plugin settings not reset to defaults.', $code );
			}
		}
	}

	/**
	 * Save the current archiver as new or modified archiver.
	 *
	 * @since 1.0.0
	 */
	private function save_current() {
		if ( ! empty( $_POST ) ) {
			if ( array_key_exists( '_wpnonce', $_POST ) && wp_verify_nonce( $_POST['_wpnonce'], 'mailarchiver-archiver-edit' ) ) {
				if ( array_key_exists( 'submit', $_POST ) ) {
					$this->current_archiver['name']                         = ( array_key_exists( 'mailarchiver_archiver_misc_name', $_POST ) ? filter_input( INPUT_POST, 'mailarchiver_archiver_misc_name', FILTER_SANITIZE_STRING ) : $this->current_archiver['name'] );
					$this->current_archiver['level']                        = ( array_key_exists( 'mailarchiver_archiver_misc_level', $_POST ) ? filter_input( INPUT_POST, 'mailarchiver_archiver_misc_level', FILTER_SANITIZE_NUMBER_INT ) : $this->current_archiver['level'] );
					$this->current_archiver['privacy']['obfuscation']       = ( array_key_exists( 'mailarchiver_archiver_privacy_ip', $_POST ) ? true : false );
					$this->current_archiver['privacy']['pseudonymization']  = ( array_key_exists( 'mailarchiver_archiver_privacy_name', $_POST ) ? true : false );
					$this->current_archiver['privacy']['mailanonymization'] = ( array_key_exists( 'mailarchiver_archiver_privacy_mail', $_POST ) ? true : false );
					$this->current_archiver['privacy']['encryption']        = ( array_key_exists( 'mailarchiver_archiver_privacy_encryption', $_POST ) ? Secret::set( filter_input( INPUT_POST, 'mailarchiver_archiver_privacy_encryption', FILTER_UNSAFE_RAW ) ) : '' );
					$this->current_archiver['processors']                   = [];
					$proc = new ProcessorTypes();
					foreach ( array_reverse( $proc->get_all() ) as $processor ) {
						if ( array_key_exists( 'mailarchiver_archiver_details_' . strtolower( $processor['id'] ), $_POST ) ) {
							$this->current_archiver['processors'][] = $processor['id'];
						}
					}
					$this->current_archiver['processors'][] = 'MailProcessor';
					foreach ( $this->current_handler['configuration'] as $key => $configuration ) {
						$id = 'mailarchiver_archiver_details_' . strtolower( $key );
						if ( 'boolean' === $configuration['control']['cast'] ) {
							$this->current_archiver['configuration'][ $key ] = ( array_key_exists( $id, $_POST ) ? true : false );
						}
						if ( 'integer' === $configuration['control']['cast'] ) {
							$this->current_archiver['configuration'][ $key ] = ( array_key_exists( $id, $_POST ) ? filter_input( INPUT_POST, $id, FILTER_SANITIZE_NUMBER_INT ) : $this->current_archiver['configuration'][ $key ] );
						}
						if ( 'string' === $configuration['control']['cast'] ) {
							$this->current_archiver['configuration'][ $key ] = ( array_key_exists( $id, $_POST ) ? filter_input( INPUT_POST, $id, FILTER_SANITIZE_STRING ) : $this->current_archiver['configuration'][ $key ] );
						}
						if ( 'password' === $configuration['control']['cast'] ) {
							$this->current_archiver['configuration'][ $key ] = ( array_key_exists( $id, $_POST ) ? filter_input( INPUT_POST, $id, FILTER_UNSAFE_RAW ) : $this->current_archiver['configuration'][ $key ] );
						}
					}
					$uuid               = $this->current_archiver['uuid'];
					$archivers          = Option::network_get( 'archivers' );
					$factory            = new ArchiverFactory();
					$archivers[ $uuid ] = $factory->check( $this->current_archiver, true );
					if ( array_key_exists( 'uuid', $archivers[ $uuid ] ) ) {
						unset( $archivers[ $uuid ]['uuid'] );
					}
					Option::network_set( 'archivers', $archivers );
					$message = sprintf( esc_html__( 'Archiver %s has been saved.', 'mailarchiver' ), '<em>' . $this->current_archiver['name'] . '</em>' );
					$code    = 0;
					add_settings_error( 'mailarchiver_no_error', $code, $message, 'updated' );
					Logger::info( sprintf( 'Archiver "%s" has been saved.', $this->current_archiver['name'] ), $code );
				}
			} else {
				$message = sprintf( esc_html__( 'Archiver %s has not been saved. Please try again.', 'mailarchiver' ), '<em>' . $this->current_archiver['name'] . '</em>' );
				$code    = 2;
				add_settings_error( 'mailarchiver_nonce_error', $code, $message, 'error' );
				Logger::warning( sprintf( 'Archiver "%s" has not been saved.', $this->current_archiver['name'] ), $code );
			}
		}
	}

	/**
	 * Delete the current archiver.
	 *
	 * @since 1.0.0
	 */
	private function delete_current() {
		if ( ! empty( $_POST ) ) {
			if ( array_key_exists( '_wpnonce', $_POST ) && wp_verify_nonce( $_POST['_wpnonce'], 'mailarchiver-archiver-delete' ) ) {
				if ( array_key_exists( 'submit', $_POST ) ) {
					$uuid      = $this->current_archiver['uuid'];
					$archivers = Option::network_get( 'archivers' );
					$factory   = new ArchiverFactory();
					$factory->clean( $this->current_archiver );
					unset( $archivers[ $uuid ] );
					Option::network_set( 'archivers', $archivers );
					$message = sprintf( esc_html__( 'Archiver %s has been removed.', 'mailarchiver' ), '<em>' . $this->current_archiver['name'] . '</em>' );
					$code    = 0;
					add_settings_error( 'mailarchiver_no_error', $code, $message, 'updated' );
					Logger::notice( sprintf( 'Archiver "%s" has been removed.', $this->current_archiver['name'] ), $code );
				}
			} else {
				$message = sprintf( esc_html__( 'Archiver %s has not been removed. Please try again.', 'mailarchiver' ), '<em>' . $this->current_archiver['name'] . '</em>' );
				$code    = 2;
				add_settings_error( 'mailarchiver_nonce_error', $code, $message, 'error' );
				Logger::warning( sprintf( 'Archiver "%s" has not been removed.', $this->current_archiver['name'] ), $code );
			}
		}
	}

	/**
	 * Callback for listeners options section.
	 *
	 * @since 1.0.0
	 */
	public function listeners_options_section_callback() {
		$form = new Form();
		add_settings_field(
			'mailarchiver_listeners_options_auto',
			__( 'Activate', 'mailarchiver' ),
			[ $form, 'echo_field_select' ],
			'mailarchiver_listeners_options_section',
			'mailarchiver_listeners_options_section',
			[
				'list'        => [
					0 => [ 'manual', esc_html__( 'Selected listeners', 'mailarchiver' ) ],
					1 => [ 'auto', esc_html__( 'All available listeners (recommended)', 'mailarchiver' ) ],
				],
				'id'          => 'mailarchiver_listeners_options_auto',
				'value'       => Option::network_get( 'autolisteners' ) ? 'auto' : 'manual',
				'description' => esc_html__( 'Automatically or selectively choose which sources to listen.', 'mailarchiver' ),
				'full_width'  => true,
				'enabled'     => true,
			]
		);
		register_setting( 'mailarchiver_listeners_options_section', 'mailarchiver_listeners_options_autostart' );
	}

	/**
	 * Callback for listeners settings section.
	 *
	 * @since 1.0.0
	 */
	public function listeners_settings_section_callback() {
		$standard  = [];
		$plugin    = [];
		$theme     = [];
		$listeners = ListenerFactory::$infos;
		usort(
			$listeners,
			function( $a, $b ) {
				return strcmp( strtolower( $a['name'] ), strtolower( $b['name'] ) );
			}
		);
		foreach ( $listeners as $listener ) {
			if ( 'plugin' === $listener['class'] && $listener['available'] ) {
				$plugin[] = $listener;
			} elseif ( 'theme' === $listener['class'] && $listener['available'] ) {
				$theme[] = $listener;
			} elseif ( $listener['available'] ) {
				$standard[] = $listener;
			}
		}
		$main = [
			esc_html__( 'Standard listeners', 'mailarchiver' ) => $standard,
			esc_html__( 'Plugin listeners', 'mailarchiver' )   => $plugin,
			esc_html__( 'Theme listeners', 'mailarchiver' )    => $theme,
		];
		$form = new Form();
		foreach ( $main as $name => $items ) {
			$title = true;
			foreach ( $items as $item ) {
				add_settings_field(
					'mailarchiver_listeners_settings_' . $item['id'],
					$title ? $name : null,
					[ $form, 'echo_field_checkbox' ],
					'mailarchiver_listeners_settings_section',
					'mailarchiver_listeners_settings_section',
					[
						'text'        => sprintf( '%s (%s %s)', $item['name'], $item['product'], $item['version'] ),
						'id'          => 'mailarchiver_listeners_settings_' . $item['id'],
						'checked'     => in_array( $item['id'], Option::network_get( 'listeners' ), true ),
						'description' => null,
						'full_width'  => true,
						'enabled'     => true,
					]
				);
				register_setting( 'mailarchiver_listeners_settings_section', 'mailarchiver_listeners_settings_' . $item['id'] );
				$title = false;
			}
		}
	}

	/**
	 * Callback for archivers options section.
	 *
	 * @since 1.0.0
	 */
	public function archivers_options_section_callback() {
		$form = new Form();
		add_settings_field(
			'mailarchiver_archivers_options_autostart',
			__( 'New archiver', 'mailarchiver' ),
			[ $form, 'echo_field_checkbox' ],
			'mailarchiver_archivers_options_section',
			'mailarchiver_archivers_options_section',
			[
				'text'        => esc_html__( 'Auto-start', 'mailarchiver' ),
				'id'          => 'mailarchiver_archivers_options_autostart',
				'checked'     => Option::network_get( 'archiver_autostart' ),
				'description' => esc_html__( 'If checked, when a new archiver is added it automatically starts.', 'mailarchiver' ),
				'full_width'  => true,
				'enabled'     => true,
			]
		);
		register_setting( 'mailarchiver_archivers_options_section', 'mailarchiver_archivers_options_autostart' );
	}

	/**
	 * Callback for plugin options section.
	 *
	 * @since 1.0.0
	 */
	public function plugin_options_section_callback() {
		$form = new Form();
		if ( defined( 'MAILARCHIVER_VERSION' ) ) {
			$help  = '<img style="width:16px;vertical-align:text-bottom;" src="' . \Feather\Icons::get_base64( 'thumbs-up', 'none', '#00C800' ) . '" />&nbsp;';
			$help .= sprintf( esc_html__('Your site is currently using %s.', 'mailarchiver' ), '<em>DecaLog v' . DECALOG_VERSION .'</em>' );
		} else {
			$help  = '<img style="width:16px;vertical-align:text-bottom;" src="' . \Feather\Icons::get_base64( 'alert-triangle', 'none', '#FF8C00' ) . '" />&nbsp;';
			$help .= sprintf( esc_html__('Your site does not use any logging plugin. To log all events triggered in MailArchiver, I recommend you to install the excellent (and free) %s. But it is not mandatory.', 'mailarchiver' ), '<a href="https://wordpress.org/plugins/decalog/">DecaLog</a>' );
		}
		add_settings_field(
			'mailarchiver_plugin_options_logger',
			esc_html__( 'Logging', 'mailarchiver' ),
			[ $form, 'echo_field_simple_text' ],
			'mailarchiver_plugin_options_section',
			'mailarchiver_plugin_options_section',
			[
				'text' => $help
			]
		);
		register_setting( 'mailarchiver_plugin_options_section', 'mailarchiver_plugin_options_logger' );
		add_settings_field(
			'mailarchiver_plugin_options_usecdn',
			__( 'Resources', 'mailarchiver' ),
			[ $form, 'echo_field_checkbox' ],
			'mailarchiver_plugin_options_section',
			'mailarchiver_plugin_options_section',
			[
				'text'        => esc_html__( 'Use public CDN', 'mailarchiver' ),
				'id'          => 'mailarchiver_plugin_options_usecdn',
				'checked'     => Option::network_get( 'use_cdn' ),
				'description' => esc_html__( 'Use CDN (jsDelivr) to serve MailArchiver scripts and stylesheets.', 'mailarchiver' ),
				'full_width'  => true,
				'enabled'     => true,
			]
		);
		register_setting( 'mailarchiver_plugin_options_section', 'mailarchiver_plugin_options_usecdn' );
		add_settings_field(
			'mailarchiver_plugin_options_nag',
			__( 'Admin notices', 'mailarchiver' ),
			[ $form, 'echo_field_checkbox' ],
			'mailarchiver_plugin_options_section',
			'mailarchiver_plugin_options_section',
			[
				'text'        => esc_html__( 'Display', 'mailarchiver' ),
				'id'          => 'mailarchiver_plugin_options_nag',
				'checked'     => Option::network_get( 'display_nag' ),
				'description' => esc_html__( 'Allows MailArchiver to display admin notices throughout the admin dashboard.', 'mailarchiver' ) . '<br/>' . esc_html__( 'Note: MailArchiver respects DISABLE_NAG_NOTICES flag.', 'mailarchiver' ),
				'full_width'  => true,
				'enabled'     => true,
			]
		);
		register_setting( 'mailarchiver_plugin_options_section', 'mailarchiver_plugin_options_nag' );
	}

	/**
	 * Callback for archiver misc section.
	 *
	 * @since 1.0.0
	 */
	public function archiver_misc_section_callback() {
		$icon  = '<img style="vertical-align:middle;width:34px;margin-top: -2px;padding-right:6px;" src="' . $this->current_handler['icon'] . '" />';
		$title = $this->current_handler['name'];
		echo '<h2>' . $icon . '&nbsp;' . $title . '</h2>';
		echo '<p style="margin-top: -10px;margin-left: 6px;">' . $this->current_handler['help'] . '</p>';
		$form = new Form();
		add_settings_field(
			'mailarchiver_archiver_misc_name',
			__( 'Name', 'mailarchiver' ),
			[ $form, 'echo_field_input_text' ],
			'mailarchiver_archiver_misc_section',
			'mailarchiver_archiver_misc_section',
			[
				'id'          => 'mailarchiver_archiver_misc_name',
				'value'       => $this->current_archiver['name'],
				'description' => esc_html__( 'Used only in admin dashboard.', 'mailarchiver' ),
				'full_width'  => true,
				'enabled'     => true,
			]
		);
		register_setting( 'mailarchiver_archiver_misc_section', 'mailarchiver_archiver_misc_name' );
		add_settings_field(
			'mailarchiver_archiver_misc_level',
			__( 'Archived emails', 'mailarchiver' ),
			[ $form, 'echo_field_select' ],
			'mailarchiver_archiver_misc_section',
			'mailarchiver_archiver_misc_section',
			[
				'list'        => Archive::get_levels( $this->current_handler['minimal'] ),
				'id'          => 'mailarchiver_archiver_misc_level',
				'value'       => $this->current_archiver['level'],
				'description' => esc_html__( 'What kind of emails should be archived.', 'mailarchiver' ),
				'full_width'  => true,
				'enabled'     => true,
			]
		);
		register_setting( 'mailarchiver_archiver_misc_section', 'mailarchiver_archiver_misc_level' );
	}

	/**
	 * Callback for archiver delete section.
	 *
	 * @since 1.0.0
	 */
	public function archiver_delete_section_callback() {
		$icon  = '<img style="vertical-align:middle;width:34px;margin-top: -2px;padding-right:6px;" src="' . $this->current_handler['icon'] . '" />';
		$title = $this->current_handler['name'];
		echo '<h2>' . $icon . '&nbsp;' . $title . '</h2>';
		echo '<p style="margin-top: -10px;margin-left: 6px;">' . $this->current_handler['help'] . '</p>';
		$form = new Form();
		add_settings_field(
			'mailarchiver_archiver_delete_name',
			__( 'Name', 'mailarchiver' ),
			[ $form, 'echo_field_input_text' ],
			'mailarchiver_archiver_delete_section',
			'mailarchiver_archiver_delete_section',
			[
				'id'          => 'mailarchiver_archiver_delete_name',
				'value'       => $this->current_archiver['name'],
				'description' => null,
				'full_width'  => true,
				'enabled'     => false,
			]
		);
		register_setting( 'mailarchiver_archiver_delete_section', 'mailarchiver_archiver_delete_name' );
		add_settings_field(
			'mailarchiver_archiver_delete_level',
			__( 'Archived emails', 'mailarchiver' ),
			[ $form, 'echo_field_select' ],
			'mailarchiver_archiver_delete_section',
			'mailarchiver_archiver_delete_section',
			[
				'list'        => Archive::get_levels( $this->current_handler['minimal'] ),
				'id'          => 'mailarchiver_archiver_delete_level',
				'value'       => $this->current_archiver['level'],
				'description' => null,
				'full_width'  => true,
				'enabled'     => false,
			]
		);
		register_setting( 'mailarchiver_archiver_delete_section', 'mailarchiver_archiver_delete_level' );
	}

	/**
	 * Callback for archiver specific section.
	 *
	 * @since 1.0.0
	 */
	public function archiver_specific_section_callback() {
		$form = new Form();
		foreach ( $this->current_handler['configuration'] as $key => $configuration ) {
			if ( ! $configuration['show'] ) {
				continue;
			}
			$id   = 'mailarchiver_archiver_details_' . strtolower( $key );
			$args = [
				'id'          => $id,
				'text'        => esc_html__( 'Enabled', 'mailarchiver' ),
				'checked'     => (bool) $this->current_archiver['configuration'][ $key ],
				'value'       => $this->current_archiver['configuration'][ $key ],
				'description' => $configuration['help'],
				'full_width'  => true,
				'enabled'     => $configuration['control']['enabled'],
				'list'        => ( array_key_exists( 'list', $configuration['control'] ) ? $configuration['control']['list'] : [] ),
			];
			foreach ( $configuration['control'] as $index => $control ) {
				if ( 'type' !== $index && 'cast' !== $index ) {
					$args[ $index ] = $control;
				}
			}
			add_settings_field(
				$id,
				$configuration['name'],
				[ $form, 'echo_' . $configuration['control']['type'] ],
				'mailarchiver_archiver_specific_section',
				'mailarchiver_archiver_specific_section',
				$args
			);
			register_setting( 'mailarchiver_archiver_specific_section', $id );
		}
	}

	/**
	 * Callback for archiver privacy section.
	 *
	 * @since 1.0.0
	 */
	public function archiver_privacy_section_callback() {
		$form = new Form();
		add_settings_field(
			'mailarchiver_archiver_privacy_ip',
			__( 'Remote IPs', 'mailarchiver' ),
			[ $form, 'echo_field_checkbox' ],
			'mailarchiver_archiver_privacy_section',
			'mailarchiver_archiver_privacy_section',
			[
				'text'        => esc_html__( 'Obfuscation', 'mailarchiver' ),
				'id'          => 'mailarchiver_archiver_privacy_ip',
				'checked'     => $this->current_archiver['privacy']['obfuscation'],
				'description' => esc_html__( 'If checked, recorded fields will contain hashes instead of real IPs.', 'mailarchiver' ) . '<br/>' . esc_html__( 'Note: it concerns all fields except email content.', 'mailarchiver' ),
				'full_width'  => true,
				'enabled'     => true,
			]
		);
		register_setting( 'mailarchiver_archiver_privacy_section', 'mailarchiver_archiver_privacy_ip' );
		add_settings_field(
			'mailarchiver_archiver_privacy_name',
			__( 'Users', 'mailarchiver' ),
			[ $form, 'echo_field_checkbox' ],
			'mailarchiver_archiver_privacy_section',
			'mailarchiver_archiver_privacy_section',
			[
				'text'        => esc_html__( 'Pseudonymisation', 'mailarchiver' ),
				'id'          => 'mailarchiver_archiver_privacy_name',
				'checked'     => $this->current_archiver['privacy']['pseudonymization'],
				'description' => esc_html__( 'If checked, recorded fields will contain hashes instead of user IDs & names.', 'mailarchiver' ) . '<br/>' . esc_html__( 'Note: it concerns all fields except email content.', 'mailarchiver' ),
				'full_width'  => true,
				'enabled'     => true,
			]
		);
		register_setting( 'mailarchiver_archiver_privacy_section', 'mailarchiver_archiver_privacy_name' );
		add_settings_field(
			'mailarchiver_archiver_privacy_mail',
			__( 'Email adresses', 'mailarchiver' ),
			[ $form, 'echo_field_checkbox' ],
			'mailarchiver_archiver_privacy_section',
			'mailarchiver_archiver_privacy_section',
			[
				'text'        => esc_html__( 'Masking', 'mailarchiver' ),
				'id'          => 'mailarchiver_archiver_privacy_mail',
				'checked'     => $this->current_archiver['privacy']['mailanonymization'],
				'description' => esc_html__( 'If checked, recorded fields will contain hashes instead of email adresses.', 'mailarchiver' ) . '<br/>' . esc_html__( 'Note: it concerns only "to" and "from" fields.', 'mailarchiver' ),
				'full_width'  => true,
				'enabled'     => true,
			]
		);
		register_setting( 'mailarchiver_archiver_privacy_section', 'mailarchiver_archiver_privacy_mail' );
		/*add_settings_field(
			'mailarchiver_archiver_privacy_encryption',
			__( 'Encryption key', 'mailarchiver' ),
			[ $form, 'echo_field_input_text' ],
			'mailarchiver_archiver_privacy_section',
			'mailarchiver_archiver_privacy_section',
			[
				'id'          => 'mailarchiver_archiver_privacy_encryption',
				'value'       => Secret::get( $this->current_archiver['privacy']['encryption'] ),
				'description' => esc_html__( 'Key used to encrypt mail body. Let blank to not encrypt it.', 'mailarchiver' ) . '<br/>' . esc_html__( 'Note: XXXXXXXXXXXXXXXXXXXXXXXX.', 'mailarchiver' ),
				'full_width'  => true,
				'enabled'     => true,
			]
		);
		register_setting( 'mailarchiver_archiver_privacy_section', 'mailarchiver_archiver_privacy_encryption' );
		*/
	}

	/**
	 * Callback for archiver privacy section.
	 *
	 * @since 1.0.0
	 */
	public function archiver_details_section_callback() {
		$form = new Form();
		$id   = 'mailarchiver_archiver_details_dummy';
		add_settings_field(
			$id,
			__( 'Standard', 'mailarchiver' ),
			[ $form, 'echo_field_checkbox' ],
			'mailarchiver_archiver_details_section',
			'mailarchiver_archiver_details_section',
			[
				'text'        => esc_html__( 'Included', 'mailarchiver' ),
				'id'          => $id,
				'checked'     => true,
				'description' => esc_html__( 'Allows to record standard MailArchiver information.', 'mailarchiver' ),
				'full_width'  => true,
				'enabled'     => false,
			]
		);
		register_setting( 'mailarchiver_archiver_details_section', $id );
		$proc = new ProcessorTypes();
		foreach ( array_reverse( $proc->get_all() ) as $processor ) {
			$id = 'mailarchiver_archiver_details_' . strtolower( $processor['id'] );
			add_settings_field(
				$id,
				$processor['name'],
				[ $form, 'echo_field_checkbox' ],
				'mailarchiver_archiver_details_section',
				'mailarchiver_archiver_details_section',
				[
					'text'        => esc_html__( 'Included', 'mailarchiver' ),
					'id'          => $id,
					'checked'     => in_array( $processor['id'], $this->current_archiver['processors'], true ),
					'description' => $processor['help'],
					'full_width'  => true,
					'enabled'     => ( 'WordpressHandler' !== $this->current_archiver['handler'] ) && ( 'PushoverHandler' !== $this->current_archiver['handler'] ),
				]
			);
			register_setting( 'mailarchiver_archiver_details_section', $id );
		}
	}

}
