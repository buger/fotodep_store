<?php
/*
Template Name: Image Gallery
*/

get_header(); ?>

<div id="content">

    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <div class="hentry-meta">
                <h1><?php the_title(); ?></h1>
            </div>

            <div class="hentry-content clear">
                <?php the_content(); ?>
            </div>

        </div> <!-- .page -->

    <?php endwhile; // end of the loop. ?>

</div><!-- #content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
