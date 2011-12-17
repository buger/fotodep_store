<?php
/*
Template Name: Sitemap
*/

get_header(); ?>

<div id="content">

    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <div class="hentry-meta">
                <h1><?php the_title(); ?></h1>
            </div>

            <div class="hentry-content clear">
                <h3><?php _e('Pages', 'unspoken'); ?></h3>
                <ul><?php wp_list_pages('title_li='); ?></ul>

                <h3><?php _e('Categories', 'unspoken'); ?></h3>
                <ul><?php wp_list_categories('title_li=&show_count=1'); ?></ul>

                <h3><?php _e('Posts per category', 'unspoken'); ?></h3>
                <?php
                    $categories = get_categories('hierarchical=0');
                    foreach ( $categories as $category ) {
                        echo '<h4>' . $category->name . '</h4>';
                        echo '<ul>';
                        $posts = get_posts( array(
                            'numberposts' => -1,
                            'category' => $category->cat_ID,
                            'order' => 'ASC'
                        ) );
                        foreach ( $posts as $post ) {
                            setup_postdata($post);
                            printf( __('<li><a href="%1$s">%2$s</a> &mdash; Comments (%3$s)</li>', 'unspoken'), get_permalink($post->ID), get_the_title($post->ID), get_comments_number('0', '1', '%') );
                        }
                        echo '</ul>';
                    }
                ?>
            </div>

        </div> <!-- .page -->

    <?php endwhile; // end of the loop. ?>

</div><!-- #content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
