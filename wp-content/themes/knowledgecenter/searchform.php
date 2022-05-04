<?php
/**
 * Search form
 *
 * @package KnowledgeCenter
 * @subpackage KnowledgeCenter
 * @since KnowledgeCenter 1.0
 */

?>
<form role="search" method="get" id="searchform" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<?php $kc_form_id = rand( 100, 9999 ); ?>
    <div class="field">
        <div class="control has-icons-left">
            <label class="screen-reader-text" for="s<?php echo absint( $kc_form_id ); ?>">
                <?php esc_html_x( 'Search for:', 'label', 'knowledgecenter' ); ?>
            </label>
            <input type="text" value="<?php the_search_query(); ?>" name="s" id="s<?php echo absint( $kc_form_id ); ?>" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'knowledgecenter' ); ?>" class="input is-rounded"/>
            <span class="icon is-small is-left">
                <span class="kc-icon icon-search"></span>
            </span>
        </div>
    </div>
</form>