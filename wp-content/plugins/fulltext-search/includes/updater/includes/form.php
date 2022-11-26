<?php

/**  
 * Copyright 2013-2018 Epsiloncool
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
 *  @copyright 2013-2018
 *  @license GPL v3
 *  @package Wordpress Fulltext Search Pro
 *  @author Epsiloncool <info@e-wm.org>
 */

?><div class="wrap">

	<?php if(!$status) : ?>
	<p style="padding:10px 20px; background: #d54e21; color: #fff;">
		<?php _e('UPDATES UNAVAILABLE! Please subscribe or enter your license key below to enable automatic updates.', 'fulltext-search'); ?>
		&nbsp;<a style="color: #fff;" href="<?php echo WPFTS_Update::get_upgrade_url( array( 'utm_source' => 'external', 'utm_medium' => 'wpfts', 'utm_campaign' => 'settings-page' ) ); ?>" target="_blank"><?php _e('Subscribe Now', 'fulltext-search'); ?> &raquo;</a>
	</p>
	<?php endif; ?>

	<h3 class="wpfts-settings-form-header">
		<?php _e('Updates &amp; Support Subscription', 'fulltext-search'); ?>
		<span> &mdash; </span>
		<?php if($status) : ?>
		<i style="color:#3cb341;"><?php _e('Active!', 'fulltext-search'); ?></i>
		<?php else : ?>
		<i style="color:#ae5842;"><?php _e('Not Active!', 'fulltext-search'); ?></i>
		<?php endif; ?>
	</h3>

	<?php if(isset($_POST['wpfts-updater-nonce'])) : ?>
	<div class="updated">
		<p><?php _e('License key saved!', 'fulltext-search'); ?></p>
	</div>
	<?php endif; ?>

	<p>
		<?php echo sprintf( __( 'Enter your <a%s>license key</a> to enable remote updates and support.', 'fulltext-search' ), ' href="https://fulltextsearch.org/my-account/?utm_source=external&utm_medium=wpfts&utm_campaign=settings-page" target="_blank"' ) ?>
	</p>
	<?php if(is_multisite()) : ?>
	<p>
		<strong><?php _e( 'NOTE:', 'fulltext-search' ); ?></strong> <?php _e('This applies to all sites on the network.', 'fulltext-search'); ?>
	</p>
	<?php endif; ?>
	<form action="" method="post">

		<input type="password" name="email" value="<?php echo self::get_subscription_email(); ?>" class="regular-text" />

		<p class="submit">
			<input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Subscription Settings', 'fulltext-search' ); ?>">
			<?php wp_nonce_field('updater-nonce', 'wpfts-updater-nonce'); ?>
		</p>
	</form>

</div>