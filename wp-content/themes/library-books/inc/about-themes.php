<?php
// about theme info
add_action('admin_menu', 'library_books_abouttheme');
function library_books_abouttheme()
	{
	add_theme_page(esc_html__('Theme Info', 'library-books') , esc_html__('Theme Info', 'library-books') , 'edit_theme_options', 'library_books_guide', 'library_books_mostrar_guide');
	}
// guidline for about theme
function library_books_mostrar_guide()
	{
// custom function about theme customizer
	$return = add_query_arg(array());
?>
<style type="text/css">
@media screen and (min-width: 800px) {
.col-left {float:left; width: 99%; text-align:center;}
}
</style>
<div class="wrapper-info">
	<div class="col-left">
   		   <div style="font-size:16px; font-weight:bold; padding-bottom:10px; border-bottom:1px solid #ccc; margin-bottom:15px; margin-top:10px;">
			  <?php esc_html_e('Theme Info', 'library-books'); ?>
		   </div>
           <div style="text-align:center; font-weight:bold;">
				<a href="<?php echo esc_url(LIBRARY_BOOKS_LIVE_DEMO); ?>" target="_blank"><?php esc_html_e('Live Demo', 'library-books'); ?></a> | 
				<a href="<?php echo esc_url(LIBRARY_BOOKS_PRO_THEME_URL); ?>"><?php esc_html_e('Buy Pro', 'library-books'); ?></a> | 
				<a href="<?php echo esc_url(LIBRARY_BOOKS_THEME_DOC); ?>" target="_blank"><?php esc_html_e('Documentation', 'library-books'); ?></a>
                <div style="height:5px"></div>
			</div>
          <p><?php
	esc_html_e('Library Books WordPress theme caters education, author, selling ebooks, PDFs online, journalists, editors, publishers, course providers, online book stores, literature, journalism. Teachers, institutes, training academy, coaching centre, LMS, guidance, counselling centre, elearning, e-learning, kindergartens, playschools, day care centres, primary schools.', 'library-books'); ?></p>
	<a href="<?php
	echo esc_url(LIBRARY_BOOKS_FREE_THEME_URL); ?>"><img src="<?php
	echo esc_url(get_template_directory_uri()); ?>/images/free-vs-pro.jpg" alt="" /></a>
	</div><!-- .col-left -->
	<!-- .col-right -->
</div><!-- .wrapper-info -->
<?php } ?>