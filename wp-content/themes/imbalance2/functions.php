<?php
require_once ( get_stylesheet_directory() . '/theme-options.php' );
if (!is_admin()) {
	wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', get_bloginfo('stylesheet_directory').'/libs/jquery-1.6.1.min.js' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery_masonry', get_bloginfo('stylesheet_directory').'/libs/jquery.masonry.min.js' );
	wp_enqueue_script( 'jquery_ui', get_bloginfo('stylesheet_directory').'/libs/jquery-ui.custom.min.js' );
	
	// javascript for infinite scroll
	$imbalance2_theme_options = get_option('imbalance2_theme_options');
	if ( $imbalance2_theme_options['navigation'] == 1 )
	{
		wp_enqueue_script( 'jquery_infinitescroll', get_bloginfo('stylesheet_directory').'/libs/jquery.infinitescroll.min.js' );
	}
}

// shortcodes
function imbalance2_wide( $atts, $content = null )
{
	return '<div class="wide">' . do_shortcode($content) . '</div>';
}
add_shortcode( 'wide', 'imbalance2_wide' );

function imbalance2_aside( $atts, $content = null )
{
	return '<div class="aside">' . do_shortcode($content) . '</div>';
}
add_shortcode( 'aside', 'imbalance2_aside' );

// 210px width images for the grid
if ( function_exists( 'add_theme_support' ) )
{
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 210 );
}
if ( function_exists( 'add_image_size' ) )
{
	add_image_size( 'homepage-thumb', 210 );
}

// font output for css
function getFonts()
{
	global $imbalance2_theme_options;
	
	if ($imbalance2_theme_options['font'] == 0) return 'Georgia, "Times New Roman", Serif';
	return '"Helvetica Neue", Helvetica, Arial, "Sans-Serif"';
}

// favicon for <head>
function getFavicon()
{
	global $imbalance2_theme_options;
	
	return '<link rel="shortcut icon" href="'.($imbalance2_theme_options['favicon'] != '' ? $imbalance2_theme_options['favicon'] : get_bloginfo('stylesheet_directory').'/favico.ico').'" />';
}

// color option for css
function getColor()
{
	global $imbalance2_theme_options;
	
	return $imbalance2_theme_options['color'] != '' ? $imbalance2_theme_options['color'] : '#f05133';
}

// fluid grid option for css
function fluidGrid()
{
	global $imbalance2_theme_options;

	return $imbalance2_theme_options['fluid'];
}

// images only option for css
function imagesOnly()
{
	global $imbalance2_theme_options;

	return $imbalance2_theme_options['images_only'];
}

// google analytics
function imbalance2google()
{
	global $imbalance2_theme_options;

	return $imbalance2_theme_options['google'];
}

// custom menu
class Imbalance2_Walker_Nav_Menu extends Walker_Nav_Menu {
	function start_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<div class=\"imbalance2_submenu_container\"><ul class=\"sub-menu\"><li><ul class=\"imbalance2_submenu\">\n";
	}

	function end_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul></li></ul></div>\n";
	}
}

/**
 * Functions and definitions
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 720;

/** Tell WordPress to run imbalance2_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'imbalance2_setup' );

if ( ! function_exists( 'imbalance2_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override imbalance2_setup() in a child theme, add your own imbalance2_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Ten 1.0
 */
function imbalance2_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'imbalance2', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'header-left' => __( 'Header Left Navigation', 'imbalance2' ),
		'header-center' => __( 'Header Center Navigation', 'imbalance2' ),
		'header-right' => __( 'Header Right Navigation', 'imbalance2' ),
		'footer-left' => __( 'Footer Left Navigation', 'imbalance2' ),
		'footer-right' => __( 'Footer Right Navigation', 'imbalance2' )
	) );
}
endif;

/**
 * Sets the post excerpt length to 40 characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 * @since Twenty Ten 1.0
 * @return int
 */
function imbalance2_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'imbalance2_excerpt_length' );

