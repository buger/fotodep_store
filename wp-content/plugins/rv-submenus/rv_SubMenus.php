<?php
/*
Plugin Name: RVSubMenu
Plugin URI: http://www.richardvenancio.com/category/wordpress
Description: This plug-in installs a sidebar widget to display a submenu according to the wordpress's menu
Version: 1.3
Author: Richard Venancio
Author URI: http://www.richardvenancio.com/
*/
?>
<?php
/*  Copyright 2011  Richard Venancio  (email : contato@richardvenancio.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
class RV_Sub_Menu_Widget extends WP_Widget {
	private $currentMenu = NULL;
	private $ancestorTop = NULL;
	private $ancestors = array();
	private $menuLevel = array();

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	function RV_Sub_Menu_Widget() {
		$widget_ops = array( 'classname' => 'widget_submenu_rv', 'description' => __( "Use this widget to show specific submenu from your wordpress's menu.", 'rv_sub_menu' ) );
		$this->WP_Widget( 'widget_submenu_rv', __( 'Submenu sidebar widget', 'rv_sub_menu' ), $widget_ops );
		$this->alt_option_name = 'widget_submenu_rv';

		$this->menuLevel[0] = 0;
		add_action( 'wp_update_nav_menu_item', array(&$this, 'add_nivel_menu_item'), 10, 3);
		
		add_filter( 'wp_nav_menu_objects', array(&$this, 'get_current_nav_menu') );
		
		
	}
	
	function add_nivel_menu_item($menu_id, $menu_item_db_id, $args){
		$this->menuLevel[$menu_item_db_id] = $this->menuLevel[$args['menu-item-parent-id']] + 1;
		
		$args['menu-item-level'] = $this->menuLevel[$menu_item_db_id];		
		update_post_meta( $menu_item_db_id, '_menu_item_level', $args['menu-item-level'] );
	}
	
	function get_current_nav_menu( $sorted_menu_items )
	{
		$current_element_markers = array('current-menu-ancestor' );
		
		foreach ( $sorted_menu_items as $menu_item ) {
			if(array_intersect( $current_element_markers, $menu_item->classes )){
				if(!$this->ancestorTop )$this->ancestorTop = $menu_item;
				$this->ancestors[] = $menu_item->ID;
			}
			if ( $menu_item->current ) {
				$this->currentMenu = $menu_item->ID;
				if(!$this->ancestorTop) $this->ancestorTop = $menu_item;
				break;
			}
		}

		return $sorted_menu_items;
	}
	

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @param array An array of standard parameters for widgets in this theme
	 * @param array An array of settings for this widget instance
	 * @return void Echoes it's output
	 **/
	function widget( $args, $instance ) {
		ob_start();
		
		if(!$this->currentMenu) return;
		
		extract( $args, EXTR_SKIP );
		
		if( ! isset( $instance['MenuID'] )){
			return;
		}
	        

		if( ! isset( $instance['MenuLevel'] )){
			$instance['MenuLevel'] = 2;
		}
		if( ! isset( $instance['ShowSubMenus'] )){
			$instance['ShowSubMenus'] = 0;
		}
		if( ! isset( $instance['SubMenusLevel'] )){
			$instance['SubMenusLevel'] = 0;
		}
		if( ! isset( $instance['ShowTitleAncestor'] )){
			$instance['ShowTitleAncestor'] = 0;
		}

		$menu_args = array(
			'meta_query' => array(
				array(
					'key' => '_menu_item_menu_item_parent',
					'value' => $this->ancestorTop->ID,
					'type' => 'numeric'
				)/*,
				array(
					'key' => '_menu_item_level',
					'value' => $instance['MenuLevel'],
					'type' => 'numeric'
				)*/
			)
		 );
		
		$menu_items = wp_get_nav_menu_items($instance['MenuID'], $menu_args);

		if ( $menu_items ) :
            echo '<script type="text/javascript">document.body.className+=" with-submenu-rv"; </script>';
			echo $before_widget;
			$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);
			
			if($instance['ShowTitleAncestor']){
				$title = $this->ancestorTop->title;
			}
			
			if($title){
				echo $before_title;
				echo $title; // Can set this with a widget option, or omit altogether
				echo $after_title;
			}
	
			?>
			<ul id="submenu-<?php echo $instance['MenuID']?>" class="submenu-rv">
			<?php 
			foreach ( (array) $menu_items as $key => $item ){
				$lvl = $instance['MenuLevel']+1;
				echo $this->start_li($item, $lvl);
				if(1 == (int)$instance['ShowSubMenus']){
					echo $this->get_submenu_items($instance, $item->ID, $lvl);
				}
				echo $this->end_li();
			}
			?>
			</ul>
			<?php
			echo $after_widget;
		endif; #( $menu_items )
		ob_flush();
	}
	
	function get_submenu_items($instance, $menu_parent, $lvl){
		$menu_args = array(
			'meta_query' => array(
				array(
					'key' => '_menu_item_menu_item_parent',
					'value' => $menu_parent,
					'type' => 'numeric'
				),
				array(
					'key' => '_menu_item_level',
					'value' => $lvl,
					'type' => 'numeric'
				)
			)
		 );
		 
		 $menu_items = wp_get_nav_menu_items($instance['MenuID'], $menu_args);
		
		$output = "";
		if ( $menu_items ){
			$output.= '<ul class="submenu-lvl-'.$lvl.' sub-submenu submenu-'.$menu_parent.'">';
			$lvl++;
			foreach ( (array) $menu_items as $key => $item ){
				$output.= $this->start_li($item, $lvl);
				if(1 == (int)$instance['ShowSubMenus'] && (0 == (int)$instance['SubMenusLevel'] || (($lvl+1) - $instance['MenuLevel']) <= $instance['SubMenusLevel'])){
					$output.= $this->get_submenu_items($instance, $item->ID, $lvl);
				}
				$output.= $this->end_li();
			}
			$output.= '</ul>';
		}
		return $output;
	}
	
	function start_li($item, $lvl){
		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'rv-submenu-item-' . $item->ID;
		$classes[] = 'rv-submenu-item-lvl-' . $lvl;
		$classes[] = 'rv-submenu-item';
		if(in_array($item->ID, $this->ancestors)) $classes[] = 'rv-submenu-item-ancestor';
		if($this->currentMenu == $item->ID) $classes[] = 'submenu-item-current';
		
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'submenu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= '<li' . $id . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		
		return $output;
	}
	
	function end_li(){
		return '</li>'."\n";
	}

	/**
	 * Deals with the settings when they are saved by the admin. Here is
	 * where any validation should be dealt with.
	 **/
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['MenuID'] = (int) $new_instance['MenuID'];
		$instance['MenuLevel'] = (int) $new_instance['MenuLevel'];
		$instance['ShowSubMenus'] = (int) $new_instance['ShowSubMenus'];
		$instance['SubMenusLevel'] = (int) $new_instance['SubMenusLevel'];
		$instance['ShowTitleAncestor'] = (int) $new_instance['ShowTitleAncestor'];
		
		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_submenu_rv'] ) )
			delete_option( 'widget_submenu_rv' );

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_submenu_rv', 'widget' );
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 **/
	function form( $instance ) {
		$title = isset( $instance['title']) ? esc_attr( $instance['title'] ) : '';
		$MenuID = isset( $instance['MenuID']) ? esc_attr( $instance['MenuID'] ) : '';
		$MenuLevel = isset( $instance['MenuLevel'] ) ? absint( $instance['MenuLevel'] ) : 1;
		$ShowSubMenus = isset( $instance['ShowSubMenus']) ? absint( $instance['ShowSubMenus'] ) : 0;
		$SubMenusLevel = isset( $instance['SubMenusLevel'] ) ? absint( $instance['SubMenusLevel'] ) : 0;
		$ShowTitleAncestor = isset( $instance['ShowTitleAncestor'] ) ? absint( $instance['ShowTitleAncestor'] ) : 0;
?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'rv_sub_menu' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
			<p>
                <input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'ShowTitleAncestor' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ShowTitleAncestor' ) ); ?>" value="1" <?php echo (1 == $ShowTitleAncestor ? 'checked="checked"' : '') ?> />
                <label for="<?php echo esc_attr( $this->get_field_id( 'ShowTitleAncestor' ) ); ?>"><?php _e( "Show ancestor's name as title", 'rv_sub_menu' ); ?></label>
            </p>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'MenuID' ) ); ?>"><?php _e( 'Menu:', 'rv_sub_menu' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'MenuID' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'MenuID' ) ); ?>" class="widefat">
            	<option value="0"><?php _e( 'Select a menu', 'rv_sub_menu' ); ?></option>
                <?php
				$loc = get_nav_menu_locations();
				foreach((array)$loc as $k => $v):
					$sel = ($v == $MenuID ? 'selected="selected"' : '');
				?>			
                	<option value="<?php echo esc_attr($v->term_id)?>" <?php echo $sel?> ><?php echo $k?><?php echo $v->url?></option>
                <?php endforeach;?>	
            </select>
            </p>
            
			<p><small><?php _e( 'Level start in 1', 'rv_sub_menu' ); ?></small><br/>
            <label for="<?php echo esc_attr( $this->get_field_id( 'MenuLevel' ) ); ?>"><?php _e( 'Start level:', 'rv_sub_menu' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'MenuLevel' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'MenuLevel' ) ); ?>" type="text" value="<?php echo esc_attr( $MenuLevel ); ?>" size="3" /></p>
            
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'ShowSubMenus' ) ); ?>"><?php _e( 'Show sub-menu:', 'rv_sub_menu' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'ShowSubMenus' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'ShowSubMenus' ) ); ?>" class="widefat">
            	<option value="0" <?php echo (0 == $ShowSubMenus ? 'selected="selected"' : '') ?>><?php _e( 'No', 'rv_sub_menu' ); ?></option>
                <option value="1" <?php echo (1 == $ShowSubMenus ? 'selected="selected"' : '') ?>><?php _e( 'Yes', 'rv_sub_menu' ); ?></option>
            </select>
            </p>
            
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'SubMenusLevel' ) ); ?>"><?php _e( 'Depth of sub-menu:', 'rv_sub_menu' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'SubMenusLevel' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'SubMenusLevel' ) ); ?>" type="text" value="<?php echo esc_attr( $SubMenusLevel ); ?>" size="3" />
            <small><?php _e( '0 show all', 'rv_sub_menu' ); ?>.</small>
            </p>
		<?php
	}
}

if(function_exists('add_action')){
  add_action('widgets_init', create_function('', 'register_widget("RV_Sub_Menu_Widget");'));
  $plugin_dir = basename(dirname(__FILE__)).'/languages';
  load_plugin_textdomain( 'rv_sub_menu', false, $plugin_dir);
}
?>
