<?php get_header(); ?>

<div id="content">
    <div class="archive">
        
        <?php /* If this is a category archive */ if (is_category()) { ?>
        <div class="title"><?php printf( __('%s', 'unspoken'), single_cat_title('', false) ); ?><a href="javascript: void(0);" id="mode" class="<?php if ($_COOKIE['mode'] == 'grid') echo 'flip'; ?>"></a></div>
        <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
        <div class="title"><?php printf( __('&quot;%s&quot; tag', 'unspoken'), single_tag_title('', false) ); ?><a href="javascript: void(0);" id="mode" class="<?php if ($_COOKIE['mode'] == 'grid') echo 'flip'; ?>"></a></div>
        <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
        <div class="title"><?php printf( __('%s', 'unspoken'), get_the_date() ); ?><a href="javascript: void(0);" id="mode" class="<?php if ($_COOKIE['mode'] == 'grid') echo 'flip'; ?>"></a></div>
        <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
        <div class="title"><?php printf( __('%s', 'unspoken'), get_the_date( __('F Y') ) ); ?><a href="javascript: void(0);" id="mode" class="<?php if ($_COOKIE['mode'] == 'grid') echo 'flip'; ?>"></a></div>
        <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
        <div class="title"><?php printf( __('%s', 'unspoken'), get_the_date( __('Y') ) ); ?><a href="javascript: void(0);" id="mode" class="<?php if ($_COOKIE['mode'] == 'grid') echo 'flip'; ?>"></a></div>
        <?php /* If this is an author archive */ } elseif (is_author()) {
            if(get_query_var('author_name')) :
                $curauth = get_userdatabylogin(get_query_var('author_name'));
            else :
                $curauth = get_userdata(get_query_var('author'));
            endif;
            $authorname = '';
            if ( $curauth->display_name == '' ) {
                $authorname = $curauth->nickname;
            } else {
                $authorname = $curauth->display_name;
            }
        ?>
        <div class="title"><?php printf( __('%s', 'unspoken'), $authorname ); ?><a href="javascript: void(0);" id="mode" class="<?php if ($_COOKIE['mode'] == 'grid') echo 'flip'; ?>"></a></div>
        <?php /* If this is a paged archive */ } elseif ( isset( $_GET['paged'] ) && !empty( $_GET['paged'] ) ) { ?>
        <div class="title"><?php __e('Latest entries', 'unspoken'); ?><a href="javascript: void(0);" id="mode" class="<?php if ($_COOKIE['mode'] == 'grid') echo 'flip'; ?>"></a></div>
        <?php } ?>

        <?php rewind_posts(); ?>

        <?php get_template_part('loop'); ?>

        <?php get_template_part('pagination'); ?>

    </div> <!-- .archive -->
</div> <!-- #content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
