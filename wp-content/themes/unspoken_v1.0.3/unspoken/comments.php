<div id="comments">
    <div class="comments">

        <?php if ( post_password_required() ) : ?>
				<p class="nopassword"><?php // _e( 'This post is password protected. Enter the password to view any comments.', 'unspoken' ); ?></p>
			</div><!-- .comments -->
			</div><!-- #comments -->
        <?php
                /* Stop the rest of comments.php from being processed,
                 * but don't kill the script entirely -- we still have
                 * to fully load the template.
                 */
                return;
            endif;
        ?>

        <?php if ( have_comments() ) : ?>
            <?php if ( function_exists( 'unspoken_commentCount' ) && unspoken_commentCount( 'comments' ) != 0 ) : ?>

                <div class="block-title" id="comments-title"><?php
                printf( _n( '1 comment on this post', '%1$s total comments on this post', unspoken_commentCount('comments'), 'unspoken' ),
                number_format_i18n( unspoken_commentCount('comments') ) );
                ?><a href="#respond"><?php _e( 'Submit yours','unspoken' ); ?></a></div>

                <div class="comments-inn">
                    <ol class="commentlist">
                        <?php wp_list_comments(array('type' => 'comment', 'callback' => 'unspoken_comment' ) ); ?>
                    </ol>
                </div><!-- .comments-inn -->

                <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
                    <div class="navigation clear">
                        <div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'unspoken' ) ); ?></div>
                        <div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'unspoken' ) ); ?></div>
                    </div><!-- .navigation -->
                <?php endif; // check for comment navigation ?>

            <?php endif; // if ( function_exists( 'unspoken_commentCount' ) && unspoken_commentCount('comments') != 0 ) ?>

            <?php if ( function_exists( 'unspoken_commentCount' ) && unspoken_commentCount( 'pings' ) != 0 && !isset( $_GET['cpage'] ) ) : ?>

                <div class="block-title"><?php
                printf( _n( '1 pingback on this post', '%1$s total pingbacks on this post', unspoken_commentCount('pings'), 'unspoken' ),
                number_format_i18n( unspoken_commentCount('pings') ) );
                ?></div>

                <div class="comments-inn">
                    <ol class="commentlist pings">
                        <?php wp_list_comments(array('type' => 'pings', 'callback' => 'unspoken_comment' ) ); ?>
                    </ol>
                </div><!-- .comments-inn -->
                
            <?php endif; // if ( function_exists( 'unspoken_commentCount' ) && unspoken_commentCount('pings') != 0 ) ?>

        <?php else : // or, if we don't have comments:

            /* If there are no comments and comments are closed,
             * let's leave a little note, shall we?
             */
            if ( ! comments_open() ) :
        ?>
            <p class="nocomments"><?php // _e( 'Comments are closed.', 'unspoken' ); ?></p>
        <?php endif; // end ! comments_open() ?>

        <?php endif; // end have_comments() ?>
    </div><!-- .comments -->

    <?php get_template_part( 'commentform' ); ?>

</div><!-- #comments -->
