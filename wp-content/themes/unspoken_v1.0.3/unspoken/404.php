<?php get_header(); ?>

<div id="content">
    <div class="archive">
        
        <div class="title"><?php _e('Sorry, that page doesn’t exist!', 'unspoken'); ?></div>

        <div id="post-0" class="post hentry error404 not-found clear">
            <p><?php _e( 'The page you\'ve requested can not be displayed. It appears you\'ve missed your intended destination, either
through a bad or outdated link, or a typo in the page you were hoping to reach.', 'unspoken' ); ?></p>
            <p><a href="<?php bloginfo('url'); ?>"><?php _e( 'Back to homepage', 'unspoken' ); ?></a></p>
        </div><!-- #post-0 -->

    </div> <!-- .archive -->
</div> <!-- #content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
