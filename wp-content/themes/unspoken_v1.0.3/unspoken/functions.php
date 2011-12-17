<?php
/***** Theme setup *****/

load_theme_textdomain('unspoken', get_template_directory() . '/languages');
add_theme_support( 'post-thumbnails' );
add_theme_support( 'automatic-feed-links' );
add_custom_background();

function unspoken_setup() {
    add_image_size( 'micro', 50, 50, true );
    add_image_size( 'mini', 140, 90, true );
    add_image_size( 'mini-ec', 135, 80, true );
    add_image_size( 'general', 300, 180, true );
    add_image_size( 'slide-home', 360, 225, true );
    if ( get_option('unspoken_mag_use') ) add_image_size( 'slide-magazine', 940, 400, true );
    add_image_size( 'video-mini', 134, 74, true );
    add_image_size( 'video', 460, 260, true );
    update_option('embed_size_w', 620);
    if (!isset($_COOKIE['mode'])) setcookie('mode', ( get_option('unspoken_view_type') == '0' ) ? 'list' : 'grid', time() + 60 * 60 * 24 * 30, COOKIEPATH, COOKIE_DOMAIN, false);
    global $menus;
    if ( function_exists( 'register_nav_menus' ) ) {
        register_nav_menus($menus);
    }
}
add_action( 'init', 'unspoken_setup' );

// Default options setup
if ( is_admin() && isset($_GET['activated'] ) && $pagenow == 'themes.php' ) {
    update_option('unspoken_pagination_mode', 0 );
    update_option('unspoken_mag_auto', true );
    update_option('unspoken_mag_delay', 7000 );
	update_option('unspoken_postedon_date', true);
    update_option('unspoken_postedon_comm', true);
    update_option('unspoken_postedon_author', true);
    update_option('unspoken_rel_type1', true);
    update_option('unspoken_share_type1', true);
    update_option('unspoken_slider_auto', true);
    update_option('unspoken_slider_delay', 7000);
    update_option('unspoken_video_visible', 4);
}

// Add items to the admin bar
function unspoken_admin_bar() {
    global $wp_admin_bar;
    if ( !is_super_admin() || !is_admin_bar_showing() )
        return;
    
    $wp_admin_bar->add_menu( array( 'id' => 'wpshower_admin', 'title' => __( 'Unspoken Theme', 'unspoken' ), 'href' => FALSE ) );
    $wp_admin_bar->add_menu( array( 'parent' => 'wpshower_admin', 'title' => __( 'Theme Options', 'unspoken' ), 'href' => '/wp-admin/themes.php?page=theme_options.php' ) );
    $wp_admin_bar->add_menu( array( 'parent' => 'wpshower_admin', 'title' => __( 'Support Forum', 'unspoken' ), 'href' => 'http://wpshower.com/forums' ) );
}
add_action( 'admin_bar_menu', 'unspoken_admin_bar', 1000 );

// Theme Options

require_once (TEMPLATEPATH . '/core/theme_options.php');

// Advanced post options

require_once (TEMPLATEPATH . '/core/advanced_post_options.php');

// Widgets

require_once (TEMPLATEPATH . '/core/widgets.php');

// Shortcodes

require_once (TEMPLATEPATH . '/core/shortcodes.php');

// Video
require_once (TEMPLATEPATH . '/lib/AutoEmbed.php');

/***** Navigation & Menu *****/

$menus = array(
    __('Top menu', 'unspoken') => __('Top menu', 'unspoken'),
    __('Navigation', 'unspoken') => __('Navigation', 'unspoken'),
    __('Navigation footer', 'unspoken') => __('Navigation footer', 'unspoken'),
    __('Footer left linkset', 'unspoken') => __('Footer left linkset', 'unspoken'),
    __('Footer right linkset', 'unspoken') => __('Footer right linkset', 'unspoken')
);
foreach ( $menus as $key=>$value  ) {
    if ( !is_nav_menu( $key ) ) wp_update_nav_menu_item( wp_create_nav_menu( $key ), 1 );
}

class extended_walker extends Walker_Nav_Menu{
	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {

		if ( !$element )
			return;

		$id_field = $this->db_fields['id'];

		//display this element
		if ( is_array( $args[0] ) )
			$args[0]['has_children'] = ! empty( $children_elements[$element->$id_field] );

		//Adds the 'parent' class to the current item if it has children
		if( ! empty( $children_elements[$element->$id_field] ) )
			array_push($element->classes,'parent');

		$cb_args = array_merge( array(&$output, $element, $depth), $args);

		call_user_func_array(array(&$this, 'start_el'), $cb_args);

		$id = $element->$id_field;

		// descend only when the depth is right and there are childrens for this element
		if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {

			foreach( $children_elements[ $id ] as $child ){

				if ( !isset($newlevel) ) {
					$newlevel = true;
					//start the child delimiter
					$cb_args = array_merge( array(&$output, $depth), $args);
					call_user_func_array(array(&$this, 'start_lvl'), $cb_args);
				}
				$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
			}
			unset( $children_elements[ $id ] );
		}

		if ( isset($newlevel) && $newlevel ){
			//end the child delimiter
			$cb_args = array_merge( array(&$output, $depth), $args);
			call_user_func_array(array(&$this, 'end_lvl'), $cb_args);
		}

		//end this element
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array(&$this, 'end_el'), $cb_args);
	}
}

