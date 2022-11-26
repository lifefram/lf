<?php 

$ss360_has_been_interacted = get_option('ss360_review_interaction')!=null;
$ss360_modification_count = (int) get_option('ss360_config_modifications');


if(!$ss360_has_been_interacted && $ss360_modification_count > 4){
?>
<div id="cta-rating" class="wrapper wrapper--narrow">
    <div class="block block--first flex flex--center flex--column block--reverse" style="position: relative;">
        <h2 class="flex flex--center" style="font-size:20px;margin:0;flex-wrap:wrap;">
            <?php esc_html_e('Love this plugin?','site-search-360') ?>&nbsp;<a class="review-cta" href="https://wordpress.org/plugins/site-search-360/#reviews" target="_blank"><?php esc_html_e('Rate us!', 'site-search-360') ?></a>
            <img src="<?php echo plugins_url('images/icons/heart.svg',  dirname(__FILE__)); ?>" width="20" height="20" style="margin:0 0.75em;">
            <?php esc_html_e('Not enjoying the plugin?','site-search-360') ?>&nbsp;<a class="review-cta" href="mailto:mail@sitesearch360.com?subject=Wordpress Plugin Feedback (siteId: <?php echo get_option('ss360_siteId'); ?>)" title="mail@sitesearch360.com"><?php esc_html_e('Tell us why.', 'site-search-360') ?></a>
        </h2>
        <button id="cta-rating--close" class="button button--close" aria-label="<?php esc_html_e('Close', 'site-search-360'); ?>" style="font-size: 1.5em; margin-top:0; right:15px; position: absolute">&times;</button>
    </div>
</div>

<script type="text/javascript">
(function(){
    var updateOption = function(){
        jQuery.post(ajaxurl, {action: 'ss360_review'})
    }
    jQuery("#cta-rating--close").on("click", function(e){
        updateOption();
        jQuery("#cta-rating").fadeOut(400, function(e){
            jQuery("#cta-rating").remove();
        });
    });

    jQuery("a.review-cta").on("click", updateOption);
  
}());  
</script>

<?php } ?>