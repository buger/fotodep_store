<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
<head> 
  <title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>          
  <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" title="no title" charset="utf-8"/>
  <?php wp_head(); ?>
</head>
<body>

<div id="main_container">

  <div id="menu_container">
    <ul class="left_list">
      <li><a href="/">Home</a></li>
      <li><a href="/about/">About</a></li>
      <li><a href="/service/">Services</a></li>
      <li><a href="/resume/">Resume</a></li>
      <li><a href="/contact/">Contact</a></li>
    </ul>
  
  
    <ul class="right_list">
      <li><a href="http://www.twitter.com"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/twitter-icon.png" /></a></li>
      <li><a href="http://www.facebook.com"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/facebook-icon.png" /></a></li>
      <li><a href="http://www.flickr.com"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/flickr-icon.png" /></a></li>
    </ul>
    
    <div class="clear"></div>
  </div><!--//menu_container-->
  
  <div id="header_container">
    <a href="<?php echo bloginfo('url'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/logo.png" class="logo" /></a>
    
   
    
    <div class="clear"></div>
  </div><!--//header_container-->
  
  <div id="header_category_container">
    
    <div class="clear"></div>
  </div><!--//header_category_container-->