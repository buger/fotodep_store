<?php
/**
 * @package FixImageMargins
 * @author Justin Adie
 * @version 0.1.0
 */
/*
Plugin Name: FixImageMargins
Plugin URI: #
Description: removes the silly 10px margin from the new caption based images
Author: Justin Adie
Version: 0.1.0
Author URI: http://rathercurious.net
*/
class fixImageMargins{
    public $xs = 0; //change this to change the amount of extra spacing

    public function __construct(){
        add_filter('img_caption_shortcode', array(&$this, 'fixme'), 10, 3);
    }
    public function fixme($x=null, $attr, $content){

        extract(shortcode_atts(array(
                'id'    => '',
                'align'    => 'alignnone',
                'width'    => '',
                'caption' => ''
            ), $attr));

        if ( 1 > (int) $width || empty($caption) ) {
            return $content;
        }

        if ( $id ) $id = 'id="' . $id . '" ';

    return '<div ' . $id . 'class="wp-caption ' . $align . '" style="width: ' . ((int) $width + $this->xs) . 'px">'
    . $content . '<p class="wp-caption-text">' . $caption . '</p></div>';
    }
}
$fixImageMargins = new fixImageMargins();
