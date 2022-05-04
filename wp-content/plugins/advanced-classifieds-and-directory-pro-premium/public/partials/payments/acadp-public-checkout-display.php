<?php

/**
 * This template displays the checkout page.
 *
 * @link    https://pluginsware.com
 * @since   1.0.0
 *
 * @package Advanced_Classifieds_And_Directory_Pro
 */
?>

<div class="acadp acadp-user acadp-checkout">
	<?php acadp_status_messages(); ?>

	<p><?php esc_html_e( 'Please review your order, and click Purchase once you are ready to proceed.', 'advanced-classifieds-and-directory-pro' ); ?></p>
    
    <form id="acadp-checkout-form" class="form-vertical" method="post" action="" role="form">
		<table id="acadp-checkout-form-data" class="table table-stripped table-bordered">
        	<?php foreach ( $options as $option ) : ?>            	
                <?php if ( 'header' == $option['type'] ) { ?>                
                	<tr>
                		<td colspan="2">
                    		<h3 class="acadp-no-margin"><?php echo esc_html( $option['label'] ); ?></h3>
                        	<?php if ( isset( $option['description'] ) ) echo esc_html( $option['description'] ); ?>
                    	</td>
                	</tr>                
            	<?php } else { ?>
					<!--Mel: 24/12/21-->
					<tr>
						<td colspan="2">
                        	<?php if ( isset( $option['label'] ) ) : ?>
								<h4 class="acadp-no-margin"><?php echo esc_html( $option['label'] ); ?></h4>
                            <?php endif; ?>
                    		<?php if ( isset( $option['description'] ) ) echo esc_html( $option['description'] ); ?>
                		</td>
					</tr>
                	<tr>
                		<td colspan="2">
                    		<?php
							switch ( $option['type'] ) {
								case 'checkbox' :
									$checked = isset( $option['selected'] ) && 1 == $option['selected'] ? ' checked' : '';
									printf( '<input type="checkbox" name="%s[]" value="%s" class="acadp-checkout-fee-field" data-price="%s" %s/>', esc_attr( $option['name'] ), esc_attr( $option['value'] ), esc_attr( $option['price'] ), $checked );
									break;
								case 'radio' :
									$checked = isset( $option['selected'] ) && 1 == $option['selected'] ? ' checked' : '';
									
									//Mel: 26/12/21. Create list of donation options with increments
									echo '<ul class="no-marker"><li>';
									printf( '<input type="radio" name="%s" value="%s" class="acadp-checkout-fee-field" data-price="%s" %s/>', esc_attr( $option['name'] ), esc_attr( $option['value'] ), round(esc_attr( $option['price'], 0) ), $checked );
									echo '<label>' . esc_html__( '$', 'advanced-classifieds-and-directory-pro' ) . round(esc_attr( $option['price'], 0) ) . '</label></li>';
									printf( '<li><input type="radio" name="%s" value="%s" class="acadp-checkout-fee-field" data-price="%s" />', esc_attr( $option['name'] ), esc_attr( $option['value'] ), esc_attr( $option['price'] + 100 ) );
									echo '<label>' . esc_html__( '$', 'advanced-classifieds-and-directory-pro' ) . esc_attr( $option['price'] + 100 ) . '</label></li>';
									printf( '<li><input type="radio" name="%s" value="%s" class="acadp-checkout-fee-field" data-price="%s" />', esc_attr( $option['name'] ), esc_attr( $option['value'] ), esc_attr( $option['price'] + 200 ) );
									echo '<label>' . esc_html__( '$', 'advanced-classifieds-and-directory-pro' ) . esc_attr( $option['price'] + 200 ) . '</label></li>';
									printf( '<li><input type="radio" name="%s" value="%s" class="acadp-checkout-fee-field" data-price="%s" />', esc_attr( $option['name'] ), esc_attr( $option['value'] ), esc_attr( $option['price'] + 400 ) );
									echo '<label>' . esc_html__( '$', 'advanced-classifieds-and-directory-pro' ) . esc_attr( $option['price'] + 400 ) . '</label></li>';
									printf( '<li><input type="radio" id="other" name="%s" value="%s" class="acadp-checkout-fee-field" data-price="" />', esc_attr( $option['name'] ), esc_attr( $option['value'] ) );
									echo '<label>' . esc_html__( 'Other', 'advanced-classifieds-and-directory-pro' ) . '</label></li>';
									echo '</ul>';
									printf( '<label>' . esc_html__( 'Other Amount:', 'advanced-classifieds-and-directory-pro' ) . '</label> <input type="number" name="%s" class="acadp-checkout-fee-field-other" />', esc_attr( $option['name'] ) );
									printf( '<input type="hidden" name="%s" value="%s" />', esc_attr( $option['name'] ), esc_attr( $option['value'] ) );
									//Mel: End

									break;
							}                    		
							?>
                    	</td>
						
						<!--Mel: 24/12/21
						<td>
                        	<?php //if ( isset( $option['label'] ) ) : ?>
								<h4 class="acadp-no-margin"><?php //echo esc_html( $option['label'] ); ?></h4>
                            <?php //endif; ?>
                    		<?php //if ( isset( $option['description'] ) ) echo esc_html( $option['description'] ); ?>
                		</td>-->
        				<!--Mel: 11/11/21
						<td align="right" class="text-right"><?php //echo esc_html( acadp_format_payment_amount(  $option['price'] ) ); ?> </td>
						-->
        			</tr>
                <?php } ?>           	
            <?php endforeach; ?>    		
            <tr>
            	<td class="acadp-vertical-middle">
                	<strong><?php esc_html_e( 'Payable amount' , 'advanced-classifieds-and-directory-pro'); //printf( esc_html__( 'Payable amount [%s]', 'advanced-classifieds-and-directory-pro' ), acadp_get_payment_currency() );?>
					</strong>
                </td>
                <td class="acadp-vertical-middle">
					<!--Mel: 11/11/21. Make currency dynamic and display currency and amount in one line-->
					<div id="currency"></div>
					<div id="acadp-checkout-total-amount"></div>
				</td>
            </tr>
    	</table>
        
        <div id="acadp-payment-gateways" class="panel panel-default">
        	<div class="panel-heading"><?php esc_html_e( 'Choose payment method', 'advanced-classifieds-and-directory-pro' ); ?></div>
            
            <?php the_acadp_payment_gateways(); ?>
        </div>
        
        <div id="acadp-cc-form"></div>
        
        <p id="acadp-checkout-errors" class="text-danger"></p>
        
		<!--Mel: 28/12/21. To add amount value to be captured by place_order function -->
		<input type="hidden" name="amount" >
		
        <?php wp_nonce_field( 'acadp_process_payment', 'acadp_checkout_nonce' ); ?>
        <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>" />
        <div class="pull-right">
        	<a href="<?php echo esc_url( acadp_get_manage_listings_page_link() ); ?>" class="btn btn-default"><?php esc_html_e( 'Not now', 'advanced-classifieds-and-directory-pro' ); ?></a>
        	<input type="submit" id="acadp-checkout-submit-btn" class="btn btn-primary" value="<?php esc_attr_e( 'Proceed to payment', 'advanced-classifieds-and-directory-pro' ); ?>" />
        </div>
    </form>
</div>
