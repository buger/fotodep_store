<?php
/*
Plugin Name: Advanced Sidebar Menu
Plugin URI: http://www.vimm.com
Description: Creates dynamic menu based on child/parent relationship.
Author: Mat Lipe
Version: 1.4.4
Author URI: http://www.vimm.com

    email: mat@lipeimagination.info

*/

// register FooWidget widget
add_action( 'widgets_init', create_function( '', 'return register_widget("advanced_sidebar_menu");' ) );

class advanced_sidebar_menu extends WP_Widget {

#-----------------------------------------------------------------------------------------------------------------------------------
	  // this creates the widget form for the dashboard
	function form( $instance ) {
			
			?>
			
            <p> Include Parent Page <input id="<?php echo $this->get_field_name('include_parent'); ?>" 
            	name="<?php echo $this->get_field_name('include_parent'); ?>" type="checkbox" value="checked" 
            	<?php echo $instance['include_parent']; ?>/></p>
			
            			
			<p> Include Parent Even With No Children<input id="<?php echo $this->get_field_name('include_childless_parent'); ?>"
			name="<?php echo $this->get_field_name('include_childless_parent'); ?>" type="checkbox" value="checked" 
					<?php echo $instance['include_childless_parent']; ?>/></p>
			<?php 
		}

#------------------------------------------------------------------------------------------------------------------------------
	// this allows more than one instance

	function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['include_childless_parent'] = strip_tags($new_instance['include_childless_parent']);
			$instance['include_parent'] = strip_tags($new_instance['include_parent']);
			return $instance;
		}

#-------------------------------------------------------------------------------------------------------------------------

  	// This decides the name of the widget
	function advanced_sidebar_menu( ) {
				/* Widget settings. */
		$widget_ops = array( 'classname' => 'sidebar-menu', 'description' => 'Creates a menu of all the pages using the child/parent relationship' );


		/* Create the widget. */
		$this->WP_Widget( 'advanced_sidebar_menu', 'Sidebar Menu', $widget_ops, $control_ops );
		}


#---------------------------------------------------------------------------------------------------------------------------

    // adds the output to the widget area on the page
	function widget($args, $instance) {
	 		 global $wpdb;
	 		 global $p;
	  		global $post;
		   
		//makes the custom menu	
           
	    	 #-- if the post has parrents
			if($post->ancestors){
				$parent = $wpdb->get_var( "SELECT post_parent from wp_posts WHERE ID=".$post->ID );
			 
				//--- If there is a parent of the post set $p to it and check if there is a parent as well
				while($parent != FALSE){
						$p = $parent;
				    	$parent = $wpdb->get_var( "SELECT post_parent from wp_posts WHERE ID=".$parent);
				}
		
			} else {
				#--------- If this is the parent ------------------------------------------------
				$p = $post->ID;
			}
		

			$result = $wpdb->get_results( "SELECT ID FROM wp_posts WHERE post_parent = $p AND post_type='page' Order by menu_order" );
	
    		#---- if there are no children do not display the parent unless it is check to do so
    		if($result != false || $instance['include_childless_parent'] == 'checked' ){
				
				  //Start the menu 
				echo '<div id="'.$args['widget_id'].'" class="advanced-sidebar-menu widget">';
				
				
		    	echo   '<ul class="parent-sidebar-menu" >'; 
				#-- if the checkbox to include parent is checked
    			if( $instance['include_parent'] == 'checked' ){
		     		 $parent_toggle = TRUE;

				#-- list the parent page
       	 			wp_list_pages("sort_column=menu_order&title_li=&echo=1&depth=1&include=".$p);
					echo '<ul class="child-sidebar-menu">';
				}
			}

              //=----------------------------------- makes the link list -----------------------------------------
		foreach($result as $pID){
         	 #--echo the current page from the $result
			wp_list_pages("sort_column=menu_order&title_li=&echo=1&depth=1&include=".$pID->ID);
          
	      		#-- if the link that was just listed is the current page we are on
			if($pID->ID == $post->ID or $pID->ID == $post->post_parent or in_array($pID->ID, $post->ancestors) ){
		
			 	#-- Create a new menu with all the children under it
				echo '<ul class="grandchild-sidebar-menu">';

				wp_list_pages("sort_column=menu_order&title_li=&echo=1&depth=3&child_of=".$pID->ID);
	
				echo '</ul>';
		   
			}
    
		}
	   		#-- if the options above echoed the parent and therefore added another ul
		if( $parent_toggle == TRUE ){
	 		 echo '</ul>';
		}
		
		  #-- If there was a menu close it off
		if($result != false || $instance['include_childless_parent'] == 'checked' ){
		     
			 echo '</ul></div><!-- end of very-custom-menu -->';
		}
     	     
	} #== /widget()
	
} #== /Class