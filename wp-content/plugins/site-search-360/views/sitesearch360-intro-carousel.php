<section id="ss360" class="wrap flex flex--center">
    <div class="wrapper wrapper--fancy">
        <div class="block block--first flex flex--center flex--column carousel">
            <h1><a href="https://sitesearch360.com" target="_blank" class="logo__link"><img aria-label="Site Search 360" class="logo" src="<?php echo plugins_url('images/logo.svg',  dirname(__FILE__))?>"></a></h1>
            <!-- Slides -->
            <div class="carousel_slider" id="slider">
                <!-- Page 1 -->
                <section class="flex flex--center flex--column carousel_slide" aria-hidden="false" style="left: 0;" role="region">
                    <h2><?php esc_html_e('Welcome to the world of…', 'site-search-360') ?></h2>
                    <div class="carousel_content">
                        <div class="carousel_group carousel_group--left">
                            <span class="carousel_feature"><?php echo sprintf(wp_kses(__('…<span class="carousel_highlight">more relevant search results,</span>', 'site-search-360'), array('span'=>array('class'=>array()))));?></span>
                            <img class="carousel_image" role="presentation" src="<?php echo plugins_url('images/intro/01_01.png', dirname(__FILE__)) ?>"/>
                        </div>
                        <div class="carousel_group carousel_group--right">
                            <span class="carousel_feature"><?php echo sprintf(wp_kses(__('and <span class="carousel_highlight">insightful statistics</span>.', 'site-search-360'), array('span'=>array('class'=>array()))));?></span>
                            <img class="carousel_image" role="presentation" src="<?php echo plugins_url('images/intro/01_02.png', dirname(__FILE__)) ?>"/>
                        </div>
                    </div>
                </section>
                <!-- Page 2 -->
                <section class="flex flex--center flex--column carousel_slide" aria-hidden="true" style="left: 100%;" role="region">
                    <h2><?php esc_html_e('Show results as you want.', 'site-search-360') ?></h2>
                    <div class="carousel_content">
                        <div class="carousel_group carousel_group--left">
                            <span class="carousel_feature"><?php echo sprintf(wp_kses(__('It takes only <span class="carousel_highlight">a few clicks</span>.', 'site-search-360'), array('span'=>array('class'=>array()))));?></span>
                            <img class="carousel_image" role="presentation" src="<?php echo plugins_url('images/intro/02_01.png', dirname(__FILE__)) ?>"/>
                        </div>
                        <div class="carousel_group carousel_group--right">
                            <span class="carousel_feature"><?php echo sprintf(wp_kses(__('<span class="carousel_highlight">No coding</span> required!', 'site-search-360'), array('span'=>array('class'=>array()))));?></span>
                            <img class="carousel_image" role="presentation" src="<?php echo plugins_url('images/intro/02_02.png', dirname(__FILE__)) ?>"/>
                        </div>
                    </div>
                </section>
                <!-- Page 3 -->
                <section class="flex flex--center flex--column carousel_slide" aria-hidden="true" style="left: 200%;" role="region">
                    <h2><?php esc_html_e('Make your own decisions.', 'site-search-360') ?></h2>
                    <div class="carousel_content">
                        <div class="carousel_group carousel_group--left">
                            <span class="carousel_feature"><?php echo sprintf(wp_kses(__('Use your <span class="carousel_highlight">theme styles</span> or <span class="carousel_highlight">make your own</span>.', 'site-search-360'), array('span'=>array('class'=>array()))));?></span>
                            <img class="carousel_image" role="presentation" src="<?php echo plugins_url('images/intro/03_02.png', dirname(__FILE__)) ?>"/>
                        </div>
                        <div class="carousel_group carousel_group--right">
                            <span class="carousel_feature"><?php echo sprintf(wp_kses(__('Enhance your search box with <span class="carousel_highlight">autosuggestions</span>.', 'site-search-360'), array('span'=>array('class'=>array()))));?></span>
                            <img class="carousel_image" role="presentation" src="<?php echo plugins_url('images/intro/03_01.png', dirname(__FILE__)) ?>"/>
                        </div>
                    </div>
                </section>
                <!-- Page 4 -->
                <section class="flex flex--center flex--column carousel_slide" aria-hidden="true" style="left: 300%;" role="region">
                    <h2><?php esc_html_e('Tweak search results.', 'site-search-360') ?></h2>
                    <div class="carousel_content">
                        <div class="carousel_group carousel_group--left">
                            <span class="carousel_feature"><?php echo sprintf(wp_kses(__('Add <span class="carousel_highlight">synonyms</span>,', 'site-search-360'), array('span'=>array('class'=>array()))));?></span>
                            <img class="carousel_image" role="presentation" src="<?php echo plugins_url('images/intro/04_01.png', dirname(__FILE__)) ?>"/>
                        </div>
                        <div class="carousel_group carousel_group--right">
                            <span class="carousel_feature"><?php echo sprintf(wp_kses(__('<span class="carousel_highlight">boost</span> what you want,…', 'site-search-360'), array('span'=>array('class'=>array()))));?></span>
                            <img class="carousel_image" role="presentation" src="<?php echo plugins_url('images/intro/04_02.png', dirname(__FILE__)) ?>"/>
                        </div>
                    </div>
                </section>
                <!-- Page 5 -->
                <section class="flex flex--center flex--column carousel_slide" aria-hidden="true" style="left: 300%;" role="region">
                    <h2><?php esc_html_e('Stuck? Our support team is always ready to help!', 'site-search-360') ?></h2>
                    <div class="carousel_content flex flex--center">
                        <div class="flex flex--column flex--center m-1 feature_icon">
                            <img width="83" class="m-b-1" role="presentation" src="<?php echo plugins_url('images/intro/05_01.svg', dirname(__FILE__)) ?>">
                            <span class="carousel_feature carousel_highlight"><a title="mail@sitesearch360.com" href="mailto:mail@sitesearch360.com"><?php esc_html_e('email', 'site-search-360') ?></a></span>
                        </div>
                        <div class="flex flex--column flex--center m-1 feature_icon">
                            <img width="66" class="m-b-1" role="presentation" src="<?php echo plugins_url('images/intro/05_02.svg', dirname(__FILE__)) ?>">
                            <span class="carousel_feature carousel_highlight"><a href="https://gitter.im/site-search-360/Lobby" target="_blank"><?php esc_html_e('live chat', 'site-search-360') ?></a></span>
                        </div>                      
                    </div>
                </section>
            </div>
            <!-- Pagination -->
            <nav class="flex flex--column flex--center carousel_navigation">
                <div class="pagination flex flex--center" role="menubar">
                    <button role="menuitem" aria-label="<?php esc_html_e('Page','site-search-360') ?> 1" data-page="1" class="pagination_item pagination_item--active"><span role="presentation" class="pagination_inner"></span></button>
                    <button role="menuitem" aria-label="<?php esc_html_e('Page','site-search-360') ?> 2" data-page="2" class="pagination_item"><span role="presentation" class="pagination_inner"></span></button>
                    <button role="menuitem" aria-label="<?php esc_html_e('Page','site-search-360') ?> 3" data-page="3" class="pagination_item"><span role="presentation" class="pagination_inner"></span></button>
                    <button role="menuitem" aria-label="<?php esc_html_e('Page','site-search-360') ?> 4" data-page="4" class="pagination_item"><span role="presentation" class="pagination_inner"></span></button>
                    <button role="menuitem" aria-label="<?php esc_html_e('Page','site-search-360') ?> 5" data-page="5" class="pagination_item"><span role="presentation" class="pagination_inner"></span></button>
                </div>
                <button class="button button--stretch" id="next"><?php esc_html_e('Next','site-search-360') ?></button>
                <button class="button button--stretch button--highlight" id="finish" style="display: none;"><?php esc_html_e('Finish','site-search-360') ?></button>
                <a class="hint track-skip" id="skip-link" href="<?php echo $_SERVER['REQUEST_URI'] ?>" style="transition: opacity 0.3s ease-in-out;"><strong><?php esc_html_e('Skip introduction.', 'site-search-360') ?></strong></a>
            </nav>
        </div>
    </div>
