<?php
/*
Template Name: Blog
*/

get_header(); ?>

<div id="content">
    <div class="archive">

        <div class="title"><?php _e('Latest entries', 'unspoken'); ?><a href="javascript: void(0);" id="mode" class="<?php if ($_COOKIE['mode'] == 'grid') echo 'flip'; ?>"></a></div>

        <?php
            $paged = ( !empty($paged) ) ? $paged : $page;
            query_posts(array(
                'posts_per_page' =>  get_option('posts_per_page'),
                'paged' => $paged
            ));
        ?>
        
        <?php get_template_part('loop'); ?>

        <?php get_template_part('pagination'); ?>

        <?php  wp_reset_query(); ?>

    </div> <!-- .archive -->
</div> <!-- #content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
