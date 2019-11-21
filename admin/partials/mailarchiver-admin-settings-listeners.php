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
wp_enqueue_script(MAILARCHIVER_ASSETS_ID);
?>

<form action="
	<?php
echo esc_url(
	add_query_arg(
		array(
			'page'    => 'mailarchiver-settings',
			'action'  => 'do-save',
			'tab'     => 'listeners',
		),
		admin_url( 'options-general.php' )
	)
);
?>
	" method="POST">
	<?php do_settings_sections( 'mailarchiver_listeners_options_section' ); ?>
	<div id="listeners-settings" class="hidden">
		<?php do_settings_sections( 'mailarchiver_listeners_settings_section' ); ?>
	</div>
	<?php wp_nonce_field( 'mailarchiver-listeners-options' ); ?>
	<p><?php echo get_submit_button( esc_html__( 'Reset to Defaults', 'mailarchiver' ), 'secondary', 'reset-to-defaults', false ); ?>&nbsp;&nbsp;&nbsp;<?php echo get_submit_button( null, 'primary', 'submit', false ); ?></p>
</form>
