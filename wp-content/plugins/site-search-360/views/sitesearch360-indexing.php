<?php
    $ss360_plugin = new SiteSearch360Plugin();
    $ss360_post_types = $ss360_plugin->getPostTypes();
    $ss360_indexing_mode = get_option("ss360_indexing_mode");
    $ss360_reindex_started = false;

    if (!empty($_POST) && isset($_POST['_wpnonce'])) {
        if($_POST['action'] == 'reindex') {        
            $ss360_client = new SiteSearch360Client();
            $mode_changed = $ss360_indexing_mode != $_POST['ss360_indexing_mode'];
            $ss360_indexing_mode = $_POST['ss360_indexing_mode'];
            update_option('ss360_indexing_mode', $ss360_indexing_mode);
            $ss360_empty_index = isset($_POST['emptyIndex']) && $_POST['emptyIndex'] == 'on';

            if($ss360_indexing_mode=='db'){
				$ss360_planInfo = $ss360_client->planInfo();
				if ($ss360_planInfo != NULL && isset($ss360_planInfo['plan']) && isset($ss360_planInfo['plan']['pageLimit'])) {
					update_option('ss360_page_limit', $ss360_planInfo['plan']['pageLimit']);
				}
				foreach($ss360_post_types as $ss360_post_type){
                    $ss360_to_index = $ss360_plugin->getPostCount($ss360_post_type);
                    $ss360_to_db_key = 'ss360_'.$ss360_post_type.'_to_index';
                    $ss360_offset_db_key = 'ss360_'.$ss360_post_type.'_index_offset';
                    update_option($ss360_to_db_key, $ss360_to_index);
                    update_option($ss360_offset_db_key, 0);
                }
                if($mode_changed){
                    $ss360_client->disableAutoIndexing();
                    if($ss360_empty_index) {
                        $ss360_client->emptyEntireIndex();
                    }
                }
                delete_option('ss360_is_indexed');
            } else {
                if($mode_changed){
                    $ss360_client->enableAutoIndexing($ss360_empty_index);
                    if($ss360_empty_index) {
                        $ss360_client->emptyEntireIndex();
                    }
                }
                $ss360_client->startRecrawl();
                $ss360_reindex_started = true;
            }
        } else if($_POST['action'] == 'stop'){
            update_option('ss360_is_indexed', true);
        }

    }	

    $ss360_total = 0;
    $ss360_done = 0;
    foreach($ss360_post_types as $ss360_post_type){
        $ss360_to_db_key = 'ss360_'.$ss360_post_type.'_to_index';
        $ss360_offset_db_key = 'ss360_'.$ss360_post_type.'_index_offset';
        $ss360_to_index = get_option($ss360_to_db_key);
        if($ss360_to_index != NULL){
            $ss360_total += $ss360_to_index;
        } else {
			$ss360_total += $ss360_plugin->getPostCount($ss360_post_type);
		}
		$ss360_page_limit = get_option('ss360_page_limit');
		if ($ss360_page_limit != NULL) {
			$ss360_total = min($ss360_page_limit, $ss360_total);
		}
        $ss360_offset = get_option($ss360_offset_db_key);
        if($ss360_offset != NULL){
            $ss360_done += $ss360_offset;
        } 
    }

 
    $ss360_percent_done = 0;
    $ss360_remaining_time_mins = 1;

	if($ss360_total != 0){
    	$ss360_percent_done = round(($ss360_done/$ss360_total) * 100);
    	$ss360_remaining_time_mins = max(1, round(($ss360_total-$ss360_done)/600));// assume 1 second per 10 posts and don't show 0 mins
	}

    $ss360_is_indexed = get_option("ss360_is_indexed");
    if($ss360_indexing_mode == null){
        $ss360_indexing_mode = 'db';
    }
    $ss360_uses_acf = $ss360_plugin->usesACFs();
?>


