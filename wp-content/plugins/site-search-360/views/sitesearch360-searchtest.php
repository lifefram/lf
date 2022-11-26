<?php 
    $ss360_client = new SiteSearch360Client();
    $ss360_plugin = new SiteSearch360Plugin();
    $ss360_jwt = $ss360_client->presign();
    if(!isset($requestUri)) {
		$requestUri = esc_url($_SERVER['REQUEST_URI']);
	}
?>

<?php 
    if(!empty($_POST) && isset($_POST['_wpnonce']) && $_POST['action'] == 'ss360_updateInputs'){ ?>
        <span class="m-t-1 c-g"><?php esc_html_e('The configuration has been updated, let\'s try again.', 'site-search-360'); ?></span>
<?php    } 
?>

<div>
    <form id="query-test" name="ss360_confirm_search" method="post" action="<?php echo $requestUri; ?>">
        <input type="hidden" name="page" value="3">
        <input type="hidden" name="action" value="ss360_page">
        <?php wp_nonce_field(); ?>
        <section class="flex flex--column flex--center m-t-1 query-test-content" id="waiting">
            <h3><?php esc_html_e('Let\'s make sure your search is working as expected!', 'site-search-360') ?></h3>
            <a target="_blank" href="//<?php echo $_SERVER['SERVER_NAME'];?>" class="hint hint--large" style="height:auto;"><?php esc_html_e('Please go to your website and test the search.','site-search-s360') ?></a>
            <p><?php esc_html_e('Waiting for a query...', 'site-search-360'); ?></p>
            <div role="presentation" class="loader loader--negative m-b-1"></div>
            <button class="button button--negative troubleshooting-toggle hidden--sm"><?php esc_html_e('I\'ve tested the search and it didn\'t work.','site-search-360') ?></button>
            <button class="button button--negative troubleshooting-toggle hidden--lg"><?php esc_html_e('The search didn\'t work.','site-search-360') ?></button>
            <a href="#" class="hint hint--negative submitter"><?php esc_html_e('Skip for now.','site-search-360') ?></a>
        </section>
        <section class="flex flex--column flex--center m-t-1" style="display: none;" id="done">
            <h3><?php esc_html_e('Alright, everything seems to be working as expected!', 'site-search-360') ?></h3>
            <span class="hint hint--large" style="height:auto;"><?php esc_html_e('We\'ve registered a query from your page. Great job!','site-search-s360') ?></span>
            <div role="presentation" class="m-v-1"><i><img src="<?php echo plugins_url('images/icons/check_circle.svg',  dirname(__FILE__))  ?>" height="64" width="64"></i></div>
        
            <button type="submit" class="button button--padded<?php echo ($ss360_integration_type=='filter'?' button--highlight':'') ?>"><?php echo ($ss360_integration_type=='filter'?esc_html__('Finish', 'site-search-360'):esc_html__('Next', 'site-search-360')) ?></button>
            <a href="#" class="hint hint--negative troubleshooting-toggle hidden--sm"><?php esc_html_e('No, the search didn\'t work.','site-search-360') ?></a>
            <a href="#" class="hint hint--negative troubleshooting-toggle hidden--lg"><?php esc_html_e('The search didn\'t work.','site-search-360') ?></a>
        </section>
    </form>
    <section class="flex flex--column m-t-1 b-t-3 troubleshooting" style="display:none;">
        <h3><?php esc_html_e('Troubleshooting', 'site-search-360'); ?></h3>
        <ul class="m-t-0">
            <?php if($ss360_integration_type!='filter'){ ?>
                <li class="m-b-1">
                    <strong><?php esc_html_e('Did we detect the correct search box and search button?', 'site-search-360') ?></strong>
                    <br/>
                    <span class="l--high"><?php echo sprintf(wp_kses(__(
                        'Please check that the following <a href="https://www.w3schools.com/cssref/css_selectors.asp" target="_blank">CSS selectors</a> are matching your search box and search button. Don`t have a search button? No worries, just keep the input blank.', 'site-search-360'
                    ), array('a'=>array('href'=>array(), 'class'=>array()))));
                    ?></span>
                    <br/>
                    <form id="selector-form" class="flex flex--column" name="ss360_updateInputs" method="post" action="<?php echo $requestUri ?>">
                        <input type="hidden" name="action" value="ss360_updateInputs">
                        <input type="hidden" name="page" value="2">
                        <?php wp_nonce_field(); ?>
                        <div class="flex">
                            <div class="flex flex--column flex--1">
                                <div class="flex a-c column--sm" style="justify-content: space-between;">
                                    <label class="label label--inline" for="sbs--i"><?php esc_html_e('Search Box Selector', 'site-search-360') ?></label>
                                    <input id="sbs--i" class="input input--inline" type="text" name="searchBoxSelector" value="<?php echo htmlspecialchars($ss360_plugin->getConfig()['searchBox']['selector']) ?>" placeholder="#searchBox">
                                </div>
                                <?php if($ss360_integration_type=='full'){ ?>
                                    <div class="flex a-c column--sm" style="justify-content: space-between;">
                                        <label class="label label--inline" for="sbt--i"><?php esc_html_e('Search Button Selector', 'site-search-360') ?></label>
                                        <input id="sbt--i" type="text" class="input input--inline" name="searchButtonSelector" value="<?php echo htmlspecialchars($ss360_plugin->getConfig()['searchBox']['searchButton']) ?>" placeholder="#searchButton">
                                    </div>  
                                <?php } ?>
                                <button type="submit" class="button button--padded"><?php esc_html_e('Update', 'site-search-360') ?></button>
                            </div>
                            <div class="flex flex--column flex--1 flex--center">
                            </div>
                        </div>
                    </form>
                </li>
                <li class="m-v-1">
                    <strong><?php esc_html_e('Does your template have a search input and a search result page?', 'site-search-360') ?></strong>
                    <br/>
                    <span class="l--high"><?php echo sprintf(wp_kses(__(
                        'If not, please add a search box to your WordPress template and change the <a class="current-url" href="">Integration</a> to <i>Custom Mode</i>. You can use the following HTML template:', 'site-search-360'
                    ), array('a'=>array('href'=>array(), 'class'=>array()), 'i'=>array())))
                    ?></span>
                    <br/>
                    <pre><code class="language-html">&lt;input type="search" id="searchBox"&gt;</code></pre>
                </li>
            <?php } ?>
            <li class="if-index-empty <?php $ss360_integration_type=='filter' ? 'm-b-1' : 'm-v-1' ?>">
                <strong><?php esc_html_e('Still haven\'t found a solution?', 'site-search-360')?></strong>
                <br/>
                <span class="l--high"><?php 
                     echo sprintf(wp_kses(__(
                    '<a href="https://gitter.im/site-search-360/Lobby" target="_blank">Chat with us</a> or <a class="troubleshooting-mail" href="mailto:mail@sitesearch360.com?subject=Wordpress Plugin Configuration" title="mail@sitesearch360.com">write us an email.</a> We\'ll be happy to help.', 'site-search-360'
                    ), array('a'=>array('href'=>array(), 'target'=>array(), 'title'=>array(), 'class'=>array()))))?>
                </span>
            </li>
        </ul>
    </section>
