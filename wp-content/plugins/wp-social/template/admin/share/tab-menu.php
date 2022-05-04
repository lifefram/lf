<?php
defined( 'ABSPATH') || exit;

$active_tab = isset($_GET["tab"]) ? $_GET["tab"] : 'wslu_counter_setting';
?>

<div class="wslu-main-header">
	<h1>
		<img src="<?php echo esc_url( WSLU_LOGIN_PLUGIN_URL . 'assets/images/icon-title.png' ); ?>" alt="">
		<?php _e('WP Social Share Settings', 'wp-social'); ?>
	</h1>
</div>

<div class="wslu-nav-tab-wrapper">
	<ul>
		<li><a href="?page=wslu_share_setting" class="nav-tab <?php if($active_tab == 'wslu_counter_setting'){echo 'nav-tab-active';} ?> "><?php _e('Share Settings', 'wp-social'); ?></a></li>
		<li><a href="?page=wslu_share_setting&tab=wslu_providers" class="nav-tab <?php if($active_tab == 'wslu_providers'){echo 'nav-tab-active';} ?>"><?php _e('Providers', 'wp-social'); ?></a></li>
		<li><a href="?page=wslu_share_setting&tab=wslu_style_setting" class="nav-tab <?php if($active_tab == 'wslu_style_setting'){echo 'nav-tab-active';} ?>"><?php _e('Style Settings', 'wp-social'); ?></a></li>
	</ul>
</div>
