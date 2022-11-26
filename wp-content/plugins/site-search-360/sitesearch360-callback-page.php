<?php 
    $ss360_plugin = new SiteSearch360Plugin();
    $ss360_type = $ss360_plugin->getType();
    $ss360_updated_flag = false;

    $ss360_callbacks = get_option('ss360_callbacks');
    if($ss360_callbacks == NULL) {
        $ss360_callbacks = array('init' => '', 'preSearch' => '', 'postSearch' => '', 'preSuggest' => '', 'navigationClick' => '', 'preRender' => '');
    } else {
        $ss360_callbacks = json_decode($ss360_callbacks, true);
	}
	if(!isset($ss360_callbacks['navigationClick'])) {
		$ss360_callbacks['navivationClick'] = '';
	}
	if(!isset($ss360_callbacks['preRender'])) {
		$ss360_callbacks['preRender'] = '';
	}
    if (!empty($_POST) && isset($_POST['_wpnonce'])) {
        $ss360_updated_flag = true;
        if(isset($_POST['initCallback'])) {
            $ss360_callbacks['init'] = stripslashes($_POST['initCallback']);
        }
        if(isset($_POST['preSuggestCallback'])) {
            $ss360_callbacks['preSuggest'] = stripslashes($_POST['preSuggestCallback']);
        }   
        if(isset($_POST['preSearchCallback'])) {
            $ss360_callbacks['preSearch'] = stripslashes($_POST['preSearchCallback']);
        }   
        if(isset($_POST['postSearchCallback'])) {
            $ss360_callbacks['postSearch'] = stripslashes($_POST['postSearchCallback']);
		}        
		if(isset($_POST['navigationClickCallback'])) {
            $ss360_callbacks['navigationClick'] = stripslashes($_POST['navigationClickCallback']);
		}
		if (isset($_POST['preRenderCallback'])) {
			$ss360_callbacks['preRender'] = stripslashes($_POST['preRenderCallback']);
		}
        update_option('ss360_callbacks', json_encode($ss360_callbacks));
    }

    $ss360_initCallback = $ss360_callbacks['init'];
    $ss360_preSuggestCallback = $ss360_callbacks['preSuggest'];
    $ss360_preSearchCallback = $ss360_callbacks['preSearch'];
    $ss360_postSearchCallback = $ss360_callbacks['postSearch'];
    $ss360_navigationClickCallback = $ss360_callbacks['navigationClick'];
	$ss360_preRenderCallback = $ss360_callbacks['preRender'];
  
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
            <h2><?php esc_html_e('Custom Callbacks', 'site-search-360') ?></h2>
            <p class="m-v-1"><?php esc_html_e('Here you can add custom JavaScript functions.','site-search-360')?>
                <strong><?php esc_html_e(' You only need to write the function body, the definition will be added automatically.','site-search-s360');?></strong>
                <?php esc_html_e('Please only add custom callbacks to your search if you’re an advanced JavaScript user/have enough experience with JavaScript.', 'site-search-360');?>
            </p>
            <?php wp_nonce_field(); ?>
            <h3><?php esc_html_e('Init Callback', 'site-search-360')?></h3>
            <p class="m-v-1"><?php esc_html_e('This callback is called once the search plugin has been initialized and doesn\'t accept any arguments.')?></p>
            <input id="init-callback-input" type="hidden" value="<?php echo htmlspecialchars($ss360_initCallback)?>" name="initCallback">
            <textarea id="init-callback" rows="10" class="w-100"><?php echo $ss360_initCallback?></textarea>   

            <h3><?php esc_html_e('Presuggest Callback', 'site-search-360')?></h3>
            <p class="m-v-1"><?php echo sprintf(wp_kses(__('This callback is called before the suggestions are retrieved. Accepts <code>query</code> (the current value of the search input) and <code>searchBox</code> (the selected search box node) as arguments. Should return a boolean indicating whether search suggestions should be shown.', 'site-search-360'), array('code'=>array())))?></p>
            <input id="preSuggest-callback-input" type="hidden" value="<?php echo htmlspecialchars($ss360_preSuggestCallback)?>" name="preSuggestCallback">
            <textarea id="preSuggest-callback" rows="10" class="w-100"><?php echo $ss360_preSuggestCallback?></textarea>

            <?php if($ss360_type == 'full') { ?>
                <h3><?php esc_html_e('Presearch Callback', 'site-search-360')?></h3>
                <p class="m-v-1"><?php echo sprintf(wp_kses(__('This callback is called before the search results are retrieved. Accepts <code>query</code> (the current value of the search input), <code>sorting</code> (the active sorting option) and <code>searchBox</code> (the selected search box node) as arguments. Should return a boolean indicating whether search results should be shown.', 'site-search-360'), array('code'=>array())))?></p>
                <input id="preSearch-callback-input" type="hidden" value="<?php echo htmlspecialchars($ss360_preSearchCallback)?>" name="preSearchCallback">
                <textarea id="preSearch-callback" rows="10" class="w-100"><?php echo $ss360_preSearchCallback?></textarea>      

                <h3><?php esc_html_e('Postsearch Callback', 'site-search-360')?></h3>
                <p class="m-v-1"><?php echo sprintf(wp_kses(__('This callback is called after the search results are shown. Accepts <code>data</code> (the full search response JSON object) as argument.', 'site-search-360'), array('code'=>array())))?></p>
                <input id="postSearch-callback-input" type="hidden" value="<?php echo htmlspecialchars($ss360_postSearchCallback)?>" name="postSearchCallback">
                <textarea id="postSearch-callback" rows="10" class="w-100"><?php echo $ss360_postSearchCallback?></textarea>  

				<h3><?php esc_html_e('Navigation Click Callback', 'site-search-360')?></h3>
                <p class="m-v-1"><?php echo sprintf(wp_kses(__('This callback is called after navigation entry (such as tab) is clicked. Accepts <code>contentGroup</code> (the activated content group name).', 'site-search-360'), array('code'=>array())))?></p>
                <input id="navigationClick-callback-input" type="hidden" value="<?php echo htmlspecialchars($ss360_navigationClickCallback)?>" name="navigationClickCallback">
                <textarea id="navigationClick-callback" rows="10" class="w-100"><?php echo $ss360_navigationClickCallback?></textarea>      
				
				<h3><?php esc_html_e('Prerender Callback', 'site-search-360')?></h3>
                <p class="m-v-1"><?php echo sprintf(wp_kses(__('This callback is called before the search results are rendered. Accepts <code>suggests</code> (the search result array), and <code>result</code> (the whole search result response) as arguments.', 'site-search-360'), array('code'=>array())))?></p>
                <input id="preRender-callback-input" type="hidden" value="<?php echo htmlspecialchars($ss360_preRenderCallback)?>" name="preRenderCallback">
                <textarea id="preRender-callback" rows="10" class="w-100"><?php echo $ss360_preRenderCallback?></textarea>      
            <?php } ?>

            <div class="flex flex--center w-100 m-t-1">
                <button id="submit-btn" class="button button--padded" type="submit"><?php esc_html_e('Save', 'site-search-360'); ?></button>
            </div>
        </form>    
    </section>
