<?php
/*
Plugin Name: Advanced Sidebar Menu
Plugin URI: http://www.vimm.com
Description: Creates dynamic menu based on child/parent relationship.
Author: Mat Lipe
Version: 1.2
Author URI: http://www.vimm.com

    email: mat@lipeimagination.info

*/

// register FooWidget widget
add_action( 'widgets_init', create_function( '', 'return register_widget("advanced_sidebar_menu");' ) );

class advanced_sidebar_menu extends WP_Widget {

#-----------------------------------------------------------------------------------------------------------------------------------
	  // this creates the widget form for the dashboard
	function form( $instance ) {
			if ( $instance ) {
				$exclude = esc_attr( $instance[ 'exclude' ] );
			}
			else {
				$exclude = '';
			}
			?>
			<p>
			<label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Pages To Be Excluded From Parents. "If a page is excluded from Parents the child pages will become the parents and not be shown in this menu. Make sure to add commas between IDs e.g. 4,11,6"'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo $exclude; ?>" />
			</p>
            <p> Include Parent Page <input id="<?php echo $this->get_field_name('include_parent'); ?>" name="<?php echo $this->get_field_name('include_parent'); ?>" type="checkbox" value="checked" <?php echo $instance['include_parent']; ?>/></p>
			<?php 
		}

#------------------------------------------------------------------------------------------------------------------------------
	// this allows more than one instance

	function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['exclude'] = strip_tags($new_instance['exclude']);
			$instance['include_parent'] = strip_tags($new_instance['include_parent']);
			return $instance;
		}

#-------------------------------------------------------------------------------------------------------------------------

  	// This decides the name of the widget
	function advanced_sidebar_menu( ) {
			parent::WP_Widget( 'advanced_sidebar_menu', $name = 'Advanced Sidebar Menu' );
		}


#---------------------------------------------------------------------------------------------------------------------------

    // adds the output to the widget area on the page
	function widget($args, $instance) {
		?>
		<div id="<?php echo $args['widget_id']; ?>" class="advanced-sidebar-menu"><ul class="parent-sidebar-menu" >
   	 <?php

	  global $wpdb;
	  global $p;
	  global $post;
	  
	  if( $instance['exclude'] != '' ){
		  $exclude = explode( ',', $instance['exclude'] );
	  }
	  
 
	//makes the custom menu	
 
		if($post->ancestors){
			$parent = $wpdb->get_var( "SELECT post_parent from wp_posts WHERE ID=".$post->ID );
			 
			while($parent != FALSE){
		//----------------------------------- make the pages listed in the exludes menu not show ---------------------------------	
			  if(isset($exclude)){
				foreach( $exclude as $e ){
					if( $parent == $e ){
						$toggle = 'yes';
					}
				}}
				  if( $toggle == 'yes' ){
					  $toggle = '';
				  } else {
		//--------------------------------------------------------------------------------------------------------------------			  
					$p = $parent;
				  }
				$parent = $wpdb->get_var( "SELECT post_parent from wp_posts WHERE ID=".$parent);
			}
		
		} else {
			#--------- If this is the parent ------------------------------------------------
			$p = $post->ID;
		}

	$result = $wpdb->get_results( "SELECT ID FROM wp_posts WHERE post_parent = $p AND post_type='page' Order by menu_order" );
   
    if( $instance['include_parent'] == 'checked' ){
	
        wp_list_pages("sort_column=menu_order&title_li=&echo=1&depth=1&include=".$p);
			echo '<ul class="child-sidebar-menu">';
	}

              //=----------------------------------- makes the link list -----------------------------------------
	foreach($result as $pageID){
    
		wp_list_pages("sort_column=menu_order&title_li=&echo=1&depth=1&include=".$pageID->ID);

		if($pageID->ID == $post->ID or $pageID->ID == $post->post_parent or in_array($pageID->ID, $post->ancestors) ):
		   echo '<ul class="grandchild-sidebar-menu">';
		   wp_list_pages("sort_column=menu_order&title_li=&echo=1&depth=3&child_of=".$pageID->ID);
  	     echo '</ul>';
		   endif;
    

	}
		if( $instance['include_parent'] == 'checked' ){
	  echo '</ul>';
	}
	?>
     	  </ul></div>
     	       <!-- end of very-custom-menu -->
     	       <?php
	}
}
?>