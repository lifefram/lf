<?php
    $ss360_plugin = new SiteSearch360Plugin();
    $ss360_siteId = urlencode($ss360_plugin->getSiteId());
?>

<section id="ss360" class="wrap wrap--blocky flex flex--column flex--center">
    <section class="wrapper bg-db flex column--sm">
        <div class="flex flex--column flex--2">
            <h2 class="c-w m-b-0"><strong><?php esc_html_e('Configure.','site-search-360') ?><br/><?php esc_html_e('Preview.','site-search-360') ?>&nbsp;<?php esc_html_e('Deploy.','site-search-360') ?></strong></h2>
            <h3 class="c-b"><strong><?php esc_html_e('Perfect site search solution in three simple steps.', 'site-search-360'); ?></strong></h3>
            <p style="color:rgba(255,255,255,0.775);font-size:1.2em;line-height:normal;" class="m-t-0">
                <?php esc_html_e('Customize every single visual element of your interface and create the perfect custom search engine for your website using our search designer.', 'site-search-360') ?>
            </p>
        </div>
        <div class="flex flex--column flex--1" style="justify-content: flex-end;padding-left:2em;">
            <img src="<?php echo plugins_url('images/search-designer.svg', __FILE__)?>" style="max-height:250px;" class="m-b-1">
            <button id="redirecter" class="button"><?php echo esc_html_e('Let\'s get creative', 'site-search-360'); ?></button>    
        </div>   
    </section>

    
    <?php 
        if(!empty($_POST) && isset($_POST['_wpnonce']) && $_POST['action']=='ss360_import_designer'){
            $ss360_client = new SiteSearch360Client();
            $ss360_config = $ss360_client->getGlobalSS360Config();
            if($ss360_config['ss360Config']==null){
                update_option('ss360_config_modifications', ((int) get_option('ss360_config_modifications')) + 1);
                ?>
                <section class="wrapper wrapper--narrow bg-r message">
                    <div class="block block--first flex">
                        <span class="c-r"><?php esc_html_e('Sorry, but we couldn`t find any Search Designer configuration. Make sure to design your search before importing the settings.', 'site-search-360'); ?></span>
                        <button class="button button--close message__close" aria-label="<?php esc_html_e('Close', 'site-search-360'); ?>">&times;</button>
                    </div>
                </section>
           <?php } else {
                $ss360_plugin->saveConfig(json_decode($ss360_config['ss360Config']));
            ?>
               <section class="wrapper wrapper--narrow bg-g message">
                   <div class="block block--first flex">
                       <span><?php esc_html_e('The Search Designer styles have been imported, enjoy your brand new look!', 'site-search-360'); ?></span>
                       <button class="button button--close message__close" aria-label="<?php esc_html_e('Close', 'site-search-360'); ?>">&times;</button>
                   </div>
               </section>
            <?php }
        }
    ?>


    <section class="wrapper wrapper--narrow">
        <div class="block block--first flex flex--column flex--center">
            <h2><?php esc_html_e('Import styles from Search Designer', 'site-search-360') ?></h2>
            <div class="flex w-100 column--sm">
                <form id="import-form" class="flex--1 flex flex--center p-r-2" style="justify-content:flex-end;" method="post" name="ss360_import_designer" action="<?php esc_url($_SERVER['REQUEST_URI'])?>">
                    <input type="hidden" name="action" value="ss360_import_designer">
                    <?php wp_nonce_field(); ?>
                    <button type="submit" class="button button--padded"><?php echo esc_html_e('Import Settings', 'site-search-360') ?></button>
                </form>
                <ol class="flex--1 flex--1 flex flex--column flex--center p-l-2" style="align-items:flex-start;">
                    <li><a href="https://sitesearch360.com/search-designer/?siteId=<?php echo $ss360_siteId ?>&source=wordpress" target="_blank"><?php esc_html_e('Go to the Search Designer.', 'site-search-360'); ?></a></li>
                    <li><?php esc_html_e('Customize your search interface.','site-search-360'); ?></li>
                    <li><a href="#" id="deployer"><?php esc_html_e('Deploy.','site-search-360'); ?></a></li>
                </ol>
            </div>
        </div>
    </section>
</section>

<script>
(function(){
    jQuery("#deployer").on("click", function(e){
        jQuery("#import-form").submit();
    });
    jQuery("#redirecter").on("click", function(e){
        window.location.href = "https://sitesearch360.com/search-designer/?siteId=<?php echo $ss360_siteId ?>&source=wordpress";
    });

    jQuery(".message__close").on("click", function(e){
        jQuery(e.target).parents(".message").fadeOut();
    })
}());
</script>
<script src="<?php echo plugins_url('assets/sitesearch360_admin_scripts.js',  __FILE__)  ?>" async></script>