/**
 * Replaces "[...]" (appended to automatically generated excerpts).
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string An ellipsis
 */
function imbalance2_auto_excerpt_more( $more ) {
	return '';
}
add_filter( 'excerpt_more', 'imbalance2_auto_excerpt_more' );

/**
 * Remove inline styles printed when the gallery shortcode is used.
 *
 * Galleries are styled by the theme in Twenty Ten's style.css. This is just
 * a simple filter call that tells WordPress to not use the default styles.
 *
 * @since Twenty Ten 1.2
 */
add_filter( 'use_default_gallery_style', '__return_false' );

/**
 * Deprecated way to remove inline styles printed when the gallery shortcode is used.
 *
 * This function is no longer needed or used. Use the use_default_gallery_style
 * filter instead, as seen above.
 *
 * @since Twenty Ten 1.0
 * @deprecated Deprecated in Twenty Ten 1.2 for WordPress 3.1
 *
 * @return string The gallery style filter, with the styles themselves removed.
 */
function imbalance2_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
// Backwards compatibility with WordPress 3.0.
if ( version_compare( $GLOBALS['wp_version'], '3.1', '<' ) )
	add_filter( 'gallery_style', 'imbalance2_remove_gallery_css' );

if ( ! function_exists( 'imbalance2_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own imbalance2_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Ten 1.0
 */
function imbalance2_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
			<div class="comment-avatar">
				<?php echo get_avatar( $comment, 60 ); ?>
			</div>
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'imbalance2' ); ?></em>
			<br />
		<?php endif; ?>
		
			<div class="comment-author">
				<?php printf( __( '%s', 'imbalance2' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
			</div>

			<div class="comment-meta commentmetadata">
				<?php
					/* translators: 1: date, 2: time */
					printf( __( '%1$s at %2$s', 'imbalance2' ), get_comment_date(),  get_comment_time() ); ?><?php edit_comment_link( __( '(Edit)', 'imbalance2' ), ' ' );
				?>
			</div><!-- .comment-meta .commentmetadata -->

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->

			<div class="comment-body"><?php comment_text(); ?></div>
		</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'imbalance2' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'imbalance2' ), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 *
 * To override this in a child theme, remove the filter and optionally add your own
 * function tied to the widgets_init action hook.
 *
 * This function uses a filter (show_recent_comments_widget_style) new in WordPress 3.1
 * to remove the default style. Using Twenty Ten 1.2 in WordPress 3.0 will show the styles,
 * but they won't have any effect on the widget in default Twenty Ten styling.
 *
 * @since Twenty Ten 1.0
 */
function imbalance2_remove_recent_comments_style() {
	add_filter( 'show_recent_comments_widget_style', '__return_false' );
}
add_action( 'widgets_init', 'imbalance2_remove_recent_comments_style' );

if ( ! function_exists( 'imbalance2_posted_by' ) ) :
function imbalance2_posted_by() {
	printf( __( '<span class="meta-sep">By</span> %1$s', 'imbalance2' ),
		sprintf( '<a href="%1$s" title="%2$s">%3$s</a>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'imbalance2' ), get_the_author() ),
			get_the_author()
		)
	);
}
endif;

if ( ! function_exists( 'imbalance2_posted_on' ) ) :
function imbalance2_posted_on() {
	printf( __( '%1$s', 'imbalance2' ),
		sprintf( '<span class="entry-date">%1$s</span>',
			get_the_date()
		)
	);
}
endif;

if ( ! function_exists( 'imbalance2_posted_in' ) ) :
function imbalance2_posted_in() {
	if ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( '%1$s', 'imbalance2' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%2$s" title="Permalink to %3$s" rel="bookmark">permalink</a>.', 'imbalance2' );
	}
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;

if ( ! function_exists( 'imbalance2_tags' ) ) :
function imbalance2_tags() {
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) printf(__( '<div class="entry-tags"><span>Tags:</span> %1$s</div>', 'imbalance2' ), $tag_list );
}
endif;
