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

use Mailarchiver\Plugin\Feature\Events;

$eventListTable = new Events();
$eventListTable->prepare_items();

?>

<div class="wrap">
	<h2><?php echo sprintf( esc_html__( '%s Viewer', 'mailarchiver' ), MAILARCHIVER_PRODUCT_NAME );?></h2>
	<?php $eventListTable->views(); ?>
	<form id="events-filter" method="get" action="<?php echo admin_url('admin.php'); ?>">
		<input type="hidden" name="page" value="mailarchiver-viewer" />
		<?php $eventListTable->display(); ?>
	</form>
</div>
