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

<div class="alignleft actions bulkactions">
    <label for="larchiver_id" class="screen-reader-text"><?php esc_html_e('Choose events log to display', 'mailarchiver');?></label>
    <select name="archiver_id" id="archiver_id">
		<?php foreach ($list->get_archivers() as $l) { ?>
            <option <?php echo ($list->get_current_Log_id() === $l['id'] ? 'selected="selected"' : ''); ?> value="<?php echo $l['id']; ?>"><?php echo $l['name']; ?> (<?php ($l['running']?esc_html_e('running', 'mailarchiver'):esc_html_e('paused', 'mailarchiver')); ?>)</option>
		<?php } ?>
    </select>
    <input type="submit" class="button action" value="<?php esc_html_e('Apply', 'mailarchiver');?>"  />
</div>
