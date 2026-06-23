<?php
$display = get_field( 'post_display_tag_cloud', $args['postId'] ?? null );

if ( ! $display ) return;

$count = get_field( 'post_tag_cloud_count', $args['postId'] ?? null ) ?: 5;
$tags  = get_the_tags( $args['postId'] ?? null );

if ( ! $tags ) return;

$tags = array_slice( $tags, 0, $count );
?>
<div class="today-tag-cloud">
    <h2>More Topics</h2>
    <?php foreach ( $tags as $tag ) : ?>
        <a href="<?php echo get_tag_link( $tag ); ?>"><?php echo esc_html( $tag->name ); ?></a>
    <?php endforeach; ?>
</div>
