<?php get_header(); ?>

<div class="page_meta clear">
    <div class="rss">
        <a href="<?php bloginfo('rss2_url'); ?>">Subscribe to RSS feed</a>
    </div>
    <div class="heading">
        <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
        <?php /* If this is a category archive */ if (is_category()) { ?>
        <h3><?php printf(__('%s'), single_cat_title('', false)); ?></h3>
        <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
        <h3><?php printf(__('Tag &#8216;%s&#8217;'), single_tag_title('', false) ); ?></h3>
        <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
        <h3><?php printf(_c('%s|Daily archive'), get_the_time(__('F jS, Y'))); ?></h3>
        <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
        <h3><?php printf(_c('%s|Monthly archive'), get_the_time(__('F, Y'))); ?></h3>
        <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
        <h3><?php printf(_c('%s|Yearly archive'), get_the_time(__('Y'))); ?></h3>
        <?php /* If this is an author archive */ } elseif (is_author()) { ?>
        <h3><?php _e('Author Archive'); ?></h3>
        <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
        <h3><?php _e('Blog Archives'); ?></h3>
        <?php } ?>
    </div>
    <?php if(function_exists('catlist')) { catlist(); } ?>
    <?php get_search_form(); ?>
</div>

<div class="posts">

<?php get_template_part('loop');  // Loop template (loop.php) ?>

<?php get_template_part('pagination');  // Pagination template for WP-PageNavi support (pagination.php) ?>

</div>

<?php get_footer(); ?>
