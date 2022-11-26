<div class="wrapper wrapper--narrow">
    <div class="block block--first flex">
        <div role="presentation" class="flex flex--column flex--center flex--1 hidden--sm">
            <img src="<?php echo plugins_url('images/icons/control_panel.svg',  dirname(__FILE__)); ?>" height="375">
        </div>
        <section class="flex flex--column flex--1 cp-features">
            <h2><?php esc_html_e('Perfect your search engine at our', 'site-search-360')?>&nbsp;<a class="cp-link" href="<?php echo $ss360_jwt; ?>" target="_blank"><?php esc_html_e('control panel.','site-search-360')?></a></h2>
            <ul class="features" style="padding-left: 3em">
              <li class="feature"><?php esc_html_e('Fine-tune your search', 'site-search-360') ?></li>
              <li class="feature"><?php esc_html_e('Take full control over your index', 'site-search-360') ?></li>
              <li class="feature"><?php esc_html_e('Adjust your dictionary and improve important queries', 'site-search-360') ?></li>
              <li class="feature"><?php esc_html_e('Tune-up your filters', 'site-search-360') ?></li>
              <li class="feature"><?php esc_html_e('See more detailed statistics', 'site-search-360') ?></li>
            </ul>
        </section>
    </div>
</div>

<style>
.cp-features {
    padding: 1em 0 0 3em;
}

@media(max-width: 767px){
    .cp-features {
        padding: 0;
    }
}
</style>