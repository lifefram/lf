<?php 
    $ss360_plugin = new SiteSearch360Plugin();
    $ss360_siteId = $ss360_plugin->getSiteId();
    $ss360_updated_flag = false;
    if (!empty($_POST) && isset($_POST['_wpnonce'])) {
        if(!isset($_POST['ss360Config']) || $_POST['ss360Config']==''){

        }else{
            $ss360_configuration = json_decode(stripslashes($_POST['ss360Config']), true);
            // ensure siteId is set
            if(!isset($ss360_configuration['siteId'])){
                $ss360_configuration['siteId'] = $ss360_siteId;
            }
            // clean up
            if(isset($ss360_configuration['suggestions'])){
                $ss360_suggest_c = $ss360_configuration['suggestions'];
                if(isset($ss360_suggest_c['dataPoints']) && empty($ss360_suggest_c['dataPoints'])){
                    unset($ss360_suggest_c['dataPoints']);
                }
                if(isset($ss360_suggest_c['emptyQuerySuggestions']) && empty($ss360_suggest_c['emptyQuerySuggestions'])){
                    unset($ss360_suggest_c['emptyQuerySuggestions']);
                }
                if(isset($ss360_suggest_c['querySuggestionHeadline']) && empty($ss360_suggest_c['querySuggestionHeadline'])){
                    unset($ss360_suggest_c['querySuggestionHeadline']);
                }
                if(isset($ss360_suggest_c['extraHtml']) && empty($ss360_suggest_c['extraHtml'])){
                    unset($ss360_suggest_c['extraHtml']);
                }
                if(isset($ss360_suggest_c['viewAllLabel']) && empty($ss360_suggest_c['viewAllLabel'])){
                    unset($ss360_suggest_c['viewAllLabel']);
                }
                if(empty($ss360_suggest_c)){
                    unset($ss360_configuration['suggestions']);
                }else {
                    $ss360_configuration['suggestions'] = $ss360_suggest_c;
                }
            }
            if(isset($ss360_configuration['style'])){
                $ss360_style_c = $ss360_configuration['style'];
                if(isset($ss360_style_c['suggestions']) && empty($ss360_style_c['suggestions'])){
                    unset($ss360_style_c['suggestions']);
                }
                if(isset($ss360_style_c['searchBox']) && empty($ss360_style_c['searchBox'])){
                    unset($ss360_style_c['searchBox']);
                }
                if(empty($ss360_style_c)){
                    unset($ss360_configuration['style']);
                }else {
                    $ss360_configuration['style'] = $ss360_style_c;
                }
            }
            if(isset($ss360_configuration['searchBox'])){
                $ss360_sb_c = $ss360_configuration['searchBox'];
                if(isset($ss360_sb_c['placeholder']) && empty($ss360_sb_c['placeholder'])){
                    unset($ss360_sb_c['placeholder']);
                }
                if(isset($ss360_sb_c['searchButton']) && empty($ss360_sb_c['searchButton'])){
                    unset($ss360_sb_c['searchButton']);
                }
                if(empty($ss360_sb_c)){
                    unset($ss360_configuration['searchBox']);
                }else {
                    $ss360_configuration['searchBox'] = $ss360_sb_c;
                }
            }
            if(isset($ss360_configuration['results'])){
                $ss360_res_c = $ss360_configuration['results'];
                if(isset($ss360_res_c['embedConfig']) && (!isset($ss360_res_c['embedConfig']['contentBlock']) || empty($ss360_res_c['embedConfig']['contentBlock']))){
                    unset($ss360_res_c['embedConfig']);
                }
                if(isset($ss360_res_c['fullScreenConfig']) && (!isset($ss360_res_c['fullScreenConfig']['trigger']) || empty($ss360_res_c['fullScreenConfig']['trigger']))){
                    unset($ss360_res_c['fullScreenConfig']);
                }
                if(empty($ss360_res_c)){
                    unset($ss360_configuration['results']);
                }else {
                    $ss360_configuration['results'] = $ss360_res_c;
                }
            }
            if(isset($ss360_configuration['contentGroups'])){
                $ss360_cg_c = $ss360_configuration['contentGroups'];
                if(isset($ss360_cg_c['include']) && empty($ss360_cg_c['include'])){
                    unset($ss360_cg_c['include']);
                }
                if(isset($ss360_cg_c['exclude']) && empty($ss360_cg_c['exclude'])){
                    unset($ss360_cg_c['exclude']);
                }
                if(empty($ss360_cg_c)){
                    unset($ss360_configuration['contentGroups']);
                }else {
                    $ss360_configuration['contentGroups'] = $ss360_cg_c;
                }
            }

            // save
            $ss360_plugin->saveConfig($ss360_configuration);
            $ss360_updated_flag = true;
            update_option('ss360_config_modifications', ((int) get_option('ss360_config_modifications')) + 1);
        }
    }
    $ss360_config = $ss360_plugin->getConfig();
