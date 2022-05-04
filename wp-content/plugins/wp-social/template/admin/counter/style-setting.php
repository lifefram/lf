<?php
defined( 'ABSPATH') || exit;
?>
<div class="wslu-social-login-main-wrapper">
	<?php

	require( __DIR__ . '/tab-menu.php');

	/**
	 * todo - check this notice box too : WP1-217
     * make a function and call it here
     *
	 */
	if($message_provider == 'show'){?>
        <div class="admin-page-framework-admin-notice-animation-container">
            <div id="XS_Social_Login_Settings" 
                 class="updated admin-page-framework-settings-notice-message admin-page-framework-settings-notice-container notice is-dismissible" 
                 style="margin: 1em 0; visibility: visible; opacity: 1;">
                <p><?php echo esc_html__('Styles data have been updated.', 'wp-social');?></p>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo esc_html__('Dismiss this notice.', 'wp-social');?></span></button>
            </div>
        </div>
	<?php }?>


    <form action="<?php echo esc_url(admin_url().'admin.php?page=wslu_counter_setting&tab=wslu_style_setting');?>" name="xs_style_submit_form" method="post" id="xs_style_form">
        <div class="xs-social-block-wraper">
            <div class="xs-global-section">
               
                <!-------------------------------- 
                     Social counter hover aniamtion 
                  -------------------------------->
                  <?php if (isset($share_hover_effects)) : ?>
                    <div class="wslu-single-item">
                        <div class="wlsu-hover-effect">

                        <h2 class="wlsu-hover-effect__title"> 
                            <?php echo esc_html__('Select Hover Effects', 'wp-social'); ?>
                        </h2>
                        <div class="wlsu-hover-effect__content">
                            <?php foreach ($share_hover_effects as $key => $value) : ?>
                                <div class="wlsu-hover-effect__item">
                                    <input 
                                        <?php echo (isset($value['exclude'])) ? 'data-exclude="' . esc_attr(json_encode($value['exclude'])) . '"' : '' ?>
                                        type="radio"
                                        class="wlsu-hover-effect-select"
                                        name="xs_style[hover_effect]"
                                        id="<?php echo esc_attr($key); ?>"
                                        value="<?php echo esc_attr($key); ?>" 
                                        <?php echo ($selectedEffect == $key) ? 'checked' : ''; ?>/>

                                    <label for="<?php echo esc_attr($key); ?>" >
                                        <img src="<?php echo esc_url(WSLU_PRO_LOGIN_PLUGIN_URL . 'assets/images/counter-hover-preview/' . $key . '.png'); ?>"/>
                                        <span> <?php echo esc_html($value['name']); ?> </span>
                                    </label>
                                </div>

                            <?php endforeach; ?>
                        </div>
                            
                        </div>

                    </div> 
                <?php endif; ?><!-- ./ End social share hover animation -->

               
                
                <!-------------------------------- 
                    Social counter  Styles 
                -------------------------------->
                <h2 class="wlsu-hover-effect__title wlsu-style-data">
                    <?php echo esc_html__('Select Counter Style', 'wp-social'); ?>
                </h2>
                <div class="wslu-social-style-data">

					<?php foreach($styleArr AS $styleKey => $styleValue): ?>

                        <div class="wslu-single-social-style-item <?php echo esc_attr( ( (!did_action('wslu_social_pro/plugin_loaded')) && ($styleValue['package'] == 'pro') ) ? 'wslu-style-pro': 'wslu-style-free' ); ?>">

                            <label for="_login_button_style__<?= $styleKey;?>" class="social_radio_button_label xs_label_wp_login">

                                <div class="wslu-style-img xs-login-<?= $styleKey;?> <?php echo (isset($return_data['login_button_style']) && $return_data['login_button_style'] == $styleKey ) ? 'style-active ' : '';?>">

                                        <img src="<?php echo esc_url(WSLU_LOGIN_PLUGIN_URL.'assets/images/screenshort/counter/'.$styleValue['design'].'.png'); ?>" alt="<?= $styleValue['name']; ?>">

                                        <?php if(!in_array('wp-social-pro/wp-social-pro.php', apply_filters('active_plugins', get_option('active_plugins')))) : ?>
                                            <a href="https://wpmet.com/plugin/wp-social/pricing/" class="wslu-buy-now-btn"><?php esc_html_e('Buy Now', 'wp-social'); ?></a>
                                        <?php endif; ?>
                                </div>

                                <input class="social_radio_button wslu-global-radio-input share-input-name"
                                            type="radio"
                                            id="_login_button_style__<?= $styleKey;?>"
                                            name="xs_style[login_button_style]"
                                            value="<?= $styleKey;?>"
                                <?php echo esc_attr( ( (!did_action('wslu_social_pro/plugin_loaded')) && ($styleValue['package'] == 'pro') ) ? 'disabled="disabled"': '' ); ?>
                                
                                <?php echo ($selectedStyle == $styleKey) ? 'checked' : ''; ?> >

                                <?php 
                                    echo esc_html__($styleValue['name'], 'wp-social');
                                    echo (!did_action('wslu_social_pro/plugin_loaded')) && ($styleValue['package'] == 'pro') ? '<span class="wslu-go-pro-text">(' . esc_html('Pro Only', 'elementskit') . ')</span>' : '';
                                ?>

                            </label>
                        </div>
					<?php endforeach; ?>

                </div>

                <div class="wslu-social-style-hidden-inputs">
                    <label>
                        <input type="text" class="wslu-main-content-input" name="xs_style[login_button_style][style]" value="<?php echo (isset($return_data['login_button_style']['style']) ? $return_data['login_button_style']['style'] : ''); ?>">
						<!-- ?php esc_html_e('Main Content', 'wp-social'); ?-->
                    </label>
                </div>
            
                <div class="wslu-right-content wslu-right-content--share">
                    <button type="submit" name="style_setting_submit_form" class="xs-btn btn-special small"><?php echo esc_html__('Save Changes', 'wp-social');?></button>
                </div>

            </div>
        </div>
        <div class="xs-backdrop"></div>
    </form>
</div>