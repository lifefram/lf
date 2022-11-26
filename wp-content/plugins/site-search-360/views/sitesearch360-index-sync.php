<?php 
    $syncOnSave = get_option('ss360_sync_on_save');
    $syncOnStatus = get_option('ss360_sync_on_status');
    $syncOnFuture = get_option('ss360_sync_on_future');
    $syncOnDelete = get_option('ss360_sync_on_delete');
    if($syncOnSave==null){
        $syncOnSave = true;
    }
    if($syncOnStatus==null){
        $syncOnStatus = true;
    }
    if($syncOnFuture==null){
        $syncOnFuture = true;
    }
    if($syncOnDelete==null){
        $syncOnDelete = true;
    }
    if (!empty($_POST) && isset($_POST['_wpnonce']) && $_POST['action']=='ss360_updateIndexSynchronization') {
        $syncOnSave = isset($_POST['syncOnSave']) && $_POST['syncOnSave']=='on';          
        $syncOnStatus = isset($_POST['syncOnStatus']) && $_POST['syncOnStatus']=='on';          
        $syncOnFuture = isset($_POST['syncOnFuture']) && $_POST['syncOnFuture']=='on';          
        $syncOnDelete = isset($_POST['syncOnDelete']) && $_POST['syncOnDelete']=='on';
        update_option('ss360_sync_on_save', $syncOnSave ? 1 : 0);          
        update_option('ss360_sync_on_status', $syncOnStatus ? 1 : 0);          
        update_option('ss360_sync_on_future', $syncOnFuture ? 1 : 0);          
        update_option('ss360_sync_on_delete', $syncOnDelete ? 1 : 0);          
    }
    if(!isset($requestUri)) {
		$requestUri = esc_url($_SERVER['REQUEST_URI']);
	}
?>
<div class="wrapper wrapper--narrow">
    <div class="block block--first flex flex--column">
        <h2><?php esc_html_e('Index Synchronization', 'site-search-360') ?></h2>
        <form id="index-sync" name="ss360_updateIndexSynchronization" method="post" action="<?php echo $requestUri; ?>" >
            <?php wp_nonce_field(); ?>
            <input type="hidden" name="action" value="ss360_updateIndexSynchronization">
            <ul>
                <li>
                    <label class="checkbox">
                        <?php esc_html_e('Synchronize on post save', 'site-search-360') ?>
                        <input class="fake-hide" type="checkbox" id="syncOnSave" name="syncOnSave" <?php echo $syncOnSave ? 'checked':''?>/>
                        <span class="checkbox_checkmark"></span>
                    </label>
                </li>
                <li>
                    <label class="checkbox">
                        <?php esc_html_e('Synchronize on post status change', 'site-search-360') ?>
                        <input class="fake-hide" type="checkbox" id="syncOnStatus" name="syncOnStatus" <?php echo $syncOnStatus ? 'checked':''?>/>
                        <span class="checkbox_checkmark"></span>
                    </label>
                </li>
                <li>
                    <label class="checkbox">
                        <?php esc_html_e('Synchronize when scheduled post is published', 'site-search-360') ?>
                        <input class="fake-hide" type="checkbox" id="syncOnFuture" name="syncOnFuture" <?php echo $syncOnFuture ? 'checked':''?>/>
                        <span class="checkbox_checkmark"></span>
                    </label>
                </li>
                <li>
                    <label class="checkbox">
                        <?php esc_html_e('Synchronize on post delete', 'site-search-360') ?>
                        <input class="fake-hide" type="checkbox" id="syncOnDelete" name="syncOnDelete" <?php echo $syncOnDelete ? 'checked':''?>/>
                        <span class="checkbox_checkmark"></span>
                    </label>
                </li>
            </ul>
            <div class="flex flex--center w-100">
                <button class="button button--padded" type="submit"><?php esc_html_e('Save', 'site-search-360'); ?></button>
            </div>
        </form>
    </div>
</div>