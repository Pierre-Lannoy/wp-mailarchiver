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

// phpcs:ignore
$active_tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'loggers' );

?>

<div class="wrap">

	<h2><?php echo esc_html( sprintf( esc_html__( '%s Settings', 'mailarchiver' ), MAILARCHIVER_PRODUCT_NAME ) ); ?></h2>
	<?php settings_errors(); ?>

	<h2 class="nav-tab-wrapper">
		<a href="
		<?php
		echo esc_url(
			add_query_arg(
				array(
					'page' => 'mailarchiver-settings',
					'tab'  => 'loggers',
				),
				admin_url( 'options-general.php' )
			)
		);
		?>
		" class="nav-tab <?php echo 'loggers' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Loggers', 'mailarchiver' ); ?></a>
        <a href="
		<?php
		echo esc_url(
			add_query_arg(
				array(
					'page' => 'mailarchiver-settings',
					'tab'  => 'listeners',
				),
				admin_url( 'options-general.php' )
			)
		);
		?>
        " class="nav-tab <?php echo 'listeners' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Listeners', 'mailarchiver' ); ?></a>
		<a href="
		<?php
		echo esc_url(
			add_query_arg(
				array(
					'page' => 'mailarchiver-settings',
					'tab'  => 'misc',
				),
				admin_url( 'options-general.php' )
			)
		);
		?>
		" class="nav-tab <?php echo 'misc' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Options', 'mailarchiver' ); ?></a>
		<a href="
		<?php
		echo esc_url(
			add_query_arg(
				array(
					'page' => 'mailarchiver-settings',
					'tab'  => 'about',
				),
				admin_url( 'options-general.php' )
			)
		);
		?>
		" class="nav-tab <?php echo 'about' === $active_tab ? 'nav-tab-active' : ''; ?>" style="float:right;"><?php esc_html_e( 'About', 'mailarchiver' ); ?></a>
	</h2>

	<?php if ( 'loggers' === $active_tab ) { ?>
		<?php include __DIR__ . '/mailarchiver-admin-settings-loggers.php'; ?>
	<?php } ?>
	<?php if ( 'listeners' === $active_tab ) { ?>
		<?php include __DIR__ . '/mailarchiver-admin-settings-listeners.php'; ?>
	<?php } ?>
	<?php if ( 'misc' === $active_tab ) { ?>
		<?php include __DIR__ . '/mailarchiver-admin-settings-options.php'; ?>
	<?php } ?>
	<?php if ( 'about' === $active_tab ) { ?>
		<?php include __DIR__ . '/mailarchiver-admin-settings-about.php'; ?>
	<?php } ?>
</div>
