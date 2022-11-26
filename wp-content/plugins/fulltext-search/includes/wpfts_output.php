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

require_once dirname(__FILE__).'/wpfts_htmltools.php';

class WPFTS_Output 
{
	public $mimetype_groups;

	public function __construct()
	{
		$this->mimetype_groups = array(
			1 => array(__('Image', 'fulltext-search'), array('image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/tiff', 'image/x-icon')),
			2 => array(__('Video', 'fulltext-search'), array('video/x-ms-asf', 'video/x-ms-wmv', 'video/x-ms-wmx', 'video/x-ms-wm', 'video/avi', 'video/divx', 'video/x-flv', 'video/quicktime', 'video/mpeg', 'video/mp4', 'video/ogg', 'video/webm', 'video/x-matroska')),
			3 => array(__('Text', 'fulltext-search'), array('text/plain', 'text/csv', 'text/tab-separated-values', 'text/calendar', 'text/richtext', 'text/css', 'text/html')),
			4 => array(__('Audio', 'fulltext-search'), array('audio/mpeg', 'audio/x-realaudio', 'audio/wav', 'audio/ogg', 'audio/midi', 'audio/x-ms-wma', 'audio/x-ms-wax', 'audio/x-matroska')),
			5 => array(__('Misc App', 'fulltext-search'), array('application/rtf', 'application/javascript', 'application/pdf', 'application/x-shockwave-flash', 'application/java', 'application/x-tar', 'application/zip', 'application/x-gzip', 'application/rar', 'application/x-7z-compressed', 'application/x-msdownload')),
			6 => array(__('Microsoft Office', 'fulltext-search'), array('application/msword', 'application/vnd.ms-powerpoint', 'application/vnd.ms-write', 'application/vnd.ms-excel', 'application/vnd.ms-access', 'application/vnd.ms-project', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-word.document.macroEnabled.12', 'application/vnd.openxmlformats-officedocument.wordprocessingml.template', 'application/vnd.ms-word.template.macroEnabled.12', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel.sheet.macroEnabled.12', 'application/vnd.ms-excel.sheet.binary.macroEnabled.12', 'application/vnd.openxmlformats-officedocument.spreadsheetml.template', 'application/vnd.ms-excel.template.macroEnabled.12', 'application/vnd.ms-excel.addin.macroEnabled.12', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.ms-powerpoint.presentation.macroEnabled.12', 'application/vnd.openxmlformats-officedocument.presentationml.slideshow', 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12', 'application/vnd.openxmlformats-officedocument.presentationml.template', 'application/vnd.ms-powerpoint.template.macroEnabled.12', 'application/vnd.ms-powerpoint.addin.macroEnabled.12', 'application/vnd.openxmlformats-officedocument.presentationml.slide', 'application/vnd.ms-powerpoint.slide.macroEnabled.12', 'application/onenote')),
			7 => array(__('OpenOffice', 'fulltext-search'), array('application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.presentation', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.oasis.opendocument.graphics', 'application/vnd.oasis.opendocument.chart', 'application/vnd.oasis.opendocument.database', 'application/vnd.oasis.opendocument.formula')),
			8 => array(__('WordPerfect', 'fulltext-search'), array('application/wordperfect')),
			9 => array(__('iWork', 'fulltext-search'), array('application/vnd.apple.keynote', 'application/vnd.apple.numbers', 'application/vnd.apple.pages')),
			10 => array(__('Used-Defined', 'fulltext-search'), array()),
			11 => array(__('Not Registered', 'fulltext-search'), array()),
		);
	}

	public function status_box($post, $status = false, $is_return = false) 
	{	
		global $wpfts_core, $wpfts_gstatus;
		
		if (!$status) {
			if ($wpfts_gstatus) {
				$status = $wpfts_gstatus;
			} else {
				$status = $wpfts_core->get_status();
			}
		}

		ob_start();
		?>
		<div class="card mb-2" id="wpfts_status_box">
			<div class="card-header bg-info text-white"><?php echo __('Search Engine Status', 'fulltext-search'); ?></div>
			<div class="card-body">
		<p class="wpfts_status">
			<span class="wpfts_data_isdisabled" style="display: <?php echo $status['enabled'] ? 'none' : 'inline-block'; ?>;">
				<span class="wpfts_status_bullet wpfts_red" title="<?php echo __('The Search Index was disabled in configuration.', 'fulltext-search'); ?>">&#9679;</span>&nbsp;<b><?php echo __('Disabled', 'fulltext-search'); ?></b>
			</span>
			<span class="wpfts_data_isindexready" style="display: <?php echo ($status['enabled'] && $status['index_ready']) ? 'inline-block' : 'none'; ?>;">
				<span class="wpfts_status_bullet wpfts_green">&#9679;</span>&nbsp;<b><?php echo __('Active', 'fulltext-search'); ?></b>
			</span>
			<span class="wpfts_data_isindexready_not" style="display: <?php echo ($status['enabled'] && (!$status['index_ready'])) ? 'inline-block' : 'none'; ?>;">
				<span class="wpfts_status_bullet wpfts_yellow" title="<?php echo __('The Search Index will be activated after the indexing process is complete.', 'fulltext-search'); ?>">&#9679;</span>&nbsp;<b><?php echo __('Awaiting', 'fulltext-search'); ?></b>
			</span>
		</p>
		<p><?php echo __('In Index', 'fulltext-search'); ?>: <b><span id="wpfts_st_inindex" class="wpfts_data_n_inindex"><?php echo $status['n_inindex']; ?></span></b> <?php echo __('records', 'fulltext-search'); ?></p>
		<?php 

		$is_ok = false;
		$is_slow_warning = false;
		if ($status['autoreindex']) {

			$is_index_enabled = true;
			$is_slow_warning = true;

			//$percent = (0.0 + intval($status['n_actual'])) * 100 / (intval($status['n_inindex']) + intval($status['n_pending']));
			if (intval($status['n_inindex']) > 0) {
				$percent = (0.0 + intval($status['n_actual'])) * 100 / (intval($status['n_inindex']));
			} else {
				$percent = 0;
			}

			if ($status['nw_total'] > 0) {
				$percent2 = sprintf('%.2f', $status['nw_act'] * 100 / $status['nw_total']);
			} else {
				$percent2 = 0;
			}

			if (($status['n_pending'] > 0) || ($status['n_tw'] > 0)) {
				// Main indexing mode
				$is_indexing = true;
			} else {
				$is_indexing = false;
				if ($status['nw_act'] < $status['nw_total']) {
					$is_optimization = true;
					$is_slow_warning = true;
				} else {
					$is_optimization = false;
					if ($status['n_tw'] < 1) {
						$is_ok = true;
						$is_slow_warning = false;
					}
				}

			}
		} else {
			$is_index_enabled = false;
		}

		$is_pause = intval($status['is_pause']);

		ob_start();
		?>
			<div style="display:block;position:absolute;right:30px;">
				<button type="button" class="btn btn-default btn-sm wpfts_set_pause_on wpfts_data_pause_btn_on" style="color:#888;display:<?php echo $is_pause ? 'none' : 'inline-block'; ?>;" title="<?php echo __('Pause', 'fulltext-search'); ?>"><i class="fa fa-pause"></i></button> 
				<button type="button" class="btn btn-default btn-sm wpfts_set_pause_off wpfts_data_pause_btn_off" style="color:green;display:<?php echo $is_pause ? 'inline-block' : 'none'; ?>;" title="<?php echo __('Continue Indexing', 'fulltext-search'); ?>"><i class="fa fa-play"></i></button>
				<div style="clear:both;"></div>
    		</div>
		<?php

		$pause_block = ob_get_clean();

		?>
		<div class="wpfts_indexer_info wpfts_data_is_indexing_or_optimization" style="display: <?php echo $is_indexing || $is_optimization ? 'block' : 'none'; ?>;">
			<div class="wpfts_data_is_indexing" style="display: <?php echo $is_indexing ? 'block' : 'none'; ?>">
				<?php echo $pause_block; ?>
				<span class="wpfts_data_is_pause_not" style="display: <?php echo $is_pause ? 'none' : 'block'; ?>;">
					<p><span class="wpfts_indexing_status_bullet wpfts_yellow">&#9679;</span>&nbsp;<?php echo __('Processing post records', 'fulltext-search'); ?></p>
				</span>
				<span class="wpfts_data_is_pause" style="display: <?php echo $is_pause ? 'block' : 'none'; ?>;">
					<p><span class="wpfts_indexing_status_bullet wpfts_yellow">&#9679;</span>&nbsp;<?php echo __('Indexing is PAUSED', 'fulltext-search'); ?></p>
				</span>

				<span><?php echo __('Actual', 'fulltext-search'); ?>: <b><span id="wpfts_st_actual" class="wpfts_data_n_actual"><?php echo $status['n_actual']; ?></span></b> <?php echo __('records', 'fulltext-search'); ?></span><br>
				<span><?php echo __('Pending', 'fulltext-search'); ?>: <b><span id="wpfts_st_pending" class="wpfts_data_n_pending"><?php echo $status['n_pending']; ?></span></b> <?php echo __('records', 'fulltext-search'); ?></span>
				<div id="wpfts_st_1">
				<span class="wpfts_data_is_pause_not" style="display: <?php echo $is_pause ? 'none' : 'block'; ?>;">
					<p class="wpfts_st_percent"><img src="<?php echo $wpfts_core->root_url; ?>/style/waiting16.gif" alt="" title="<?php echo __('Indexing is in progress', 'fulltext-search'); ?>">&nbsp;<span class="wpfts_data_percent"><?php echo sprintf('%.2f', $percent).'%'; ?></span></p>
				</span>
				<span class="wpfts_data_is_pause" style="display: <?php echo $is_pause ? 'block' : 'none'; ?>;">
					<p class="wpfts_st_percent"><b>...</b>&nbsp;<span class="wpfts_data_percent"><?php echo sprintf('%.2f', $percent).'%'; ?></span></p>
				</span>

				<p><?php echo __('Est. time left: ', 'fulltext-search'); ?><span class="wpfts_st_esttime wpfts_data_est_time"><?php echo $status['est_time']; ?></span></p>
				</div>
			</div>
			<div class="wpfts_data_is_optimization" style="display: <?php echo $is_optimization ? 'block' : 'none'; ?>">
				<p><span class="wpfts_indexing_status_bullet wpfts_green">&#9679;</span>&nbsp;<b class="wpfts_data_n_actual"><?php echo $status['n_actual']; ?></b> <?php echo __('post records done', 'fulltext-search'); ?></p>
				<?php echo $pause_block; ?>
				<span class="wpfts_data_is_pause_not" style="display: <?php echo $is_pause ? 'none' : 'block'; ?>;">
					<p><span class="wpfts_indexing_status_bullet wpfts_yellow">&#9679;</span>&nbsp;<b><?php echo __('Optimizing Index', 'fulltext-search'); ?></b></p>
				</span>
				<span class="wpfts_data_is_pause" style="display: <?php echo $is_pause ? 'block' : 'none'; ?>;">
					<p><span class="wpfts_indexing_status_bullet wpfts_yellow">&#9679;</span>&nbsp;<b><?php echo __('Optimizing is PAUSED', 'fulltext-search'); ?></b></p>
				</span>
				<div class="progress wpfts_data_n_act_total" title="<?php echo $status['nw_act'].' / '.$status['nw_total']; ?>">
					<div class="progress-bar progress-bar-striped progress-bar-animated wpfts_data_percent2" role="progressbar" aria-valuenow="<?php echo $percent2; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percent2; ?>%"><?php echo $percent2; ?>%</div>
				</div>
			</div>
		</div>
		<div class="wpfts_data_is_slow_warning" style="display: <?php echo $is_slow_warning ? 'block' : 'none'; ?>;">
			<p style="color: #00a000;"><i><?php echo __('Your site may run slower while indexing. It will go back to normal when the process is complete.', 'fulltext-search'); ?></i></p>
		</div>
		<div class="wpfts_data_is_ok" style="display: <?php echo $is_ok ? 'block' : 'none'; ?>;">
			<?php echo $pause_block; ?>
			<span class="wpfts_data_is_pause_not" style="display: <?php echo $is_pause ? 'none' : 'block'; ?>;">
				<p><span class="wpfts_indexing_status_bullet wpfts_green">&#9679;</span>&nbsp;<?php echo __('Index is OK', 'fulltext-search'); ?></p>
			</span>
			<span class="wpfts_data_is_pause" style="display: <?php echo $is_pause ? 'block' : 'none'; ?>;">
				<p><span class="wpfts_indexing_status_bullet wpfts_yellow">&#9679;</span>&nbsp;<?php echo __('Index is PAUSED', 'fulltext-search'); ?></p>
			</span>
		</div>
		<div class="wpfts_data_is_index_disabled" style="display: <?php echo (!$is_index_enabled) ? 'block' : 'none'; ?>;">
			<?php echo $pause_block; ?>
			<p><span class="wpfts_indexing_status_bullet wpfts_red">&#9679;</span>&nbsp;<?php echo __('Indexing is disabled', 'fulltext-search'); ?></p>
		</div>
			</div>
		</div>
		<?php

		$output = ob_get_clean();
		
		if ($is_return) {
			return $output;
		} else {
			echo $output;
		}
	}

	public function status_box_top($post, $status = false, $is_return = false) 
	{	
		global $wpfts_core, $wpfts_gstatus;
		
		if (!$status) {
			if ($wpfts_gstatus) {
				$status = $wpfts_gstatus;
			} else {
				$status = $wpfts_core->get_status();
			}
		}

		ob_start();

		// Title
		?>
		<div class="wpfts_top_indexstatus">
			<div>

		<h6><?php echo __('Indexing Engine Status', 'fulltext-search'); ?></h6>
		<?php

		// Search engine status
		// @todo

		// Get status values
		$is_ok = false;
		//$is_slow_warning = false;
		$is_indexing = false;
		$is_optimization = false;
		$percent = 0;
		$percent2 = 0;
		$is_pending = false;
		$is_records = false;
		$is_index_enabled = false;

		if ($status['autoreindex']) {

			$is_index_enabled = true;
			//$is_slow_warning = true;

			//$percent = (0.0 + intval($status['n_actual'])) * 100 / (intval($status['n_inindex']) + intval($status['n_pending']));
			if (intval($status['n_inindex']) > 0) {
				$percent = (0.0 + intval($status['n_actual'])) * 100 / (intval($status['n_inindex']));
				$percent = (intval($status['n_actual']) < intval($status['n_inindex'])) ? min(99.99, $percent) : $percent;
			} else {
				$percent = 0;
			}

			if ($status['nw_total'] > 0) {
				$percent2 = sprintf('%.2f', $status['nw_act'] * 100 / $status['nw_total']);
			} else {
				$percent2 = 0;
			}

			if (($status['n_pending'] > 0) || ($status['n_tw'] > 0)) {
				// Main indexing mode
				$is_pending = true;
				$is_indexing = true;
			} else {
				$is_records = true;
				$is_indexing = false;
				if ($status['nw_act'] < $status['nw_total']) {
					$is_optimization = true;
					//$is_slow_warning = true;
				} else {
					$is_optimization = false;
					if ($status['n_tw'] < 1) {
						$is_ok = true;
						//$is_slow_warning = false;
					}
				}

			}
		} else {
			$is_index_enabled = false;
		}

		$is_pause = intval($status['is_pause']);

		ob_start();
		?>
			<div style="display:block;position:absolute;right:0px;">
				<button type="button" class="btn btn-default btn-sm wpfts_set_pause_on wpfts_data_pause_btn_on" style="color:#888;display:<?php echo $is_pause ? 'none' : 'inline-block'; ?>;" title="<?php echo __('Pause', 'fulltext-search'); ?>"><i class="fa fa-pause"></i></button> 
				<button type="button" class="btn btn-default btn-sm wpfts_set_pause_off wpfts_data_pause_btn_off" style="color:green;display:<?php echo $is_pause ? 'inline-block' : 'none'; ?>;" title="<?php echo __('Continue Indexing', 'fulltext-search'); ?>"><i class="fa fa-play"></i></button>
				<div style="clear:both;"></div>
    		</div>
		<?php

		$pause_block = ob_get_clean();

		// Block Switchers
		$data_is_ok = ($is_ok && (!$is_pause)) ? 'block' : 'none';
		$data_is_paused_st = ($is_ok && $is_pause) ? 'block' : 'none';
		$data_is_index_disabled = !$is_index_enabled ? 'block' : 'none';
		$data_is_indexing = ($is_indexing && (!$is_pause)) ? 'block' : 'none';
		$data_is_indexing_paused = ($is_indexing && $is_pause) ? 'block' : 'none';
		$data_is_optimization = ($is_optimization && (!$is_pause)) ? 'block' : 'none';
		$data_is_optimization_paused = ($is_optimization && $is_pause) ? 'block' : 'none';

		$data_is_pending = $is_pending ? 'block' : 'none';
		$data_is_records = $is_records ? 'block' : 'none';

		$data_is_esttime = ($is_indexing) ? 'block' : 'none';
		$data_is_ready4changes = ((!$is_indexing) && (!$is_pause)) ? 'block' : 'none';
		$data_is_tempstopped = ((!$is_indexing) && $is_pause) ? 'block' : 'none';

		$data_est_time = $status['est_time'];

		$data_et_paused = ($is_indexing && $is_pause) ? 'inline-block' : 'none';
		$data_et_counting = ($is_indexing && (!$is_pause) && ($status['est_time'] == '--:--:--')) ? 'inline-block' : 'none';
		$data_et_esttime = ($is_indexing && (!$is_pause) && ($status['est_time'] != '--:--:--')) ? 'inline-block' : 'none';

		// Fill the template
		?>
		<div class="wpfts_ixst_body" id="wpfts_status_box">
		<div class="wpfts_ixst_row">
			<span class="wpfts_data_is_ok" style="display: <?php echo $data_is_ok; ?>;">
				<span class="wpfts_status_bullet wpfts_green">&#9679;</span>&nbsp;<?php echo __('Idle', 'fulltext-search'); ?>
			</span>
			<span class="wpfts_data_is_paused_st" style="display: <?php echo $data_is_paused_st; ?>;">
				<span class="wpfts_status_bullet wpfts_yellow"><i class="fa fa-pause" style="font-size: 0.7em;"></i></span>&nbsp;<?php echo __('Paused', 'fulltext-search'); ?>
			</span>
			<span class="wpfts_data_is_index_disabled" style="display: <?php echo $data_is_index_disabled; ?>;">
				<span class="wpfts_status_bullet wpfts_red">&#9679;</span>&nbsp;<?php echo __('Disabled', 'fulltext-search'); ?>
			</span>
			<span class="wpfts_data_is_indexing" style="display: <?php echo $data_is_indexing; ?>;">
				<img src="<?php echo $wpfts_core->root_url; ?>/style/waiting16_y.gif" alt="" title="<?php echo __('Indexing is in progress', 'fulltext-search'); ?>">&nbsp;<?php echo __('Indexing', 'fulltext-search'); ?>...&nbsp;<span class="wpfts_data_percent"><?php echo sprintf('%.2f', $percent).'%'; ?></span>
			</span>
			<span class="wpfts_data_is_indexing_paused" style="display: <?php echo $data_is_indexing_paused; ?>;">
				<span class="wpfts_status_bullet wpfts_yellow" title="<?php echo __('Indexing is temporary stopped', 'fulltext-search'); ?>"><i class="fa fa-pause" style="font-size: 0.7em;"></i></span>&nbsp;<?php echo __('Indexing is paused', 'fulltext-search'); ?>&nbsp;(<span class="wpfts_data_percent"><?php echo sprintf('%.2f', $percent).'%'; ?></span>)
			</span>
			<span class="wpfts_data_is_optimization" style="display: <?php echo $data_is_optimization; ?>;">
				<img src="<?php echo $wpfts_core->root_url; ?>/style/waiting16_y.gif" alt="" title="<?php echo __('Optimizing the index', 'fulltext-search'); ?>">&nbsp;<?php echo __('Optimizing', 'fulltext-search'); ?>...&nbsp;<span class="wpfts_data_percent2"><?php echo sprintf('%.2f', $percent2).'%'; ?></span>
			</span>
			<span class="wpfts_data_is_optimization_paused" style="display: <?php echo $data_is_optimization_paused; ?>;">
				<span class="wpfts_status_bullet wpfts_yellow" title="<?php echo __('Optimizing is temporary stopped', 'fulltext-search'); ?>"><i class="fa fa-pause" style="font-size: 0.7em;"></i></span>&nbsp;<?php echo __('Optimizing is paused', 'fulltext-search'); ?>&nbsp;(<span class="wpfts_data_percent2"><?php echo sprintf('%.2f', $percent2).'%'; ?></span>)
			</span>

		</div>
		<?php

		// Pending or Total
		?>
		<div class="wpfts_ixst_row">
			<span class="wpfts_data_is_pending" style="display:<?php echo $data_is_pending; ?>">
				<?php echo __('Pending', 'fulltext-search'); ?>: <b><span id="wpfts_st_pending" class="wpfts_data_n_pending"><?php echo $status['n_pending']; ?></span></b> <?php echo __('of', 'fulltext-search'); ?> <b><span id="wpfts_st_records" class="wpfts_data_n_inindex"><?php echo $status['n_inindex']; ?></span></b>
			</span>
			<span class="wpfts_data_is_records" style="display:<?php echo $data_is_records; ?>">
				<?php echo __('Records', 'fulltext-search'); ?>: <b><span id="wpfts_st_records2" class="wpfts_data_n_inindex"><?php echo $status['n_inindex']; ?></span></b>
			</span>
		</div>
		<?php
		// ETA and pause button

		?>
		<div class="wpfts_ixst_row" style="position: relative;">
			<?php echo $pause_block; ?>
			<span class="wpfts_data_is_esttime" style="display: <?php echo $data_is_esttime; ?>">
				<?php echo __('Est. Time: ', 'fulltext-search'); ?>
				<span class="wpfts_st_esttime wpfts_data_est_time wpfts_data_et_esttime" style="display: <?php echo $data_et_esttime; ?>"><?php echo $data_est_time; ?></span>
				<span class="wpfts_data_et_paused" style="display: <?php echo $data_et_paused; ?>">[<?php echo __('Paused', 'fulltext-search'); ?>]</span>
				<span class="wpfts_data_et_counting" style="display: <?php echo $data_et_counting; ?>"><?php echo __('Counting...', 'fulltext-search'); ?></span>
			</span>
			<span class="wpfts_data_is_ready4changes" style="display: <?php echo $data_is_ready4changes; ?>"><i>...<?php echo __('Ready for changes', 'fulltext-search'); ?>...</i></span>
			<span class="wpfts_data_is_tempstopped" style="color:#ff0;display: <?php echo $data_is_tempstopped; ?>"><i>...<?php echo __('Temporary stopped', 'fulltext-search'); ?>...</i></span>
		</div>
		</div>
			</div>
		</div>
		<?php

		$output = ob_get_clean();
		
		if ($is_return) {
			return $output;
		} else {
			echo $output;
		}
	}

	public function useful_box($post)
	{
		global $wpfts_core;
		
		ob_start();
		?>
		<div class="card mb-2">
			<div class="card-header bg-secondary text-white"><?php echo __('Useful Information', 'fulltext-search'); ?></div>
			<div class="card-body">
				<p><span class="lead"><?php echo sprintf(__('Current Version v%s', 'fulltext-search'), WPFTS_VERSION); ?></span></p>
				<p><a href="<?php echo $wpfts_core->_wpfts_domain; ?>" target="_blank"><?php echo __('WPFTS Home', 'fulltext-search'); ?></a></p>
				<p><a href="<?php echo $wpfts_core->_wpfts_domain.$wpfts_core->_documentation_link; ?>" target="_blank"><?php echo __('Documentation', 'fulltext-search'); ?></a></p>
				<p><?php echo __('Stucked? Get a solution or ask your question on', 'fulltext-search'); ?> <a href="<?php echo $wpfts_core->_wpfts_domain.$wpfts_core->_forum_link; ?>" target="_blank"><?php echo __('The WPFTS Community Forum', 'fulltext-search'); ?></a></p>
				<hr>
				<p><?php echo __('Do you need a feature?', 'fulltext-search'); ?> <a href="<?php echo $wpfts_core->_wpfts_domain.'/contact/'; ?>"><?php echo __('Request here.', 'fulltext-search'); ?></a></p>
				<p><a href="<?php echo $wpfts_core->_wpfts_domain.'/buy'; ?>" target="_blank" style="color:red;"><?php echo __('Get WPFTS Pro', 'fulltext-search'); ?></a></p>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	public function control_box($post = null) 
	{
		global $wpfts_core;
		
		$enabled = intval($wpfts_core->get_option('enabled'));
		$autoreindex = intval($wpfts_core->get_option('autoreindex'));
		$is_wpadmin = intval($wpfts_core->get_option('is_wpadmin'));
		$is_fixmariadb = intval($wpfts_core->get_option('is_fixmariadb'));
		$is_optimizer = intval($wpfts_core->get_option('is_optimizer'));
		
		ob_start();
		?>
		<div class="card mb-2 mt-4 wpfts_smartform" data-name="form_controlbox">
			<?php wp_nonce_field( 'wpfts_options_controlbox', 'wpfts_options-nonce_controlbox' ); ?>
			<div class="card-header bg-light"><div class="row"><span class="col-9"><?php echo __('Control Panel', 'fulltext-search'); ?></span><span class="col-3 text-right sf_savelink_place"></span></div></div>
			<div class="card-body">
				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Enable FullText Search', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150">
						<?php
						echo WPFTS_Htmltools::makeLabelledCheckbox('wpfts_enabled', 1, __('Enabled', 'fulltext-search'), $enabled);
						?>
					</div>
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint1" role="button" aria-expanded="false" aria-controls="wf_hint1"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint1">
						<p class="text-secondary"><i><?php echo __('If not enabled, the regular integrated "not indexed" WordPress search will be used', 'fulltext-search'); ?></i></p>
					</div>
				</div>

				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Auto-index', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150">
						<?php
						echo WPFTS_Htmltools::makeLabelledCheckbox('wpfts_autoreindex', 1, __('Enabled', 'fulltext-search'), $autoreindex);
						?>
					</div>	
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint2" role="button" aria-expanded="false" aria-controls="wf_hint2"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint2">
						<p class="text-secondary"><i><?php echo __('Normally, WP FullText Search will auto index any new post or post changes even if you disabled the previous option. Disabling this option will completely stop all plugin functions. However, you probably have to do a full index rebuild, if you activate the plugin again.', 'fulltext-search'); ?></i><br>
						<?php echo __('<strong>WARNING</strong>: Disabling this option is NOT recommended!', 'fulltext-search'); ?></p>
					</div>
				</div>

				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
					</div>
					<div class="col fixed-150 font-weight-bolder">
						<button type="button" class="btn btn-info btn-sm wpfts_btn_rebuild" name="wpfts_btn_rebuild" data-confirm="<?php echo htmlspecialchars(__('This action will completely rebuild the search index completely, which could take some time. Are you sure?', 'fulltext-search')); ?>"><?php echo __('Rebuild Index', 'fulltext-search'); ?></button>
						<span class="wpfts_show_resetting"><img src="<?php echo $wpfts_core->root_url; ?>/style/waiting16.gif" alt="">&nbsp;<?php echo __('Resetting', 'fulltext-search'); ?></span>
					</div>
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint3" role="button" aria-expanded="false" aria-controls="wf_hint3"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint3">
						<p class="text-secondary"><i><?php echo sprintf(__('Use this button when you need to completely rebuild search index database, for example, when you changed custom <b>wpfts_index_post</b> filter function. Remember that this operation could take a long time. Please refer for <a href="%s" target="_blank">documentation</a> for more information.', 'fulltext-search'), $wpfts_core->_wpfts_domain.$wpfts_core->_documentation_link); ?></i></p>
					</div>
				</div>				

				<div class="row">
					<div class="col col-12 mb-3">
						<hr>
						<h5>Experimental Options</h5>
					</div>
				</div>

				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Use in WP Admin', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150">
						<?php
						echo WPFTS_Htmltools::makeLabelledCheckbox('wpfts_is_wpadmin', 1, __('Enabled', 'fulltext-search'), $is_wpadmin);
						?>
					</div>	
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint4" role="button" aria-expanded="false" aria-controls="wf_hint4"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint4">
						<p class="text-secondary"><i><?php echo __('You can let the WPFTS plugin make searches inside WP Admin, however, this is an EXPERIMENTAL feature and can make some issues.', 'fulltext-search'); ?></i></p>
					</div>
				</div>
				
				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Fix MariaDB bug', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150">
						<?php
						echo WPFTS_Htmltools::makeLabelledCheckbox('wpfts_is_fixmariadb', 1, __('Enabled', 'fulltext-search'), $is_fixmariadb);
						?>
					</div>
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint5" role="button" aria-expanded="false" aria-controls="wf_hint5"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint5">
						<p class="text-secondary"><i><?php echo __('The server MariaDB v10.3+ has a known bug <a href="https://jira.mariadb.org/browse/MDEV-21614" target="_blank">#21614</a> where searches may sometimes give incorrect results or no results at all. This option fixes the problem by disabling the corresponding MariaDB algorithm. The option is irrelevant if your hosting uses a MySQL server.', 'fulltext-search'); ?></i></p>
					</div>
				</div>
				
				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Enable Index Optimizer', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150">
						<?php
						echo WPFTS_Htmltools::makeLabelledCheckbox('wpfts_is_optimizer', 1, __('Enabled', 'fulltext-search'), $is_optimizer);
						?>
					</div>
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint6" role="button" aria-expanded="false" aria-controls="wf_hint6"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint6">
						<p class="text-secondary"><i><?php echo __('The Index Optimizer may increase search speed by 30-50%, but it takes additional time for indexing and consumes an essential part of DB space, CPU, and RAM. If you have any hosting limitations on those resources, do not enable this option.', 'fulltext-search'); ?></i></p>
					</div>
				</div>
				
				<div class="sf_savebtn_place"></div>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	public function step1_query_filter($post)
	{
		global $wpfts_core;

		ob_start();
		?><div class="mb-2 mt-2 wpfts_smartform" data-name="form_step1_query_preprocessing">
			<?php wp_nonce_field( 'wpfts_options_step1_query_preprocessing', 'wpfts_options-nonce_step1_query_preprocessing' ); ?>
			<div class=""><div class="row"><span class="col-9"><h5><?php echo __('STEP #1: Query Preprocessing', 'fulltext-search'); ?></h5></span><span class="col-3 text-right sf_savelink_place"></span></div></div>
			<div class="bg-light">
				<p>
				<?php echo __('At the very first step, the plugin removes too short words, stop words and punctuation marks from the search phrase. After that, the phrase is broken into words.', 'fulltext-search'); ?>
				</p>
				<div class="row">
					<div class="col-12">
						<div class="bd-callout bg-white">

				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Internal Query Filter', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150">
						<?php
						$internal_sf = intval($wpfts_core->get_option('internal_search_terms'));
						echo WPFTS_Htmltools::makeLabelledCheckbox('wpfts_internal_search_terms', 1, __('Enabled', 'fulltext-search'), $internal_sf);
						?>
					</div>	
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint_intfilter" role="button" aria-expanded="false" aria-controls="wf_hint_intfilter"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint_intfilter">
						<p class="text-secondary"><i><?php echo __('Cleans up the query string before using it for search. Uncheck this only if you are using own text splitting algorithm.', 'fulltext-search'); ?></i></p>
					</div>
				</div>
							
						</div>
					</div>
				</div>
				<div class="sf_savebtn_place"></div>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	public function step2_find_records($post)
	{
		global $wpfts_core;

		$deflogic = $wpfts_core->get_option('deflogic');

		$deflogic_data = array(
			0 => 'AND',
			1 => 'OR',
		);
	
		ob_start();
		?><div class="mb-2 mt-2 wpfts_smartform" data-name="form_step2_find_records">
			<?php wp_nonce_field( 'wpfts_options_step2_find_records', 'wpfts_options-nonce_step2_find_records' ); ?>
			<div class=""><div class="row"><span class="col-9"><h5><?php echo __('STEP #2: Find Records', 'fulltext-search'); ?></h5></span><span class="col-3 text-right sf_savelink_place"></span></div></div>
			<div class="bg-light">
				<p>
				<?php echo __('Then, the algorithm effectively scans the index to find those entries in which the words (or parts of them) mentioned in the query are found.', 'fulltext-search'); ?>
				</p>
				<div class="row">
					<div class="col-12">
						<div class="bd-callout bg-white">
				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Default Search Logic', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150">
						<div class="wpfts_search_logic_group">
						<?php
							echo WPFTS_Htmltools::makeRadioGroup('wpfts_deflogic', $deflogic_data, $deflogic, array());
						?>
						</div>
					</div>	
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint_deflogic" role="button" aria-expanded="false" aria-controls="wf_hint_deflogic"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint_deflogic">
						<p class="text-secondary"><i><?php echo __('This option tells the search engine whether all query words should contain in the found post (AND) or any of these words (OR).', 'fulltext-search'); ?></i></p>
					</div>
				</div>

				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Deeper Search', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150">
						<?php
							$dps = intval($wpfts_core->get_option('deeper_search'));
							echo WPFTS_Htmltools::makeLabelledCheckbox('wpfts_deeper_search', 1, __('Enabled', 'fulltext-search'), $dps);
						?>
					</div>	
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint_deepersearch" role="button" aria-expanded="false" aria-controls="wf_hint_deepersearch"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint_deepersearch">
						<p class="text-secondary"><i><?php echo __('Enables searching substrings in the middle of words. This is much slower than usual search, so use it with care. Keep it disabled if you have any issues with MySQL performance.', 'fulltext-search'); ?></i></p>
					</div>
				</div>
				<?php
				
				ob_start();
				?>
				<div class="wpfts_pro_only">
				<div class="row">
					<div class="col-12">
						<p><i><?php echo sprintf(__('Options below available in %s Pro version %s only', 'fulltext-search'), '<a href="https://fulltextsearch.org/" target="_blank">', '</a>'); ?></i></p>
					</div>
				</div>

				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Search in File Contents', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150">
						<label for="lchwpfts_display_attachments"><input type="checkbox" value="1" id="lchwpfts_display_attachments" disabled="disabled">&nbsp;<span><?php echo __('Enabled', 'fulltext-search'); ?></span></label>
					</div>	
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint_filecontents" role="button" aria-expanded="false" aria-controls="wf_hint_filecontents"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint_filecontents">
						<p class="text-secondary"><i><?php echo __('When checked on, WPFTS will search attachments by contents and show them in search results like usual posts.', 'fulltext-search'); ?></i></p>
					</div>
				</div>

				<?php
				// Mime types justifications

				$mtg = $this->mimetype_groups;
				$mt_keys = array();
				foreach ($mtg as $k => $d) {
					if (count($d[1]) > 0) {
						foreach ($d[1] as $dd) {
							$mt_keys[$dd] = $k;
						}
					}
				}

				$enabled_mt = wp_get_mime_types();

				$used_mt = $wpfts_core->GetUsedMimetypes();

				// Detect non-registered mime-types
				$non_registered = $used_mt;
				foreach ($enabled_mt as $k => $d) {
					if (isset($non_registered[$d])) {
						unset($non_registered[$d]);
					}
				}
				foreach ($non_registered as $k => $d) {
					$enabled_mt[$k] = $k;
					$mt_keys[$k] = 11;
				}

				$listed_mt = array();
				
				$mt_stat = array();
				$mt_used_stat = array();
				foreach ($enabled_mt as $k => $d) {
					$ks = 10;
					$stt = &$mt_stat;
					$nn = 0;
					if (isset($mt_keys[$d])) {
						$ks = $mt_keys[$d];
					}
					if (isset($used_mt[$d])) {
						$stt = &$mt_used_stat;
						$nn = $used_mt[$d];
					}
					if (!isset($stt[$ks])) {
						$stt[$ks] = array();
					}
					$stt[$ks][$k] = array($d, $nn);
				}

				$render_group = function($group_id, $mt_stat) use ($mtg, $listed_mt) 
				{
					if (isset($mt_stat[$group_id]) && (count($mt_stat[$group_id]) > 0)) {
						?>
						<tr>
							<td><b><?php echo __($mtg[$group_id][0]); ?></b></td>
							<td>
							<?php 
							foreach ($mt_stat[$group_id] as $k => $tt) {
								$d = $tt[0];
								$nn = $tt[1];
								$key = 'ft_'.str_replace('|', '_', $k);
								$key_name = ($group_id != 11) ? mb_strtoupper(str_replace('|', ', ', $k)) : str_replace('|', ', ', $k);
							?>
							<div class="wpfts_mt_item"><label for="<?php echo $key; ?>" title="<?php echo htmlspecialchars($d); ?>"><input type="checkbox" value="<?php echo htmlspecialchars($d); ?>" id="<?php echo $key; ?>" disabled="disabled"> <?php echo htmlspecialchars($key_name); if ($nn > 0) { echo ' ('.$nn.')'; } ?></label></div>
							<?php
							}
							?>
							</td>
					</tr>
					<?php
				}
			};

			?><div class="ft_limit_filetypes">
				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Limit File Types', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150">
						<div style="padding: 0px 0px 15px 0px;"><label for="ft_mt_all"><input type="checkbox" id="ft_mt_all" value="1" disabled="disabled"> <?php echo __('Allow All', 'fulltext-search'); ?></label></div>
					</div>
				</div>
				<div class="row">
					<div class="col fixed-200 d-none d-xl-block">
					</div>
					<div class="col">
						<p><i><?php echo __('Alternatively, you can allow to show attachments with these file types only', 'fulltext-search'); ?></i></p>

						<div class="ft_used_mt">
						<h5><?php echo __('Currently used file types (amount of files found)', 'fulltext-search'); ?></h5>
						<table class="ft_used_mt_table ft_mt_table table table-striped">
						<col width="150">
						<col>
						<?php
						foreach (array(3,7,5,6,1,2,4,8,9,10,11) as $dd) {
							$render_group($dd, $mt_used_stat);
						}
						?>
						</table>
						<a href="#" class="ft_mt_show_extra_mimetypes"><?php echo __('Show All File Types &gt;&gt;', 'fulltext-search'); ?></a>
					</div>
					<div class="ft_selector" style="display:none;">
						<h5><?php echo __('Other File Formats (not used yet on this website)', 'fulltext-search'); ?></h5>
						<table class="ft_selector_table ft_mt_table table table-striped">
						<col width="150">
						<col>
						<?php
						foreach (array(3,7,5,6,1,2,4,8,9,10,11) as $dd) {
							$render_group($dd, $mt_stat);
						}
						?>
						</table>
					</div>
					</div>
				</div>
				</div>

			</div>
						<?php
						
						echo apply_filters('wpfts_out_mimetype_part', ob_get_clean(), $this);
						
						?>
						</div>
					</div>
				</div>
				<div class="sf_savebtn_place"></div>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	public function step3_relevance_box($post) 
	{
		global $wpfts_core;
		
		ob_start();
		?><div class="mb-2 mt-2 wpfts_smartform" data-name="form_step3_calculate_relevance">
			<?php wp_nonce_field( 'wpfts_options_step3_calculate_relevance', 'wpfts_options-nonce_step3_calculate_relevance' ); ?>
			<div class=""><div class="row"><span class="col-9"><h5><?php echo __('STEP #3: Calculate Relevance', 'fulltext-search'); ?></h5></span><span class="col-3 text-right sf_savelink_place"></span></div></div>
			<div class="bg-light">
				<p>
				<?php echo __('The relevance formula is based on the classic <a href="https://en.wikipedia.org/wiki/Tf-idf" target="_blank">TF-IDF</a> equation. You can justify the value by assigning some weights to specific clusters, post types or date ranges (*in development*) which will give you additional flexibility.', 'fulltext-search'); ?>
				</p>
				<div class="row">
					<div class="col-12">
						<div class="bd-callout bg-white">
				
				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Cluster Weights', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-350">
						<table class="table table-sm table-condensed">
						<thead class="thead-light">
						<tr>
							<th style="width: 1%;"><?php echo __('Weight', 'fulltext-search'); ?></th>
							<th><?php echo __('Cluster Name', 'fulltext-search'); ?></th>
						</tr>
						</thead>
						<?php
						
							$cluster_types = $wpfts_core->get_cluster_types();
							$cluster_weights = $wpfts_core->get_option('cluster_weights');

							$order_weights = array(
								'post_title' => 50,
								'post_content' => 30,
							);
							uasort($cluster_types, function ($v1, $v2) use ($order_weights) 
							{
								$w1 = isset($order_weights[$v1]) ? $order_weights[$v1] : 1;
								$w2 = isset($order_weights[$v2]) ? $order_weights[$v2] : 1;
								if ($w1 > $w2) {
									return -1;
								} else {
									if ($w1 < $w2) {
										return 1;
									} else {
										return strcasecmp($v1, $v2);
									}
								}
							});

							foreach ($cluster_types as $d) {
								$name = 'eclustertype_' . $d;
								$w = isset($cluster_weights[$d]) ? floatval($cluster_weights[$d]) : 0.5;
							
								echo '<tr><td>'.WPFTS_Htmltools::makeText($w, array('name' => $name, 'class' => 'wpfts_short_input60 text-sm')).'</td><td><label for="'.$name.'_id"><span>'.htmlspecialchars($d).'</span></label></td>';
							}
						?>
							
						</table>
					</div>	
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint_cw" role="button" aria-expanded="false" aria-controls="wf_hint_cw"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint_cw">
						<p class="text-secondary"><i><?php echo __('"Cluster" is a part of post (either title, content or even specific part which you can define using <b>wpfts_index_post</b> filter). You can assign some relevance weight to each of them.', 'fulltext-search'); ?></i></p>
					</div>
				</div>

						</div>
					</div>
				</div>

				<div class="sf_savebtn_place"></div>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	public function step4_ordering($post)
	{
		global $wpfts_core;
		
		ob_start();
		?><div class="mb-2 mt-2 wpfts_smartform" data-name="form_step4_sort_results">
			<?php wp_nonce_field( 'wpfts_options_step4_sort_results', 'wpfts_options-nonce_step4_sort_results' ); ?>
			<div class=""><div class="row"><span class="col-9"><h5><?php echo __('STEP #4: Sort Results', 'fulltext-search'); ?></h5></span><span class="col-3 text-right sf_savelink_place"></span></div></div>
			<div class="bg-light">
				<p>
				<?php echo __('To be useful, the results should be shown in the specified order. It\'s a good place to set it up.', 'fulltext-search'); ?>
				</p>
				<div class="row">
					<div class="col-12">
						<div class="bd-callout bg-white">

				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Search Order By', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-250">
						<?php
						$mainsearch_orderby = $wpfts_core->get_option('mainsearch_orderby');
						$a = array(
							'relevance' => __('Relevance (WP default)', 'fulltext-search'),
							'ID' => __('Post ID', 'fulltext-search'),
							'author' => __('Author', 'fulltext-search'),
							'title' => __('Title', 'fulltext-search'),
							'name' => __('Post Slug', 'fulltext-search'),
							'type' => __('Post Type', 'fulltext-search'),
							'date' => __('Created Date', 'fulltext-search'),
							'modified' => __('Modified Date', 'fulltext-search'),
							'parent' => __('Parent Post ID', 'fulltext-search'),
							'rand' => __('Random', 'fulltext-search'),
							'comment_count' => __('Comment Count', 'fulltext-search'),
						);
						echo WPFTS_Htmltools::makeSelect($a, $mainsearch_orderby, array('name' => 'wpfts_mainsearch_orderby'));
						?>
					</div>	
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint_orderby" role="button" aria-expanded="false" aria-controls="wf_hint_orderby"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint_orderby">
						<p class="text-secondary"><i><?php echo __('Search results will be ordered by selected field.', 'fulltext-search'); ?></i></p>
					</div>
				</div>
				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Search Order', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-250">
					<?php
						$mainsearch_order = $wpfts_core->get_option('mainsearch_order');
						$a = array(
							'DESC' => 'DESC',
							'ASC' => 'ASC',
						);
						echo WPFTS_Htmltools::makeSelect($a, $mainsearch_order, array('name' => 'wpfts_mainsearch_order'));
						?>
					</div>	
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint_orderd" role="button" aria-expanded="false" aria-controls="wf_hint_orderd"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint_orderd">
						<p class="text-secondary"><i><?php echo __('You can select the direction of sorting.', 'fulltext-search'); ?></i></p>
					</div>
				</div>							
						</div>
					</div>
				</div>
				<div class="sf_savebtn_place"></div>
				
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	public function step5_smart_excerpts_box($post)
	{
		global $wpfts_core;
		
		ob_start();
		?><div class="mb-2 mt-2 wpfts_smartform" data-name="form_step5_show_results">
			<?php wp_nonce_field( 'wpfts_options_step5_show_results', 'wpfts_options-nonce_step5_show_results' ); ?>
			<div class=""><div class="row"><span class="col-9"><h5><?php echo __('STEP #5: Show Results', 'fulltext-search'); ?></h5></span><span class="col-3 text-right sf_savelink_place"></span></div></div>
			<div class="bg-light">
				<p>
				<?php echo sprintf(__('WPFTS can output search results in a Google-like way - showing only sentences which contains search words and highlighting them. Wordpress by default does not show any content for result items if the items are attachments. Smart Excerpts function can output attachment content too. <a href="%s" target="_blank">Read more</a>.', 'fulltext-search'), 'https://fulltextsearch.org/documentation/#smart_excerpts'); ?>
				</p>
				<div class="row">
					<div class="col-12">
						<div class="bd-callout bg-white">

				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Enable Smart Excerpts', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150">
						<?php
						$is_smart_excerpts = intval($wpfts_core->get_option('is_smart_excerpts'));
						echo WPFTS_Htmltools::makeLabelledCheckbox('wpfts_is_smart_excerpts', 1, __('Enabled', 'fulltext-search'), $is_smart_excerpts);
						?>
					</div>	
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint_enable_se" role="button" aria-expanded="false" aria-controls="wf_hint_enable_se"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint_enable_se">
						<p class="text-secondary"><i><?php echo __('Replaces Wordpress excerpts by WPFTS Smart Excerpts in search results', 'fulltext-search'); ?></i></p>
					</div>
				</div>
				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Optimal Length', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150">
						<?php
						$optimal_length = intval($wpfts_core->get_option('optimal_length'));
						echo WPFTS_Htmltools::makeText($optimal_length, array('name' => 'wpfts_optimal_length', 'style' => 'width: 100%;'));
						?>
					</div>	
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint_optselength" role="button" aria-expanded="false" aria-controls="wf_hint_optselength"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint_optselength">
						<p class="text-secondary"><i><?php echo __('WPFTS will try to keep excerpt length between 90% and 110% of this value', 'fulltext-search'); ?></i></p>
					</div>
				</div>

				<div class="row mt-3-sm">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Include to excerpt:', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-250">
						<ul>
							<li><?php 
								$is_smart_excerpt_text = intval($wpfts_core->get_option('is_smart_excerpt_text'));
								echo WPFTS_Htmltools::makeLabelledCheckbox('wpfts_is_smart_excerpt_text', 1, __('Smart Excerpt text', 'fulltext-search'), $is_smart_excerpt_text);
								?>
							</li>
							<li><?php
								$is_show_score = intval($wpfts_core->get_option('is_show_score'));
								echo WPFTS_Htmltools::makeLabelledCheckbox('wpfts_is_show_score', 1, __('Score/Relevance', 'fulltext-search'), $is_show_score);
								?>
							</li>
							<li><?php
								$is_not_found_words = intval($wpfts_core->get_option('is_not_found_words'));
								echo WPFTS_Htmltools::makeLabelledCheckbox('wpfts_is_not_found_words', 1, __('"Not Found" words', 'fulltext-search'), $is_not_found_words);
								?>
							</li>
						</ul>
						<?php
						
						ob_start();
						?>
						<div class="wpfts_pro_only">
						<p class="mt-2 font-weight-bolder"><i><?php echo __('Attachments Only:', 'fulltext-search'); ?></i></p>
						<ul>
							<li><label for="lchwpfts_is_file_ext"><input type="checkbox" value="1" id="lchwpfts_is_file_ext" disabled="disabled">&nbsp;<span><?php echo __('File Extension', 'fulltext-search'); ?></span></label>
							</li>
							<li><label for="lchwpfts_is_filesize"><input type="checkbox" value="1" id="lchwpfts_is_filesize" disabled="disabled">&nbsp;<span><?php echo __('Filesize', 'fulltext-search'); ?></span></label>
							</li>
							<li><label for="lchwpfts_is_direct_link"><input type="checkbox" value="1" id="lchwpfts_is_direct_link" disabled="disabled">&nbsp;<span><?php echo __('Direct Download Link', 'fulltext-search'); ?></span></label>
							</li>
							<li><label for="lchwpfts_is_title_direct_link"><input type="checkbox" value="1" id="lchwpfts_is_title_direct_link" disabled="disabled">&nbsp;<span><?php echo __('Link Title to File Download', 'fulltext-search'); ?></span></label>
							</li>
						</ul>
						</div>
						<?php
						
						echo apply_filters('wpfts_out_smart_excerpts_files', ob_get_clean(), $this);
						
						?>
					</div>
					<div class="col col-xl col-12">
						<p><?php echo __('Demo Output:', 'fulltext-search'); ?> <span><a data-toggle="collapse" href="#wf_hint_dohint" role="button" aria-expanded="false" aria-controls="wf_hint_dohint"><i class="fa fa-info-circle"></i></a></span></p>
						<div class="collapse" id="wf_hint_dohint">
							<p class="text-secondary"><i><?php echo __('Optimal Length is ignored here', 'fulltext-search'); ?></i></p>
						</div>

						<?php
							$wpfts_result_item = new WPFTS_Result_Item();
							$wpfts_result_item->demo_mode = true;
						?>
						<div class="wpfts_smart_excerpts_preview mb-3">
							<h2><a href="<?php echo esc_url($wpfts_result_item->TitleLink()); ?>" rel="bookmark"><?php echo $wpfts_result_item->TitleText(); ?></a></h2>
							<div class="wpfts-result-item">
								<?php echo $wpfts_result_item->Excerpt(); ?>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Custom CSS Styling', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150 mb-3">
						<div class="btn btn-info btn-sm btn_se_style_preview mb-2"><?php echo __('Preview Styles', 'fulltext-search'); ?></div>
						<div class="btn btn-secondary btn-sm btn_se_style_reset"><?php echo __('Reset to Default', 'fulltext-search'); ?></div>
					</div>
					<div class="col col-xl col-12">
						<?php
							$custom_se_css = $wpfts_core->get_option('custom_se_css');
							echo '<div id="wpfts_se_styles_editor">'.$custom_se_css.'</div>';
						?>
						<textarea id="wpfts_se_styles_editor_hidden" name="wpfts_se_styles" style="display:none;"><?php echo $custom_se_css; ?></textarea>
						<i><?php echo __('This CSS snippet will be automatically minimized upon usage with a frontend.', 'fulltext-search'); ?></i>
						<?php echo '<style type="text/css" id="wpfts_se_styles_node">'.$wpfts_core->ReadSEStylesMinimized().'</style>'; ?>
					</div>
				</div>						

						</div>
					</div>
				</div>

				<p><i><?php echo __('Notice: this is a <b>beta version</b> of the Smart Excerpt function. In case it does not work for your theme/site, please do not hesistate to send us some information with screenshots and theme name <a href="https://fulltextsearch.org/contact/" target="_blank">here</a>.', 'fulltext-search'); ?></i></p>

				<div class="sf_savebtn_place"></div>
			</div>
		</div>

		<?php

		return ob_get_clean();
	}

	public function indexing_box($post) 
	{
		global $wpfts_core;
		
		//$minlen = intval($wpfts_core->get_option('minlen'));
		//$maxrepeat = intval($wpfts_core->get_option('maxrepeat'));
		$stopwords = $wpfts_core->get_option('stopwords');
		//$epostype = $wpfts_core->get_option('epostype');
		
		ob_start();
		?>
		<div class="wpfts_smartform bg-light" data-name="form_indexingbox">
			<?php wp_nonce_field( 'wpfts_options_indexingbox', 'wpfts_options-nonce_indexingbox' ); ?>
			<div class=""><div class="row"><span class="col-9"><h5><?php echo __('Indexing Defaults', 'fulltext-search'); ?></h5></span><span class="col-3 text-right sf_savelink_place"></span></div></div>
			<div>
				<p>
				<?php echo __('For maximum compatibility with the standard WordPress search, by default, we always index the title and the content of all posts, pages, and custom posts and put them in clusters "post_title" and "post_content", respectively. But this can be modified if required.', 'fulltext-search'); ?>
				</p>
				<div class="row">
					<div class="col-12">
						<div class="bd-callout bg-white">

				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Index Shortcodes Content', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150">
						<?php
						$content_open_shortcodes = intval($wpfts_core->get_option('content_open_shortcodes'));
						echo WPFTS_Htmltools::makeLabelledCheckbox('wpfts_content_open_shortcodes', 1, __('Enabled', 'fulltext-search'), $content_open_shortcodes);
						?>
					</div>	
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint_ie_striptags" role="button" aria-expanded="false" aria-controls="wf_hint_ie_striptags"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint_ie_striptags">
						<p class="text-secondary"><i><?php echo __('Renders registered shortcodes in the <code>post_content</code> before indexing.', 'fulltext-search'); ?></i></p>
					</div>
				</div>

				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Remove Non-Text HTML Nodes', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150">
						<?php
						$is_remove_html_nodes = intval($wpfts_core->get_option('content_is_remove_nodes'));
						echo WPFTS_Htmltools::makeLabelledCheckbox('wpfts_content_is_remove_nodes', 1, __('Enabled', 'fulltext-search'), $is_remove_html_nodes);
						?>
					</div>	
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint_ie_removenodes" role="button" aria-expanded="false" aria-controls="wf_hint_ie_removenodes"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint_ie_removenodes">
						<p class="text-secondary"><i><?php echo __('Removes <code>style</code> and <code>script</code> HTML nodes with their content.', 'fulltext-search'); ?></i></p>
					</div>
				</div>

				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Strip HTML Tags From Post Contents', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150">
						<?php
						$content_strip_tags = intval($wpfts_core->get_option('content_strip_tags'));
						echo WPFTS_Htmltools::makeLabelledCheckbox('wpfts_content_strip_tags', 1, __('Enabled', 'fulltext-search'), $content_strip_tags);
						?>
					</div>	
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint_ie_striptags" role="button" aria-expanded="false" aria-controls="wf_hint_ie_striptags"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint_ie_striptags">
						<p class="text-secondary"><i><?php echo __('Removes HTML tags and comments from the <code>post_content</code> while indexing (useful for Gutenberg-driven sites).', 'fulltext-search'); ?></i></p>
					</div>
				</div>
				<?php
				
				ob_start();
				
				?>
				<div class="wpfts_pro_only">
				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Include Attachments', 'fulltext-search'); ?><br><?php echo __('(Available in Pro only)', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-150">
						<label for="lchwpfts_include_attachments"><input type="checkbox" value="1" id="lchwpfts_include_attachments" disabled="disabled">&nbsp;<span><?php echo __('Enabled', 'fulltext-search'); ?></span></label>
					</div>
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint_ie_incatt" role="button" aria-expanded="false" aria-controls="wf_hint_ie_incatt"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint_ie_incatt">
						<p class="text-secondary"><i><?php echo __('Allow for posts to be searchable by the content of their attached files. When enabled, this option will include attachments\' index to their parent post indexes.', 'fulltext-search'); ?></i></p>
					</div>
				</div>
				</div>
				<?php

				echo apply_filters('wpfts_out_include_attachments', ob_get_clean(), $this);
				
				?>
						</div>
					</div>
				</div>
				
				<div class="sf_savebtn_place"></div>
			</div>
		</div>

		
			<?php /*
			<tr>
				<th><?php echo __('Stop Words', 'fulltext-search'); ?></th>
				<td colspan="2">
					<p><?php echo __('A comma-separated list of custom stop words', 'fulltext-search'); ?></p>
					<div>
					<?php
						echo WPFTS_Htmltools::makeTextarea(
								$stopwords, array('name' => 'wpfts_stopwords', 'class' => 'wpfts_long_textarea', 'placeholder' => __('the, a, an, ...etc', 'fulltext-search'))
						);
					?>
					</div>
				</td>
			</tr>
			*/ ?>
			
			
			<?php
			/*
			<tr>
				<th><?php echo __('Exclude Post Types', 'fulltext-search'); ?></th>
				<td colspan="2">
					<p>Check post types which will be excluded from index</p>
					<div class="wpfts_scroller">
					<?php
						
						$post_types = $wpfts_core->get_post_types();

						foreach ($post_types as $k => $d) {
							$name = 'epostype_' . $k;
							echo WPFTS_Htmltools::makeLabelledCheckbox($name, 1, $d . ' (' . $k . ')', (isset($epostype[$k]) && ($epostype[$k])) ? 1 : 0);
						}
					?>
					</div>
					<p><i>(To search for posts of selected post types built-in WordPress algorithm will be used.)</i></p>
				</td>
			</tr>
			*/
			?>
		
		<?php

		return ob_get_clean();
	}

	public function extraction_box($post)
	{
		ob_start();
		?>
		<div class="wpfts_smartform" data-name="form_extractionbox">
			<?php wp_nonce_field( 'wpfts_options_extractionbox', 'wpfts_options-nonce_extractionbox' ); ?>
			<div class=""><div class="row"><span class="col-9"><h5><?php echo __('File Extraction Rules', 'fulltext-search'); ?></h5></span><span class="col-3 text-right sf_savelink_place"></span></div></div>
			<div class="bg-light">
				<p><i><?php echo __('This option is available in Pro version only', 'fulltext-search'); ?></i></p>
				<div class="wpfts_pro_only">
				<p><?php echo sprintf(__('To search for files by their contents, the plugin places the text extracted from them into the search index. Extracting text is quite a resource-intensive operation, and the power of your server may not be enough for most file types. Therefore, we developed an external service (%s) that produces this work efficiently and quickly.', 'fulltext-search'), '<a href="https://textmill.io/">Textmill.io</a>'); ?></p>
				<p><?php echo __('For a number of reasons, you can refuse to use this service, but in this case, the conversion quality and the number of supported file types will be significantly less.', 'fulltext-search'); ?></p>
				<div class="row">
					<div class="col-12">
						<div class="bd-callout bg-white">
				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Extraction Engine', 'fulltext-search'); ?>
					</div>
					<div class="col fixed-250">
						<div class="wpfts_search_logic_group">
							<label for="rgwpfts_extraction_engine_1">
								<input type="radio" id="rgwpfts_extraction_engine_1" value="1" disabled="disabled">&nbsp;<?php echo __('TextMill.io then Native PHP', 'fulltext-search'); ?></label><label for="rgwpfts_extraction_engine_0">
								<input type="radio" id="rgwpfts_extraction_engine_0" value="0" disabled="disabled">&nbsp;<?php echo __('Native PHP only', 'fulltext-search'); ?></label>						
						</div>
					</div>
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint_exteng" role="button" aria-expanded="false" aria-controls="wf_hint_exteng"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint_exteng">
						<p class="text-secondary"><i><?php echo __('<a href="https://textmill.io/">TextMill.io</a> is an external processing service, which supports a wide range of formats. Native PHP means local processing of attachments. We recommend <a href="https://textmill.io/">TextMill.io</a> at the moment.', 'fulltext-search'); ?></i></p>
						<p class="text-secondary"><i><?php echo __('Note: Native PHP only supports medium-quality PDF parsing at the moment. Plain-text based formats (TXT, CSS, HTML, HTM etc) are always processing by Native PHP.', 'fulltext-search'); ?></i></p>
					</div>
				</div>
						</div>
					</div>
				</div>
				<div class="sf_savebtn_place"></div>

				</div>					

			</div>
		</div>


		<?php

		return apply_filters('wpfts_out_extraction_box', ob_get_clean(), $this);
	}

	public function index_engine_tester($post) 
	{
		global $wpfts_core;

		$testpostid = $wpfts_core->get_option('testpostid');

		ob_start();
		?><div class="mb-2 mt-2" id="form_indextester">
			<?php wp_nonce_field( 'wpfts_options_indextester', 'wpfts_options-nonce_indextester' ); ?>
			<div class=""><h5><?php echo __('Index Engine Tester', 'fulltext-search'); ?></h5></div>
			<div class="bg-light">
				<p><?php echo __('Before the data from your posts (pages, meta-fields, etc.) gets into the Search Index, they go through a number of built-in WPFTS filters, including a custom hook <code>wpfts_index_post</code>. Enter the ID of any WordPress record to see what data will come to the Search Index.', 'fulltext-search'); ?></p>
				<div class="row">
					<div class="col-12">
						<div class="bd-callout bg-white">
				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Post ID', 'fulltext-search'); ?>
					</div>
					<div class="col">
						<div class="form-row">
						<?php
							echo WPFTS_Htmltools::makeText($testpostid, array('name' => 'wpfts_testpostid', 'class' => 'wpfts_middle_input form-control', 'style' => 'width:150px;'));
						?>
						<?php
							echo WPFTS_Htmltools::makeButton(__('Test Filter', 'fulltext-search'), array('id' => 'wpfts_testbutton', 'type' => 'button', 'class' => 'btn btn-info'));
						?></div>
					</div>	
					<div class="col d-xl-none text-right">
						<p><a data-toggle="collapse" href="#wf_hint_testfilter" role="button" aria-expanded="false" aria-controls="wf_hint_testfilter"><i class="fa fa-info-circle"></i></a></p>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint_testfilter">
						<p class="text-secondary"><i><a href="#wf_hint_testfilter2" data-toggle="collapse" aria-expanded="false" aria-controls="wf_hint_testfilter2"><?php echo __('Where do I get this Post ID?', 'fulltext-search'); ?></a></i></p>
						<div class="collapse" id="wf_hint_testfilter2">
							<p class="text-secondary"><i><?php echo __('Open any Edit Post page, check the URL and find <code>post=<b>&lt;number&gt;</b></code> part there. The <b>&lt;number&gt;</b> is an actual Post ID', 'fulltext-search'); ?></i></p>
						</div>
					</div>
				</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-12" id="wpfts_test_filter_output">

					</div>
				</div>

			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	public function search_tester($post)
	{
		global $wpfts_core, $wpdb;
		
		$testquery = $wpfts_core->get_option('testquery');
		$tq_disable = $wpfts_core->get_option('tq_disable');
		$tq_nocache = $wpfts_core->get_option('tq_nocache');
		$tq_post_status = $wpfts_core->get_option('tq_post_status');
		$tq_post_type = $wpfts_core->get_option('tq_post_type');
		
		$post_statuses = array(
			'any' => __('* (Any)', 'fulltext-search'),
			'publish' => __('publish (Published)', 'fulltext-search'),
			'future' => __('future (Future)', 'fulltext-search'),
			'draft' => __('draft (Draft)', 'fulltext-search'),
			'pending' => __('pending (Pending)', 'fulltext-search'),
			'private' => __('private (Private)', 'fulltext-search'),
			'trash' => __('trash (Trash)', 'fulltext-search'),
			'auto-draft' => __('auto-draft (Auto-Draft)', 'fulltext-search'),
			'inherit' => __('inherit (Inherit)', 'fulltext-search'),
		);
		
		$q = 'select distinct post_type from `'.$wpdb->posts.'` order by post_type asc';
		$res = $wpfts_core->db->get_results($q, ARRAY_A);
		
		$post_types = array('any' => __('* (Any)', 'fulltext-search'));
		foreach ($res as $d) {
			$post_types[$d['post_type']] = $d['post_type'];
		}
		
		ob_start();
		?>
		<div class="mb-2 mt-2" id="form_searchtester">
			<?php wp_nonce_field( 'wpfts_options_searchtester', 'wpfts_options-nonce_searchtester' ); ?>
			<div class=""><h5><?php echo __('Search Tester', 'fulltext-search'); ?></h5></div>
			<div class="bg-light">
				<p>
				<?php echo __('You can test search with any query here. Standard wordpress <b>WP_Query</b> object with WPFTS features will be used.', 'fulltext-search'); ?>
				</p>
				<div class="row">
					<div class="col-12">
						<div class="bd-callout bg-white">
						<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Query', 'fulltext-search'); ?>
					</div>
					<div class="col">
						<div class="form-row">
						<?php
							echo WPFTS_Htmltools::makeText($testquery, array('name' => 'wpfts_testquery', 'class' => 'wpfts_middle_input form-control', 'style' => 'width:150px;'));
						?>
						<?php
							echo WPFTS_Htmltools::makeButton(__('Test Search', 'fulltext-search'), array('id' => 'wpfts_testquerybutton', 'type' => 'button', 'class' => 'btn btn-info'));
						?></div>
					</div>
					<div class="col d-xl-none text-right">
						<?php /*
						<p><a data-toggle="collapse" href="#wf_hint_testfilter" role="button" aria-expanded="false" aria-controls="wf_hint_testfilter"><i class="fa fa-info-circle"></i></a></p>
						*/ ?>
					</div>
					<div class="col col-xl col-12 d-xl-block collapse" id="wf_hint_testfilter">
						<?php /*
						<p class="text-secondary"><i><a href="#wf_hint_testfilter2" data-toggle="collapse" aria-expanded="false" aria-controls="wf_hint_testfilter2"><?php echo __('Where do I get this Post ID?', 'fulltext-search'); ?></a></i></p>
						<div class="collapse" id="wf_hint_testfilter2">
							<p class="text-secondary"><i><?php echo __('Open any Edit Post page, check the URL and find <code>post=<b>&lt;number&gt;</b></code> part there. The <b>&lt;number&gt;</b> is an actual Post ID', 'fulltext-search'); ?></i></p>
						</div>
						*/ ?>
					</div>
				</div>
				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
					</div>
					<div class="col">
						<div class="mt-2">
							<p><b><?php echo __('Additional Options', 'fulltext-search'); ?></b></p>
							<div class="row">
								<div class="col-12">
								<span style="margin-right: 20px;"><?php
									echo WPFTS_Htmltools::makeCheckbox($tq_disable, array('id' => 'wpfts_tq_disable', 'name' => 'wpfts_tq_disable', 'class' => 'wpfts_middle_input', 'value' => 1), '&nbsp;'.__('Disable WPFTS', 'fulltext-search'));
								?></span>
								<span style="margin-right: 20px;"><?php
									echo WPFTS_Htmltools::makeCheckbox($tq_nocache, array('id' => 'wpfts_tq_nocache', 'name' => 'wpfts_tq_nocache', 'class' => 'wpfts_middle_input', 'value' => 1), '&nbsp;'.__('Disable SQL cache', 'fulltext-search'));
								?></span>
								</div>
							</div>
							
							<div class="row">
								<div class="col-12 col-sm-12 col-md-12 col-lg-6">
								<span style="margin-right: 20px;"><?php
									echo __('Post Type:', 'fulltext-search').'&nbsp;'; 
									echo WPFTS_Htmltools::makeSelect($post_types, $tq_post_type, array('id' => 'wpfts_tq_post_type', 'name' => 'wpfts_tq_post_type', 'class' => 'wpfts_middle_input form-control'));
								?></span>
								</div>
								<div class="col-12 col-sm-12 col-md-12 col-lg-6">
								<span style="margin-right: 20px;"><?php
									echo __('Post Status:', 'fulltext-search').'&nbsp;';
									echo WPFTS_Htmltools::makeSelect($post_statuses, $tq_post_status, array('id' => 'wpfts_tq_post_status', 'name' => 'wpfts_tq_post_status', 'class' => 'wpfts_middle_input form-control'));
								?></span>
								</div>
							</div>
						</div>

					</div>	
					
				</div>							
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-12" id="wpfts_test_search_output">

					</div>
				</div>


			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	public function licensing_box($post)
	{
		$t = WPFTS_Updater::get_ext_status();

		$lic_v = isset($t['is_active']) ? $t['is_active'] : 0;
		//$is_url_fopen = isset($t['is_url_fopen']) ? $t['is_url_fopen'] : 0;
		$is_api_ok = isset($t['is_api_ok']) ? $t['is_api_ok'] : 0;
		$tm_data_lic = isset($t['tm_data_lic']) ? $t['tm_data_lic'] : array();
		$is_expired = isset($t['is_expired']) ? $t['is_expired'] : 0;
		$is_eval = isset($t['is_eval']) ? $t['is_eval'] : 0;
		$renew_link = isset($t['upgrade_url']) ? $t['upgrade_url'] : '#';

		$email = WPFTS_Updater::get_subscription_email();

		ob_start();
		?>
		<div class="card mb-2 mt-2" id="form_licensing">
			<?php wp_nonce_field( 'wpfts_options_licensing', 'wpfts_options-nonce_licensing' ); ?>
			<div class="card-header bg-secondary text-white"><?php echo __('Licensing', 'fulltext-search'); ?></div>
			<div class="card-body bg-light">
				<div class="row">
					<div class="col-12">

					<?php if(!$lic_v)
					{
						?><div class="bd-callout bd-callout-danger bg-white text-danger" style="background-color: #ffebed !important;">
							<p><?php _e('<b>UPDATES UNAVAILABLE!</b> Please enter your License Key below to enable automatic updates.', 'fulltext-search'); ?>
							&nbsp;<a href="<?php echo WPFTS_Updater::get_upgrade_url( array( 'utm_source' => 'external', 'utm_medium' => 'wpfts', 'utm_campaign' => 'settings-page' ) ); ?>" target="_blank"><?php _e('Upgrade Now', 'fulltext-search'); ?> &raquo;</a></p>
							<?php if(is_multisite()) { ?>
							<p>
								<strong><?php _e( 'NOTE:', 'fulltext-search' ); ?></strong> <?php _e('This applies to all sites on the network.', 'fulltext-search'); ?>
							</p>
							<?php } ?>
							</div>
						<?php 
					}
					?>
						<h4 class="wpfts-settings-form-header">
						<?php 
						if($lic_v) {
							_e('Updates and Support Subscription is <span class="text-success">Active</span>', 'fulltext-search');
						} else { 
							_e('Updates and Support Subscription is <span class="text-danger">Disabled</span>', 'fulltext-search');
						} 
						?>
						</h4>
						
						<?php
							// Check if we have allow_url_fopen
							//if ($is_url_fopen) {
								// This is enabled
								if ($is_api_ok) {
									// API is accessible
									if ($is_expired) {
										if ($is_eval) {
											$error_message = sprintf(__('This evaluation key has expired. Get Pro key <a href="%s">now</a>.', 'fulltext-search'), 'https://fulltextsearch.org/buy/');
										} else {
											$error_message = sprintf(__('This key has expired. <a href="%s">Renew now</a>', 'fulltext-search'), $renew_link);
										}
									} else {
										$error_message = sprintf(__('This key is not valid. Get valid one <a href="%s">here</a>.', 'fulltext-search'), 'https://fulltextsearch.org/buy/');
									}
								} else {
									// Show a message
									?>
										<div class="bd-callout bd-callout-danger bg-light">
										<?php
										echo __('Warning! We can not connect to WPFTS server. There can be some reasons: either your network connection is not active, or your server is behind the firewall or domain <b>fulltextsearch.org</b> or it\'s IP is blacklisted.<br>We recommend you to contact your server administrator to help you with this.', 'fulltext-search');
										?>
										</div>
									<?php

									$error_message = __('Update API is not accessible', 'fulltext-search');
								}
							/*} else {
								// Show a message
								?>
								<div class="bd-callout bd-callout-danger bg-light">
									<?php
									echo __('Warning! You have disabled external URL access in PHP configuration, thus license key validation and automatic uploads are impossible.<br>Please set <b>allow_url_fopen = 1</b> in your <b>php.ini</b> to fix this.', 'fulltext-search');
									?>
								</div>
								<?php

								$error_message = __('Update API is not accessible', 'fulltext-search');
							}*/
						?>
					</div>
				</div>

				<div class="row">
					<div class="col-12">
						<div class="bd-callout bg-white">

				<div class="row">
					<div class="col fixed-200 font-weight-bolder">
						<?php echo __('Your License Key', 'fulltext-search'); ?>
					</div>
					<div class="col">
						<div class="form-group">
							<div class="input-group">
								<input type="password" name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control" />
								<div class="input-group-append">
									<div class="btn btn-info wpfts_submit4"><?php echo __('Save', 'fulltext-search'); ?></div>
								</div>
							</div>
							<?php
								if ((strlen($email) > 0) && (!$lic_v)) {
									echo '<span class="text-danger">'.$error_message.'</span>';
								}
								?>

						</div>
					</div>
				</div>

						</div>
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-12">
						<div class="bd-callout bg-white">
							<h5><div style="width:24px;height:24px;;display:inline-block;" title="The Meaning of Your File">
									<?php
										include dirname(__FILE__).'/templates/textmillio_icon.svg'; 
									?>
									</div> <?php echo __('<a href="https://textmill.io" target="_blank">TextMill.io</a> License Information', 'fulltext-search'); ?></h5>

									<table class="table table-sm table-hover table-bordered">
						<tr>
							<th class="bg-secondary text-white"><?php echo __('Status', 'fulltext-search'); ?></th>
							<td><?php echo (isset($tm_data_lic['status'])) ? $tm_data_lic['status'] : __('Disabled', 'fulltext-search'); ?></td>
						</tr>
						<tr>
							<th class="bg-secondary text-white"><?php echo __('Current Plan', 'fulltext-search'); ?></th>
							<td><?php echo (isset($tm_data_lic['plan'])) ? $tm_data_lic['plan'] : '-'; ?></td>
						</tr>
						<tr>
							<th class="bg-secondary text-white"><?php echo __('Active Till', 'fulltext-search'); ?></th>
							<td><?php 
								$exp_ts = isset($tm_data_lic['active_till']) ? strtotime($tm_data_lic['active_till']) : 0;
								$days_left = ($exp_ts - current_time('timestamp')) / (3600 * 24);
								$s_days = '';
								if ($days_left > 0) {
									$s_days = ' ('.sprintf(__('%s days left', 'fulltext-search'), floor($days_left)).')';
									if ($days_left <= 30) {
										$s_days = '<span style="color: red;">'.$s_days.'</span>';
									}
								}
								echo ((isset($tm_data_lic['active_till'])) ? date(get_option( 'date_format' ).' H:i', $exp_ts) : '-').$s_days; 
							?></td>
						</tr>
						<?php
						if (isset($tm_data_lic['hint'])) {
						?>
						<tr>
							<th class="bg-secondary text-white"><?php echo __('Hint', 'fulltext-search'); ?></th>
							<td><span class="text-danger"><?php echo $tm_data_lic['hint']; ?></span></td>
						</tr>
						<?php
						}
						?>
						</table>
						</div>

					</div>
				</div>

			</div>
		</div>
		<?php

		return ob_get_clean();
	}	
}