</div>

<script>
    (function(){
        jQuery(".troubleshooting-toggle").on("click", function(e){
            e.preventDefault();
            e.stopPropagation();
            jQuery(".troubleshooting").show();
            jQuery([document.documentElement, document.body]).animate({
                scrollTop: jQuery(".troubleshooting").offset().top
            }, 300);
        });

        var checkStartDate = Math.round(new Date().getTime()/1000);
        var checkQueries = function(){
            jQuery.get("<?php echo $ss360_client->getBaseUrl() ?>sites/queries/live?afterTimestamp="+checkStartDate + "&token=<?php echo urlencode(get_option( 'ss360_api_token' )) ?>").success(function(data){
                if(data.lastQueries.length > 0){
                    clearInterval(intervalId);
                    jQuery("#waiting").hide();
                    jQuery("#done").show();
                }
            }).fail(console.error);
        }
        // check twice every three seconds
        var intervalId = setInterval(checkQueries, 1500);
        jQuery(".current-url").attr("href", window.location.href);
        jQuery(".submitter").on("click", function(e){
            jQuery(e.target).parents("form").submit();
        });

        jQuery(".index-control-link").attr("href", "<?php echo $ss360_jwt; ?>&next=indexControl");

        var trMail = jQuery(".troubleshooting-mail");
        trMail.attr("href", trMail.attr("href") + " (siteId: <?php echo get_option('ss360_siteId')?>)");
    }());
</script>

<link href="<?php echo plugins_url('assets/prism.css',  dirname(__FILE__))  ?>" rel="stylesheet">
<script src="<?php echo plugins_url('assets/prism.js',  dirname(__FILE__))  ?>"></script>