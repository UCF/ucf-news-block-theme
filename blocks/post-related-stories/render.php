<?php
$post_id = $args['postId'] ?? get_the_ID();
if ( ! $post_id ) return;

$primary_tag = get_field( 'post_primary_tag', $post_id );
if ( ! $primary_tag ) {
    $tags        = wp_get_post_tags( $post_id );
    $primary_tag = $tags[0] ?? null;
}

if ( ! $primary_tag ) return;

$query = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => 8,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'post__not_in'   => array( $post_id ),
    'tax_query'      => array(
        array(
            'taxonomy' => 'post_tag',
            'field'    => 'term_id',
            'terms'    => array( $primary_tag->term_id ),
        ),
    ),
) );

if ( ! $query->have_posts() ) return;
?>
<div class="wp-block-group related-stories">
    <hr class="wp-block-separator">
    <?php echo render_block( array( 'blockName' => 'core/separator', 'attrs' => array() ) ); ?>

    <h2 class="wp-block-heading">Related Stories</h2>

    <div class="wp-block-query related-stories__query">
        <div class="wp-block-post-template is-layout-grid columns-4">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php echo render_block( array(
                    'blockName' => 'ucf-today/story',
                    'attrs'     => array( 'variant' => 'stacked-title' ),
                ) ); ?>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>

</section>
