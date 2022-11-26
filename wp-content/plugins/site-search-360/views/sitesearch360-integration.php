<?php 
if(!isset($requestUri)) {
    $requestUri = esc_url($_SERVER['REQUEST_URI']);
}
?>
<form id="sr-type" name="ss360_setType" method="post" action="<?php echo $requestUri; ?>" >
    <?php wp_nonce_field(); ?>
    <input type="hidden" name="action" value="ss360_setType">
    <input type="hidden" name="page" value="2">
    <?php if($ss360_is_configured==null){ ?>
        <h3><?php esc_html_e('Integration','site-search-360') ?></h3>
    <?php } else { ?>
        <h2><?php esc_html_e('Integration','site-search-360') ?></h2>
    <?php } ?>
    <div class="integration flex m-b-1 column--sm">
        <div class="integration_option">
            <input id="sr-filter" class="radio fake-hide" type="radio" value="filter" name="ss360_sr_type" <?php echo $ss360_integration_type=='filter'?'checked':''?>>
            <div class="integration_option_inner">
                <div class="integration_option_top flex flex--center flex--column" style="justify-content: flex-start;">
                    <label for="sr-filter" class="integration_option_title"><?php esc_html_e('Built-in mode','site-search-360') ?></label>
                    <img class="integration_option_image" src="<?php echo plugins_url('images/icons/scooter.svg',  dirname(__FILE__))?>" width="175" height="175">
                    <img class="integration_option_image integration_option_image--active" src="<?php echo plugins_url('images/icons/scooter_blue.svg',  dirname(__FILE__))?>" width="175" height="175">
                    <p class="integration_option_description">
                        <?php esc_html_e('Keep the car — replace the engine.', 'site-search-s360') ?>
                    </p>
                </div>
                <div class="integration_option_bottom flex flex--center">
                    <p class="integration_option_metaphor"><?php esc_html_e('Default search widget enhanced by Site Search 360 — put a faster, smarter search bicycle behind your search bar. Keep your WordPress theme`s search result layout.','site-search-360') ?></p>
                </div>
            </div>
        </div>
        <div class="integration_option">
            <input id="sr-suggest" class="radio fake-hide" type="radio" value="suggestions" name="ss360_sr_type" <?php echo $ss360_integration_type=='suggestions'?'checked':''?>>
            <div class="integration_option_inner">
                <div class="integration_option_top flex flex--center flex--column" style="justify-content: flex-start;">
                    <label for="sr-suggest" class="integration_option_title"><?php esc_html_e('Built-in mode + live search','site-search-360') ?></label>
                    <img class="integration_option_image" src="<?php echo plugins_url('images/icons/ugly_car.svg',  dirname(__FILE__))?>" width="175" height="175">
                    <img class="integration_option_image integration_option_image--active" src="<?php echo plugins_url('images/icons/ugly_car_blue.svg',  dirname(__FILE__))?>" width="175" height="175">
                    <p class="integration_option_description">
                        <?php esc_html_e('Keep the car — replace the engine and tune it up with live search suggestions.', 'site-search-s360') ?>
                    </p>
                </div>
                <div class="integration_option_bottom flex flex--center">
                    <p class="integration_option_metaphor"><?php esc_html_e('Display instant search results as your visitor types in a search query.','site-search-360') ?></p>
                </div>
            </div>
        </div>
        <div class="integration_option">
            <input id="sr-full" class="radio fake-hide" type="radio" value="full" name="ss360_sr_type" <?php echo $ss360_integration_type=='full'?'checked':''?>>
            <div class="integration_option_inner">
                <div class="integration_option_top flex flex--center flex--column" style="justify-content: flex-start;">
                    <label class="integration_option_title" for="sr-full"><?php esc_html_e('Custom mode','site-search-360') ?></label>
                    <img class="integration_option_image" src="<?php echo plugins_url('images/icons/race_car.svg',  dirname(__FILE__))?>" width="175" height="175">
                    <img class="integration_option_image integration_option_image--active" src="<?php echo plugins_url('images/icons/race_car_blue.svg',  dirname(__FILE__))?>" width="175" height="175">
                    <p class="integration_option_description">
                        <?php esc_html_e('Replace the car and get access to a whole new set of search features and settings.', 'site-search-s360') ?>
                    </p>
                </div>
                <div class="integration_option_bottom flex flex--center">
                    <p class="integration_option_metaphor"><?php esc_html_e('Go for the full Site Search 360 experience: customize your search result navigation and layout, styles, and texts. Enjoy your new powerful accessibility-conscious and mobile optimized search.','site-search-360') ?></p>
                </div>
            </div>
        </div>                   
    </div>
    <div class="flex flex--center w-100">
        <button class="button button--padded" type="submit"><?php $ss360_is_configured==null ? esc_html_e('Continue', 'site-search-360') : esc_html_e('Save', 'site-search-360'); ?></button>
    </div>
</form>
<script type="text/javascript">
    (function(){
        // preload images
        jQuery("#sr-type .integration_option_image:not(.integration_option_image--active)").each(function(){
            var self = jQuery(this);
            var img = new Image();
            img.src = self.attr("src");
        })


        var alignHeight = function(selector, firstTime){
            var parts = jQuery(selector);
            var maxHeight = parts.get().reduce(function(acc, el){return Math.max(acc, jQuery(el).outerHeight())}, -1);
            if(firstTime){
                parts.find("img").each(function(){
                    var src = jQuery(this).attr("src");
                    var img = new Image();
                    img.src = src;
                    img.onload = function(){
                        parts.css("height", "");
                        alignHeight(selector, false);
                    }
                });
            }
            parts.css("height", maxHeight);
        }
        alignHeight("#sr-type .integration_option_top", true);
        alignHeight("#sr-type .integration_option_bottom", true);
        var onValueChanged = function(value){
            if(value==="filter"){
                jQuery("#start-nav-3").hide();
                jQuery(".page-info").text("<?php echo sprintf(esc_html__('Step %d of %d','site-search-360'), 1, 2) ?>");
            }else {
                jQuery(".page-info").text("<?php echo sprintf(esc_html__('Step %d of %d','site-search-360'), 1, 3) ?>");
                jQuery("#start-nav-3").show();
            }
        }
        jQuery("#sr-type .integration_option").on("click", function(e){
            var group = jQuery(e.target).hasClass("integration_option") ? jQuery(e.target) : jQuery(e.target).parents(".integration_option");
            var radio = group.find("input[type='radio']");
            radio.prop("checked", true);
            onValueChanged(radio.val());
        });
        var activeValue = jQuery("input[name='ss360_sr_type']:checked").val();
      
        jQuery("input[name='ss360_sr_type']").on("change", function(e){
            onValueChanged(e.target.value);
        });

        jQuery("#start-nav-2").click(function(e){
            jQuery("form[name='ss360_setType']").submit();
        });
    }());
</script>  