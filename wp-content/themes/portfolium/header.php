<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php bloginfo('text_direction'); ?>" xml:lang="<?php bloginfo('language'); ?>">
    <head>
        <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
        <title><?php
            global $page, $paged;
            wp_title('|', true, 'right');
            bloginfo('name');
            $site_description = get_bloginfo('description', 'display');
            if ( $site_description && ( is_home() || is_front_page()))
                echo " | $site_description";
            if ($paged >= 2 || $page >= 2)
                echo ' | ' . sprintf( __('Page %s'), max($paged, $page));

            ?></title>
        <link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/i/favico.ico" type="image/x-icon" />
        <meta http-equiv="Content-language" content="<?php bloginfo('language'); ?>" />
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
        <!--[if IE]><link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_url'); ?>/ie.css" /><![endif]-->
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
        <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
        <link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>"/>
        <link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
        <?php
			wp_enqueue_script('jquery');
			if ( is_singular() ) wp_enqueue_script('slideshow', get_template_directory_uri() . '/js/jquery.cycle.all.min.js', 'jquery', false);
			wp_enqueue_script('lazyload', get_template_directory_uri() . '/js/jquery.lazyload.mini.js', 'jquery', false);
			wp_enqueue_script('script', get_template_directory_uri() . '/js/script.js', 'jquery', false);
		?>
        <?php wp_head(); ?>
        <?php if ( is_singular() ) : ?>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery('#show').after('<div id="show_nav">').cycle({
                        fx:     'fade',
                        speed:  500,
                        next: '#show',
                        timeout: 0,
                        pager:  '#show_nav'
                    });
                });
            </script>
        <?php endif; ?>
    </head>
    <body>
        <div class="wrapper">
            <div class="header clear">
                <h1 class="logo"><a href="<?php bloginfo('home'); ?>"><?php bloginfo('name'); ?></a></h1>
                <?php wp_nav_menu(array('menu' => 'Header', 'theme_location' => 'Header', 'depth' => 2, 'container' => false, 'menu_class' => 'nav jsddm', 'walker' => new extended_walker())); ?>
            </div>
            <div class="middle clear">
