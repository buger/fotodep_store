<?php

/*** Top navigation ***/

function register_menu() {
    register_nav_menu('Header', __('Header'));
}
add_action( 'init', 'register_menu' );

if ( !is_nav_menu('Header')) {
    $menu_id = wp_create_nav_menu('Header');
    wp_update_nav_menu_item($menu_id, 1);
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

/*** Commentlist ***/

function commentlist($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    ?>
    <li id="li-comment-<?php comment_ID() ?>">
        <div id="comment-<?php comment_ID(); ?>" <?php comment_class('comment_item clear'); ?>>
            <div class="comment_meta">Posted on <?php printf( __('%1$s'), get_comment_date()); ?> by <?php printf(__('<cite class="fn">%s</cite>'), get_comment_author_link()) ?></div>
            <div class="comment_text"><?php comment_text() ?></div>
        </div>
<?php
}

/*** Custom Posts ***/

register_taxonomy(
    'works',
    'portfolio',
    array(
        'label' => __('Portfolio Categories'),
        'singular_label' => __('Portfolio Category'),
        'hierarchical' => true,
        'query_var' => true,
        'rewrite' => true,
        'show_in_nav_menus' => true,
    )
);

register_post_type(
    'portfolio',
    array(
        'label' => __('Portfolio'),
        'singular_label' => __('Work'),
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => true,
        'query_var' => true,
        'show_in_nav_menus' => true,
        'menu_position' => 3,
        'taxonomies' => array('portfolio'),
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'custom-fields'),
        '_builtin' => false, // It's a custom post type, not built in!
));

/*** Images ***/

add_theme_support('post-thumbnails');
update_option('thumbnail_size_w', 145);
update_option('thumbnail_size_h', 145);
update_option('large_size_w', 785);

/*** Misc ***/

function commentdata_fix($commentdata) {
    if ( $commentdata['comment_author_url'] == 'WWW') {
        $commentdata['comment_author_url'] = '';
    }
    if ($commentdata['comment_content'] == 'Write your comment') {
        $commentdata['comment_content'] = '';
    }
    return $commentdata;
}
add_filter('preprocess_comment','commentdata_fix');

function getTinyUrl($url) {
    $tinyurl = file_get_contents("http://tinyurl.com/api-create.php?url=".$url);
    return $tinyurl;
}

function get_blogurl() {
    if (get_option('show_on_front') == 'page' && get_option('page_for_posts') != 0) {
        $blogpage = get_page(get_option('page_for_posts'));
        echo $blogpage -> guid;
    } else {
        echo get_option('home');
    }
}

function catlist() { ?>
    <ul class="tags jsddm">
        <li>
            <a href="#">Blog categories</a>
            <ul class="taglist">
                <?php wp_list_categories('title_li=&hierarchical=0&'); ?>
            </ul>
        </li>
    </ul>
<?php
}

function n_posts_link_attributes(){
	return 'class="nextpostslink"';
}
function p_posts_link_attributes(){
	return 'class="previouspostslink"';
}
add_filter('next_posts_link_attributes', 'n_posts_link_attributes');
add_filter('previous_posts_link_attributes', 'p_posts_link_attributes');


?>