</section>

<script type="text/javascript">
    (function(){
        jQuery(".message__close").on("click", function(e){
            jQuery(e.target).parents(".message").fadeOut();
        });
        var scripts = ['<?php echo plugins_url('assets/codemirror.min.js',  __FILE__)  ?>','<?php echo plugins_url('assets/codemirror_js_mode.min.js',  __FILE__)  ?>',
                            '<?php echo plugins_url('assets/esprima.min.js',  __FILE__)  ?>'];
        var pop = function() {
            if(scripts.length > 0){
                var e = document.createElement("script");
                e.onload = pop;
                e.src=scripts.splice(0,1)[0];
                e.async = !0;
                document.getElementsByTagName("body")[0].appendChild(e);        
            } else {
                setTimeout(function(){
                    var callbackTypes = ['init', 'preSuggest', 'preSearch', 'postSearch', 'navigationClick', 'preRender'];
                    var errors = [];
                    var submitButton = jQuery("#submit-btn");
                    callbackTypes.forEach(function(type) {
                        var checkingForErrors = false;
                        var editor = document.getElementById(type+'-callback');
                        var inputField = jQuery('#'+type+'-callback-input');
                        var codemirrorInstance = window.CodeMirror(function(elt){
                            editor.parentNode.replaceChild(elt, editor);
                        }, {
                            value: editor.value,
                            mode: "javascript",
                            lineNumbers: true
                        });
                        codemirrorInstance.on("change", function(){
                            //TODO: validation
                            inputField.val(codemirrorInstance.getValue());
                            if(!checkingForErrors) {
                                checkingForErrors = true;
                                setTimeout(function(){
                                    try {
                                        window.esprima.parse('function a(){'+codemirrorInstance.getValue()+'}');
                                        errors.splice(errors.indexOf(type), 1);
                                    } catch(err){
                                        if(errors.indexOf(type) === -1) {
                                            errors.push(type);
                                        }
                                    }
                                    if(errors.length === 0) {
                                        submitButton.attr("disabled", null);
                                    } else {
                                        submitButton.attr("disabled", "disabled");
                                    }
                                    checkingForErrors = false;
                                }, 200);
                            }
                        });
                    });
                }, 0);
            }
        }
        pop();
    })();
</script>
<style type="text/css">
    .CodeMirror pre {
        margin: 0 !important;
    }
</style>

<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('assets/codemirror.min.css',  __FILE__)  ?>">
<script src="<?php echo plugins_url('assets/sitesearch360_admin_scripts.js',  __FILE__)  ?>" async></script>