/***** Comment template *****/

if ( ! function_exists( 'unspoken_comment' ) ) {
    function unspoken_comment( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment;
        switch ( $comment->comment_type ) :
		    case '' :
        ?>
        <li id="li-comment-<?php comment_ID(); ?>">
             <div <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
                 <table>
                     <col style="width: 70px;"/>
                     <col style="width: 90px;"/>
                     <col />
                     <tr>
                         <td>
                             <?php
                             if (get_option('unspoken_skin') && get_option('unspoken_skin') != 'default') {
                                echo get_avatar($comment, 60, get_bloginfo('template_url') . '/skins/unspoken-' . get_option('unspoken_skin') . '/images/no-avatar.png');
                             } else {
                                echo get_avatar($comment, 60, get_bloginfo('template_url') . '/images/no-avatar.png');
                             }
                             ?>
                             <div class="reply">
                                 <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
                             </div><!-- .reply -->
                         </td>
                         <td>
                             <div class="comment-meta commentmetadata">
                                 <p class="comment-author vcard"><?php comment_author_link(); ?></p>
                                 <p><?php comment_date(); ?></p>
                                 <p><?php edit_comment_link( __( 'Edit', 'unspoken' ), ' ' ); ?></p>
                             </div>
                         </td>
                         <td>
                             <div class="comment-text">
                                 <?php comment_text(); ?>
                                 <?php if ( $comment->comment_approved == '0' ) : ?>
                                     <em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'unspoken' ); ?></em>
                                 <?php endif; ?>
                             </div>
                         </td>
                     </tr>
                 </table>
             </div><!-- #comment-##  -->
        <?php
			    break;
            case 'pingback'  :
            case 'trackback' :
        ?>
        <li id="li-comment-<?php comment_ID(); ?>">
            <div <?php comment_class(); ?> id="ping-<?php comment_ID(); ?>">
                <div class="comment-text"><?php comment_author_link(); ?></div>
            </div><!-- #ping-##  -->
        <?php
                break;
        endswitch;
    }
}

/***** Misc *****/

// "Posted on" function
function unspoken_posted_on( $args ) {
    extract( $args );
    $data = array();
    if ( $date ) $data[] = get_the_date();
    if ( $category ) $data[] = get_the_category_list( ', ' );
    if ( $comment ) $data[] = sprintf(__( '<a href="%1$s">%2$u Comments</a>', 'unspoken' ), get_comments_link(), get_comments_number());
    if ( $author ) $data[] = sprintf('<a class="url fn n" href="%1$s" title="%2$s">%3$s</a>', get_author_posts_url( get_the_author_meta( 'ID' ) ), sprintf(esc_attr__( 'View all posts by %s', 'unspoken' ), get_the_author() ), get_the_author());
    $output = '';
    foreach ( $data as $k=>$v ) {
        if ( $k > 0 && $v != '' ) $output .= ' &middot; ';
        $output .= $v;
    }
    echo $output;
}

// Fixes margins from images with caption
require_once (TEMPLATEPATH . '/lib/FixImageMargins.php');

// Comments, trackbacks and pings separate count

// unspoken_commentCount(); //echoes the comment count
// unspoken_commentCount('comments'); //same as the example on top
// unspoken_commentCount('pings'); //echoes number of trackbacks and pingbacks
// unspoken_commentCount('trackbacks'); //echoes number of trackbacks
// unspoken_commentCount('pingbacks'); //echoes number of pingbacks

function unspoken_commentCount($type = 'comments'){

	if($type == 'comments'):
		$typeSql = 'comment_type = ""';
	elseif($type == 'pings'):
		$typeSql = 'comment_type != ""';
	elseif($type == 'trackbacks'):
		$typeSql = 'comment_type = "trackback"';
	elseif($type == 'pingbacks'):
		$typeSql = 'comment_type = "pingback"';
	endif;

	global $wpdb;

    $oneText = '1';
    $moreText = '%';
    $noneText = '0';

    $result = $wpdb->get_var('
        SELECT
            COUNT(comment_ID)
        FROM
            '.$wpdb->comments.'
        WHERE
            '.$typeSql.' AND
            comment_approved="1" AND
            comment_post_ID= '.get_the_ID()
    );

	if($result == 0):
		return str_replace('%', $result, $noneText);
	elseif($result == 1):
		return str_replace('%', $result, $oneText);
	elseif($result > 1):
		return str_replace('%', $result, $moreText);
	endif;

}