<div id="indexControl" class="wrapper <?php echo $ss360_is_configured==null ? 'wrapper--fancy' : 'wrapper--narrow'; ?>">
    <div class="block block--first">
        <section class="flex flex--column <?php $ss360_is_configured==null ? 'flex--center' : ''; ?>">
            <?php if($ss360_is_indexed==null || $ss360_reindex_started) { ?>
                <h2><?php if($ss360_is_configured==null){ esc_html_e('One moment please! We are preparing your search.', 'site-search-360');} else{esc_html_e('Index Control','site-search-360');} ?></h2>
                <?php if($ss360_is_indexed == null){?>
                <div class="progress flex flex--column flex--center">
                    <span class="progress__percent"><?php echo $ss360_percent_done; ?>%</span>
                    <div role="progressbar" aria-valuemin="0" aria-valuemax="100" adia-valuenow="<?php echo $ss360_percent_done; ?>" class="progress__bar" style="width:<?php echo $ss360_percent_done;?>%;"></div>
                </div>
                <span class="c-r m-t-1 close-warn" style="text-align:center;"><?php esc_html_e('Please don\'t close this page until the indexing is finished.', 'site-search-360') ?></span>
                <span class="m-t-1 remaining-time" style="text-align:center;"><strong><?php esc_html_e('Remaining time', 'site-search-360') ?>:</strong>&nbsp;<span class="remaining-time-nr"><?php echo $ss360_remaining_time_mins; ?></span>&nbsp;<span class="remaining-time-unit"><?php esc_html_e('minute(s)','site-search-360')?></span></span>
                <?php if($ss360_is_configured != null){ ?>
                <form class="flex flex--center m-t-2" id="stop--reindexing" method="post" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>">
                        <?php wp_nonce_field() ?>
                        <input type="hidden" name="action" value="stop">
                        <button id="stop-reindex-button" class="button button--padded button--negative" type="submit"><?php esc_html_e('Stop Re-Indexing', 'site-search-360') ?></button>
                </form>
                <div class="flex flex--center m-t-1" id="complete-message" style="display:none;" ><strong class="c-b"><?php esc_html_e('Re-Index Complete', 'site-search-360');?></strong></div>
                <?php } ?>
               <?php } else { ?>
                    <span style="text-align:center">
                        <?php esc_html_e('The re-index has been started. You can check the progress in the ', 'site-search-360')?>
                        <a class="cp-link" href="<?php echo $ss360_jwt; ?>&next=indexControl" target="_blank"><?php esc_html_e('control panel.','site-search-360')?></a>
                    </span>
                <?php } ?>
                <?php if($ss360_is_configured==null){ ?>
                    <form class="flex flex--center m-t-2" style="display: none;" id="finish--f" method="get" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>">
                        <button class="button button--padded button--highlight" type="submit"><?php esc_html_e('Continue', 'site-search-360') ?></button>
                    </form>
                <?php } ?>
            <?php } else { ?>
                <h2 class="m-b-0"><?php esc_html_e('Index Control', 'site-search-360') ?></h2>
                <form class="flex flex--center flex--column" method="POST" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>#indexControl">
                    <?php wp_nonce_field() ?>
                    <input type="hidden" name="action" value="reindex">
                    <p><?php esc_html_e('To display search results, we need to index your content first. When you update, add, or delete pages or posts, Site Search 360 takes care of instantly reflecting the changes in your search results.', 'site-search-360'); ?></p>
                    <?php if($ss360_uses_acf && $ss360_indexing_mode == 'db') { ?>
                        <strong class="w-100 m-t-0 m-b-1"><?php echo sprintf(wp_kses(__('You can set up Advanced Custom Fields to be indexed with Database indexing on the <a href="%s">Indexing</a> page.', 'site-search-360'), array('a'=>array('href'=>array()))), menu_page_url('sitesearch360-indexing', false)); ?></strong>
                    <?php } ?>
                    <span class="m-b-1" style="width: 100%;"><?php esc_html_e('Choose how the search engine should get your data:', 'site-search-360')?></span>
                    <div class="integration flex flex--center m-b-1 column--sm" style="justify-content: space-evenly;margin-bottom:2em;">
                        <div class="integration_option" style="flex-basis: calc(39% - 1px);max-width: calc(39% - 1px);">
                            <input id="indexing-db" class="radio fake-hide" type="radio" value="db" name="ss360_indexing_mode" <?php echo $ss360_indexing_mode == 'db' ? 'checked':''?>>
                            <div class="integration_option_inner">
                                <div class="integration_option_top flex flex--center flex--column" style="justify-content:flex-start">
                                    <label for="indexing-db" class="integration_option_title m-b-0"><?php esc_html_e('Database indexing', 'site-search-360'); ?></label>
                                    <p class="integration_option_description">
                                        <?php esc_html_e('Let WordPress define what content is relevant for your search results. Only data included in your WordPress database will be indexed.', 'site-search-360'); ?>
                                    </p>
                                    <span style="width: 100%;"><strong><?php esc_html_e('Use this mode:', 'site-search-360');?></strong></span>
                                    <ul style="list-style: disc;padding-left: 2em;">
                                        <li><?php esc_html_e('To automatically filter out duplicate URLs', 'site-search-360') ?></li>
                                        <li><?php esc_html_e('To only use content that you enter in the WordPress editor', 'site-search-360') ?></li>
                                        <li><?php esc_html_e('If your site is not live yet (e.g. localhost builds)', 'site-search-360') ?></li>
                                    </ul>
                                </div>
                                <div class="integration_option_bottom flex flex--center">
                                    <ul class="integration_option_metaphor" style="list-style: disc;padding-left: 2em;">
                                        <li><?php esc_html_e('Requires no additional configuration', 'site-search-360') ?></li>
                                        <li><?php esc_html_e('Offers limited customizability', 'site-search-360') ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="integration_option" style="flex-basis: calc(39% - 1px);max-width: calc(39% - 1px);">
                            <input id="indexing-crawler" class="radio fake-hide" type="radio" value="crawler" name="ss360_indexing_mode" <?php echo $ss360_indexing_mode == 'crawler' ? 'checked':''?>>
                            <div class="integration_option_inner">
                                <div class="integration_option_top flex flex--center flex--column" style="justify-content:flex-start">
                                    <label for="indexing-crawler" class="integration_option_title m-b-0"><?php esc_html_e('Crawler indexing', 'site-search-360'); ?></label>
                                    <p class="integration_option_description">
                                        <?php esc_html_e('Let the powerful Site Search 360 crawler retrieve content from your live site. The data will be taken directly from the HTML code.', 'site-search-360'); ?>
                                    </p>
                                    <span style="width: 100%;"><strong><?php esc_html_e('Use this mode:', 'site-search-360');?></strong></span>
                                    <ul style="list-style: disc;padding-left: 2em;">
                                        <li><?php esc_html_e('To index multiple sites or include custom plugin content', 'site-search-360') ?></li>
                                        <li><?php esc_html_e('To exclude any unwanted pages or content blocks from your search', 'site-search-360') ?></li>
                                        <li><?php esc_html_e('To organize results in custom content groups or fine-tune your search snippets (if you use Custom mode for Integration)', 'site-search-360') ?></li>
                                    </ul>
                                </div>
                                <div class="integration_option_bottom flex flex--center">
                                <ul class="integration_option_metaphor" style="list-style: disc;padding-left: 2em;">
                                        <li><?php esc_html_e('Gives you full control over your search content', 'site-search-360') ?></li>
                                        <li><?php esc_html_e('Might require a bit extra configuration time', 'site-search-360') ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a class="crawler-customization m-b-1" target="_blank" href="<?php echo $ss360_jwt;?>&next=crawlerSettings" <?php echo $ss360_indexing_mode == 'db' ? 'style="display:none;"' :  '' ?>><?php esc_html_e('Configure the crawler in the control panel.', 'site-search-360') ?></a>
                    <label class="checkbox" id="empty-index-toggle" style="margin-bottom:1em;display:none;">
                        <?php esc_html_e('Empty entire index before re-indexing', 'site-search-360') ?>
                        <input class="fake-hide" type="checkbox" name="emptyIndex"checked/>
                        <span class="checkbox_checkmark"></span>
                    </label>
                    <button id="reindex-button" class="button button--padded button--highlight" type="submit"><?php esc_html_e('Re-Index All Content', 'site-search-360') ?></button>
                </form>
            <?php } ?>
        </section>
    </div>
