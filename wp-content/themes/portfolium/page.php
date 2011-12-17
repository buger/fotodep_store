<?php get_header(); ?>

<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>

    <div <?php post_class('clear'); ?> id="post-<?php the_ID(); ?>">
        <div class="post_list_meta">
            <h2><?php the_title(); ?></h2>
        </div>
        <div class="post_content">
            <?php the_content(); ?>
        </div>
    </div>

    <?php comments_template(); ?>

    <?php endwhile; else: ?>

		<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

<?php endif; ?>

</div>

<?php get_footer(); ?>