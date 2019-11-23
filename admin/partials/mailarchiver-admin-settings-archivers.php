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

use Mailarchiver\Plugin\Feature\Archivers;

$archivers = new Archivers();
$archivers->prepare_items();

$button = '<a href="#" class="page-title-action add-trigger">' . esc_html__( 'Add an Archiver', 'mailarchiver' ) . '</a>'

?>

<style>.tablenav{display:none !important;}</style>

<p>&nbsp;</p>
<p><?php echo $button; ?></p>
<div class="add-text" style="display:none;">
	<div id="wpcom-stats-meta-box-container" class="metabox-holder">
		<div class="postbox-container" style="width: 100%;margin-right: 10px;">
			<?php require MAILARCHIVER_ADMIN_DIR . 'partials/mailarchiver-admin-settings-archiver-choose.php'; ?>
		</div>
	</div>
</div>
<?php $archivers->display(); ?>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$(".add-trigger").click(function() {
			$(".add-text").slideToggle(400);
		});
	});
</script>
