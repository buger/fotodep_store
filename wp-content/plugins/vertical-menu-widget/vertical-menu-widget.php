<?
/*
Plugin Name: Vertical Menu Widget
Plugin URI: http://wizardinternetsolutions.com/plugins/vertical-menu-widget/
Description: With this plugin you can create flat menus or show only the active categories children in a fold out style step menu.
Author: Wizard Internet Solutions
Version: 0.9
Author URI: http://wizardinternetsolutions.com
*/
function only_active_wp_nav($menuitems, $simple){
		// Nothing returned get out
		if (!$menuitems) return;
		// Remove the container div
		$pos = strpos($menuitems, '<li');
		$menuitems = substr($menuitems,$pos);
		$menuitems = str_replace( '</div>', '', $menuitems);
		// Change the sub menu div class name
		$menuitems = str_replace( 'sub-menu','children', $menuitems);
		//change the id to a class
		$menuitems = str_replace('id="menu-item-','class="page_item page-item-', $menuitems);
		$menuitems = str_replace('" class="menu-item menu', ' menu-item menu', $menuitems); 
		$menuitems = str_replace('menu-item menu-item', 'page_item page-item', $menuitems);
		//create an array of menu items
		$templist = explode("\n",$menuitems);
		//variables to use fo hide show menu
		if($simple == '1') $simple = true;
		else $simple = false;
		$showchildren = $simple;
		$menulevel=0;
		foreach ($templist as $value){
			$str=trim($value);
			if ($str) {
				// Open child menu unordered List
				$pos = strpos($str, '<ul class');
				if ($pos===0) {
					if ($showchildren) $menulist = $menulist .$value ."\n";
					$menulevel++;
				}
				// Close child menu unordered list
				$pos = strpos($str, '</ul>');
				if ($pos===0) {
					if ($showchildren) $menulist = $menulist .$value ."\n";
					$menulevel--;
				}
				// Close child menu list item
				$pos = strpos($str, '</li>');
				if ($pos===0) {
					if ($showchildren) $menulist = $menulist .$value."\n"; 
				}
				// menu item build the string
				$pos = strpos($str, '<li');
				if ($pos===0) {
					$str = $value;
					$pos = strpos($value, '<li');
					$str = preg_replace('/current[-|_]+[page|post|category|menu]+[-|_]+item+.*<a/', 'current_page_item"><a  class="active" ', $str); 
					$str = preg_replace('/current[-|_]+[page|post|category|menu]+[-|_]+ancestor+.*<a/', 'current_page_ancestor"><a class="active" ', $str);
					$str = preg_replace('/current[-|_]+[page|post|category|menu]+[-|_]+parent+.*<a/', 'current_page_ancestor"><a class="active" ', $str);
					if ($menulevel === 0) $showchildren = $simple;
					$additem = false;
					if(strpos($str,'current_page_ancestor') || strpos($str,'current_page_parent') || strpos($str,'current_page_item')) {
						$showchildren = true;
					}
					// Checks if parent is active and open to display children
					$menulvl = $menulevel - 1;
					if ( ($menulevel == 0) || (strpos($$menulvl,'class="active')) || ($simple == true) ) $additem = true;
					if ($additem){
						$menulist = $menulist .$str ."\n"; 
					}
					// Sets parent string to check children
					$$menulevel = $str;
				}
			}	
		}
		//$menulist = $menulist . '</ul>';
		echo $menulist;
}
/* Custom Product Specials Widget */
class vert_menu_widget extends WP_Widget {
	function vert_menu_widget() {
		// widget actual processes
		parent::WP_Widget(false, $name = 'Vertical Menu', array(
			'description' => 'Displays a Vertical Menu'
		));
	}
	function widget($args, $instance) {
		extract($args);
		echo $before_widget;
		$instance['title'] = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		if ( !empty($instance['title']) )
			echo $before_title . $instance['title'] . $after_title;
		echo '<ul class="menu ' . $instance['class'] . '">';
			$menuitems = wp_nav_menu( array( 'fallback_cb' => '', 'menu' =>  $instance['nav_menu'], 'echo' => false));
			only_active_wp_nav($menuitems, $instance['simple']);	
		//echo '</ul>';	
		echo $after_widget;
	}
	function update($new_instance, $old_instance) {
		return $new_instance;
	}
	function form($instance) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$class = isset( $instance['class'] ) ? $instance['class'] : '';
		$nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';
		// Get menus
		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
		// If no menus exists, direct the user to go and create some.
		if ( !$menus ) {
			echo '<p>'. sprintf( __('No menus have been created yet. <a href="%s">Create some</a>.'), admin_url('nav-menus.php') ) .'</p>';
			return;
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('class'); ?>"><?php _e('Class:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('class'); ?>" name="<?php echo $this->get_field_name('class'); ?>" value="<?php echo $class; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('nav_menu'); ?>"><?php _e('Select Menu:'); ?></label>
			<select id="<?php echo $this->get_field_id('nav_menu'); ?>" name="<?php echo $this->get_field_name('nav_menu'); ?>">
		<?php
			foreach ( $menus as $menu ) {
				$selected = $nav_menu == $menu->term_id ? ' selected="selected"' : '';
				echo '<option'. $selected .' value="'. $menu->term_id .'">'. $menu->name .'</option>';
			}
		?>
			</select>
		</p>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('simple'); ?>"><?php _e('Simple Menu:'); ?></label>
			<select id="<?php echo $this->get_field_id('simple'); ?>" name="<?php echo $this->get_field_name('simple'); ?>">
            	<option<?php echo $instance['simple'] == '1' ? ' selected="selected"' : ''; ?> value="1">Simple Menu</option>
                <option<?php echo $instance['simple'] == '0' ? ' selected="selected"' : ''; ?> value="0">Foldout Menu</option>
			</select>
		</p>
		<?php
	}
}
add_action('widgets_init', 'register_vertical_menu_widget');
function register_vertical_menu_widget() {
	register_widget('vert_menu_widget');
}
?>