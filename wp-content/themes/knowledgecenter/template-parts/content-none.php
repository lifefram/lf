<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package KnowledgeCenter
 * @subpackage KnowledgeCenter
 * @since KnowledgeCenter 1.0
 */

?>

<section class="no-results not-found section is-medium">
	<header>
		<h1 class="title is-spaced has-text-centered"><?php esc_html_e( 'Nothing Found', 'knowledgecenter' ); ?></h1>
	</header>

	<div class="page-content content mt-5">
		<?php
		if ( is_home() && current_user_can( 'publish_posts' ) ) :

			printf(
				'<p>' . wp_kses(
				/* translators: 1: link to WP admin new post page. */
					__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'knowledgecenter' ),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				) . '</p>',
				esc_url( admin_url( 'post-new.php' ) )
			);

		elseif ( is_search() ) :
			?>

			<p class="subtitle"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'knowledgecenter' ); ?></p>
			<?php
			get_search_form();

		else :
			?>

			<p class="subtitle"><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'knowledgecenter' ); ?></p>
			<?php
			get_search_form();

		endif;
		?>
	</div>
</section>