</div>

<?php if($ss360_is_indexed==null){ ?>
    <script type="text/javascript">
    (function(){
            var currentProgress = <?php echo $ss360_percent_done ;?>;
            var percentage = jQuery(".progress__percent");
            var progressBar = jQuery(".progress__bar");
            var updateProgress = function(percentDone){
                percentage.text(percentDone + '%');
                progressBar.css("width", percentDone + "%");
                progressBar.attr("aria-valuenow", percentDone);
            };
            var intervalId = -1;
            var isIndexing = true;
            var isIndexBeingStopped = false;
            jQuery("#stop-reindex-button").on("click", function(){
                isIndexBeingStopped = true;
            })
            window.onbeforeunload = function(){
                if(isIndexing && !isIndexBeingStopped){
                    return "Closing this page will stop the indexing of your content. Do you want to close?";
                }
            }
            var indexDurations = [];
            var indexChunk = undefined;
			<?php if($ss360_page_limit != NULL && $ss360_page_limit < 200) { ?>
			indexChunk = 50;
			<?php } ?>
            var indexContent = function(){
                var startTimestamp = new Date().getTime();
                var postData =  {action: 'ss360_index'};
                if(indexChunk !== undefined){
                    postData['chunkSize'] = indexChunk;
                }
                jQuery.post(ajaxurl, postData).done(function(data){
                    var indexed = parseInt(data.data.indexed);
                    var total = parseInt(data.data.total);
                    clearInterval(intervalId);
                    updateProgress(currentProgress);
                    if(indexed >= total){
                        isIndexing = false;
                        updateProgress(100);
                        jQuery("#finish--f").show();
                        jQuery("#stop--reindexing").hide();
                        jQuery("#complete-message").show();
                        jQuery(".close-warn, .remaining-time").hide();
                        jQuery("#finish--f").find("button").on("click", function(e){
                            e.preventDefault();
                            e.stopPropagation();
                            window.location.reload();
                        });
                    } else {
                        var progressCache = currentProgress;
                        currentProgress = Math.round(indexed/total * 100);
                        var diff = currentProgress - progressCache;
                        var timeDiff = new Date().getTime() - startTimestamp;
                        indexDurations.push({
                            percentDone: diff,
                            duration: timeDiff
                        });
                        var percentSum = 0;
                        var durationSum = 0;
                        indexDurations.forEach(function(duration){
                            percentSum += duration.percentDone;
                            durationSum += duration.duration;
                        });
                        var remainingDuration = (100 - currentProgress) * (durationSum/percentSum);
                        if(remainingDuration > 60 * 1000){
                            jQuery(".remaining-time-nr").text(Math.round(remainingDuration/ (60 * 1000)));
                        }else {
                            jQuery(".remaining-time-unit").text("<?php esc_html_e("seconds", 'site-search-360'); ?>");
                            jQuery(".remaining-time-nr").text(Math.round(remainingDuration/1000));
                        }
                        intervalId = setInterval(function(){
                            progressCache++;
                            updateProgress(progressCache);
                            if(progressCache===currentProgress){
                                clearInterval(intervalId);
                            }
                        }, 300);
                        indexContent();
                    }
                }).fail(function(){
                    if(indexChunk === undefined){
                        indexChunk = 50;
                    }else { //probably too many posts being processed at once - try to decrease the chunk size
                        indexChunk = Math.round(indexChunk * 0.6);
                        indexChunk = Math.max(indexChunk, 1);
                    }
                    setTimeout(indexContent, indexChunk > 1 ? 1 : 500); // try again o.O
                });      
            }
            indexContent();
    }());
    </script>
<?php } ?>