?>
<section id="ss360" class="wrap wrap--blocky flex flex--column flex--center">
    <?php 
        if($ss360_updated_flag){ ?>
            <section class="wrapper wrapper--narrow bg-g message">
                <div class="block block--first flex">
                    <span><?php esc_html_e('The configuration has been saved.', 'site-search-360'); ?></span>
                    <button class="button button--close message__close" aria-label="<?php esc_html_e('Close', 'site-search-360'); ?>">&times;</button>
                </div>
            </section>
       <?php }
    ?>
    <section class="wrapper wrapper--narrow">
        <form class="block block--first"  method="post" name="ss360_edit_config" action="<?php esc_url($_SERVER['REQUEST_URI'])?>">
            <?php wp_nonce_field(); ?>
            <input id="value-field" type="hidden" value="<?php echo htmlspecialchars(json_encode($ss360_config)); ?>" name="ss360Config">
            <h2><?php esc_html_e('Advanced Configuration', 'site-search-360') ?></h2>
            <p class="m-v-1"><?php esc_html_e('Click on a parameter in the Documentation section to add it to the Editor. Parameters already present in the Editor will not be added, e.g. if you accidentally click on something twice.', 'site-search-360'); ?></p>
            <p class="m-v-1"><?php esc_html_e('All parameter values defined in the Documentation are defaults and will be applied automatically. So you only need to copy the settings that you wish to override, e.g. to hide error messages, set showErrors to false.', 'site-search-360'); ?></p>
            <p class="m-v-1"><?php echo sprintf(wp_kses(__('See our <a target="_blank" href="https://docs.sitesearch360.com/installation">Installation Docs</a> to learn more about configuring your search interface.', 'site-search-360'), array('a'=>array('href'=>array(),'target'=>array())))); ?></p>
            <div class="flex column--sm">
                <section class="flex flex--column flex--center flex--1" style="justify-content: flex-start;">
                    <h3><?php esc_html_e('Editor', 'site-search-360'); ?></h3>
                    <div id="jsoneditor" style="width: 100%; height: 600px;"></div>
                </section>
                <section class="flex flex--column flex--center flex--1" style="justify-content: flex-start;">
                    <h3><?php esc_html_e('Documentation', 'site-search-360'); ?></h3>
                    <div id="documentation" class="flex flex--column" style="width: 100%; height: 600px"></div>
                </section>
            </div>
            <div class="flex flex--center w-100 m-t-1">
                <button id="submit-btn" class="button button--padded" type="submit"><?php esc_html_e('Save', 'site-search-360'); ?></button>
            </div>
        </form>    
    </section>
</section>

