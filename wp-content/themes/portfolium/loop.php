<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>

    <div <?php post_class('post_list clear'); ?> id="post-<?php the_ID(); ?>">
        <h2><a href="<?php the_permalink() ?>" title="<?php printf( esc_attr__('Permalink to %s'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
        <div class="post_list_meta">
            <p class="post_date">Posted on <?php the_time(__('F jS, Y')) ?> by <?php the_author() ?></p>
            <p class="post_cat"><?php the_category(', '); ?></p>
            <p class="post_comms"><?php comments_popup_link(__('No Comments'), __('1 Comment'), __('% Comments'), '', __('Comments Closed') ); ?></p>
            <div class="post_share">&mdash;<br /><a href="javascript: void(0);" class="sharethis">Share this post</a>
                <ul class="sharelist">
                    <li><a href="http://facebook.com/share.php?u=<?php the_permalink() ?>&amp;amp;t=<?php echo urlencode(the_title('','', false)) ?>" target="_blank">Facebook</a>
    </li>
                    <li><a href="http://twitter.com/home?status=<?php the_title(); ?> <?php echo getTinyUrl(get_permalink($post->ID)); ?>" target="_blank">Twitter</a></li>
                    <li><a href="http://digg.com/submit?phase=2&amp;amp;url=<?php the_permalink() ?>&amp;amp;title=<?php echo urlencode(the_title('','', false)) ?>" target="_blank">Digg</a>
    </li>
                    <li><a href="http://stumbleupon.com/submit?url=<?php the_permalink() ?>&amp;amp;title=<?php echo urlencode(the_title('','', false)) ?>" target="_blank">StumbleUpon</a>
    </li>
                    <li><a href="http://del.icio.us/post?url=<?php the_permalink() ?>&amp;amp;title=<?php echo urlencode(the_title('','', false)) ?>" target="_blank">Del.icio.us</a>
    </li>
                </ul>
            </div>
        </div>
        <div class="post_content">
            <?php the_content(false); ?>
            <p class="more">&mdash; <a href="<?php the_permalink() ?>#more">Read more</a></p>
        </div>
    </div>

    <?php endwhile; ?>
<?php endif; ?>
