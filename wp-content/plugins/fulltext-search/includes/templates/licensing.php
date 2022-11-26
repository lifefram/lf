<?php

require_once dirname(__FILE__).'/../wpfts_output.php';

$out = new WPFTS_Output();

?>
			<form method="post" id="wpftsi_form4">

				<div class="row">
					<div class="col-12">
						<div class="bd-callout bd-callout-info bg-white">
							<p><?php echo __('Licensing your copy of the WPFTS Pro is necessary for the normal operation of Textmill.io, receiving regular product updates and first-class technical support. Make sure that the license code is entered correctly and that it is in Active state.', 'fulltext-search'); ?></p>
							<p><?php echo __('Don\'t have a license code? <a href="https://fulltextsearch.org/?utm_source=extwp&utm_medium=wpfts&utm_campaign=licensinghint" target="_blank">Get yours here</a>.', 'fulltext-search'); ?></p>
						</div>
					</div>
				</div>

				<?php
					echo $out->licensing_box(null);
				?>
			</form>

			<div class="wrap">
	
				<?php if(isset($_POST['wpfts-updater-nonce'])) : ?>
				<div class="updated">
					<p><?php _e('License key saved!', 'fulltext-search'); ?></p>
				</div>
				<?php endif; ?>
	
				<form action="" method="post">
	
				</form>
			</div>
<?php
