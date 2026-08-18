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
$active_tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'archivers' );

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
					'tab'  => 'archivers',
				),
				admin_url( 'admin.php' )
			)
		);
		?>
		" class="nav-tab <?php echo 'archivers' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Archivers', 'mailarchiver' ); ?></a>
		<a href="
		<?php
		echo esc_url(
			add_query_arg(
				array(
					'page' => 'mailarchiver-settings',
					'tab'  => 'misc',
				),
				admin_url( 'admin.php' )
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
				admin_url( 'admin.php' )
			)
		);
		?>
		" class="nav-tab <?php echo 'about' === $active_tab ? 'nav-tab-active' : ''; ?>" style="float:right;"><?php esc_html_e( 'About', 'mailarchiver' ); ?></a>
		<?php if ( class_exists( 'Mailarchiver\Plugin\Feature\Wpcli' ) ) { ?>
            <a href="
            <?php
			echo esc_url(
				add_query_arg(
					array(
						'page' => 'mailarchiver-settings',
						'tab'  => 'wpcli',
					),
					admin_url( 'admin.php' )
				)
			);
			?>
            " class="nav-tab <?php echo 'wpcli' === $active_tab ? 'nav-tab-active' : ''; ?>" style="float:right;">WP-CLI</a>
		<?php } ?>
	</h2>

	<?php if ( 'archivers' === $active_tab ) { ?>
		<?php include __DIR__ . '/mailarchiver-admin-settings-archivers.php'; ?>
	<?php } ?>
	<?php if ( 'misc' === $active_tab ) { ?>
		<?php include __DIR__ . '/mailarchiver-admin-settings-options.php'; ?>
	<?php } ?>
	<?php if ( 'about' === $active_tab ) { ?>
		<?php include __DIR__ . '/mailarchiver-admin-settings-about.php'; ?>
	<?php } ?>
	<?php if ( 'wpcli' === $active_tab ) { ?>
		<?php wp_enqueue_style( MAILARCHIVER_ASSETS_ID ); ?>
		<?php echo do_shortcode( '[mailarchiver-wpcli]' ); ?>
	<?php } ?>
    <div style="min-height: 100px; position: fixed; bottom: 4vh; right: 4vw; z-index: 10000">
        <div style="background-color: #FFF; padding: 20px; border-radius: 4px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2)">
            <img style="width:60px; margin-right: 20px;" src="<?php echo \PerfOpsOne\Resources::get_sponsor_base64_logo(); ?>"/><div style="float: right; text-align: center;padding-top:10px">The PerfOps One plugins suite is sponsored by <br/><a href="https://hosterra.eu">Hosterra - Ethical & Sustainable Internet Hosting</a></div>
        </div>
    </div>
</div>
