<?php
/*
 * The content used by the knowledge base files.
 */
?>

<?php $exclude_cats = get_post_meta( get_the_ID(), 'myknowledgebase-exclude-cats', true );
if ( !empty( $exclude_cats ) ) {
	$exclude = esc_attr( $exclude_cats );
} else if ( get_theme_mod( 'myknowledgebase_exclude' ) ) :
	$exclude = esc_attr( get_theme_mod( 'myknowledgebase_exclude' ) );
else :
	$exclude = '';
endif;
$myknowledgebase_cat_args = array(
	'hide_empty' => 0,
	'exclude' => $exclude,
	'orderby' => 'name',
	'order' => 'asc'
);
$myknowledgebase_cats = get_categories( $myknowledgebase_cat_args );

foreach ( $myknowledgebase_cats as $category ) :
	if ( get_theme_mod( 'myknowledgebase_post_count' ) == "yes" ) :
		$count = '<span class="cat-post-count">('.$category->category_count.')</span>';
	else :
		$count = '';
	endif;
	echo '<li class="cat-list">';
		echo '<div class="cat-name"><a href="'.get_category_link( $category->cat_ID ).'" title="'.$category->name.'" >'.$category->name.'</a> '.$count.'</div>';
		if ( get_theme_mod( 'myknowledgebase_cat_description' ) == "yes" ) :
			if ( category_description( $category->cat_ID ) ) :
				echo '<div class="cat-description">'.wp_kses_post( category_description( $category->cat_ID ) ).'</div>';
			endif;
		endif;

		if ( get_theme_mod( 'myknowledgebase_posts' ) ) :
			$posts_per_page = esc_attr( get_theme_mod( 'myknowledgebase_posts' ) );
		else :
			$posts_per_page = -1;
		endif;

		if ( get_theme_mod( 'myknowledgebase_order' ) == "name" ) :
			$order_by = 'name';
			$the_order = 'asc';
		else :
			$order_by = 'date';
			$the_order = 'desc';
		endif;

		$myknowledgebase_post_args = array(
			'post_type' => 'post',
			'tax_query' => array(
				array(
					'taxonomy' => 'category',
					'field' => 'term_id',
					'terms' => $category->term_id,
					'include_children' => false,
				)
			),
			'posts_per_page' => $posts_per_page,
			'orderby' => $order_by,
			'order' => $the_order
		);

		$myknowledgebase_posts = get_posts( $myknowledgebase_post_args );
		echo '<ul class="cat-post-list">';
			foreach( $myknowledgebase_posts AS $single_post ) :
				if (get_the_title( $single_post->ID ) == false) :
					$title = __( '(no title)', 'myknowledgebase' );
				else :
					$title = get_the_title( $single_post->ID );
				endif;
				echo '<li class="cat-post-name"><a href="'.get_permalink( $single_post->ID ).'" rel="bookmark" title="'.$title.'">'.$title.'</a>';
				if ( get_theme_mod( 'myknowledgebase_post_meta' ) == "yes" ) { 
					echo '<div class="cat-post-meta">';
					echo '<span class="cat-post-meta-date"><a href="'.esc_url( get_permalink( $single_post->ID ) ).'">'.esc_attr( get_the_date(get_option( 'date_format' ), $single_post->ID) ).'</a></span>';
					echo '<span class="cat-post-meta-sep">'.' | '.'</span>';
					echo '<span class="cat-post-meta-author">'.sprintf( '<a href="%1$s">%2$s</a>', esc_url( get_author_posts_url( $single_post->post_author ) ), esc_attr( get_the_author_meta( 'display_name', $single_post->post_author ) ) ).'</span>';
					echo '</div>';
				}
				echo '</li>';
			endforeach;
		echo '</ul>';
		if ( get_theme_mod( 'myknowledgebase_view_all' ) == "yes" ) {
			echo '<div class="cat-view-all"><a href="'.get_category_link( $category->cat_ID ).'" title="'.$category->name.'" >'.__( 'View All &raquo;', 'myknowledgebase' ).'</a></div>';
		}
	echo '</li>';
endforeach; ?>
