<div class="wrapper wrapper--narrow">
    <div class="block block--first flex flex--center flex--column">
        <h2><?php esc_html_e('Any questions, ideas, feedback?', 'site-search-360') ?></h2>
        <div class="flex flex--center">
            <a class="flex flex--column flex--center m-1" href="mailto:mail@sitesearch360.com?subject=Wordpress Plugin Feedback (siteId: <?php echo get_option('ss360_siteId'); ?>)" title="mail@sitesearch360.com">
                <img width="83" class="m-b-1" role="presentation" src="<?php echo plugins_url('images/icons/email.svg', dirname(__FILE__)) ?>">
                <span><?php esc_html_e('write us an email', 'site-search-360') ?></span>
            </a>
            <a class="flex flex--column flex--center m-1" href="https://gitter.im/site-search-360/Lobby" target="_blank">
                <img width="66" class="m-b-1" role="presentation" src="<?php echo plugins_url('images/icons/gitter.svg', dirname(__FILE__)) ?>">
                <span><?php esc_html_e('chat with us', 'site-search-360') ?></span>
            </a>                      
        </div>
    </div>
</div>