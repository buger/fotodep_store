<?php get_header(); ?> 
<?php get_sidebar(); ?>  
            <div id="main">
              <?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>
				<div class="entry">
                <div class="postmetadata">
                    <?php previous_post_link('%link', '&larr; Back to article<br />&nbsp;<br />'); ?>
                	<span>Published by</span> <?php the_author() ?><br />
                    <?php printf(__('<span>Filled under:</span> %s'), get_the_category_list(', ')); ?><br />
					<?php comments_popup_link(__('No Comments'), __('1 Comment'), __('% Comments'), '', __('Comments Closed') ); ?><br />
					<?php edit_post_link(__('Edit this image'), '<br />', ''); ?>
                    
                </div>
                <h1><?php the_title(); ?></h1>
                <div class="article" id="post-<?php the_ID(); ?>">

<?php if ( wp_attachment_is_image() ) :
	$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
	foreach ( $attachments as $k => $attachment ) {
		if ( $attachment->ID == $post->ID )
			break;
	}
	$k++;
	// If there is more than 1 image attachment in a gallery
	if ( count( $attachments ) > 1 ) {
		if ( isset( $attachments[ $k ] ) )
			// get the URL of the next image attachment
			$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
		else
			// or get the URL of the first image attachment
			$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
	} else {
		// or, if there's only 1 image attachment, get the URL of the image
		$next_attachment_url = wp_get_attachment_url();
	}
?>
						<p class="attachment"><a href="<?php echo $next_attachment_url; ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php
							$attachment_size = apply_filters( 'twentyten_attachment_size', 900 );
							echo wp_get_attachment_image( $post->ID, array( $attachment_size, 9999 ) ); // filterable image width with, essentially, no limit for image height.
						?></a></p>

						<div id="nav-below" class="navigation">
							<div class="nav-previous"><?php previous_image_link( 'thumbnail' ); ?></div>
							<div class="nav-next"><?php next_image_link( 'thumbnail' ); ?></div>
						</div><!-- #nav-below -->
<?php else : ?>
						<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php echo basename( get_permalink() ); ?></a>
<?php endif; ?>



 

                </div>
                <div id="comments">
					<?php comments_template(); ?>
                </div>
            <?php endwhile; ?>
            <?php else : ?>
                <h1 id="error"><?php _e("Sorry, but you are looking for something that isn&#8217;t here."); ?></h1>
            <?php endif; ?>
            </div>         
            </div>

<?php get_footer(); ?>
