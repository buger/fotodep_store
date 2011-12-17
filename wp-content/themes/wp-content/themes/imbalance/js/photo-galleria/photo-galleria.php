<?php
/****************************************************************
Plugin Name: Photo Galleria
Plugin URI: http://graphpaperpress.com/2008/05/31/photo-galleria-plugin-for-wordpress/
Description: This plugin replaces the default gallery feature in WordPress 2.5+ with a minimal, jquery-powered gallery.
Version: 0.2.5
Author: Thad Allender
Author URI: http://graphpaperpress.com
License: GPL
*****************************************************************
Thanks to DevKick.com
http://devkick.com/lab/galleria/
*****************************************************************
Thanks to Justin Tadlock for code snippets
http://justintadlock.com/
*****************************************************************
Big thanks to Chandra Maharzan for debugging and awesomifying
http://nhuja.com
*****************************************************************/

// define constants
$foldername ='photo-galleria';
load_plugin_textdomain($foldername, '/'.dirname(plugin_basename(__FILE__)));
$pluginURI = '/themes/wp-content/themes/imbalance/js/';


// load scripts
if (!is_admin()) add_action( 'init', 'photo_galleria_load_scripts' );
function photo_galleria_load_scripts( ) {
	wp_enqueue_script('photo-galleria','/wp-content/themes/imbalance/js/photo-galleria/js/jquery.galleria.js', array('jquery'));
	wp_enqueue_style('photo-galleria-css', '/wp-content/themes/imbalance/js/photo-galleria/css/galleria.css', array(), '1.0' );
}

// add scripts to header
function photo_galleria_js_head(){

if(!is_admin()){
echo "
<script type='text/javascript'>
//<![CDATA[
jQuery(function($) {
		
		$('ul.gallery_list').addClass('show_gallery'); // adds new class name to maintain degradability
		$('.galleria_wrapper').remove();"; 
		
		echo "
		$('ul.show_gallery').galleria({
			history   : false, 
			clickNext : true,			
			onImage   : function(image,caption,thumb) { 
				
				// fade in the image &amp; caption
				if(!($.browser.mozilla && navigator.appVersion.indexOf('Win')!=-1) ) { // FF/Win fades large images terribly slow
				image.css('display','none').fadeIn(160);
				}
				caption.css('display','none').fadeIn(160);
				
				// fetch the thumbnail container
				var _li = thumb.parents('li');
				
				// fade out inactive thumbnail
				_li.siblings().children('img.selected').fadeTo(500,0.8);
				
				// fade in active thumbnail
				thumb.fadeTo('fast',1).addClass('selected');
				
				// add a title for the clickable image
				image.attr('title','Click for next image »');				
				
			},

			
			onThumb : function(thumb) { // thumbnail effects goes here
				
				// fetch the thumbnail container
				var _li = thumb.parents('li');
								
				// if thumbnail is active, fade all the way.
				var _fadeTo = _li.is('.active') ? '1' : '0.8';
				
				// fade in the thumbnail when finnished loading
				thumb.css({display:'none',opacity:_fadeTo}).fadeIn(1500);
				
				// hover effects
				thumb.hover(
					function() { thumb.fadeTo('fast',1); },
					function() { _li.not('.active').children('img').fadeTo('fast',0.8); } // don't fade out if the parent is active
				)
			}
		});";
	echo "// $('ul.show_gallery li:first').addClass('active'); // uncomment to display first image when gallery loads
	});	
	//]]>
	</script>";
}
}

add_action('wp_head','photo_galleria_js_head');

// modifies the gallery shortcode 
function photo_galleria_shortcode($attr) {

global $post;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}
	extract(shortcode_atts(array(
		'orderby' => 'menu_order ASC, ID ASC',
		'id' => $post->ID,
		'itemtag' => 'dl',
		'icontag' => 'dt',
		'captiontag' => 'dd',
		'columns' => 3,
		'size' => 'medium',
	), $attr));

	$id = intval($id);
	$attachments = get_children("post_parent=$id&post_type=attachment&post_mime_type=image&orderby={$orderby}");

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $id => $attachment )
			$output .= wp_get_attachment_link($id, $size, true) . "\n";
		return $output;
	}

	$listtag = tag_escape($listtag);
	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;



	// Open gallery
	$output = apply_filters('gallery_style', '<div class="photogalleria"><ul class="gallery_list">');

	// Loop through each gallery item
	foreach ( $attachments as $id => $attachment ) {
		
		// Attachment page ID
		$att_page = get_attachment_link($id);
		// Returns array
		$img = wp_get_attachment_image_src($id, $size);
		$img = $img[0];
		// If no caption is defined, set the title and alt attributes to title
		$title = $attachment->post_excerpt;
		if($title == '') $title = $attachment->post_title;
		// If no description is defined, set the description to the caption
		$description = $attachment->post_content;
		if($description == '') $description = $title;

		// Set the link to the attachment URL
		
		$output .= "\n\t\t<li>";
		
		/*
		if(!is_single()) {
			$output .= "<a href=\"".get_permalink()."\" title=\"$title\">";
		}
		*/
		
		// Output image
		$output .= '<img src="'.$img.'" alt="'.$title.'" title="'.$description.'" />';
	
		/*
		if(!is_single()) {
			// Close link
			$output .= "</a>";
		}
		*/
		
		$output .= "</li>";

	// Close individual gallery item

	}
// Close gallery
	$output .= "\n\t</ul>\n</div>";
	return $output;
}

/************************************************
Important stuff that runs this thing
************************************************/

// Remove original gallery shortcode
	remove_shortcode(gallery);

// Add a new shortcode
	add_shortcode('gallery', 'photo_galleria_shortcode');
?>