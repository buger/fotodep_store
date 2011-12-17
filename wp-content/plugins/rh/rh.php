<?php
/**
 * @package Russian WordPress helper
 * @author MyWordPress.Ru team
 * @version 1.0.1
 */
/*
Plugin Name: MyWordPress.Ru Russian Helper
Plugin URI: http://mywordpress.ru/
Description: Плагин позволяет Вам отправлять сообщения о неправильном переводе прямо из интерфейса администратора
Author: MyWordPress.Ru team
Version: 1.0.1
Author URI: http://mywordpress.ru/
*/

function rushelper_notice() {
	if ( !$nag_count = get_option( 'rushelper_nag_count' ) )
		$nag_count = 0;
	if ($nag_count < 10) {
  	echo "<div class='update-nag'>Вы можете указать на ошибку в переводе, выделив текст и нажав Ctrl+Alt+R. <small><a href='http://mywordpress.ru/translation/' target='_blank'>Подробнее...</a></small></div>";
	  update_option('rushelper_nag_count', ++$nag_count);
	}
}

function rushelper_admin_footer_notice($text) {
	return $text."<br/><br/>Вы можете указать на ошибку в переводе, выделив текст и нажав Ctrl+Alt+R. <small><a href='http://mywordpress.ru/translation/' target='_blank'>Подробнее...</a></small>";  
}

function rushelper_admin_head()
{
  $scripts = array('/wp-content/plugins/rh/js/jquery.colorbox-min.js', '/wp-content/plugins/rh/js/shortcut.js', '/wp-content/plugins/rh/js/err-submit.js');
  foreach ($scripts as $script)
    printf("<script type='text/javascript' src='%s'></script>", $script); 
    
  printf("<link rel='stylesheet' href='%s' type='text/css' media='all' />", '/wp-content/plugins/rh/css/colorbox.css');
}

add_action("admin_head", "rushelper_admin_head");
add_action( 'admin_notices', 'rushelper_notice', 3 );
add_action( 'admin_footer_text', 'rushelper_admin_footer_notice');

?>