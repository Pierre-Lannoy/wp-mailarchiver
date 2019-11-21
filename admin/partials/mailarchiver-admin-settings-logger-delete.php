<?php
/**
 * Provide a admin-facing view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package    Plugin
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

?>

<div class="wrap">
	<form action="
	<?php
	echo esc_url(
		add_query_arg(
			array(
				'page'   => 'mailarchiver-settings',
				'action' => 'do-delete',
				'tab'    => 'loggers',
				'uuid'   => $current_logger['uuid'],
			),
			admin_url( 'options-general.php' )
		)
	);
	?>
	" method="POST">
		<?php do_settings_sections( 'mailarchiver_logger_delete_section' ); ?>
		<?php wp_nonce_field( 'mailarchiver-logger-delete' ); ?>
		<p><?php esc_html_e( 'Are you sure you want to permanently remove this logger?', 'mailarchiver' ); ?></p>
		<p><?php echo get_submit_button( esc_html__( 'Abort', 'mailarchiver' ), 'secondary', 'cancel', false ); ?>&nbsp;&nbsp;&nbsp;<?php echo get_submit_button( esc_html__( 'Remove Permanently', 'mailarchiver' ), 'primary', 'submit', false ); ?></p>
	</form>
</div>
