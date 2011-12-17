<?php if ( have_posts() ) : ?>
    <?php $i = 0; ?>
    <?php while ( have_posts() ) : the_post(); $i++; ?>

    <div class="post_home">
        <a href="<?php the_permalink() ?>" class="thumb" title="<?php the_title(); ?>">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail(array(145,145)); ?>
            <?php else : ?>
                <img src="<?php bloginfo('template_url'); ?>/i/noimage.jpg" width="145" height="145" alt=""/>
            <?php endif; ?>
        </a>
        <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
    </div>

    <?php if ($i % 6 == 0) echo '<div style="clear: both;"></div>'?>
    <?php endwhile; ?>
<?php endif; ?>
