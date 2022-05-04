<?php
/**
 * the closing of the main content elements and the footer element
 *
 * @package iknowledgebase
 */
?>

</main>
<footer class="footer mt-6 py-4">

    <div class="navbar is-transparent">
        <div class="container">
            <div class="navbar-item copyright pl-0">
				<?php
				$iknowledgebase_option = get_option( 'iknowledgebase_settings', '' );
				if ( ! empty( $iknowledgebase_option['footer_text'] ) ) {
					echo esc_attr( $iknowledgebase_option['footer_text'] );
				} else {
					echo '&copy; ' . esc_attr( date_i18n( esc_attr__( 'Y', 'iknowledgebase' ) ) ) . ' ' . esc_attr( get_bloginfo( 'name' ) );
				}
				?>
            </div>

            <div id="main-menu" class="navbar-menu is-active">
                <div class="navbar-end">
					<?php
					wp_nav_menu( array(
						'theme_location'  => 'footer-menu',
						'depth'           => '1',
						'container'       => '',
						'container_class' => '',
						'container_id'    => '',
						'menu_class'      => '',
						'menu_id'         => '',
						'items_wrap'      => '%3$s',
						'walker'          => new IKnowledgebaseBase_Walker_Nav_Menu(),
						'fallback_cb'     => '',
					) );
					?>
                    <div class="navbar-item copyright pr-0 is-hidden-desktop">
		                <?php
//		                $iknowledgebase_option = get_option( 'iknowledgebase_settings', '' );
//		                if ( ! empty( $iknowledgebase_option['footer_text'] ) ) {
//			                echo esc_attr( $iknowledgebase_option['footer_text'] );
//		                } else {
//			                echo '&copy; ' . esc_attr( date_i18n( esc_attr__( 'Y', 'iknowledgebase' ) ) ) . ' ' . esc_attr( get_bloginfo( 'name' ) );
//		                }
		                ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</footer>

<?php wp_footer(); ?>
</body></html>
