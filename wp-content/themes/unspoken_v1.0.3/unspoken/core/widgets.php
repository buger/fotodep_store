<?php

/***** Register widgetized areas *****/
function unspoken_widgets_init() {
    // Homepage area.
	register_sidebar( array(
		'name' => __( 'Homepage', 'unspoken' ),
		'id' => 'homepage-widget-area',
		'description' => __( 'Homepage area works only with "WPSHOWER Home" & "WPSHOWER Ad Place"', 'unspoken' ),
		'before_widget' => '<div id="%1$s" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<span class="no-title">',
		'after_title' => '</span>',
	) );

	// Sidebar
	register_sidebar( array(
		'name' => __( 'Sidebar', 'unspoken' ),
		'id' => 'sidebar-widget-area',
		'description' => __( 'The sidebar widget area', 'unspoken' ),
		'before_widget' => '<div id="%1$s" class="%2$s widget">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

    // Tabs Widget area
	register_sidebar( array(
		'name' => __( 'Tabs widget area', 'unspoken' ),
		'id' => 'tabs-widget-area',
		'description' => __( 'Please fill in the title!', 'unspoken' ),
		'before_widget' => '<div class="tabs-box"><div id="%1$s" class="%2$s widget">',
		'after_widget' => '</div></div>',
		'before_title' => '<span class="no-title">',
		'after_title' => '</span>',
	) );

    // Area located in the header, use for ad.
	register_sidebar( array(
		'name' => __( 'Header Ad Place', 'unspoken' ),
		'id' => 'header-widget-area',
		'description' => __( 'An ad place, max width 468px', 'unspoken' ),
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '<span class="no-title">',
		'after_title' => '</span>',
	) );

    // Area located below the header, use for ad.
	register_sidebar( array(
		'name' => __( 'Ad place under the menu', 'unspoken' ),
		'id' => 'topcontent-widget-area',
		'description' => __( '', 'unspoken' ),
		'before_widget' => '<div id="%1$s" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<span class="no-title">',
		'after_title' => '</span>',
	) );

	// Area located above the footer.
	register_sidebar( array(
		'name' => __( 'Bottom', 'unspoken' ),
		'id' => 'bottom-widget-area',
		'description' => __( 'The bottom widget area', 'unspoken' ),
		'before_widget' => '<div id="%1$s" class="%2$s widget">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

}
/** Register sidebars by running unspoken_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'unspoken_widgets_init' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 * This function uses a filter (show_recent_comments_widget_style) new in WordPress 3.1
 * to remove the default style.
 */
function unspoken_remove_recent_comments_style() {
	add_filter( 'show_recent_comments_widget_style', '__return_false' );
}
add_action( 'widgets_init', 'unspoken_remove_recent_comments_style' );

/***** Twitter *****/
include_once (TEMPLATEPATH . '/core/widgets/twitter.php');

/***** Get Connected *****/
include_once (TEMPLATEPATH . '/core/widgets/getconnected.php');

/***** Ad Place *****/
include_once (TEMPLATEPATH . '/core/widgets/ad_place.php');

/***** Archive *****/
include_once (TEMPLATEPATH . '/core/widgets/archive.php');

/***** Tabs *****/
include_once (TEMPLATEPATH . '/core/widgets/tabs.php');

/***** Flickr *****/
include_once (TEMPLATEPATH . '/core/widgets/flickr.php');

/***** Recent Posts *****/
include_once (TEMPLATEPATH . '/core/widgets/recent_posts.php');

/***** Recent Comments *****/
include_once (TEMPLATEPATH . '/core/widgets/recent_comments.php');

/***** Popular Posts *****/
include_once (TEMPLATEPATH . '/core/widgets/popular_posts.php');

/***** Homepage *****/
include_once (TEMPLATEPATH . '/core/widgets/homepage_block.php');
