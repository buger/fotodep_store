<?php

// Place content above the post
function unspoken_wide( $atts, $content = null ){
    return '<div class="wide">' . do_shortcode($content) . '</div>';
}
add_shortcode( 'wide', 'unspoken_wide' );

// Place content to the post sidebar (left side)
function unspoken_aside( $atts, $content = null ){
    return '<div class="aside">' . do_shortcode($content) . '</div>';
}
add_shortcode( 'aside', 'unspoken_aside' );

// Button
function unspoken_button( $atts, $content = null ) {
    extract(shortcode_atts(array(
    'link' => '#',
    'size' => 'small',
    'bg' => '',
    'color' => '',
    'target' => '',
    'float'	=> ''
    ), $atts));

	$size = ($size) ? ' button-'.$size : 'button-small';
    $bg = ($bg) ? 'background-color: '.$bg.';' : '';
    $color = ($color) ? ' color: '.$color.';' : '';
	$target = ($target == 'blank') ? ' target="_blank"' : '';
    $float = ($float) ? ' align'.$float : '';

	$out = '<a' .$target. ' style="'.$bg.$color.'" class="unspoken-button' .$size.$float. '" href="' .$link. '">' .do_shortcode($content). '</a>';

    return $out;
}
add_shortcode('button', 'unspoken_button');

// Infobox
function unspoken_box( $atts, $content = null ) {
    extract(shortcode_atts(array(
    'bg' => '#f9f1cb',
    'color' => '#3b3b3b'
    ), $atts));

    $bg = 'background-color: '.$bg.';';
    $color = ' color: '.$color.';';

	$out = '<div style="'.$bg.$color.'" class="unspoken-box">' .do_shortcode($content). '</div>';

    return $out;
}
add_shortcode('box', 'unspoken_box');

// List
function unspoken_list( $atts, $content = null ) {
    extract(shortcode_atts(array(
    'style' => 'tick'
    ), $atts));

    $style = ($style) ? ' list-'.$style : '';

	$out = '<div class="unspoken-list' . $style . '">' .do_shortcode($content). '</div>';

    return $out;
}
add_shortcode('list', 'unspoken_list');

// Social
function unspoken_social( $atts, $content = null ) {
    extract(shortcode_atts(array(
    'type' => 'tweet',
    'float' => ''
    ), $atts));

    switch ( $type ) {
        case 'tweet' :
            $out = '<a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
            break;
        case 'digg' :
            $out = '<script type="text/javascript"> (function() { var s = document.createElement(\'SCRIPT\'), s1 = document.getElementsByTagName(\'SCRIPT\')[0]; s.type = \'text/javascript\'; s.async = true; s.src = \'http://widgets.digg.com/buttons.js\'; s1.parentNode.insertBefore(s, s1); })(); </script> <a class="DiggThisButton DiggMedium"></a>';
            break;
        case 'fblike' :
            $out = '<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="' . get_permalink() . '" show_faces="false" width="460" font=""></fb:like>';
    }
    $float_cont = ( $float ) ? $float : '';

    $float = ( $float_cont ) ? ' align' . $float_cont : '';
    $clear = ( $float_cont ) ? ' style="clear: ' . $float_cont . ';"' : '';

    $out = '<div' . $clear . ' class="unspoken-social' . $float . '">' . $out . '</div>';

    return $out;
}
add_shortcode('social', 'unspoken_social');

// Columns
// 2 column
function unspoken_twocol_one( $atts, $content = null ) {
    extract(shortcode_atts(array(), $atts));

	$out = '<div class="unspoken-twocol-one">' .do_shortcode($content). '</div>';

    return $out;
}
add_shortcode('twocol_one', 'unspoken_twocol_one');

function unspoken_twocol_one_last( $atts, $content = null ) {
    extract(shortcode_atts(array(), $atts));

	$out = '<div class="unspoken-twocol-one last">' .do_shortcode($content). '</div><div class="clear"></div>';

    return $out;
}
add_shortcode('twocol_one_last', 'unspoken_twocol_one_last');

// 4 column
function unspoken_fourcol_one( $atts, $content = null ) {
    extract(shortcode_atts(array(), $atts));

	$out = '<div class="unspoken-fourcol-one">' .do_shortcode($content). '</div>';

    return $out;
}
add_shortcode('fourcol_one', 'unspoken_fourcol_one');

function unspoken_fourcol_one_last( $atts, $content = null ) {
    extract(shortcode_atts(array(), $atts));

	$out = '<div class="unspoken-fourcol-one last">' .do_shortcode($content). '</div><div class="clear"></div>';

    return $out;
}
add_shortcode('fourcol_one_last', 'unspoken_fourcol_one_last');
