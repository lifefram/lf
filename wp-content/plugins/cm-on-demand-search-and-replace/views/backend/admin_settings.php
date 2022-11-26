<?php if ( !empty( $messages ) ): ?>
	<div class="updated" style="clear:both"><p><?php echo $messages; ?></p></div>
<?php endif; ?>

<br/>

<br/>

<div class="cminds_settings_description">
	<?php
	echo do_shortcode( '[cminds_free_activation id="cmodsar"]' );
	?>

    <form method="post">
        <div>
            <div class="cmodsar_field_help_container">Warning! This option will completely erase all of the data stored by the CM On Demand Search And Replace in the database! <br/> It cannot be reverted.</div>
            <input onclick="return confirm( 'All database items of CM On Demand Search And Replace (terms, options etc.) will be erased. This cannot be reverted.' )" type="submit" name="cmodsar_pluginCleanup" value="Cleanup database" class="button cmodsar-cleanup-button"/>
            <span style="display: inline-block;position: relative;"></span>
        </div>
    </form>

	<?php
// check permalink settings
	if ( get_option( 'permalink_structure' ) == '' ) {
		echo '<span style="color:red">Your WordPress Permalinks needs to be set to allow plugin to work correctly. Please Go to <a href="' . admin_url() . 'options-permalink.php" target="new">Settings->Permalinks</a> to set Permalinks to Post Name.</span><br><br>';
	}
	?>

</div>

<?php
//include plugin_dir_path(__FILE__) . '/call_to_action.phtml';
?>

<br/>
<div class="clear"></div>

<form method="post">
	<?php wp_nonce_field( 'update-options' ); ?>
    <input type="hidden" name="action" value="update" />


    <div id="cmodsar_tabs" class="customSettingsTabs">
        <div class="custom_loading"></div>

		<?php
		CMODSAR_Base::renderSettingsTabsControls();

		CMODSAR_Base::renderSettingsTabs();
		?>
        <div id="tabs-55">
            <div class='block'>

              <table class="form-table"><tbody>
                        <tr>
                            <td><?php echo do_shortcode( '[cminds_upgrade_box id="cmodsar"]' ); ?></td>
                        </tr>
                    </tbody></table>

            </div>
        </div>

        <div id="tabs-99">
            <div class='block'>

                 <table class="form-table"><tbody>
                        <tr>
                            <td><?php echo do_shortcode( '[cminds_free_guide id="cmodsar"]' ); ?></td>
                        </tr>
                    </tbody></table>

            </div>
        </div>
    </div>
    <p class="submit" style="clear:left">
			<!-- Ticket 56905  -->
        <input type="submit" class="button-primary" value="<?php _e( 'Update All Rules' ) ?>" name="cmodsar_customSave" />
    </p>
</form>