<?php if($ss360_is_indexed != null){ ?>
    <script type="text/javascript">
        var alignHeight = function(selector){
            var parts = jQuery(selector);
            var maxHeight = parts.get().reduce(function(acc, el){return Math.max(acc, jQuery(el).outerHeight())}, -1);
            parts.css("height", maxHeight);
        }
        alignHeight("#indexControl .integration_option_top");
        alignHeight("#indexControl .integration_option_bottom");
        var initialValue = '<?php echo $ss360_indexing_mode ?>';
        var onValueChanged = function(value){
            var btn = jQuery("#indexControl #reindex-button");
            if(value === initialValue){
                btn.text('<?php esc_html_e('Re-Index All Content', 'site-search-360') ?>');
                btn.addClass("button--highlight");
                jQuery("#empty-index-toggle").hide();
            } else {
                btn.text('<?php esc_html_e('Save and Re-Index All Content', 'site-search-360') ?>');
                btn.removeClass("button--highlight");
                jQuery("#empty-index-toggle").show();
            }
            if(value === 'db'){
                jQuery(".crawler-customization").hide();
            } else {
                jQuery(".crawler-customization").show();
            }
        }
        jQuery("#indexControl .integration_option").on("click", function(e){
            var group = jQuery(e.target).hasClass("integration_option") ? jQuery(e.target) : jQuery(e.target).parents(".integration_option");
            var radio = group.find("input[type='radio']");
            radio.prop("checked", true);
            onValueChanged(radio.val());            
        });
        jQuery("input[name='ss360_indexing_mode']").on("change", function(e){
            onValueChanged(e.target.value);
        })
    </script>
<?php } ?>

<style>
#ss360 #indexControl .integration_option input:checked + .integration_option_inner,
#ss360 #indexControl .integration_option input:checked + .integration_option_inner .integration_option_bottom {
    border-color: #3D8FFF;
    border-width: 2px;
}
</style>