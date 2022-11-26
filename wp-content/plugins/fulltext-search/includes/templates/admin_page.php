<?php

/**  
 * Copyright 2013-2022 Epsiloncool
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 ******************************************************************************
 *  I am thank you for the help by buying PRO version of this plugin 
 *  at https://fulltextsearch.org/ 
 *  It will keep me working further on this useful product.
 ******************************************************************************
 * 
 *  @copyright 2013-2022
 *  @license GPLv3
 *  @package Wordpress Fulltext Search
 *  @author Epsiloncool <info@e-wm.org>
 */

require_once dirname(__FILE__).'/../wpfts_output.php';
require_once dirname(__FILE__).'/../updater/updater.php';

$out = new WPFTS_Output();

global $wpfts_core;

$version_text = 'v'.WPFTS_VERSION.'';

?>
<div class="wrap">
	<h2><?php _e('WP FullText Search Options', 'fulltext-search');?></h2>
	<div class="bs-wpfts">

	<?php

	$is_pro_installed = $wpfts_core->get_option('is_pro_installed');

	// Do we need to show an invitation message?
	$upds = $wpfts_core->GetUpdates();

	if ($upds['is_new'] && $wpfts_core->is_wpfts_settings_page) {
	?>
	<div class="row">
		<div class="col-12">
	<div class="notice notice-warning wpfts-notice">
		<hr>
		<?php

		if ($is_pro_installed) {
			echo sprintf(__('<h2>Important Notice Before You Start</h2>
			<p>First of all, thank you for your support by purchasing the copy of the WPFTS plugin. Thus you are supporting the plugin development and the whole Open Source code movement!</p>
	
			<p>Before the first launch, please do the following:</p>
			<ul>
				<li>1. <span style="color:red;"><b>Enter your license number</b></span> in the "<a href="/wp-admin/admin.php?page=wpfts-options-licensing">Licensing</a>" tab to ensure that you receive the latest updates and the correct processing of files for placement from the search index.</li>
				<li>2. Check the indexing settings in the "<a href="/wp-admin/admin.php?page=wpfts-options-indexing-engine">Indexing</a>" tab. You can configure something, but if you don’t understand how to do it right, leave it as it is for now.</li>
				<li>3. Start building the index by clicking the big button below.</li>
			</ul>
				
			<p>Usually, building an index is required only once, because the plugin will automatically update it after the changes that you make on the site.</p>
			<p>The indexing process may take a long time (it depends on the amount of data on the site) and the site may work a little slower. There is no reason to worry - this slowness will end with the end of the indexing process. To reduce the time to build the index, please <b>do not close</b> the plugin settings page.</p>
	
			<p>If you didn’t install WPFTS Add-ons and didn’t set up your own <code>wpfts_index_post</code> hook, then this time only the Titles and the main Content of the publications will be included in the index. If you want other data to participate in the search (such as <b>post meta data</b>), now is the time to read the <a href="%s" target="_blank">WPFTS Documentation</a> and make the necessary changes.</p>
	
			<p>We wish you a pleasant work with the WP FullText Search plugin.</p>
			<p>We also open for your <a href="%s" target="_blank">comments and suggestions</a>.</p>
			<p><i>WPFTS plugin development team.</i></p>', 'fulltext-search'), 
						'https://fulltextsearch.org/documentation',
						'https://fulltextsearch.org/contact/'
					);	
		} else {
			echo sprintf(__('<h2>Important Notice Before You Start</h2>
			<p>Everything is ready to index the contents of your site. When creating a Search Index, the plugin will use its own tables in the database, no your data will be affected.</p>
			<p>The process may take a long time (it depends on the amount of data on the site) and the site may work a little slower. There is no reason to worry - this slowness will end with the end of the indexing process. To reduce the time to build the index, please <b>do not close</b> the plugin settings page.</p>
			<p>If you didn’t install WPFTS Add-ons and didn’t set up your own <code>wpfts_index_post</code> hook, then this time only the Titles and the main Content of the publications will be included in the index. If you want other data to participate in the search (such as <b>post meta data</b>), now is the time to read the <a href="%s" target="_blank">WPFTS Documentation</a> and make the necessary changes.</p>
	
			<p>We wish you a pleasant work with the WP FullText Search plugin.</p>
			<p>We also thank you for your <a href="%s" target="_blank">comments and suggestions</a>.</p>
			<p><i>WPFTS plugin development team.</i></p>', 'fulltext-search'), 
				'https://fulltextsearch.org/documentation',
				'https://fulltextsearch.org/contact/'
			);
		}
		?>
		<p style="text-align: center;">
			<button type="button" class="button-primary btn_start_indexing"><?php echo __('Start Indexing', 'fulltext-search'); ?></button>&nbsp;<span class="wpfts_show_resetting"><img src="<?php echo $wpfts_core->root_url; ?>/style/waiting16.gif" alt="">&nbsp;<?php echo __('Resetting', 'fulltext-search'); ?></span>
		</p>
		<hr>
	</div>
			</div>
		</div>
	<?php
	}
	/*
	?>

	<div class="row">
		<div class="col-xl-3 col-lg-4 col-md-5 col-12">
			<?php
				require dirname(__FILE__).'/sidebar.php';
			?>
		</div>
	</div>
	<?php
	*/

	$status = $wpfts_core->get_status();

	?>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="background: url(<?php echo $wpfts_core->root_url; ?>/style/wpfts-bg.jpg);background-size: cover;">
		<div class="col-6 text-white" style="flex: 0 0 280px; min-width: 280px;">
			<div class="wpfts_logo_div">
				<div class="wpfts_logo_version"><?php echo $version_text; ?></div>
				<img src="<?php echo $wpfts_core->root_url; ?>/style/wpfts-logo-transparent.png" alt="" style="height: 100px; width: 150px;" class="d-inline-block align-top" width="150" height="100">
			</div>
			<div class="text-left wpfts_izfs_row" style="padding-left: 10px;">
				<span class="wpfts_data_isdisabled" style="display: <?php echo $status['enabled'] ? 'none' : 'inline-block'; ?>;">
					<span class="wpfts_status_bullet wpfts_red" title="<?php echo __('The Search Index was disabled in configuration.', 'fulltext-search'); ?>">&#9679;</span>&nbsp;<b style="color: #fcc;"><?php echo __('Disabled', 'fulltext-search'); ?></b>
				</span>
				<span class="wpfts_data_isindexready" style="display: <?php echo ($status['enabled'] && $status['index_ready']) ? 'inline-block' : 'none'; ?>;">
					<span class="wpfts_status_bullet wpfts_green">&#9679;</span>&nbsp;<b><?php echo __('Active', 'fulltext-search'); ?></b>
				</span>
				<span class="wpfts_data_isindexready_not" style="display: <?php echo ($status['enabled'] && (!$status['index_ready'])) ? 'inline-block' : 'none'; ?>;">
					<span class="wpfts_status_bullet wpfts_yellow" title="<?php echo __('The Search Index will be activated after the indexing process is complete.', 'fulltext-search'); ?>">&#9679;</span>&nbsp;<b><?php echo __('Awaiting', 'fulltext-search'); ?></b>
				</span>
			</div>
		</div>
		<div class="col-6 text-white" style="flex: 1 280px;max-width: calc(100% - 280px);padding-right:0;">
			<?php 
				echo $out->status_box_top(null, $status);
			?>
		</div>
	</nav>

	<div class="row">
		<div class="col-xl-3 col-lg-4 col-md-5 col-1 wpfts_col1_60" style="padding-right: 0;">
			<ul class="nav nav-tabs flex-column wpfts_tabs">
			<?php

	// Do we need an exclamation mark in the "Licensing" tab?
	$lic_suffix = '';
	if ($is_pro_installed) {
		$lic_status = WPFTS_Updater::get_subscription_status(); 

		$lic_suffix = ' <i class="fa fa-exclamation-circle text-danger"></i>';
		if ($lic_status) 
		{
			if ((isset($lic_status[0])) && isset($lic_status[0]->active)) {
				$days_left = isset($lic_status[0]->days_left) ? intval($lic_status[0]->days_left) : -100;
				$is_expired = intval($lic_status[0]->is_expired);
			
				if ($is_expired == 0) {
					if ($days_left < 30) {
						$lic_suffix = ' <i class="fa fa-exclamation-circle text-warning"></i>';
					} else {
						$lic_suffix = '';
					}
				}
			}
		}
				
	}
	
	$tabs = array(
		'wpfts-options' => array(__('Main Configuration', 'fulltext-search'), 'fa fa-cog'),
		'wpfts-options-indexing-engine' => array(__('Indexing Engine Settings', 'fulltext-search'), 'fa fa-table'),
		'wpfts-options-search-relevance' => array(__('Search & Output', 'fulltext-search'), 'fa fa-search'),
		'wpfts-options-sandbox-area' => array(__('Sandbox Area', 'fulltext-search'), 'fa fa-flask'),
		'wpfts-options-analytics' => array(__('Analytics', 'fulltext-search'), 'fa fa-chart-line'),
		//'wpfts-options-addons' => array(__('Extensions', 'fulltext-search'), 'fa fa-chart-table'),
		'wpfts-options-support' => array(__('Support & Docs', 'fulltext-search'), 'fa fa-life-ring'),
	);

	if ($is_pro_installed) {
		$tabs['wpfts-options-licensing'] = array(__('Licensing', 'fulltext-search').$lic_suffix, 'fa fa-unlock-alt');
	}

	$tabs = apply_filters('wpfts_admin_tabs', $tabs);

	$tt = array();

	$current_tab = isset($_GET['page']) ? $_GET['page'] : 'wpfts-options';
	foreach ($tabs as $tab_key => $tab_caption) {
		$active = ($current_tab == $tab_key) ? ' active' : '';
		echo '<li class="nav-item" data-name="'.$tab_key.'"><a class="nav-link'.$active.'" href="?page='.$tab_key.'"><i class="'.$tab_caption[1].'"></i><span class="wpfts_menu_text"> '.$tab_caption[0].'</span></a></li>';

		// Tab content
		ob_start();
		switch ($tab_key) {
			case 'wpfts-options':
				require dirname(__FILE__).'/main-configuration.php';
				break;
			case 'wpfts-options-indexing-engine':
				require dirname(__FILE__).'/indexing-engine.php';
				break;
			case 'wpfts-options-search-relevance':
				require dirname(__FILE__).'/search-relevance.php';
				break;
			case 'wpfts-options-sandbox-area':
				require dirname(__FILE__).'/sandbox-area.php';
				break;
			case 'wpfts-options-licensing':
				require dirname(__FILE__).'/licensing.php';
				break;
			case 'wpfts-options-analytics':
				require dirname(__FILE__).'/analytics.php';
				break;
			case 'wpfts-options-support':
				require dirname(__FILE__).'/support.php';
				break;
		default:
				// Custom tab added by addon
				if (isset($tab_caption[2])) {
					if (is_executable($tab_caption[2])) {
						echo call_user_func($tab_caption[2]);
					} else {
						if (is_string($tab_caption[2]) && (function_exists($tab_caption[2]))) {
							echo call_user_func($tab_caption[2]);
						}
					}
				}
				break;
		}
		$tab_content = ob_get_clean();
		$tt[] = '<div class="tab-pane'.$active.'" role="tabpanel" data-tabname="'.$tab_key.'">'.$tab_content.'</div>';
	}
	?>
	</ul>
	</div>
	<div class="col-xl-9 col-lg-8 col-md-7 col-11 wpfts_col11_60" style="padding-left: 0;">
		<div class="tab-content wpfts_tabs_content bg-white p-3">
		<?php echo implode('', $tt); ?>
	</div>
	<script type="text/javascript">
	jQuery(document).ready(function()
	{
		jQuery('ul.wpfts_tabs > li > a').on('click', function(e)
		{
			e.preventDefault();

			var li = jQuery(this).closest('li');
			var sel = li.attr('data-name');
			// Hide all tabs except current one
			jQuery('.tab-content.wpfts_tabs_content > .tab-pane').each(function()
			{
				if (jQuery(this).attr('data-tabname') == sel) {
					jQuery(this).addClass('active');
				} else {
					jQuery(this).removeClass('active');
				}
			});
			// Make tab highlighted
			jQuery('ul.wpfts_tabs > li').each(function()
			{
				if (jQuery(this).attr('data-name') == sel) {
					jQuery('a.nav-link', jQuery(this)).addClass('active');
				} else {
					jQuery('a.nav-link', jQuery(this)).removeClass('active');
				}
			});

			var href = jQuery(this).attr('href');
			// Let's switch URL
			window.history.pushState({'currentUrl': document.location.href, 'tab': sel}, '', href);


			return false;
		});

		window.addEventListener('popstate', function(e)
		{
			// Set previous tab
			if (e.state && e.state.tab) {
				// Set tab
				var sel = e.state.tab;
				// Hide all tabs except current one
				jQuery('.tab-content.wpfts_tabs_content .tab-pane').each(function()
				{
					if (jQuery(this).attr('data-tabname') == sel) {
						jQuery(this).addClass('active');
					} else {
						jQuery(this).removeClass('active');
					}
				});
				// Make tab highlighted
				jQuery('ul.wpfts_tabs li').each(function()
				{
					if (jQuery(this).attr('data-name') == sel) {
						jQuery('a.nav-link', jQuery(this)).addClass('active').focus();
					} else {
						jQuery('a.nav-link', jQuery(this)).removeClass('active');
					}
				});

			}
		}, false);

		window.history.replaceState({'currentUrl': document.location.href, 'tab': jQuery('ul.wpfts_tabs li a.active').closest('li').attr('data-name')}, '', document.location.href);
	});
	</script>
		</div>
	</div>
</div>

</div>