// Custom password form
function unspoken_password_form($output) {
    if ( !$output ) return;
    $output = str_replace('<input type="submit"', '<input type="submit" id="submit"', $output);
    return $output;
}
add_action('the_password_form', 'unspoken_password_form');

// Custom excerpt for comments
function unspoken_excerpt($string, $limit) {
    $more = '';
    $words = explode(" ",$string);
    if ( count($words) >= $limit) $more = '...';
    $output = strip_tags( implode(" ",array_splice($words, 0, $limit)).$more );
    echo $output;
}

// Custom excerpt more
function unspoken_excerpt_more($more) {
	return '...';
}
add_filter('excerpt_more', 'unspoken_excerpt_more');

// Get tags
function unspoken_get_tags() {
    $tags = get_tags('number=30&orderby=count&order=DESC');
    if ( $tags ) {
        $output = __( '<p>Popular tags</p>', 'unspoken' );
        $i = 0;
        foreach ($tags as $tag){
            $i++;
            $tag_link = get_tag_link($tag->term_id);
            if ( $i > 1 ) $output .= ", ";
            $output .= "<a href='{$tag_link}' title='{$tag->name}' class='{$tag->slug}'>";
            $output .= "{$tag->name}</a>";
        }
        echo $output;
    }
}

// Add wmode="transparent" to embed video code
function unspoken_add_wmode($oembvideo) {
$patterns = array();
$replacements = array();
$patterns[] = '/<\/param><embed/';
$patterns[] = '/allowscriptaccess="always"/';

$replacements[] = '</param><param name="wmode" value="transparent"></param><embed';
$replacements[] = 'wmode="transparent" allowscriptaccess="always"';

return preg_replace($patterns, $replacements, $oembvideo);

	return $oembvideo;
}
add_filter('embed_oembed_html', 'unspoken_add_wmode');


// Get logo
function unspoken_get_logo($url) {
    if ( $url ) {
        $sizes = getimagesize($url);
        return '<img src="' . $url . '" ' . $sizes[3] . ' alt="' . get_bloginfo( 'name' ) . '"/>';
    }
}

// Get font
function unspoken_get_font($font) {
    switch ( $font ) {
        case 'helvetica' :
            echo '<style type="text/css">body {font: 62.5% Helvetica, Arial, sans-serif;}</style>';
            break;
        case 'times' :
            echo '<style type="text/css">body {font: 62.5% "Times New Roman", times;}</style>';
            break;
        case 'courier' :
            echo '<style type="text/css">body {font: 62.5% "Courier New", Courier, monospace;}</style>';
            break;
        case 'verdana' :
            echo '<style type="text/css">body {font: 62.5% Verdana, Geneva, sans-serif;}</style>';
            break;
        case 'georgia' :
            echo '<style type="text/css">body {font: 62.5% Georgia, serif;}</style>';
            break;
        case 'trebuchet' :
            echo '<style type="text/css">body {font: 62.5% "Trebuchet MS", Helvetica, sans-serif;}</style>';
    }
}

function unspoken_excerpt_length($length) {
	return 33;
}
add_filter('excerpt_length', 'unspoken_excerpt_length');

// Fix wordpress editor formatting
function webtreats_formatter($content) {
	$new_content = '';

	/* Matches the contents and the open and closing tags */
	$pattern_full = '{(\[raw\].*?\[/raw\])}is';

	/* Matches just the contents */
	$pattern_contents = '{\[raw\](.*?)\[/raw\]}is';

	/* Divide content into pieces */
	$pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

	/* Loop over pieces */
	foreach ($pieces as $piece) {
		/* Look for presence of the shortcode */
		if (preg_match($pattern_contents, $piece, $matches)) {

			/* Append to content (no formatting) */
			$new_content .= $matches[1];
		} else {

			/* Format and append to content */
			$new_content .= wptexturize(wpautop($piece));
		}
	}

	return $new_content;
}

// Remove the 2 main auto-formatters
remove_filter('the_content', 'wpautop');
remove_filter('the_content', 'wptexturize');

// Before displaying for viewing, apply this function
add_filter('the_content', 'webtreats_formatter', 99);

// Add link classes for default pagination
function n_posts_link_attributes(){
	return 'class="nextpostslink"';
}
function p_posts_link_attributes(){
	return 'class="previouspostslink"';
}
add_filter('next_posts_link_attributes', 'n_posts_link_attributes');
add_filter('previous_posts_link_attributes', 'p_posts_link_attributes');

add_editor_style( 'editor-style.css' );
