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

$out = new WPFTS_Output();

?>
			<h4><?php echo __('Indexing Engine Settings', 'fulltext-search'); ?></h4>
			<form method="post" id="wpftsi_form2">
				<div class="row">
					<div class="col-12">

						<div class="bd-callout bd-callout-info bg-white">
							<p><?php echo __('Initially, the plugin collects data from the database of your website (posts, pages, custom type posts, file contents, taxonometry, meta fields etc) and places them in a special structure called the <b>Search Index</b>.', 'fulltext-search'); ?></p>
							<p><?php echo __('On this page, you indicate what data the plugin should collect, how to convert it, and in which index sections (clusters) to place.', 'fulltext-search'); ?></p>
							<p class="text-info"><?php echo __('Note: These changes <b>will not update</b> the current Search Index automatically, you need to start reindex manually.', 'fulltext-search'); ?></p>
						</div>
					</div>
				</div>

				<div style="background: #f8f9fa;">
				<ul class="nav nav-tabs mb-3 nav-tabs-inv" id="pills-tab-indexing" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="pills-indexing_box-tab" data-toggle="pill" href="#pills-indexing_box" role="tab" aria-controls="pills-indexing_box" aria-selected="true"><?php echo __('Indexing Defaults', 'fulltext-search'); ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="pills-extraction_box-tab" data-toggle="pill" href="#pills-extraction_box" role="tab" aria-controls="pills-extraction_box" aria-selected="false"><?php echo __('File Extraction Rules', 'fulltext-search'); ?></a>
					</li>
				</ul>
				<div class="tab-content" id="pills-tab-indexingContent">
  					<div class="tab-pane show active p-3" id="pills-indexing_box" role="tabpanel" aria-labelledby="pills-indexing_box-tab">
						<?php echo $out->indexing_box(null); ?>
					</div>
  					<div class="tab-pane p-3" id="pills-extraction_box" role="tabpanel" aria-labelledby="pills-extraction_box-tab">
						<?php echo $out->extraction_box(null); ?>
					</div>
				</div>
				</div>
			</form>
<?php
