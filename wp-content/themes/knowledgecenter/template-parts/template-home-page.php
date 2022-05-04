<?php
/**
 * Template Name: Home Page
 *
 * @package KnowledgeCenter
 * @subpackage KnowledgeCenter
 * @since KnowledgeCenter 1.0
 */

get_header();
?>

<main id="primary" class="site-main">
    <section class="hero is-medium">
        <div class="hero-body">
            <div class="container is-max-desktop has-text-centered">
                <div class="columns is-centered">
                    <div class="column is-8-tablet">
                        <h1 class="title is-spaced">
							<?php knowledgecenter_set_home_title(); ?>
                        </h1>
                        <p class="subtitle is-7">
	                        <?php knowledgecenter_set_home_subtitle(); ?>
                        </p>
                        <form class="field search-form">
                            <div class="control has-icons-left is-relative">
                                <label class="screen-reader-text" for="search-form">
									<?php esc_html_x( 'Search for:', 'label', 'knowledgecenter' ); ?>
                                </label>
                                <input class="input is-large is-rounded" id="search-form" name="s" type="search"
                                       placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'knowledgecenter' ); ?>"/>
                                <span class="icon is-small is-left">
                      <span class="kc-icon icon-search"></span>
                    </span>
                                <button class="search-btn">
                                    <span class="kc-icon icon-arrow-right"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section mb-6">
        <div class="container is-max-desktop py-4">
            <h2 class="title has-text-centered mb-6"><?php knowledgecenter_set_home_cat_title(); ?></h2>
            <div class="columns is-multiline">
				<?php
				$defaults   = array(
					'orderby'    => 'name',
					'order'      => 'ASC',
					'hide_empty' => 1,
				);
				$args       = apply_filters( 'knowledgecenter_category_home_args', $defaults );
				$categories = get_categories( $args );
				$categories = wp_list_filter( $categories, array( 'parent' => 0 ) );
				foreach ( $categories as $cat ) {
					$link = get_category_link( $cat->term_id );
					?>
                    <div class="column is-4">
                        <div class="box cat-box has-text-centered is-flex is-flex-direction-column">
                            <h3 class="title is-spaced is-4"><?php echo esc_html( $cat->name ); ?></h3>
                            <p class="subtitle is-6 has-text-centered is-flex-grow-1">
								<?php echo esc_html( $cat->description ); ?>. </p>
                            <a href="<?php echo esc_url( $link ); ?>"
                               class="button is-primary is-outlined"><?php esc_html_e( 'Browse all articles', 'knowledgecenter' ); ?></a>
                        </div>
                    </div>
					<?php
				}
				?>
            </div>

        </div>
    </section>

</main>

<?php
get_footer(); ?>
