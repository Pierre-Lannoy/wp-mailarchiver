<?php
/**
 * MailArchiver inline help
 *
 * Handles all inline help displays.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.2.0
 */

namespace Mailarchiver\Plugin\Feature;

use Mailarchiver\System\Environment;
use Mailarchiver\System\L10n;
use Mailarchiver\System\Role;

/**
 * Define the inline help functionality.
 *
 * Handles all inline help operations.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.2.0
 */
class InlineHelp {

	/**
	 * The current screen.
	 *
	 * @since  1.2.0
	 * @var    WP_Screen    $screen    The current screen.
	 */
	private $screen;

	/**
	 * The current tab.
	 *
	 * @since  1.2.0
	 * @var    null|string    $tab    The current tab.
	 */
	private $tab = null;

	/**
	 * The current log id.
	 *
	 * @since  1.2.0
	 * @var    null|string    $log_id    The log id.
	 */
	private $log_id = null;

	/**
	 * The current event id.
	 *
	 * @since  1.2.0
	 * @var    null|string    $event_id    The event id.
	 */
	private $event_id = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.2.0
	 */
	public function __construct() {
	}

	/**
	 * Initialize the screen and query properties.
	 *
	 * @since    1.2.0
	 */
	private function init() {
		$this->screen = get_current_screen();
		if ( ! ( $this->tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) ) {
			$this->tab = filter_input( INPUT_POST, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		}
		if ( ! ( $this->log_id = filter_input( INPUT_GET, 'logid', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) ) {
			$this->log_id = filter_input( INPUT_POST, 'logid', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		}
		if ( ! ( $this->event_id = filter_input( INPUT_GET, 'eventid', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) ) {
			$this->event_id = filter_input( INPUT_POST, 'eventid', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		}
	}

	/**
	 * Displays the sidebar in inline help.
	 *
	 * @since    1.2.0
	 */
	private function set_sidebar() {
		$content  = '<p><strong>' . esc_html__( 'For more help:', 'mailarchiver' ) . '</strong></p>';
		$content .= '<p><a href="https://wordpress.org/support/plugin/mailarchiver/">' . esc_html__( 'User support', 'mailarchiver' ) . '</a>' . L10n::get_language_markup( [ 'en' ] ) . '</p>';
		$content .= '<br/><p><strong>' . __( 'See also:', 'mailarchiver' ) . '</strong></p>';
		$content .= '<p><a href="https://perfops.one/">' . esc_html__( 'Official website', 'mailarchiver' ) . '</a>' . L10n::get_language_markup( [ 'en' ] ) . '</p>';
		$content .= '<p><a href="https://github.com/Pierre-Lannoy/wp-mailarchiver">' . esc_html__( 'GitHub repository', 'mailarchiver' ) . '</a>' . L10n::get_language_markup( [ 'en' ] ) . '</p>';
		$this->screen->set_help_sidebar( $content );
	}

	/**
	 * Get the level content.
	 *
	 * @return  string  The content to display about levels and severity.
	 * @since    1.2.0
	 */
	private function get_levels_content() {
		$content = '<p>' . sprintf( esc_html__( 'The status of an email is indicated by a "level". %s uses the following levels classification:', 'mailarchiver' ), MAILARCHIVER_PRODUCT_NAME ) . '</p>';
		foreach ( [ 'info', 'error' ] as $name ) {
			$icon     = '<img style="width:18px;float:left;padding-right:6px;" src="' . EventTypes::$icons[ strtolower( $name ) ] . '" />';
			$content .= '<p>' . $icon . '<strong>' . ucwords( strtolower( EventTypes::$level_names[ EventTypes::$levels[ strtolower( $name ) ] ] ) ) . '</strong> &mdash; ' . EventTypes::$level_texts[ strtolower( $name ) ] . '</p>';
		}
		return $content;
	}

	/**
	 * Get the archivers of a specific class.
	 *
	 * @param   string $class  The class of archivers ( 'alerting', 'debugging', 'logging').
	 * @return  string  The content to display about this class of archivers.
	 * @since    1.2.0
	 */
	private function get_archivers( $class ) {
		$handlers = new HandlerTypes();
		$content  = '';
		foreach ( $handlers->get_for_class( $class ) as $handler ) {
			$icon     = '<img style="width:18px;float:left;padding-right:6px;" src="' . $handler['icon'] . '" />';
			$content .= '<p>' . $icon . '<strong>' . $handler['name'] . '</strong> &mdash; ' . $handler['help'] . '</p>';
		}
		return $content;
	}

	/**
	 * Get the admin rights content.
	 *
	 * @return  string  The content to display about admin rights.
	 * @since    1.2.0
	 */
	private function get_admin_rights_content() {
		$content = '';
		if ( Role::SUPER_ADMIN === Role::admin_type() || Role::LOCAL_ADMIN === Role::admin_type() ) {
			$content  = '<p>' . esc_html__( 'Because your site takes part in a sites network, admin ability to view and configure archivers differ as follows:', 'mailarchiver' ) . '</p>';
			$content .= '<p><strong>' . esc_html_x( 'Network Admin', 'WordPress multisite', 'mailarchiver' ) . '</strong> &mdash; ' . esc_html__( 'Can set archivers, can view all emails in all WordPress archives.', 'mailarchiver' ) . ( Role::SUPER_ADMIN === Role::admin_type() ? ' <strong><em>' . esc_html__( 'That\'s your current role.', 'mailarchiver' ) . '</em></strong>' : '' ) . '</p>';
			$content .= '<p><strong>' . esc_html_x( 'Sites Admin', 'WordPress multisite', 'mailarchiver' ) . '</strong> &mdash; ' . esc_html__( 'Can\'t set archivers, can only view emails sent via their own sites in all authorized WordPress archives.', 'mailarchiver' ) . ( Role::LOCAL_ADMIN === Role::admin_type() ? ' <strong><em>' . esc_html__( 'That\'s your current role.', 'mailarchiver' ) . '</em></strong>' : '' ) . '</p>';
		}
		return $content;
	}

	/**
	 * Displays inline help for archivers tab.
	 *
	 * @since    1.2.0
	 */
	private function set_contextual_settings_archivers() {
		$tabs = [];
		// Overview.
		$content  = '<p>' . sprintf( esc_html__( 'This screen allows you to set the %s archivers.', 'mailarchiver' ), MAILARCHIVER_PRODUCT_NAME ) . '</p>';
		$content .= '<p>' . esc_html__( 'An archiver is a recorder of emails. It can filter them (accept or refuse to record the email based on settings) then store them or transmit them to logging/alerting services.', 'mailarchiver' );
		$content .= ' ' . esc_html__( 'You can set as many archivers as you want. All the set archivers will receive all emails and, regarding their own settings, will enrich them and record them or not.', 'mailarchiver' ) . '</p>';
		$content .= '<p>' . esc_html__( 'Archivers are classified in six main categories: alerting, forwarding, logging, archive or individual storing and testing & preview. You can find details on these categories on the corresponding tabs of this help.', 'mailarchiver' ) . '</p>';
		$tabs[]   = [
			'title'   => esc_html__( 'Overview', 'mailarchiver' ),
			'id'      => 'mailarchiver-contextual-settings-archivers-overview',
			'content' => $content,
		];
		// Alerting.
		$content = '<p>' . esc_html__( 'These archivers allow you to send alerts:', 'mailarchiver' ) . '</p>';
		$tabs[]  = [
			'title'   => esc_html__( 'Alerting', 'mailarchiver' ),
			'id'      => 'mailarchiver-contextual-settings-archivers-alerting',
			'content' => $content . $this->get_archivers( 'alerting' ),
		];
		// Forwarding.
		$content = '<p>' . esc_html__( 'These archivers allow you to forward sent emails:', 'mailarchiver' ) . '</p>';
		$tabs[]  = [
			'title'   => esc_html__( 'Forwarding', 'mailarchiver' ),
			'id'      => 'mailarchiver-contextual-settings-archivers-forwarding',
			'content' => $content . $this->get_archivers( 'forwarding' ),
		];
		// Logging.
		$content = '<p>' . esc_html__( 'These archivers send archived emails to logging services. It may be local or SaaS, free or paid services:', 'mailarchiver' ) . '</p>';
		$tabs[]  = [
			'title'   => esc_html__( 'Logging', 'mailarchiver' ),
			'id'      => 'mailarchiver-contextual-settings-archivers-logging',
			'content' => $content . $this->get_archivers( 'logging' ),
		];
		// Storing.
		$content = '<p>' . esc_html__( 'These archivers store emails in bulk:', 'mailarchiver' ) . '</p>';
		$tabs[]  = [
			'title'   => esc_html__( 'Archive storing', 'mailarchiver' ),
			'id'      => 'mailarchiver-contextual-settings-archivers-storing',
			'content' => $content . $this->get_archivers( 'storing' ),
		];
		// Istoring.
		$content = '<p>' . esc_html__( 'These archivers store each email as individual files:', 'mailarchiver' ) . '</p>';
		$tabs[]  = [
			'title'   => esc_html__( 'Individual storing', 'mailarchiver' ),
			'id'      => 'mailarchiver-contextual-settings-archivers-istoring',
			'content' => $content . $this->get_archivers( 'istoring' ),
		];
		// Testing.
		$content = '<p>' . esc_html__( 'These archivers copy emails to testing and preview services:', 'mailarchiver' ) . '</p>';
		$tabs[]  = [
			'title'   => esc_html__( 'Testing & preview', 'mailarchiver' ),
			'id'      => 'mailarchiver-contextual-settings-archivers-testing',
			'content' => $content . $this->get_archivers( 'testing' ),
		];
		// Admin Rights.
		if ( Role::SUPER_ADMIN === Role::admin_type() || Role::LOCAL_ADMIN === Role::admin_type() ) {
			$tabs[] = [
				'title'   => esc_html__( 'Admin rights', 'mailarchiver' ),
				'id'      => 'mailarchiver-contextual-settings-archivers-rights',
				'content' => $this->get_admin_rights_content(),
			];
		}
		// Levels.
		$tabs[] = [
			'title'   => esc_html__( 'Status levels', 'mailarchiver' ),
			'id'      => 'mailarchiver-contextual-settings-archivers-levels',
			'content' => $this->get_levels_content(),
		];
		foreach ( $tabs as $tab ) {
			$this->screen->add_help_tab( $tab );
		}
		$this->set_sidebar();
	}

	/**
	 * Displays inline help for listeners tab.
	 *
	 * @since    1.2.0
	 */
	private function set_contextual_settings_listeners() {
		$tabs = [];
		// Overview.
		$content  = '<p>' . sprintf( esc_html__( 'This screen allows you to set the way %s uses listeners.', 'mailarchiver' ), MAILARCHIVER_PRODUCT_NAME ) . '</p>';
		$content .= '<p>' . esc_html__( 'A listener, as its name suggests, listen to a specific component (a "source") of your WordPress instance.', 'mailarchiver' );
		$content .= ' ' . sprintf( esc_html__( 'You can choose to tell %s to activate all the available listeners, or you can manually select the sources to listen.', 'mailarchiver' ), MAILARCHIVER_PRODUCT_NAME ) . '</p>';
		$tabs[]   = [
			'title'   => esc_html__( 'Overview', 'mailarchiver' ),
			'id'      => 'mailarchiver-contextual-settings-listeners-overview',
			'content' => $content,
		];
		// Admin Rights.
		if ( Role::SUPER_ADMIN === Role::admin_type() || Role::LOCAL_ADMIN === Role::admin_type() ) {
			$tabs[] = [
				'title'   => esc_html__( 'Admin rights', 'mailarchiver' ),
				'id'      => 'mailarchiver-contextual-settings-listeners-rights',
				'content' => $this->get_admin_rights_content(),
			];
		}
		foreach ( $tabs as $tab ) {
			$this->screen->add_help_tab( $tab );
		}
		$this->set_sidebar();
	}

	/**
	 * Displays inline help for options tab.
	 *
	 * @since    1.2.0
	 */
	private function set_contextual_settings_options() {
		$tabs = [];
		// Overview.
		$content = '<p>' . sprintf( esc_html__( 'This screen allows you to set misc options of %s.', 'mailarchiver' ), MAILARCHIVER_PRODUCT_NAME ) . '</p>';
		if ( Environment::is_wordpress_multisite() ) {
			$content .= '<p><em>' . esc_html__( 'Note these options are global. They are set for all archivers, for all sites in your network.', 'mailarchiver' ) . '</em></p>';
		} else {
			$content .= '<p><em>' . esc_html__( 'Note these options are global. They are set for all archivers.', 'mailarchiver' ) . '</em></p>';
		}
		$tabs[] = [
			'title'   => esc_html__( 'Overview', 'mailarchiver' ),
			'id'      => 'mailarchiver-contextual-settings-options-overview',
			'content' => $content,
		];
		// Admin Rights.
		if ( Role::SUPER_ADMIN === Role::admin_type() || Role::LOCAL_ADMIN === Role::admin_type() ) {
			$tabs[] = [
				'title'   => esc_html__( 'Admin rights', 'mailarchiver' ),
				'id'      => 'mailarchiver-contextual-settings-options-rights',
				'content' => $this->get_admin_rights_content(),
			];
		}

		foreach ( $tabs as $tab ) {
			$this->screen->add_help_tab( $tab );
		}
		$this->set_sidebar();
	}

	/**
	 * Displays inline help for settings pages.
	 *
	 * @since    1.2.0
	 */
	public function set_contextual_settings() {
		$this->init();
		if ( ! isset( $this->tab ) ) {
			$this->set_contextual_settings_archivers();
			return;
		}
		switch ( strtolower( $this->tab ) ) {
			case 'archivers':
				$this->set_contextual_settings_archivers();
				break;
			case 'listeners':
				$this->set_contextual_settings_listeners();
				break;
			case 'misc':
				$this->set_contextual_settings_options();
				break;
		}
	}

	/**
	 * Displays inline help for main viewer page.
	 *
	 * @since    1.2.0
	 */
	private function set_contextual_viewer_main() {
		$tabs = [];
		// Overview.
		$content  = '<p>' . esc_html__( 'This screen displays the list of archived emails. This list is sorted with the most recent email at the top.', 'mailarchiver' ) . '</p>';
		$content .= '<p>' . esc_html__( 'To move forward or backward in time, use the navigation buttons at the top or bottom right of this list.', 'mailarchiver' ) . '</p>';
		$content .= '<p>' . esc_html__( 'You can restrict the display of emails according to their status levels. To do so, use the two links at the top left of the list.', 'mailarchiver' ) . '</p>';
		$content .= '<p>' . esc_html__( 'You can change the archive being viewed (if you have set more than one WordPress archiver) with the selector at the top left of the list (don\'t forget to click on the "apply" button).', 'mailarchiver' ) . '</p>';
		$content .= '<p>' . esc_html__( 'To filter the displayed emails, use the small blue funnel next to the filterable items. These filters are cumulative, you can activate simultaneously several filters.', 'mailarchiver' ) . '<br/>';
		$content .= '<em>' . esc_html__( 'Note these filters are effective even on pseudonymized or obfuscated fields.', 'mailarchiver' ) . '</em></p>';
		$tabs[]   = [
			'title'   => esc_html__( 'Overview', 'mailarchiver' ),
			'id'      => 'mailarchiver-contextual-viewer-main-overview',
			'content' => $content,
		];
		// Admin Rights.
		if ( Role::SUPER_ADMIN === Role::admin_type() || Role::LOCAL_ADMIN === Role::admin_type() ) {
			$tabs[] = [
				'title'   => esc_html__( 'Admin rights', 'mailarchiver' ),
				'id'      => 'mailarchiver-contextual-viewer-main-rights',
				'content' => $this->get_admin_rights_content(),
			];
		}
		// Levels.
		$tabs[] = [
			'title'   => esc_html__( 'Status levels', 'mailarchiver' ),
			'id'      => 'mailarchiver-contextual-viewer-main-levels',
			'content' => $this->get_levels_content(),
		];
		foreach ( $tabs as $tab ) {
			$this->screen->add_help_tab( $tab );
		}
		$this->set_sidebar();
	}

	/**
	 * Displays inline help for event screen.
	 *
	 * @since    1.2.0
	 */
	private function set_contextual_viewer_event() {
		$tabs = [];
		// Overview.
		$content  = '<p>' . esc_html__( 'This screen displays the details of a specific email.', 'mailarchiver' ) . ' ' . esc_html__( 'It consists of four to six boxes, depending on your settings, which give specific details of the email:', 'mailarchiver' ) . '</p>';
		$content .= '<p><strong>' . esc_html__( 'Message details', 'mailarchiver' ) . '</strong> &mdash; ' . esc_html__( 'General information about the email.', 'mailarchiver' ) . '</p>';
		$content .= '<p><strong>' . esc_html__( 'Recipients', 'mailarchiver' ) . '</strong> &mdash; ' . esc_html__( 'List of recipients.', 'mailarchiver' ) . '</p>';
		$content .= '<p><strong>' . esc_html__( 'Request details', 'mailarchiver' ) . '</strong> &mdash; ' . esc_html__( 'Process, user and site from where the email was sent.', 'mailarchiver' ) . '</p>';
		$content .= '<p><strong>' . esc_html__( 'Attachments', 'mailarchiver' ) . '</strong> &mdash; ' . esc_html__( 'List of the email attachements.', 'mailarchiver' ) . '</p>';
		$content .= '<p><strong>' . esc_html__( 'Headers', 'mailarchiver' ) . '</strong> &mdash; ' . esc_html__( 'List of the email headers.', 'mailarchiver' ) . '</p>';
		$content .= '<p><strong>' . esc_html__( 'Content', 'mailarchiver' ) . '</strong> &mdash; ' . esc_html__( 'The content of the email itself.', 'mailarchiver' ) . '</p>';
		$tabs[]   = [
			'title'   => esc_html__( 'Overview', 'mailarchiver' ),
			'id'      => 'mailarchiver-contextual-viewer-event-overview',
			'content' => $content,
		];
		// Layout.
		$content  = '<p>' . esc_html__( 'You can use the following controls to arrange the screen to suit your usage preferences:', 'mailarchiver' ) . '</p>';
		$content .= '<p><strong>' . esc_html__( 'Screen Options', 'mailarchiver' ) . '</strong> &mdash; ' . esc_html__( 'Use the Screen Options tab to choose which boxes to show.', 'mailarchiver' ) . '</p>';
		$content .= '<p><strong>' . esc_html__( 'Drag and Drop', 'mailarchiver' ) . '</strong> &mdash; ' . esc_html__( 'To rearrange the boxes, drag and drop by clicking on the title bar of the selected box and releasing when you see a gray dotted-line rectangle appear in the location you want to place the box.', 'mailarchiver' ) . '</p>';
		$content .= '<p><strong>' . esc_html__( 'Box Controls', 'mailarchiver' ) . '</strong> &mdash; ' . esc_html__( 'Click the title bar of the box to expand or collapse it.', 'mailarchiver' ) . '</p>';
		$tabs[]   = [
			'title'   => esc_html__( 'Layout', 'mailarchiver' ),
			'id'      => 'mailarchiver-contextual-viewer-event-layout',
			'content' => $content,
		];
		// Levels.
		$tabs[] = [
			'title'   => esc_html__( 'Status levels', 'mailarchiver' ),
			'id'      => 'mailarchiver-contextual-viewer-event-levels',
			'content' => $this->get_levels_content(),
		];
		foreach ( $tabs as $tab ) {
			$this->screen->add_help_tab( $tab );
		}
		$this->set_sidebar();
	}

	/**
	 * Displays inline help for viewer pages.
	 *
	 * @since    1.2.0
	 */
	public function set_contextual_viewer() {
		$this->init();
		if ( isset( $this->event_id ) ) {
			$this->set_contextual_viewer_event();
			return;
		}
		$this->set_contextual_viewer_main();
	}

}
