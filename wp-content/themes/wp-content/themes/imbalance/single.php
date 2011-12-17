<?php get_header(); ?> 
<?php get_sidebar(); ?>  
            <div id="main">
              <?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>
				<div class="entry">
                <div class="postmetadata">
                    <span>Published by</span> <?php the_author() ?><br />
                    <?php printf(__('<span>Filled under:</span> %s'), get_the_category_list(', ')); ?><br />
					<?php comments_popup_link(__('No Comments'), __('1 Comment'), __('% Comments'), '', __('Comments Closed') ); ?><br />
                    <?php edit_post_link(__('[Edit this entry]'), '<br />', ''); ?>
                </div>
                <h1><?php the_title(); ?></h1>
                <div class="article" id="post-<?php the_ID(); ?>">
                    <?php the_content(); ?>
                    <div class="postmetadata"><?php the_tags(__('<span>Tagged in:</span>') . ' ', ', ', '<br />'); ?>
                    <span>Share this article:</span> <a href="http://www.facebook.com/sharer.php?u=<?php the_permalink();?>%26t=<?php the_title(); ?>">Facebook</a>, <a href="http://digg.com/submit?phase=2&amp;url=<?php the_permalink(); ?>&amp;title=<?php the_title(); ?>" title="Digg this!">Digg This</a>, <a href="http://del.icio.us/post?url=<?php the_permalink(); ?>&amp;title=<?php the_title(); ?>" title="Bookmark on Delicious.">Del.icio.us</a>, <a href="http://www.stumbleupon.com/submit?url=<?php the_permalink(); ?>&amp;title=<?php the_title(); ?>" title="StumbleUpon.">StumbleUpon</a>, 
                    <?php if (function_exists('wp_get_shortlink')) { ?>
					<a href="http://twitter.com/home/?status=<?php the_title(); ?>: <?php echo wp_get_shortlink(get_the_ID()); ?>" title="Tweet this!"> Tweet this</a>
					<?php } 
					else { ?>
					<a href="http://twitter.com/home/?status=<?php the_title(); ?>: <?php the_permalink(); ?>" title="Tweet this!"> Tweet This!</a>
					<?php } ?>
                    <br />

                    <?php comments_rss_link('RSS 2.0 feed'); ?> | <a href="<?php trackback_url(); ?>">Trackback</a>
                    </div>
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
