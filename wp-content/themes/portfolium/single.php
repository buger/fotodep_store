<?php get_header(); ?>

<div class="page_meta clear">
    <div class="rss">
        <a href="<?php bloginfo('rss2_url'); ?>">Subscribe to RSS feed</a>
    </div>
    <div class="heading">
        <h3><a href="<?php if(function_exists('get_blogurl')) { get_blogurl(); } ?>">Back to blog</a></h3>
    </div>
    <?php if(function_exists('catlist')) { catlist(); } ?>
    <?php get_search_form(); ?>
</div>

<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>

    <div <?php post_class('post_single clear'); ?> id="post-<?php the_ID(); ?>">
        <h2><?php the_title(); ?></h2>
        <div class="post_list_meta">
            <p class="post_date">Posted on <?php the_time(__('F jS, Y')) ?> by <?php the_author() ?></p>
            <p class="post_cat"><?php the_category(', '); ?></p>
            <p class="post_comms"><?php comments_popup_link(__('No Comments'), __('1 Comment'), __('% Comments'), '', __('Comments Closed') ); ?></p>
            <div class="post_share">&mdash;<br /><a href="javascript: void(0);" class="sharethis">Share this post</a>
                <ul class="sharelist">
                    <li><a href="http://facebook.com/share.php?u=<?php the_permalink() ?>&amp;t=<?php echo urlencode(the_title('','', false)) ?>" target="_blank">Facebook</a>
    </li>
                    <li><a href="http://twitter.com/home?status=<?php the_title(); ?> <?php echo getTinyUrl(get_permalink($post->ID)); ?>" target="_blank">Twitter</a></li>
                    <li><a href="http://digg.com/submit?phase=2&amp;url=<?php the_permalink() ?>&amp;title=<?php the_title(); ?>" target="_blank">Digg</a>
    </li>
                    <li><a href="http://stumbleupon.com/submit?url=<?php the_permalink() ?>&amp;title=<?php echo urlencode(the_title('','', false)) ?>" target="_blank">StumbleUpon</a>
    </li>
                    <li><a href="http://del.icio.us/post?url=<?php the_permalink() ?>&amp;title=<?php echo urlencode(the_title('','', false)) ?>" target="_blank">Del.icio.us</a>
    </li>
                </ul>
            </div>
        </div>
        <div class="post_content">
            <?php the_content(); ?>
        </div>
    </div>

    <?php comments_template(); ?>

    <?php endwhile; else: ?>

		<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

<?php endif; ?>

</div>

<?php get_footer(); ?>
