<?php if ($related_query->have_posts()) : ?>
<div class="recommended">
    <div class="block-title"><?php _e( 'Recommended for you', 'unspoken' ); ?></div>
    <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
    <div class="recommended-item">
        <a href="<?php the_permalink(); ?>" class="recommended-photo" rel="bookmark"><?php if ( has_post_thumbnail() ) the_post_thumbnail('mini'); ?></a>
        <p><?php echo get_the_date(); ?></p>
        <h2><a href="<?php the_permalink(); ?>" rel="bookmark"><?php if ( get_the_title() ) the_title(); else _e( '[This post has no title]', 'unspoken' ); ?></a></h2>
    </div>
    <?php endwhile; ?>
</div>
<?php endif; ?>
