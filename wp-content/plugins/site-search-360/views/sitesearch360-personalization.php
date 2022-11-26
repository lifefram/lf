<?php
if(!isset($requestUri)) {
    $requestUri = esc_url($_SERVER['REQUEST_URI']);
}
?>
<div>
    <form id="personalization" name="ss360_personalization" method="post" action="<?php echo $requestUri; ?>">
        <input type="hidden" name="action" value="ss360_personalize">
        <input type="hidden" name="page" value="4">
        <?php wp_nonce_field(); ?>
        <h3><?php esc_html_e('Let\'s customize your search interface!', 'site-search-360') ?></h3>
         <section class="flex a-c column--sm">
            <input id="hidden-color--i" class="fake-hide" type="color" value="#4A4F62" name="themeColor">
            <label class="label m-h-1 flex--1 m-b-05--sm" for="color--i"><?php esc_html_e('Theme color', 'site-search-s360') ?></label>
            <div class="flex flex--3">
                <div id="color--p" class="input input--inline" style="width:37px;height:37px;background:#4A4F62;cursor:pointer;"></div>
                <input id="color--i" class="input input--inline" type="text" value="#4A4F62" placeholder="#4A4F62" pattern="(^#[0-9A-Fa-f]{6}$)|(^#[0-9A-Fa-f]{3}$)">
            </div>
         </section>
         <section class="flex a-c column--sm">
            <input id="hidden-color--ia" class="fake-hide" type="color" value="#3D8FFF" name="accentColor">
            <label class="label m-h-1 flex--1 m-b-05--sm" for="color--ia"><?php esc_html_e('Accent color', 'site-search-s360') ?></label>
            <div class="flex flex--3">
                <div id="color--pa" class="input input--inline" style="width:37px;height:37px;background:#3D8FFF;cursor:pointer;"></div>
                <input id="color--ia" class="input input--inline" type="text" value="#3D8FFF" placeholder="#3D8FFF" pattern="(^#[0-9A-Fa-f]{6}$)|(^#[0-9A-Fa-f]{3}$)">
            </div>
         </section>
         <?php if($ss360_integration_type=='full'){ ?>
            <section class="flex flex--column">
                <div class="flex a-c m-t-1 column--sm">
                    <span class="label m-h-1 flex--1 m-b-05--sm"><?php esc_html_e('Search result layout', 'site-search-360') ?></span>
                    <div class="flex flex--3">
                        <label class="radio">
                            <?php esc_html_e('List', 'site-search-360') ?>
                            <input id="layout-list--i" type="radio" name="layout" checked value="list" class="fake-hide">
                            <span class="radio_checkmark"></span>
                        </label>
                        <label class="radio m-l-1">
                            <?php esc_html_e('Grid', 'site-search-360') ?>
                            <input id="layout-grid--i" type="radio" class="fake-hide" name="layout" value="grid">
                            <span class="radio_checkmark"></span>
                        </label>
                    </div>
                </div>
                <div class="flex a-c m-t-1 column--sm">
                    <span class="label m-h-1 flex--1 m-b-05--sm"><?php esc_html_e('Loading animation', 'site-search-360') ?></span>
                    <div class="flex flex--3">
                        <label class="radio m-r-1" aria-label="<?php esc_html_e('Pulsating Circle', 'site-search-360') ?>">
                            Progressive loader (skeleton)
                            <input id="loading-circle--i" type="radio" name="loader" checked value="skeleton" class="fake-hide">
                            <span class="radio_checkmark"></span>
                        </label>
                        <label class="radio" aria-label="<?php esc_html_e('Pulsating Circle', 'site-search-360') ?>">
                            <div class="ss360-spinner-circle ss360-loader" style="width:25px; height:25px;">
                                <div class="ss360-double-bounce1 colorize" style="background:#4A4F62"></div>
                                <div class="ss360-double-bounce2 colorize" style="background:#4A4F62"></div>
                            </div>
                            <input id="loading-circle--i" type="radio" name="loader" value="circle" class="fake-hide">
                            <span class="radio_checkmark"></span>
                        </label>
                        <label class="radio m-l-1" aria-label="<?php esc_html_e('Flipping Square', 'site-search-360') ?>">
                            <div class="ss360-loader ss360-spinner-square colorize" style="width:25px; height:25px;background:#4A4F62"></div>
                            <input id="loading-square--i" type="radio" class="fake-hide" name="loader" value="square">
                            <span class="radio_checkmark"></span>
                        </label>
                    </div>
                </div>
                <div class="flex a-c m-t-1 column--sm">
                    <span class="label m-h-1 flex--1 m-b-05--sm"><?php esc_html_e('Voice search','site-search-360'); ?></span>
                     <div class="flex flex--3">
                        <label class="checkbox"><?php esc_html_e('Enabled', 'site-search-360') ?><input class="fake-hide" type="checkbox" name="voiceSearch" checked/><span class="checkbox_checkmark"></span></label>
                    </div>
                </div>

            </section>
            <?php } ?>
            <div class="flex flex--center w-100 m-v-1">
                <button class="button button--padded button--highlight" type="submit"><?php esc_html_e('Finish','site-search-360') ?></button>
            </div>
    </form>
</div>

<script type="text/javascript">
(function(){
    var hiddenColor = jQuery("#hidden-color--i");
    var color = jQuery("#color--i");
    var preview = jQuery("#color--p");
    var colorize = jQuery(".colorize");
    preview.on("click", function(){
        hiddenColor.click();
    })
    hiddenColor.on("change", function(e){
        color.val(e.target.value);
        preview.css("background", e.target.value);
        colorize.css("background", e.target.value);    
    });
    color.on("keyup", function(e){
        var color = e.target.value;
        if(color.indexOf("#")!==0){
            color = "#" + color;
        }
        var isValidInput  = /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(color);
        if(isValidInput){
            if(color.length===4){
                color = "#" + color[1] + color[1] + color[2] + color[2] + color[3] + color[3];
            }
            hiddenColor.val(color);
            preview.css("background", color);
            colorize.css("background", color);
        }
    });
    var hiddenColorAccent = jQuery('#hidden-color--ia');
    var accentColor = jQuery('#color--ia');
    var accentColorPreview = jQuery('#color--pa');
    accentColorPreview.on("click", function(){
        hiddenColorAccent.click();
    })
    hiddenColorAccent.on("change", function(e){
        accentColor.val(e.target.value);
        accentColorPreview.css("background", e.target.value);
    });
    accentColor.on("keyup", function(e){
        var color = e.target.value;
        if(color.indexOf("#")!==0){
            color = "#" + color;
        }
        var isValidInput  = /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(color);
        if(isValidInput){
            if(color.length===4){
                color = "#" + color[1] + color[1] + color[2] + color[2] + color[3] + color[3];
            }
            hiddenColorAccent.val(color);
            accentColorPreview.css("background", color);
        }
    });
}());
</script>