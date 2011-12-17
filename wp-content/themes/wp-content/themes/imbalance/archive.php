<?php get_header(); ?> 
				  <?php /* If this is a category archive */ if (is_category()) { ?>
                    <h1><?php printf(__('Archive for the &#8216;%s&#8217; Category'), single_cat_title('', false)); ?></h1>
                  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
                    <h1"><?php printf(__('Posts Tagged &#8216;%s&#8217;'), single_tag_title('', false) ); ?></h1>
                  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
                    <h1><?php printf(_c('Archive for %s|Daily archive page'), get_the_time(__('F jS, Y'))); ?></h1>
                  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
                    <h1><?php printf(_c('Archive for %s|Monthly archive page'), get_the_time(__('F, Y'))); ?></h1>
                  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
                    <h1><?php printf(_c('Archive for %s|Yearly archive page'), get_the_time(__('Y'))); ?></h1>
                  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
                    <h1><?php _e('Author Archive'); ?></h1>
                  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
                    <h1><?php _e('Blog Archives'); ?></h1>
                  <?php } ?>

              <ul class="mcol">
              <?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>
              	<li class="article" id="post-<?php the_ID(); ?>">

			<?php
			if ( has_post_thumbnail() ) { ?>
                    	<?php 
                    	$imgsrcparam = array(
						'alt'	=> trim(strip_tags( $post->post_excerpt )),
						'title'	=> trim(strip_tags( $post->post_title )),
						);
                    	$thumbID = get_the_post_thumbnail( $post->ID, 'background', $imgsrcparam ); ?>
                        <div class="preview"><a href="<?php the_permalink() ?>"><?php echo "$thumbID"; ?></a></div>

                    
                    <?php } else {?>
                        <div class="preview"><a href="<?php the_permalink() ?>"><img src="<?php bloginfo('template_url'); ?>/images/default-thumbnail.jpg" alt="<?php the_title(); ?>" /></a></div>
                    <?php } ?>

                    <div class="article-over">
                      <h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                      <?php the_excerpt(); ?>
                      <div class="postmetadata">
                          Posted: <?php the_time(__('F jS, Y', 'kubrick')) ?>&nbsp;&#721;&nbsp;
                          <?php comments_popup_link(__('No Comments'), __('1 Comment'), __('% Comments'), '', __('Comments Closed') ); ?><br />
                          <?php printf(__('Filled under: %s'), get_the_category_list(', ')); ?>
                      </div>
                    </div>
                </li> <?php ?>
            <?php endwhile; ?>
            <?php else : ?>


            <?php endif; ?>
            </ul>

            <?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>
            <?php endwhile; ?>
            <?php else : ?>
                <h1 id="error"><?php _e("Sorry, but you are looking for something that isn&#8217;t here."); ?></h1>
            <?php endif; ?>


            <?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?>
<?php get_footer(); ?>