<script type="text/javascript">
(function(){
    var submitBtn = jQuery("#submit-btn");
    var valueField = jQuery("#value-field");
    window.populateEditor = function(){
        var json = <?php echo json_encode($ss360_config); ?>;
        var container = document.getElementById("jsoneditor");
        var options = {
			mode: 'code',
			ace: ace,
            onChangeText: function(jsonString){
                try{
                    json = JSON.parse(jsonString);
                    valueField.val(JSON.stringify(json));
                    submitBtn.attr("disabled", null);
                }catch(ex){
                    submitBtn.attr("disabled", "disabled");
                }
            },
            mainMenuBar: false
		};

        var documentation = {
            properties: [
                {
                    key: 'siteId',
                    default: '<?php echo $ss360_siteId; ?>',
                    type: String,
                    comment: "your site id"
                },
                {
                    key: 'showErrors',
                    default: true,
                    type: Boolean,
                    comment: "whether to show implementation errors, set to false for production"
                },
                {
                    key: 'allowCookies',
                    default: true,
                    type: Boolean,
                    comment: "whether to allow the javascript to set cookies"
                },
                {
                    key: 'suggestions',
                    type: Object,
                    properties: [
                        {
                            key: 'show',
                            type: Boolean,
                            default: true,
                            comment: 'whether to show search suggestions'                            
                        },
                        {
                            key: 'showOnMobile',
                            type: Boolean,
                            default: true,
                            comment: 'whether to show search suggestions on mobile devices (less than 768px), disables specialMobileSuggestions if false'
                        },
                        {
                            key: 'maxQuerySuggestions',
                            type: Number,
                            default: 3,
                            comment: 'the maximum number of query suggestions'
                        },
                        {
                            key: 'querySuggestionHeadline',
                            type: String,
                            default: '',
                            comment: 'the headline of the query suggestions, leave blank if no headline should be shown'
                        },
                        {
                            key: 'emptyQuerySuggestions',
                            type: Object,
                            properties: [],
                            comment: '// suggestions to be shown if the search box is empty, a JSON object mapping content group names to array of suggestions, e.g. {"suggests":{_:[{name:"The Title",link:"https://mylink.de/",image:"https://placekitten.com/150/150"}]}}'
                        },
                        {
                            key: 'showImages',
                            type: Boolean,
                            default: true,
                            comment: 'show images in search suggestions'
                        },
                        {
                            key: 'num',
                            type: Number,
                            default: 6,
                            comment: 'the maximum number of search suggestions to be shown'
                        },
                        {
                            key: 'minChars',
                            type: Number,
                            default: 3,
                            comment: 'minimum number of characters before the suggestions shows'
                        },
                        {
                            key: 'maxWidth',
                            type: String,
                            default: 'auto',
                            comment: 'the maximum width of the suggest box, default: as wide as the input box, at least 275px'
                        },
                        {
                            key: 'throttleTime',
                            type: Number,
                            default: 300,
                            comment: 'the number of milliseconds before the suggest is triggered after finished input (in ms)'
                        },
                        {
                            key: 'extraHtml',
                            type: String,
                            default: '',
                            comment: 'extra HTML code that is shown in each search suggest, you can even show values of datapoints here'
                        },
                        {
                            key: 'highlight',
                            type: Boolean,
                            default: true,
                            comment: 'whether matched words should be highlighted'
                        },
                        {
                            key: 'dataPoints',
                            type: Object,
                            comment: '// mapping of data point names to extraHtml, overrides extraHtml setting, e.g. {"price": {"html": "<span>#price# $</span>", "position": 1}, "category": {"html": "<b>#category#</b>", "position": 2}}',
                            properties: []
                        },
                        {
                            key: 'viewAllLabel',
                            type: String,
                            default: '',
                            comment: "the label of a 'View All' button shown at the end of the suggestion list (leave blank if you don't want to show the search button)"
                        },
                        {
                            key: 'forceBelow',
                            type: Boolean,
                            default: false,
                            comment: 'whether to force the suggestions to be shown below the search box'
                        },
                        {
                            key: 'mobileScrollOnFocus',
                            type: Boolean,
                            default: true,
                            comment: 'whether to scroll the page in order for the search box to be to the top of the window (on screens below 768 px)'
                        },
                        {
                            key: 'infiniteScroll',
                            type: Boolean,
                            default: false,
                            comment: 'whether to show more search results once the user scrolls to the end of the result block (will only work if the navigation type is \'tabs\' or only one content group has been retrieved)'
                        },
                        {
                            key: 'hideResultsWithoutImage',
                            type: Boolean,
                            default: false,
                            comment: 'whether to hide all results that don\'t have any image or have a broken image'
                        }
                    ]
                },
                {
                    key: 'style',
                    type: Object,
                    properties: [
                        {
                            key: 'themeColor',
                            type: String,
                            default: '#4A4F62',
                            comment: 'the theme color affecting headlines, buttons, and links'
                        }, 
                        {
                            key: 'accentColor',
                            type: String,
                            default: '#3D8FFF',
                            comment: 'accent color'
                        }, 
                        {
                            key: 'suggestions',
                            type: Object,
                            properties: []
                        },
                        {
                            key: 'defaultCss',
                            type: Boolean,
                            default: true,
                            comment: 'whether to include the default CSS'
                        },
                        {
                            key: 'searchBox',
                            type: Object,
                            properties: []
                        },
                        {
                            key: 'loaderType',
                            type: String,
                            default: 'skeleton',
                            comment: 'can be "skeleton", "circle" or "square"'
                        },
                        {
                            key: 'animationSpeed',
                            type: Number,
                            default: 250,
                            comment: 'speed of the animations in milliseconds'
                        },
                        {
                            key: 'additionalCss',
                            type: String,
                            default: '',
                            comment: "additional CSS to add to the plugin's stylesheets, e.g. '#ss360-layer{background:red}'"
                        }
                    ]
                },
                {
                    key: 'searchBox',
                    type: Object,
                    properties: [
                        {
                            key: 'placeholder',
                            type: String,
                            default: '',
                            comment: 'the placeholder to show in the search box, will only be used if no placeholder is specified on the input HTML tag'
                        },
                        {
                            key: 'autofocus',
                            type: Boolean,
                            default: false,
                            comment: 'if true, the search box will get focus after initialization'
                        },
                        {
                            key: 'selector',
                            type: String,
                            default: '#searchBox',
                            comment: 'the CSS selector to the search box'
                        },
                        {
                            key: 'searchButton',
                            type: String,
                            default: '',
                            comment: 'the CSS selector to the search buttons'
                        },
                        {
                            key: 'focusLayer',
                            type: Boolean,
                            default: false,
                            comment: 'if true, a layer will be shown when the user focuses on the search input'
                        },
                        {
                            key: 'searchButtonLabel',
                            type: String,
                            default: '',
                            comment: 'the label of the search button in Site Search 360 custom search inputs, if not set, a magnifying glass icon will be rendered'
                        }
                    ]
                },
                {
                    key: 'results',
                    type: Object,
                    properties: [
                        {
                            key: 'embedConfig',
                            type: Object,
                            comment: '{"url":undefined,"contentBlock":".page-content-body"} - if url is given the page will change to that URL and look for the content block there to insert the results',
                            properties: [
                                {
                                    key: 'url',
                                    type: String,
                                    comment: 'the url of the search result page',
                                    default: ''
                                },
                                {
                                    key: 'contentBlock',
                                    type: String,
                                    comment: 'the CSS selector of the block where search results should be rendered (required for the embedConfig setting to take effect)',
                                    default: ''
                                }
                            ]
                        },
                        {
                            key: 'fullScreenConfig',
                            type: Object,
                            comment: '{"trigger": "#ss360-search-trigger", "caption": "Search this site"}, trigger is the CSS selector to the element that starts the search full screen overlay and searchCaption the caption on the full screen search page',
                            properties: [
                                {
                                    key: 'trigger',
                                    type: String,
                                    comment: 'the selector of the element that triggers the fullscreen layer  (required for the fullScreenConfig setting to take effect)',
                                    default: ''
                                },
                                {
                                    key: 'caption',
                                    type: String,
                                    comment: 'the caption of the fullscreen layer',
                                    default: 'Search this site'
                                }
                            ]
                        },
                        {
                            key: 'caption',
                            type: String,
                            comment: 'the caption of the search results',
                            default: 'Found #COUNT# search results for "#QUERY#"'
                        },
                        {
                            key: 'group',
                            type: Boolean,
                            comment: 'whether results should be grouped if content groups are available',
                            default: true
                        },
                        {
                            key: 'num',
                            type: Number,
                            comment: 'the maximum number of search results to be shown',
                            default: 96
                        },
                        {
                            key: 'highlightQueryTerms',
                            type: Boolean,
                            comment: 'whether to highlight the query terms in search results',
                            default: true
                        },
                        {
                            key: 'moreResultsButton',
                            type: String,
                            comment: 'HTML for the more results button, all results will be shown if this is null',
                            default: 'See more'
                        },
                        {
                            key: 'noResultsText',
                            type: String,
                            comment: 'the text to show when there are no results',
                            default: 'Sorry, we have not found any matches for your query.'
                        },
                        {
                            key: 'queryCorrectionText',
                            type: String,
                            comment: ' #CORRECTION# will be replaced automatically by the corrected query',
                            default: 'Did you mean "#CORRECTION#"?'
                        },
                        {
                            key: 'searchQueryParamName',
                            type: String,
                            comment: 'the name of the search query parameter',
                            default: 'ss360Query'
                        },
                        {
                            key: 'linksOpenNewTab',
                            type: Boolean,
                            comment: 'should clicking on the result links open a new tab/window?',
                            default: false
                        },
                        {
                            key: 'showSearchBoxLayover',
                            type: Boolean,
                            comment: 'whether to show search box in the search result layover',
                            default: true
                        },
                        {
                            key: 'moreResultsPagingSize',
                            type: Number,
                            comment: 'the number of new results to show each time the more results button is pressed (max: 24)',
                            default: 12
                        },
                        {
                            key: 'orderByRelevanceText',
                            type: String,
                            comment: "the text to be shown in order select box to describe 'order by relevance' option",
                            default: 'Relevance'
                        },
                        {
                            key: 'redirectOnSingle',
                            type: Boolean,
                            comment: 'whether to redirect instead of showing a single search result',
                            default: false
                        },
                        {
                            key: 'limitPerGroup',
                            type: Boolean,
                            comment: 'if set to true, the maximum number of search results will be applied to every single content group, otherwise the limit will be spread across all groups, default: true',
                            default: true
                        },
                        {
                            key: 'stripHttp',
                            type: Boolean,
                            comment: 'if set to true the protocol part (http:// or https://) will be removed from the url in case this is being shown in the search result',
                            default: false
                        },
                        {
                            key: 'cta',
                            type: Object,
                            comment: 'the CTA button configuration',
                            properties: [
                                {
                                    key: 'text',
                                    type: String,
                                    comment: 'the text of the CTA button',
                                    default: ''
                                },
                                {
                                    key: 'link',
                                    type: String,
                                    comment: 'the link to redirect to after a CTA is clicked, use #RESULT_URL# to redirect to result page',
                                    default: ''
                                },
                                {
                                    key: 'icon',
                                    type: String,
                                    comment: 'the icon to show inside of the CTA button, should be a link to an image, default "ss360:arrow", use ss360:shopping-cart to render shopping cart icon',
                                    default: 'ss360:arrow'
                                },
                                {
                                    key: 'includeContentGroups',
                                    type: Array,
                                    comment: 'json array of content group names for which the CTA should be shown',
                                    default: []
                                },
                                {
                                    key: 'excludeContentGroups',
                                    type: Array,
                                    comment: 'json array of content group names for which the CTA should not be shown',
                                    default: []
                                }
                            ]
                        },
                        {
                            key: 'placeholderImage',
                            type: String,
                            comment: 'placeholder image:  by default a striped background will be instead of missing images, set to null to collapse missing images, or to a url to load a placeholder image',
                            default: ''
                        }
                    ]
                },
                {
                    key: 'queryTerm',
                    type: Object,
                    properties: [
                        {
                            key: 'scrollIntoViewBlock',
                            type: String,
                            comment: "how to scroll the text into view on redirect and a single query term match, one of 'start', 'center', 'end' or 'none' (don't scroll into view at all)",
                            default: 'start'
                        },
                        {
                            key: 'highlight',
                            type: Boolean,
                            comment: 'whether to highlight parts of the query after redirect to a specific search result',
                            default: true
                        },
                        {
                            key: 'highlightColor',
                            type: String,
                            comment: 'the background color of highlighted text',
                            default: '#b5f948'
                        }
                    ]
                },
                {
                    key: 'contentGroups',
                    type: Object,
                    properties: [
                        {
                            key: 'include',
                            type: Array,
                            comment: 'json array of content group names to be included in the search results, default: all content groups will be included',
                            default: []
                        },
                        {
                            key: 'exclude',
                            type: Array,
                            comment: 'json array of content group names to be excluded from the search results, default: no content groups will be excluded',
                            default: []
                        },
                        {
                            key: 'otherName',
                            type: String,
                            comment: 'the name of the results not in any other content group',
                            default: ''
                        },
                        {
                            key: 'ignoreOther',
                            type: Boolean,
                            comment: 'whether or not to ignore the "other" content group',
                            default: false
                        }
                    ]
                },
                {
                    key: 'tracking',
                    type: Object,
                    properties: [
                        {
                            key: 'providers',
                            type: Array,
                            comment: "how to track, supported values: 'GA' (Google Analytics), 'GTM' (Google Tag Manager)",
                            default: []
                        }                    
                    ]
                },
                {
                    key: 'accessibility',
                    type: Object,
                    properties: [
                        {
                            key: 'isMainContent',
                            type: Boolean,
                            comment: 'whether to mark ss360 layer as main content of the page (will be wrapped in &lt;main&gt; tag)',
                            default: false
                        }, 
                        {
                            key: 'resultTopHeadingLevel',
                            type: Number,
                            comment: 'heading level to start with in search result (default h2)',
                            default: 2    
                        },
                        {
                            key: 'suggestHeadingLevel',
                            type: Number,
                            comment: 'heading level to use in search suggestions, for content group heading',
                            default: 2    
                        },
                        {
                            key: 'searchFieldLabel',
                            type: String,
                            comment: 'invisible label to be used with screen readers when search box is focused, will only be used if value is not empty and there is no label element associated to the search box',
                            default: 'Search'
                        },
                        {
                            key: 'srSuggestionsHiddenText',
                            type: String,
                            comment: 'text to announce @screen reader after search suggestions have been hidden',
                            default: 'Search suggestions are hidden'
                        },
                        {
                            key: 'srNoSuggestionsText',
                            type: String,
                            comment: 'text to announce @screen reader if no suggestions are available',
                            default: 'No search suggestions'
                        },
                        {
                            key: 'srSuggestionsCountText',
                            type: String,
                            comment: 'text to announce @screen reader after search suggestions have been shown, #COUNT# will be replaced with the suggestion count',
                            default: '#COUNT# search suggestions shown'
                        },
                        {
                            key: 'srOneSuggestionText',
                            type: String,
                            comment: 'text to announce @screen reader after search suggestions have been shown',
                            default: 'One search suggestion shown'
                        },
                        {
                            key: 'srSuggestBoxControlDescription',
                            type: String,
                            comment: 'text to announce @screen reader after search input is focused - describes keyboard controls',
                            default: 'Use up and down arrows to select available result. Press enter to go to selected search result. Touch devices users can use touch and swipe gestures.'
                        },
                    ]
                },
                {
                    key: 'specialMobileSuggest',
                    type: Object,
                    properties: [
                        {
                            key: 'enabled',
                            type: Boolean,
                            comment: 'whether to show special mobile suggests (suggestion layover will slide-in after search box is focused on mobile devices)',
                            default: false
                        },
                        {
                            key: 'breakpoint',
                            type: Number,
                            comment: 'css breakpoint to show mobile suggests (max-width: breakpoint)',
                            default: 768
                        },
                        {
                            key: 'placeholder',
                            type: String,
                            comment: 'placeholder for empty suggestions',
                            default: ''
                        },
                        {
                            key: 'searchBoxPlaceholder',
                            type: String,
                            comment: 'the special search box placeholder',
                            default: ''
                        },
                        {
                            key: 'customTopHtml',
                            type: String,
                            comment: 'additional html/text content to be shown above special mobile search box',
                            default: ''
                        },
                        {
                            key: 'animateTransitions',
                            type: Boolean,
                            comment: 'whether to animate special mobile transitions',
                            default: true
                        },
                        {
                            key: 'resizeSearchBoxOnScroll',
                            type: Boolean,
                            comment: 'whether to resize search box when user scrolls special mobile suggests',
                            default: true
                        },
                        {
                            key: 'trigger',
                            type: String,
                            comment: 'the CSS selector of a special mobile suggestion trigger - when this is clicked, the layer will slide-in',
                            default: '.ss360-special-mobile-trigger'
                        }
                    ]
                },
                {
                    key: 'smart404',
                    type: Object,
                    comment: 'Smart 404 Page - this has to be explicitly set in the Editor for the changes to take effect',
                    properties: [
                        {
                            key: 'identifier',
                            type: String,
                            comment: 'the string in the title that identifies the page as a 404 page',
                            default: 'Page not found'
                        },
                        {
                            key: 'resultSelector',
                            type: String,
                            comment: 'a CSS selector that points to the area in which the alternative links should be shown',
                            default: '#ss360-404'
                        },
                        {
                            key: 'caption',
                            type: String,
                            comment: 'caption for 404 results',
                            default: 'Try going here instead:'
                        }
                    ]
                },
                {
                    key: 'layout',
                    type: Object,
                    properties: [
                        {
                            key: 'mobile',
                            type: Object,
                            comment: 'below 992px',
                            properties: [
                                {
                                    key: 'type',
                                    default: 'list',
                                    type: String,
                                    comment: 'can be either "grid", "masonry", or "list", default: "list"'                                    
                                },
                                {
                                    key: 'showImages',
                                    default: true,
                                    type: Boolean,
                                    comment: 'whether to show images in search result'
                                },
                                {
                                    key: 'showSnippet',
                                    default: true,
                                    type: Boolean,
                                    comment: 'whether to show thext snippet in search result'
                                },
                                {
                                    key: 'showTitle',
                                    default: true,
                                    type: Boolean,
                                    comment: 'whether to show title in search result'
                                },
                                {
                                    key: 'showDataPoints',
                                    default: true,
                                    type: Boolean,
                                    comment: 'whether to show data points in search result'
                                },
                                {
                                    key: 'showUrl',
                                    default: false,
                                    type: Boolean,
                                    comment: 'whether to show link in search result'
                                },
                                {
                                    key: 'gridColsMd',
                                    default: 2,
                                    type: Number,
                                    comment: 'grid layout column count for devices between 768px and 991px'
                                },
                                {
                                    key: 'gridColsSm',
                                    default: 1,
                                    type: Number,
                                    comment: 'grid layout column count for devices below 768px'
                                }
        
                            ]
                        },
                        {
                            key: 'desktop',
                            type: Object,
                            comment: '992px and larger',
                            properties: [
                                {
                                    key: 'type',
                                    default: 'list',
                                    type: String,
                                    comment: 'can be either "grid", "masonry", or "list", default: "list"'                                    
                                },
                                {
                                    key: 'showImages',
                                    default: true,
                                    type: Boolean,
                                    comment: 'whether to show images in search result'
                                },
                                {
                                    key: 'showSnippet',
                                    default: true,
                                    type: Boolean,
                                    comment: 'whether to show thext snippet in search result'
                                },
                                {
                                    key: 'showTitle',
                                    default: true,
                                    type: Boolean,
                                    comment: 'whether to show title in search result'
                                },
                                {
                                    key: 'showDataPoints',
                                    default: true,
                                    type: Boolean,
                                    comment: 'whether to show data points in search result'
                                },
                                {
                                    key: 'showUrl',
                                    default: false,
                                    type: Boolean,
                                    comment: 'whether to show link in search result'
                                },
                                {
                                    key: 'gridColsXl',
                                    default: 4,
                                    type: Number,
                                    comment: 'grid layout column count for devices larger than 1200px'
                                },
                                {
                                    key: 'gridColsLg',
                                    default: 3,
                                    type: Number,
                                    comment: 'grid layout column count for devices between 992px and 1199px'
                                }        
                            ]
                        },
                        {
                            key: 'masonryCols',
                            type: Object,
                            comment: 'how many masonry grid columns to show, minimum width to column count mapping (default: 2 columns below 768px, 3 columns between 768px and 991px, 5 columns between 992px and 1199px and 6 columns above 1200px)',
                            properties: [
                                {
                                    key: 0,
                                    default: 2,
                                    type: Number
                                },
                                {
                                    key: 768,
                                    default: 3,
                                    type: Number
                                },
                                {
                                    key: 992,
                                    default: 5,
                                    type: Number
                                },
                                {
                                    key: 1200,
                                    default: 6,
                                    type: Number
                                }
                            ]
                        },
                        {
                            key: 'navigation',
                            type: Object,
                            properties: [
                                {
                                    key: 'position',
                                    type: String,
                                    default: 'none',
                                    comment: 'navigation "top", "left", or "none"'
                                },
                                {
                                    key: 'type',
                                    type: String,
                                    default: 'tabs',
                                    comment: 'the navigation layout "scroll" or "tabs", in case of tabs and more then 6 (position: "top") or 10 (position: "left") content groups a dropdown will be shown'
                                },
                                {
                                    key: 'tabSpacingPx',
                                    type: Number,
                                    default: 5,
                                    comment: 'spacing between tabs'
                                },
                                {
                                    key: 'borderRadiusPx',
                                    type: Number,
                                    default: 3,
                                    comment: 'tab border radius'
                                },
                                {
                                    key: 'showAllResultsTab',
                                    type: Boolean,
                                    default: true,
                                    comment: 'whether to show an \'All Results\' tab'
                                },
                                {
                                    key: 'allResultsTabName',
                                    type: String,
                                    default: 'All Results',
                                    comment: 'the name of the \'All Results\' tab'
                                }
                            ]                                                        
                        }
                    ]
                },
                {
                    key: 'voiceSearch',
                    default: {enabled: false, lang: 'en-US'},
                    type: Object,
                    properties: [
                        {
                            key: 'enabled',
                            default: false,
                            type: Boolean,
                            comment: "whether to enable voice search for supported browsers (an microphone icon will be added to your search box if Speech Recognition API is supported)"
                        }, {
                            key: 'lang',
                            default: 'en-US',
                            type: String,
                            comment: "the input language (BCP 47 language tag)"
                        }
                    ]
                },
                {
                    key: 'filters',
                    type: Object,
                    properties: [
                        {
                            key: 'enabled',
                            type: Boolean,
                            comment: 'whether to generate and show filter options',
                            default: false                            
                        },
                        {
                            key: 'position',
                            type: String,
                            comment: 'where to place the filter view, on of the followin: "top", "left" (default "left" for embed and "top" for layovers); "top" - filters will be shown above the search results, "left" - filters will be shown to the left of search results + "show filter" button will be added for mobile devices;',
                            default: "left"
                        },
                        {
                            key: 'label',
                            type: String,
                            comment: 'the label of the filter column, will be also used as screen reader text',
                            default: 'Filter'
                        },
                        {
                            key: 'showCounts',
                            type: Boolean,
                            comment: 'whether to show result counts for multiple choice filters',
                            default: true
                        },
                        {
                            key: 'showQuickDelete',
                            type: Boolean,
                            comment: 'whether to show a "Quick Delete" bar summarizing active filter options and providing a "delete all" button',
                            default: true
                        },
                        {
                            key: 'deleteAllLabel',
                            type: String,
                            comment: 'the label of the "delete all" button',
                            default: "Clear All"
                        },
                        {
                            key: 'settings',
                            type: Object,
                            comment: "range filter settings, e.g. {Price: {unit: '$', step: 1, drawHistogram: false}}",
                            properties: []
                        }
                    ]
                },
                {
                    key: 'dataPoints',
                    type: Object,
                    properties: [
                        {
                            key: 'exclude',
                            type: Array,
                            default: [],
                            comment: 'data points that should not be shown in the UI, array of data point names'
                        },
                        {
                            key: 'single',
                            type: Array,
                            default: [],
                            comment: 'data points where only the first one should be shown (if multiple values are present), array of data point names'
                        },
                        {
                            key: 'direction',
                            type: String,
                            default: 'row',
                            comment: 'the direction of the data point key-value pairs - whether the data points should be shown as a row or as a column (table)'
                        },
                        {
                            key: 'showNames',
                            type: Boolean,
                            default: true,
                            comment: 'whether to show data point names'
                        },
                        {
                            key: 'collapseBy',
                            type: String,
                            default: ', ',
                            comment: 'the (html) string to be used when merging rows of the structured data table having the same key, default: ", ", e.g. "<br/>", set to null to show data points with the same key in multiple rows'
                        }                        
                    ]
                },
                {
                    key: 'language',
                    type: String,
                    default: 'en',
                    comment: 'the language of search interface, available options: "en", "de", "fr", "nl"'
                }
            ]
        };

        var docWrapper = jQuery("#documentation");
        var editor = new JSONEditor(container, options);

        var ensureParentExists = function(property){
            var parentPath = [];
            var curProp = property;
            while(curProp.parent!==undefined){
                parentPath.splice(0, 0, curProp.parent.key);
                curProp = curProp.parent;
            }
            if(parentPath.length > 0){
                var obj = json;
                parentPath.forEach(function(part){
                    if(obj[part]===undefined){
                        obj[part] = {};
                    }
                    obj = obj[part];
                });
                return obj;
            }
            return json;
        }

        var buildPropertyDefault = function(property){
            if(property.type !== Object){
                return property.default;
            }
            if(property.properties === undefined || property.properties.length === 0){
                return {};
            }
            return property.properties.reduce(function(acc, childProperty){
                if(childProperty.type === Object){
                    acc[childProperty.key] = buildPropertyDefault(childProperty);
                }else {
                    acc[childProperty.key] = childProperty.default;
                }
                return acc;
            }, {});
            
        }

        var processProperty = function(property, depth, isLast){
            depth = depth || 1;
            var padding = Array(depth*2 + 1).join("&nbsp;");
            var node = jQuery("<span class='token-key'>"+padding + property.key+"<span class='token-plain'>: </span></span>");
            var value = jQuery("<span></span>")
            if(property.type === Boolean || property.type === Number){
                value.addClass(property.type === Boolean ? "token-boolean" : "token-number");
                value.html(property.default + (isLast ? '' : '<span class="token-plain">,</span>'));
            }else if(property.type===String){
                value.addClass("token-string");
                value.html("'"+property.default+"'" + (isLast ? '' : '<span class="token-plain">,</span>'))
            }else if(property.type===Array) {
                value.addClass("token-array");
                value.html(JSON.stringify(property.default) + (isLast ? '' : '<span class="token-plain">,</span>'));
            }else if(property.type===Object){
                value.addClass("token-plain");
                value.text("{" + (!property.properties || property.properties.length===0? ('}' + (!isLast ? ',':'') ):''));
            }
            node.append(value);
            if(property.comment!==undefined){
                node.append('&nbsp;<span class="token-comment">//&nbsp;'+property.comment+'</span>')
            }
            docWrapper.append(node);
            if(property.properties && property.properties.length > 0){
                property.properties.forEach(function(childProperty, idx){
                    childProperty.parent = property;
                    processProperty(childProperty, depth+1, idx === property.properties.length-1);
                });
                docWrapper.append('<span class="token-plain">'+padding+'}' + (isLast ? '':',')+'</span>')
            }
            node.on("click", function(e){
                var toUpdate = ensureParentExists(property);
                if(toUpdate[property.key]!==undefined){ 
                }else { 
                    var val = property.type === Object ? buildPropertyDefault(property) : property.default;
                    toUpdate[property.key] = val;
                    editor.set(json);
                    valueField.val(JSON.stringify(json));
                }
            });
        }
        docWrapper.append('<span class="token-plain">{</span>')
        documentation.properties.forEach(function(property, idx){
            processProperty(property, 1, idx===documentation.properties.length-1);
        });
        docWrapper.append('<span class="token-plain">}</span>');

        // set json
        editor.set(json);
    }
    jQuery(".message__close").on("click", function(e){
        jQuery(e.target).parents(".message").fadeOut();
    });
}());
</script>