</section>

<script type="text/javascript">
(function(){
    var currentPage = 1;
    var wrpr = jQuery("#ss360");
    var pages = wrpr.find("button.pagination_item");
    var next = wrpr.find("#next");
    var finish = wrpr.find("#finish");
    var skip = wrpr.find("#skip-link");
    var slider = wrpr.find("#slider");
    var slides = slider.find(".carousel_slide");
    var togglePage = function(newPage){
        jQuery(pages[currentPage-1]).removeClass("pagination_item--active");
        jQuery(pages[newPage-1]).addClass("pagination_item--active");
        currentPage = newPage;
        if(currentPage===5){
            next.hide();
            finish.show();
            skip.css("opacity", 0);
        }else {
            next.show();
            finish.hide();
            skip.css("opacity", 1);
        }
        slide();
    };

    var slide = function(){
        for(var i = 0; i<slides.length; i++){
            var offset = (i+1) - currentPage;
            var sld =  jQuery(slides[i]);
            sld.css("left", (offset * 100) + '%');
            sld.attr("aria-hidden", offset!==0);
        }   
    }

    pages.click(function(e){
        var nextPage = parseInt(jQuery(e.target.nodeName==="BUTTON" ? e.target : e.target.parentNode).data("page"));
        togglePage(nextPage);
    });
    next.click(function(e){
        togglePage(currentPage+1);
    });
    finish.click(function(e){
		window.location.href = '<?php echo $_SERVER['REQUEST_URI'] ?>';
    });
    var resizeSlider = function(){
        var maxHeight = slides.get().reduce(function(acc, slide){return Math.max(acc, jQuery(slide).outerHeight())} , 0);
        slider.css("height", maxHeight);
    }

    var resizeTimeoutId = -1;
    slider.find("img").each(function(){
        var image = new Image();
        image.src = jQuery(this).attr("src");
        image.onload = function(){
            clearTimeout(resizeTimeoutId);
            resizeTimeoutId = setTimeout(resizeSlider, 100);
        }
    });

    jQuery(window).on("resize", function(){
        clearTimeout(resizeTimeoutId);
        resizeTimeoutId = setTimeout(resizeSlider, 100);
    });
    resizeSlider();

    // index in the background so the user doesn't have to wait that long for the dashboard
    var indexChunk = undefined;
    var indexContent = function(){
    var postData =  {action: 'ss360_index'};
    if(indexChunk !== undefined){
        postData['chunkSize'] = indexChunk;
    }
    jQuery.post(ajaxurl, postData).done(function(data){
        var indexed = parseInt(data.data.indexed);
        var total = parseInt(data.data.total);
        if(indexed !== total){
            indexContent();
        }
    }).fail(function(){
        if(indexChunk === undefined){
            indexChunk = 50;
        }else { // probably too many posts being processed at once - try to decrease the chunk size
            indexChunk = Math.round(indexChunk * 0.6);
            indexChunk = Math.max(indexChunk, 1);
        }
        setTimeout(indexContent, indexChunk > 1 ? 1 : 500); // try again o.O
        });      
    }

    jQuery(".track-skip").on("click", function(e){
        var ctrl = e.ctrlKey;
        if(!ctrl){
            e.preventDefault();
            e.stopPropagation();
        }
		if(!ctrl){
			window.location.href = '<?php echo $_SERVER['REQUEST_URI'] ?>';
		}
    });
    
    indexContent();
}());

</script>

<script src="<?php echo plugins_url('assets/sitesearch360_admin_scripts.js',  dirname(__FILE__))  ?>" async></script>