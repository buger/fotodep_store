<?php
/*
Plugin Name: Advanced Sidebar Menu
Plugin URI: http://lipeimagination.info
Description: Creates dynamic menu based on child/parent relationship.
Author: Mat Lipe
Version: 2.1
Author URI: http://lipeimagination.info
Since: 1/26/12
Email: mat@lipeimagination.info

*/

#-- Register the Widget
add_action( 'widgets_init', create_function( '', 'return register_widget("advanced_sidebar_menu");' ) );

class advanced_sidebar_menu extends WP_Widget {

#-----------------------------------------------------------------------------------------------------------------------------------
	  // this creates the widget form for the dashboard
	function form( $instance ) {
				  		 require( 'advanced-sidebar-menu.js' );
			?>
			
			
			
            <p> Include Parent Page <input id="<?php echo $this->get_field_name('include_parent'); ?>" 
            	name="<?php echo $this->get_field_name('include_parent'); ?>" type="checkbox" value="checked" 
            	<?php echo $instance['include_parent']; ?>/></p>
			
            			
			<p> Include Parent Even With No Children<input id="<?php echo $this->get_field_name('include_childless_parent'); ?>"
			name="<?php echo $this->get_field_name('include_childless_parent'); ?>" type="checkbox" value="checked" 
					<?php echo $instance['include_childless_parent']; ?>/></p>
					
			<p> Use Built in Styling <input id="<?php echo $this->get_field_name('css'); ?>"
			name="<?php echo $this->get_field_name('css'); ?>" type="checkbox" value="checked" 
					<?php echo $instance['css']; ?>/></p>
					
			<p> Pages to Exclude, Comma Separated:<input id="<?php echo $this->get_field_name('exclude'); ?>" 
            	name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo $instance['exclude']; ?>"/></p>
            	
            <p> Always Display Child Pages <input id="<?php echo $this->get_field_name('display_all'); ?>" 
            	name="<?php echo $this->get_field_name('display_all'); ?>" type="checkbox" value="checked" 
            	onclick="javascript:reveal_element( 'levels-<?php echo $this->get_field_name('levels'); ?>' )"
            	<?php echo $instance['display_all']; ?>/></p>
            
            <span id="levels-<?php echo $this->get_field_name('levels'); ?>" style="<?php 
                  if( $instance['display_all'] == checked ){
                  	echo 'display:block';
                  } else {
                  	echo 'display:none';
                  } ?>"> 
            <p> Levels to Display <select id="<?php echo $this->get_field_name('levels'); ?>" 
            name="<?php echo $this->get_field_name('levels'); ?>">
            <?php 
            	for( $i= 1; $i<6; $i++ ){
            		if( $i == $instance['levels'] ){
            			echo '<option value="'.$i.'" selected>'.$i.'</option>';
            		} else {
            			echo '<option value="'.$i.'">'.$i.'</option>';
            		}
            	} 
            	echo '</select></p></span>';
		}

#------------------------------------------------------------------------------------------------------------------------------
	// this allows more than one instance

	function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['include_childless_parent'] = strip_tags($new_instance['include_childless_parent']);
			$instance['include_parent'] = strip_tags($new_instance['include_parent']);
			$instance['exclude'] = strip_tags($new_instance['exclude']);
			$instance['display_all'] = strip_tags($new_instance['display_all']);
			$instance['levels'] = strip_tags($new_instance['levels']);
			$instance['css'] = strip_tags($new_instance['css']);
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
	  		 
	  		 #-- Create a usable array of the excluded pages
	  		 $exclude = explode(',', $instance['exclude']);
		   
           
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
		
			#-- Makes this work with all table prefixes
			#-- Added 1/22/12
			global $table_prefix;
			

			$result = $wpdb->get_results( "SELECT ID FROM ".$table_prefix."posts WHERE post_parent = $p AND post_type='page' Order by menu_order" );
	
    		#---- if there are no children do not display the parent unless it is check to do so
    		if($result != false || $instance['include_childless_parent'] == 'checked' ){
    			
    		   if( $instance['css'] == 'checked' ){
    		   	  include( plugin_dir_path(__FILE__).'sidebar-menu.css' );
    		   }
				
				  //Start the menu 
				echo '<div id="'.$args['widget_id'].'" class="advanced-sidebar-menu widget">';
				
		    	
				#-- if the checkbox to include parent is checked
    			if( $instance['include_parent'] == 'checked' ){
    				echo   '<ul class="parent-sidebar-menu" >'; 
    				
		     		 $parent_toggle = TRUE;
		     		 
		        #-- If the page is not excluded from the menu		 
		     		if( !in_array($p, $exclude) ){	 
						#-- list the parent page
       	 				wp_list_pages("sort_column=menu_order&title_li=&echo=1&depth=1&include=".$p);
		     		}
       	 			
				}
			}

              //=----------------------------------- makes the Child Pages list -----------------------------------------
              
		//If there are children start the Child Sidebar Menu
       if( $result != FALSE ){
		  echo '<ul class="child-sidebar-menu">';
		  
		  #-- If they want all the pages displayed always
		  if( $instance['display_all'] == 'checked' ){
		  	   wp_list_pages("sort_column=menu_order&title_li=&echo=1&child_of=".$p."&depth=".$instance['levels']."&exclude=".$instance['exclude']);
		  } else {
       	 				
		  	#-- Display children of current page's parent only
		    foreach($result as $pID){
			
				#-- If the page is not in the excluded ones
				if( !in_array($pID->ID, $exclude) ){
         	 		#--echo the current page from the $result
					wp_list_pages("sort_column=menu_order&title_li=&echo=1&depth=1&include=".$pID->ID);
				}
          
	      			#-- if the link that was just listed is the current page we are on
				if($pID->ID == $post->ID or $pID->ID == $post->post_parent or @in_array($pID->ID, $post->ancestors) ){
		
					$kids = $wpdb->get_results( "SELECT ID FROM ".$table_prefix."posts WHERE post_parent = ".$pID->ID." AND post_type='page' " );
					if( $kids != FALSE ){
				
			 			#-- Create a new menu with all the children under it
						echo '<ul class="grandchild-sidebar-menu">';

							wp_list_pages("sort_column=menu_order&title_li=&echo=1&exclude=".$instance['exclude']."&depth=3&child_of=".$pID->ID);
	
						echo '</ul>';
					}
				}
		 	 }
		  }
		 
		 #-- Close the First Level menu
		 echo '</ul><!-- End child-sidebar-menu -->';
		
       }
		  #-- If there was a menu close it off
		if($result != false || $instance['include_childless_parent'] == 'checked' ){
		     
			if( $instance['include_parent'] == 'checked' ) {
				echo '<ul>';
			}
			 echo '</div><!-- end of very-custom-menu -->';
		}
     	     
	} #== /widget()
	
} #== /Class