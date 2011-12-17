<?php if ($related_query->have_posts()):?>
<div class="hentry-widget hentry-similar">
    <h6><?php _e( 'Similar stories', 'unspoken' ); ?></h6>
    <ul>
        <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
        <li><a href="<?php the_permalink(); ?>" rel="bookmark"><?php if ( get_the_title() ) the_title(); else _e( '[This post has no title]', 'unspoken' ); ?></a></li>
        <?php endwhile; ?>
    </ul>
</div>
<?php endif; ?>
