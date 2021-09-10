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

use Mailarchiver\Plugin\Feature\HandlerTypes;

$archiver_types = new HandlerTypes();

?>

<div id="normal-sortables" class="meta-box-sortables ui-sortable" style="overflow: hidden;">
	<div class="postbox ">
		<h3 class="hndle" style="cursor:default;"><span><?php esc_html_e( 'Please, select the type of archiver you want to add', 'mailarchiver' ); ?>&hellip;</span></h3>
		<div style="width: 100%;text-align: center;padding: 0px;" class="inside">
			<div style="display:grid;grid-template-columns: repeat(auto-fill, 120px);justify-content: center;">
                <style>
                    .actionable:hover {border-radius:6px;cursor:pointer; -moz-transition: all .1s ease-in; -o-transition: all .1s ease-in; -webkit-transition: all .1s ease-in; transition: all .1s ease-in; background: #f5f5f5;border:1px solid #e0e0e0;}
                    .actionable {border-radius:6px;cursor:pointer; -moz-transition: all .2s ease-in; -o-transition: all .2s ease-in; -webkit-transition: all .2s ease-in; transition: all .2s ease-in; background: transparent;border:1px solid transparent;}
                </style>
				<?php foreach ( $archiver_types->get_all() as $archiver ) { ?>
					<?php if ( 'system' !== $archiver['class'] ) { ?>
						<div><img id="<?php echo $archiver['id']; ?>" class="actionable" style="width:80px;" src="<?php echo $archiver['icon']; ?>"/></div>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
		<div id="major-publishing-actions">
			<div id="tip-text">&nbsp;</div>
			<div class="clear"></div>
		</div>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$(".actionable").mouseout(function() {
				$("#tip-text").html("&nbsp;");
			});
			<?php foreach ( $archiver_types->get_all() as $archiver ) { ?>
				$("#<?php echo $archiver['id']; ?>").mouseover(function() {
					$("#tip-text").html("<strong><?php echo $archiver['name']; ?></strong> - <?php echo ucfirst( $archiver['help'] ); ?>");
				});
				$("#<?php echo $archiver['id']; ?>").click(function() {
					<?php // phpcs:ignore ?>
					window.open('<?php echo add_query_arg( array( 'page'    => 'mailarchiver-settings', 'action'  => 'form-edit', 'tab'     => 'archivers', 'handler' => $archiver['id'], ), admin_url( 'admin.php' ) );?>', '_self');
				});
			<?php } ?>
		});
	</script>
</div>
