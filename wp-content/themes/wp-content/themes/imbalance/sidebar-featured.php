				<?php 
				wp_reset_query();
				query_posts("showposts=2&offset=1");
					if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    <li class="article">
			<?php
			if ( has_post_thumbnail() ) { ?>
						                    	<?php 
                    	$imgsrcparam = array(
						'alt'	=> trim(strip_tags( $post->post_excerpt )),
						'title'	=> trim(strip_tags( $post->post_title )),
						);
                    	$thumbID = get_the_post_thumbnail( $post->ID, 'background', $imgsrcparam ); ?>
                        <a href="<?php the_permalink() ?>"><?php echo "$thumbID"; ?></a>
                        <div class="article-over">
                          <h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                      <?php the_excerpt(); ?>
                          <div class="postmetadata">
                              Posted: <?php the_time(__('F jS, Y', 'kubrick')) ?>&nbsp;&#721;&nbsp;
                              <?php comments_popup_link(__('No Comments'), __('1 Comment'), __('% Comments'), '', __('Comments Closed') ); ?><br />
                              <?php printf(__('Filled under: %s'), get_the_category_list(', ')); ?>
                          </div>
                        </div>

                    
                    <?php } else {?>
						<?php $thumbID = get_post_thumbnail_id(); ?>
						<a href="<?php the_permalink() ?>"><img src="<?php bloginfo('template_url'); ?>/images/default-thumbnail.jpg" alt="<?php the_title(); ?>" /></a>
                        <div class="article-over">
                          <h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                      <?php the_excerpt(); ?>
                          <div class="postmetadata">
                              Posted: <?php the_time(__('F jS, Y', 'kubrick')) ?>&nbsp;&#721;&nbsp;
                              <?php comments_popup_link(__('No Comments'), __('1 Comment'), __('% Comments'), '', __('Comments Closed') ); ?><br />
                              <?php printf(__('Filled under: %s'), get_the_category_list(', ')); ?>
                          </div>
                        </div>
                    <?php } ?>

                    </li>
                <?php endwhile; else: 
                	endif;
					//Reset Query
					wp_reset_query();?>