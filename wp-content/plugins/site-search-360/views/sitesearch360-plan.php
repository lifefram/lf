<?php
    $ss360_client = new SiteSearch360Client();
    $ss360_plan_info = $ss360_client->planInfo();
    $ss360_tier = $ss360_plan_info['plan']['tier'];
    $ss360_imageName = 'free';
    $ss360_indexing_type = get_option("ss360_indexing_mode");
    if($ss360_indexing_type == NULL) {
        $ss360_indexing_type = 'db';
    }
    if($ss360_tier=='TRIAL' || $ss360_tier=='BATMAN' || $ss360_tier=='CUSTOM'){
        $ss360_imageName = 'batman';
    }else if($ss360_tier=='COLUMBO'){
        $ss360_imageName = 'columbo';
    }else if($ss360_tier=='HOLMES'){
        $ss360_imageName = 'holmes';
    }
    update_option('ss360_active_plan', $ss360_tier);
    $ss360_plugin = new SiteSearch360Plugin();
    $ss360_number_of_posts = $ss360_indexing_type == 'db' ? $ss360_plugin->getAllPostCount() : $ss360_plan_info['stats']['indexed'];

    $ss360_is_trial = $ss360_tier=='TRIAL';
    $ss360_query_overhead = $ss360_plan_info['stats']['queries'] > $ss360_plan_info['plan']['queryLimit'];
    $ss360_prognosis_overhead = $ss360_plan_info['stats']['queryPrognosis'] > $ss360_plan_info['plan']['queryLimit'];
    $ss360_page_overhead = $ss360_number_of_posts > $ss360_plan_info['plan']['pageLimit'];
  
    
    $ss360_warning = null;
    $ss360_note = null;
    $ss360_cta = null;

    $ss360_upgrade_plan = isset($ss360_plan_info['upgrade']) ? $ss360_plan_info['upgrade']['tier'] : 'CUSTOM';
    $ss360_upgrade_price = isset($ss360_plan_info['upgrade']) && $ss360_plan_info['upgrade']['pricePerMonth']!=0 ? $ss360_plan_info['upgrade']['pricePerMonth'] : null;
    if($ss360_upgrade_price!=null){
        $ss360_cta = '';
        $ss360_cta = $ss360_cta . esc_html(sprintf(__('($%d per month) ','site-search-360'), $ss360_upgrade_price));
    }     
    // <span class="carousel_feature"><?php echo sprintf(wp_kses(__('…more <span class="carousel_highlight">precise search results</span>,', 'site-search-360'), array('span'=>array('class'=>array()))));</span>

    $ss360_upgrade_cta = esc_html(sprintf(__('Upgrade to the %s plan', 'site-search-360'), ucfirst(strtolower($ss360_upgrade_plan))));  
    if($ss360_is_trial){
        $ss360_note = esc_html(sprintf(__('Your trial will expire in %d days. Once your trial expires your account will be downgraded to the free plan with a limit of 150 indexed pages and 1000 monthly queries. But no worries, all your adjustments will be saved.','site-search-360'), $ss360_plan_info['plan']['trialDaysLeft']));
        $ss360_upgrade_cta = esc_html__('Upgrade now', 'site-search-360');
        $ss360_cta = esc_html__('to keep your branding-free search and improved user experience.', 'site-search-360');
    } else if($ss360_query_overhead && $ss360_page_overhead){
        $ss360_warning = esc_html(sprintf(__('You have reached the query and page limit (%d pages are not searchable right now).','site-search-360'), $ss360_number_of_posts-$ss360_plan_info['plan']['pageLimit']));  
        $ss360_cta = $ss360_cta . esc_html__('to maintain superior user experience with search funcionality that covers all your content.', 'site-search-360');
    } else if($ss360_prognosis_overhead && $ss360_page_overhead){
        $ss360_note = esc_html(sprintf(__('You are approaching your query limit (our prognosis: %d queries this month).', 'site-search-360'), $ss360_plan_info['stats']['queryPrognosis']));
        $ss360_warning = esc_html(sprintf(__('You have reached the page limit (%d pages are not searchable right now).','site-search-360'), $ss360_number_of_posts-$ss360_plan_info['plan']['pageLimit']));
        $ss360_cta = $ss360_cta . esc_html__('to ensure all your pages are indexed and not to exceed your query limit.', 'site-search-360');
    } else if($ss360_page_overhead){
        $ss360_warning = esc_html(sprintf(__('You have reached the page limit (%d pages are not searchable right now).','site-search-360'), $ss360_number_of_posts-$ss360_plan_info['plan']['pageLimit']));
        $ss360_cta = $ss360_cta . esc_html__('to be able to index all your pages.', 'site-search-360');
    } else if($ss360_query_overhead){
        $ss360_warning = esc_html__('You have reached your query limit.', 'site-search-360');
        $ss360_cta = $ss360_cta . esc_html__('to put your awesome search back to work.', 'site-search-360');
    } else if($ss360_prognosis_overhead){
        $ss360_note = esc_html(sprintf(__('You are approaching your query limit (our prognosis: %d queries this month).', 'site-search-360'), $ss360_plan_info['stats']['queryPrognosis']));
        $ss360_cta = $ss360_cta . esc_html__('to make sure to fit into your query limit.', 'site-search-360');
    } else if($ss360_tier=='FREE' || $ss360_tier=='COLUMBO'){
        $ss360_upgrade_cta = esc_html__('Upgrade to the Holmes plan', 'site-search-360');
        $ss360_cta = esc_html__('($39 per month) to remove the Site Search 360 branding and to add filters to your search.');
    }
    
?>
<div class="wrapper wrapper--narrow">
    <div class="block block--first flex">
        <section class="flex flex--3 flex--column">
            <h2 style="margin-bottom:24px;"><?php esc_html_e('Plan', 'site-search-360') ?></h2>
            <ul class="flex--3">
                <li class="bullet"><strong><?php esc_html_e('Indexed pages', 'site-search-360') ?>:&nbsp;</strong><?php echo $ss360_plan_info['stats']['indexed'].'/'.$ss360_plan_info['plan']['pageLimit']; ?></li>
                <li class="bullet"><strong><?php esc_html_e('Queries', 'site-search-360') ?>:&nbsp;</strong><?php echo $ss360_plan_info['stats']['queries'].'/'.$ss360_plan_info['plan']['queryLimit']; ?></li>
            </ul>
            <?php if($ss360_note!=null){?>
                <span <?php echo $ss360_warning!=null ? 'class="m-b-1"' : '' ?>><?php echo $ss360_note; ?></span>
            <?php } ?>
            <?php if($ss360_warning!=null){?>
                <span class="c-r"><?php echo $ss360_warning; ?></span>
            <?php } ?>
            <?php if($ss360_cta!=null && $ss360_upgrade_cta!=null){?>
                <p class="m-v-1 plan-cta"><a href="<?php echo $ss360_jwt;?>&next=plan" target="_blank" class="c-g"><strong><?php echo $ss360_upgrade_cta;?></strong></a>&nbsp;<?php echo $ss360_cta; ?></p>
            <?php } ?>
        </section>
        <div class="flex flex--column flex--center flex--1 hidden--sm" role="presentation">
            <strong class="m-b-1 c-b"><?php echo $ss360_tier; ?></strong>
            <img src="<?php echo plugins_url('images/icons/plan_'.$ss360_imageName.'.svg',  dirname(__FILE__)); ?>" width="150" height="150" role="presentation">
        </div>           
    </div>
</div>


