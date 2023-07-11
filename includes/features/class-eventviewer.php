<?php
/**
 * Event viewer
 *
 * Handles a view for a specific event.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Plugin\Feature;

use Mailarchiver\System\Date;
use Mailarchiver\System\Timezone;
use Feather;
use Mailarchiver\System\Database;

use Mailarchiver\System\User;

/**
 * Define the event viewer functionality.
 *
 * Handles a view for a specific event.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class EventViewer {

	/**
	 * The screen id.
	 *
	 * @since  1.0.0
	 * @var    string    $screen_id    The screen id.
	 */
	private static $screen_id = 'mailarchiver_event_viewer';

	/**
	 * The full event detail.
	 *
	 * @since  1.0.0
	 * @var    array    $event    The full event detail.
	 */
	private $event = null;

	/**
	 * The events log id.
	 *
	 * @since  1.0.0
	 * @var    array    $logid    The events log id.
	 */
	private $logid = null;

	/**
	 * The event id.
	 *
	 * @since  1.0.0
	 * @var    array    $eventid    The event id.
	 */
	private $eventid = null;

	/**
	 * Mail body.
	 *
	 * @since  1.0.0
	 * @var    string    $body    The body, ready to print.
	 */
	private $body = 'No Content';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param   string  $logid      The events log id.
	 * @param   string  $eventid    The specific event id.
	 * @since    1.0.0
	 */
	public function __construct( $logid, $eventid ) {
		$this->logid   = $logid;
		$this->eventid = $eventid;
		$this->event   = null;
		$database      = new Database();
		$lines         = $database->load_lines( 'mailarchiver_' . str_replace( '-', '', $this->logid ), 'id', [ $this->eventid ] );
		if ( 1 === count( $lines ) ) {
			foreach ( Events::get() as $log ) {
				if ( $log['id'] === $this->logid ) {
					if ( ! array_key_exists( 'limit', $log ) || in_array( $lines[0]['site_id'], $log ['limit'] ) ) {
						$this->event = $lines[0];
						break;
					}
				}
			}
		}
		$this->prepare_body();
		wp_enqueue_script( 'postbox' );
	}

	/**
	 * Prepare the body to be printed.
	 *
	 * @since 1.0.0
	 */
	private function prepare_body() {
		$body = \json_decode( $this->event['body'], true );
		if ( is_array( $body ) && array_key_exists( 'raw', $body ) ) {
			$content = $body['raw'];
		} else {
			$content = esc_html__( 'malformed', 'mailarchiver' );
		}
		$is_html = false;
		if ( is_array( $body ) && array_key_exists( 'type', $body ) && 'raw' === $body['type'] ) {
			$search = strtolower( $body['raw'] );
			foreach ( [ '!doctype', 'html', 'body', 'span', 'table', 'div', 'ul' ] as $tag ) {
				if ( false !== strpos( $search, '<' . $tag ) ) {
					$is_html = true;
					break;
				}
			}
			if ( $is_html ) {
				$start = 0;
				if ( false !== strpos( $search, '<!doctype ' ) ) {
					$start = strpos( $search, '<!doctype ' );
				} elseif ( false !== strpos( $search, '<html ' ) ) {
					$start = strpos( $search, '<html ' );
				}
				$length = strlen( $content );
				if ( false !== strpos( $search, '</html>' ) ) {
					$length = strpos( $search, '</html>' ) + 6 - $start;
				}
				$content = substr( $content, $start, $length );
			} else {
				$content = str_replace( '"', '\"', $content );
				$content = str_replace( "\r\n", "<br>", $content );
				$content = str_replace( "\n", "<br>", $content );
				$content = str_replace( "\r", "<br>", $content );
				$content = str_replace( "\t", "<br>", $content );
				$content = '<html><body style=\"padding:10px;font-family:\'Lucida Console\', Monaco, monospace;font-size: 14px;background-color:#F1F1F1;\">' . $content . '</body></html>';
			}
		} elseif ( 'encrypted' === $body['type'] ) {
			$content = '<html><body><p>' . esc_html__( 'This content is encrypted', 'mailarchiver' ) . '</p><textarea style="font-family:\'Lucida Console\', Monaco, monospace;resize:none;width:100%;height:84%;border:none;border-radius:4px;padding:10px;background-color: #D0D0DE">' . $content . '</textarea></body></html>';
			$is_html = true;
		} else {
			$content = '<html><body>' . esc_html__( 'unknown', 'mailarchiver' ) . '</body></html>';
			$is_html = true;
		}
		if ( $is_html ) {
			$content = str_replace( '\\', '\\\\', $content );
			$content = str_replace( '"', '\"', $content );
			while ( false !== strpos( $content, '  ' ) ) {
				$content = str_replace("  ", " ", $content);
			}
		}
		$this->body = mailarchiver_strip_script_tags( $content, $is_html );
	}

	/**
	 * Append custom panel HTML to the "Screen Options" box of the current page.
	 * Callback for the 'screen_settings' filter.
	 *
	 * @param string $current Current content.
	 * @param object $screen The current screen.
	 * @return string The HTML code to append to "Screen Options".
	 * @since 1.0.0
	 */
	public function display_screen_settings( $current, $screen ) {
		if ( ! is_object( $screen ) || false === strpos( $screen->id, 'page_mailarchiver-viewer' ) ) {
			return $current;
		}
		$current .= '<div class="metabox-prefs custom-options-panel requires-autosave"><input type="hidden" name="_wpnonce-mailarchiver_viewer" value="' . wp_create_nonce( 'save_settings_mailarchiver_viewer' ) . '" />';
		$current .= $this->get_options();
		$current .= '</div>';
		return $current;
	}

	/**
	 * Add options.
	 *
	 * @since 1.0.0
	 */
	public function add_metaboxes_options() {
		$this->add_metaboxes();
	}

	/**
	 * Get the box options.
	 *
	 * @return string The HTML code to append.
	 * @since 1.0.0
	 */
	public function get_options() {
		$result  = '<fieldset class="metabox-prefs">';
		$result .= '<legend>' . esc_html__( 'Boxes', 'mailarchiver' ) . '</legend>';
		$result .= $this->meta_box_prefs();
		$result .= '</fieldset>';
		return $result;
	}

	/**
	 * Prints the meta box preferences.
	 *
	 * @return string The HTML code to append.
	 * @since 1.0.0
	 */
	public function meta_box_prefs() {
		global $wp_meta_boxes;
		$result = '';
		if ( empty( $wp_meta_boxes[ self::$screen_id ] ) ) {
			return '';
		}
		$hidden = get_hidden_meta_boxes( self::$screen_id );
		foreach ( array_keys( $wp_meta_boxes[ self::$screen_id ] ) as $context ) {
			foreach ( array( 'high', 'core', 'default', 'low' ) as $priority ) {
				if ( ! isset( $wp_meta_boxes[ self::$screen_id ][ $context ][ $priority ] ) ) {
					continue;
				}
				foreach ( $wp_meta_boxes[ self::$screen_id ][ $context ][ $priority ] as $box ) {
					if ( false === $box || ! $box['title'] ) {
						continue;
					}
					if ( 'submitdiv' === $box['id'] || 'linksubmitdiv' === $box['id'] ) {
						continue;
					}
					$box_id  = $box['id'];
					$result .= '<label for="' . $box_id . '-hide">';
					$result .= '<input class="hide-postbox-tog" name="' . $box_id . '-hide" type="checkbox" id="' . $box_id . '-hide" value="' . $box_id . '"' . ( ! in_array( $box_id, $hidden, false ) ? ' checked="checked"' : '' ) . ' />';
					$result .= $box['title'] . '</label>';
				}
			}
		}
		return $result;
	}

	/**
	 * Get the event viewer.
	 *
	 * @since 1.0.0
	 **/
	public function get() {
		echo '<div class="wrap">';
		if ( isset( $this->event ) ) {
			$icon = '<img style="width:30px;float:left;padding-right:8px;" src="' . EventTypes::$icons[ $this->event['level'] ] . '" />';
			$name = mailarchiver_strip_script_tags( $this->event['subject'], true );
			// phpcs:ignore
			echo '<h2>' . $icon . $name . '</h2>';
			settings_errors();
			echo '<style>#wpfooter{position:unset!important;}</style>';
			echo '<form name="mailarchiver_event" method="post">';
			echo '<div id="dashboard-widgets-wrap">';
			wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
			wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
			echo '    <div id="dashboard-widgets" class="metabox-holder">';
			echo '        <div id="postbox-container-1" class="postbox-container">';
			do_meta_boxes( self::$screen_id, 'advanced', null );
			echo '        </div>';
			echo '        <div id="postbox-container-2" class="postbox-container">';
			do_meta_boxes( self::$screen_id, 'side', null );
			echo '        </div>';
			echo '        <div id="postbox-container-3" class="postbox-container">';
			do_meta_boxes( self::$screen_id, 'column3', null );
			echo '        </div>';
			echo '        <div id="postbox-container-4" class="postbox-container">';
			do_meta_boxes( self::$screen_id, 'column4', null );
			echo '        </div>';
			echo '        <div id="postbox-container-5" class="postbox-container" style="width:100%">';
			do_meta_boxes( self::$screen_id, 'column5', null );
			echo '        </div>';
			echo '    </div>';
			echo '    </div>';
			echo '</div>';
			echo '</form>';
		} else {
			echo '<h2>' . esc_html__( 'Forbidden', 'mailarchiver' ) . '</h2>';
			settings_errors();
			echo '<p>' . esc_html__( 'The email archive you tried to access is out of your scope.', 'mailarchiver' ) . '</p>';
			echo '<p>' . esc_html__( 'If you think this is an error, please contact the network administrator with these details:', 'mailarchiver' );
			echo '<ul>';
			// phpcs:ignore
			echo '<li>' . sprintf( esc_html__( 'Archive: %s', 'mailarchiver' ), '<code>' . $this->logid . '</code>' ) . '</li>';
			// phpcs:ignore
			echo '<li>' . sprintf( esc_html__( 'Email: %s', 'mailarchiver' ), '<code>' . $this->eventid . '</code>' ) . '</li>';
			echo '</ul>';
			echo '</p>';
			\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->warning( sprintf( 'Trying to access out of scope email #%s from archive {%s}.', $this->eventid, $this->logid ), [ 'code' => 403 ] );
		}
		echo '</div>';
	}

	/**
	 * Add footer scripts.
	 *
	 * @since 1.0.0
	 */
	public function add_footer() {
		$result  = '<script>';
		$result .= '    jQuery(document).ready( function($) {';
		$result .= '        const iframe = document.querySelector("#mailarchiver-body-iframe");';
		$result .= '        const source = "' . $this->body . '";';
		$result .= '        iframe.src = URL.createObjectURL(new Blob([source], { type : "text/html" }));';
		$result .= '        $("#mailarchiver-body-iframe").load(function() {this.style.height = this.contentWindow.document.body.offsetHeight + 30 + "px";});';
		$result .= "        $('.if-js-closed').removeClass('if-js-closed').addClass('closed');";
		$result .= "        if(typeof postboxes !== 'undefined')";
		$result .= "            postboxes.add_postbox_toggles('" . self::$screen_id . "');";
		$result .= '    });';
		$result .= '</script>';
		// phpcs:ignore
		echo $result;
	}

	/**
	 * Add all the needed meta boxes.
	 *
	 * @since 1.0.0
	 */
	public function add_metaboxes() {
		// Left column.
		add_meta_box( 'mailarchiver-main', esc_html__( 'Message details', 'mailarchiver' ), [ $this, 'message_widget' ], self::$screen_id, 'advanced' );
		add_meta_box( 'mailarchiver-recipients', esc_html__( 'Recipients', 'mailarchiver' ), [ $this, 'recipients_widget' ], self::$screen_id, 'advanced' );
		add_meta_box( 'mailarchiver-details', esc_html__( 'Request details', 'mailarchiver' ), [ $this, 'details_widget' ], self::$screen_id, 'advanced' );
		// Right column.
		add_meta_box( 'mailarchiver-attachments', esc_html__( 'Attachments', 'mailarchiver' ), [ $this, 'attachments_widget' ], self::$screen_id, 'side' );
		add_meta_box( 'mailarchiver-headers', esc_html__( 'Headers', 'mailarchiver' ), [ $this, 'headers_widget' ], self::$screen_id, 'side' );
		// Bottom area.
		add_meta_box( 'mailarchiver-body', esc_html__( 'Content', 'mailarchiver' ), [ $this, 'body_widget' ], self::$screen_id, 'column5' );
	}

	/**
	 * Print an activity block.
	 *
	 * @param   string $content The content of the block.
	 * @since 1.0.0
	 */
	private function output_activity_block( $content ) {
		echo '<div class="activity-block" style="padding-bottom: 0;padding-top: 0;">';
		// phpcs:ignore
		echo $content;
		echo '</div>';
	}

	/**
	 * Get a section to include in a block.
	 *
	 * @param   string $content The content of the section.
	 * @return  string  The section, ready to print.
	 * @since 1.0.0
	 */
	private function get_section( $content ) {
		return '<div style="margin-bottom: 10px;">' . $content . '</div>';
	}

	/**
	 * Get an icon.
	 *
	 * @param   string $icon_name The name of the icon.
	 * @param   string $background The background color.
	 * @return  string  The icon, as image, ready to print.
	 * @since 1.0.0
	 */
	private function get_icon( $icon_name, $background = '#F9F9F9' ) {
		return '<img style="width:18px;float:left;padding-right:6px;" src="' . Feather\Icons::get_base64( $icon_name, $background, '#9999BB' ) . '" />';
	}

	/**
	 * Get content of the message widget box.
	 *
	 * @since 1.0.0
	 */
	public function message_widget() {
		// Event type.
		$icon    = '<img style="width:18px;float:left;padding-right:6px;" src="' . EventTypes::$icons[ $this->event['level'] ] . '" />';
		$level   = EventTypes::$level_texts[ $this->event['level'] ];
		$content = '<span style="width:100%;cursor: default;word-break: break-all;">' . $icon . $level . '</span>';
		$event   = $this->get_section( $content );
		// Event time.
		$time    = Date::get_date_from_mysql_utc( $this->event['timestamp'], Timezone::network_get()->getName(), 'Y-m-d H:i:s' );
		$dif     = Date::get_positive_time_diff_from_mysql_utc( $this->event['timestamp'] );
		$content = '<span style="width:100%;cursor: default;">' . $this->get_icon( 'clock' ) . $time . '</span> <span style="color:silver">(' . $dif . ')</span>';
		$hour    = $this->get_section( $content );
		// From
		$content = '<span style="width:100%;cursor: default;">' . $this->get_icon( 'at-sign', 'none' ) . $this->event['from'] . '</span>';
		$from    = $this->get_section( $content );
		// Event message.
		if ( 'info' !== $this->event['level'] ) {
			$content = '<span style="width:100%;cursor: default;word-break: break-all;">' . $this->get_icon( 'message-square' ) . $this->event['error'] . '</span>';
			$message = $this->get_section( $content );
		} else {
			$message = '';
		}
		$this->output_activity_block( $event . $hour . $from . $message );
	}

	/**
	 * Get content of the recipients widget box.
	 *
	 * @since 1.0.0
	 */
	public function recipients_widget() {
		$content = '';
		$tos     = \json_decode( $this->event['to'], true );
		if ( 0 < count( $tos ) ) {
			foreach ( $tos as $to ) {
				if ( 0 === strpos( $to, '{' ) ) {
					$content .= $this->get_section( '<span style="width:100%;cursor: default;">' . $this->get_icon( 'user' ) . esc_html__( 'Masked address', 'mailarchiver' ) . '</span>' );
				} else {
					$user = get_user_by( 'email', $to );
					if ( false !== $user ) {
						$content .= $this->get_section( '<span style="width:100%;cursor: default;">' . $this->get_icon( 'user-check' ) . $to . '<span style="margin-left:8px;color:silver">' . User::get_user_string( $user->ID ) . '</span>' . '</span>' );
					} else {
						$content .= $this->get_section( '<span style="width:100%;cursor: default;">' . $this->get_icon( 'user' ) . $to . '</span>' );
					}
				}
			}
		}
		$this->output_activity_block( $content );
	}

	/**
	 * Get content of the details widget box.
	 *
	 * @since 1.0.0
	 */
	public function details_widget() {
		// Event type.
		$channel = ChannelTypes::$channel_names[ strtoupper( $this->event['channel'] ) ];
		$content = '<span style="width:60%;cursor: default;">' . $this->get_icon( 'activity', 'none' ) . $channel . '</span>';
		$event   = $this->get_section( $content );
		// Event source.
		$class     = ClassTypes::$classe_names[ strtolower( $this->event['class'] ) ];
		$component = $this->event['component'] . ' ' . $this->event['version'];
		$content   = '<span style="width:40%;cursor: default;float:left">' . $this->get_icon( 'folder' ) . $class . '</span>';
		$content  .= '<span style="width:60%;cursor: default;">' . $this->get_icon( 'box' ) . $component . '</span>';
		$source    = $this->get_section( $content );
		// User detail.
		if ( 'anonymous' === $this->event['user_name'] ) {
			$user = $this->get_section( '<span style="width:100%;cursor: default;word-break: break-all;">' . $this->get_icon( 'user' ) . esc_html__( 'Anonymous user', 'mailarchiver' ) . '</span>' );
		} elseif ( 0 === strpos( $this->event['user_name'], '{' ) ) {
			$user = $this->get_section( '<span style="width:100%;cursor: default;word-break: break-all;">' . $this->get_icon( 'user' ) . esc_html__( 'Pseudonymized user', 'mailarchiver' ) . '</span>' );
		} elseif ( 0 !== (int) $this->event['user_id'] ) {
			$user = $this->get_section( '<span style="width:100%;cursor: default;word-break: break-all;">' . $this->get_icon( 'user-check' ) . User::get_user_string( (int) $this->event['user_id'] ) . '</span>' );
		} else {
			$user = $this->get_section( '<span style="width:100%;cursor: default;word-break: break-all;">' . $this->get_icon( 'user-x' ) . esc_html__( 'Deleted user', 'mailarchiver' ) . '</span>' );
		}
		// Site detail.
		$content = '<span style="width:100%;cursor: default;">' . $this->get_icon( 'layout' ) . $this->event['site_name'] . '</span>';
		$site    = $this->get_section( $content );

		$this->output_activity_block( $event . $source . $user . $site);
	}

	/**
	 * Get content of the headers widget box.
	 *
	 * @since 1.0.0
	 */
	public function headers_widget() {
		$content = '';
		$headers = \json_decode( $this->event['headers'], true );
		if ( 0 < count( $headers ) ) {
			foreach ( $headers as $header ) {
				$content .= $this->get_section( '<span style="width:100%;cursor: default;">' . $this->get_icon( 'list' ) . $header . '</span>' );
			}
		}
		$this->output_activity_block( $content );
	}

	/**
	 * Get content of the attachments widget box.
	 *
	 * @since 1.0.0
	 */
	public function attachments_widget() {
		$content     = '';
		$attachments = \json_decode( $this->event['attachments'], true );
		if ( 0 < count( $attachments ) ) {
			foreach ( $attachments as $attachment ) {
				$content .= $this->get_section( '<span style="width:100%;cursor: default;">' . $this->get_icon( 'file' ) . $attachment . '</span>' );
			}
		}
		$this->output_activity_block( $content );
	}

	/**
	 * Get content of the mail body.
	 *
	 * @since 1.0.0
	 */
	public function body_widget() {
		$this->output_activity_block( $this->get_section( '<iframe id="mailarchiver-body-iframe" class="mailarchiver-iframe" style="width:100%;"></iframe>' ) );
	}

}
