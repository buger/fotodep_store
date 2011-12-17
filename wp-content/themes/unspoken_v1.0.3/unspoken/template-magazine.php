<?php
/*
Template Name: Magazine
*/

get_header(); ?>

<div id="content">
    <div class="archive">
        <?php
            $args = array(
                'posts_per_page' => -1,
                'meta_key' => 'usn_slider',
                'meta_value' => 'on',
                'post__not_in' => get_option( 'sticky_posts' ),
                'cat' => 16
            );
            query_posts($args);
            if ( have_posts() ) :
        ?>
        <div id="mainposts">
            <ul>
                <?php while (have_posts() ) : the_post(); ?>
                <li>
                    <div class="mainpost">
                        <?php if ( has_post_thumbnail() ) the_post_thumbnail('slide-magazine'); ?>
                        <div class="mainpost-container">
                            <div class="bg"></div>
                            <div class="mainpost-data">
                                <div class="mainpost-meta"><?php echo get_the_date(); ?> &middot; <?php comments_popup_link ( __('0 Comments', 'unspoken'), __('1 Comment', 'unspoken'), __('% Comments', 'unspoken'), '', __('Comments off', 'unspoken')); ?> &middot; <?php the_category(', '); ?></div>
                                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <?php the_excerpt(); ?>
                            </div>
                        </div>
                    </div>
                </li>
                <?php
                    endwhile;
                    wp_reset_query();
                ?>
            </ul>
            <a href="javascript: void(0);" class="prev"></a>
            <a href="javascript: void(0);" class="next"></a>
        </div>
        <?php endif; ?>

        <div class="title"><?php _e('Latest entries', 'unspoken'); ?><a href="javascript: void(0);" id="mode" class="<?php if ($_COOKIE['mode'] == 'grid') echo 'flip'; ?>"></a></div>

        <?php
            $paged = ( !empty($paged) ) ? $paged : $page;
            query_posts(array(
                'posts_per_page' =>  get_option('posts_per_page'),
                'paged' => $paged,
		'cat' => '16'
            ));
        ?>

        <?php get_template_part('loop'); ?>

        <?php get_template_part('pagination'); ?>

        <?php wp_reset_query(); ?>

    </div> <!-- .magazine -->
</div> <!-- #content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
