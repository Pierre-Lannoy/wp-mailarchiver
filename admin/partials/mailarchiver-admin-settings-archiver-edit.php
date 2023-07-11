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
				'page'    => 'mailarchiver-settings',
				'action'  => 'do-edit',
				'tab'     => 'archivers',
				'handler' => $current_archiver['handler'],
				'uuid'    => $current_archiver['uuid'],
			),
			admin_url( 'admin.php' )
		)
	);
	?>
	" method="POST">
		<?php do_settings_sections( 'mailarchiver_archiver_misc_section' ); ?>
		<?php do_settings_sections( 'mailarchiver_archiver_specific_section' ); ?>
		<?php if ( in_array( $current_handler['class'], [ 'alerting', 'logging', 'storing' ], true ) ) { ?>
            <?php do_settings_sections( 'mailarchiver_archiver_privacy_section' ); ?>
			<?php do_settings_sections( 'mailarchiver_archiver_security_section' ); ?>
            <?php do_settings_sections( 'mailarchiver_archiver_details_section' ); ?>
		<?php } ?>
		<?php wp_nonce_field( 'mailarchiver-archiver-edit' ); ?>
		<p><?php echo get_submit_button( esc_html__( 'Cancel', 'mailarchiver' ), 'secondary', 'cancel', false ); ?>&nbsp;&nbsp;&nbsp;<?php echo get_submit_button( null, 'primary', 'submit', false ); ?></p>
	</form>
</div>