<link href="<?php echo plugins_url('assets/jsoneditor.min.css',  __FILE__)  ?>" rel="stylesheet">
<script src="<?php echo plugins_url('assets/jsoneditor.min.js',  __FILE__)  ?>" onload="populateEditor()"></script>
<script src="<?php echo plugins_url('assets/sitesearch360_admin_scripts.js',  __FILE__)  ?>" async></script>

<style>
#documentation {
    max-width: 550px;
    overflow: auto;
    background: #272822;
    font-family: Consolas,Monaco,'Andale Mono','Ubuntu Mono',monospace;
    text-shadow: 0 1px rgba(0,0,0,0.3);
    line-height: 1.5;
    font-size: 13px;
    padding: 0.5em;
}

#jsoneditor {
    padding: 0.5em;
}

#documentation > span {
    white-space: nowrap;
}

.token-plain {
    color: #fff;
}

.token-comment {
    color: #708090;
}

.token-number, .token-boolean {
    color: #ae81ff;
}

.token-string {
    color: #a6e22e;
}

.token-key {
    color: #66d9ef;
}

.token-key:hover {
    cursor: pointer;
    color: #3D8FFF;
}

.token-array {
    color: #fff;
}

#ss360 a.jsoneditor-poweredBy{ /* fair-play */
    color: #fff;
}

</style>