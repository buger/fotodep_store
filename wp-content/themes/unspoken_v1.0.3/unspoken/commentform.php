<?php if ( comments_open() ) : global $post, $id, $comment, $user_login, $user_ID, $user_identity; ?>

    <div id="respond">
         <div class="block-title"><?php _e( 'Submit your comment', 'unspoken' ); ?></div>
         <div class="comments-inn">
             <?php if ( get_option('comment_registration') && !$user_ID ) : ?>
                 <p class="message"><?php printf(__('You must be <a href="%s" title="Log in">logged in</a> to post a comment.', 'unspoken'),
					get_option('siteurl') . '/wp-login.php?redirect_to=' . get_permalink() ); ?></p>
             <?php else : ?>
                 <form action="<?php bloginfo('url'); ?>/wp-comments-post.php" method="post" id="commentform" <?php if ( get_option('require_name_email') && !$user_ID  ) { echo 'onSubmit="return validate(false);"'; } else { echo 'onSubmit="return validate(true);"'; } ?>>
                     <fieldset>
                         <table>
                             <col width="23%"/>
                             <col width="48%"/>
                             <col width="29%"/>

                         <?php if ( !$user_ID ) : ?>

                             <tr>
                                 <td class="label"><label for="author"><?php _e( 'Your name', 'unspoken' ); ?></label></td>
                                 <td>
                                     <p><input type="text" name="author" id="author" tabindex="1" class="input"/></p>
                                     <p class="alert"><?php _e('Please enter your name', 'unspoken'); ?></p>
                                 </td>
                                 <td><span><?php ( get_option('require_name_email') ) ? _e( 'Your name is required', 'unspoken' ) : ''; ?></span></td>
                             </tr>
                             <tr>
                                 <td class="label"><label for="email"><?php _e( 'Your email', 'unspoken' ); ?></label></td>
                                 <td>
                                     <p><input type="text" name="email" id="email" tabindex="2" class="input"/></p>
                                     <p class="alert"><?php _e('Please enter a valid email address', 'unspoken'); ?></p>
                                 </td>
                                 <td><span><?php ( get_option('require_name_email') ) ? _e( 'An email address is required', 'unspoken' ) : ''; ?></span></td>
                             </tr>
                             <tr>
                                 <td class="label"><label for="url"><?php _e( 'Website', 'unspoken' ); ?></label></td>
                                 <td>
                                     <p><input type="text" name="url" id="url" tabindex="3" class="input"/></p>
                                 </td>
                                 <td></td>
                             </tr>

                         <?php endif; // if ( $user_ID ) ?>

                             <tr>
                                 <td  class="label"><label for="comment"><?php _e( 'Message', 'unspoken' ); ?></label></td>
                                 <td colspan="2">
                                     <p><textarea name="comment" id="comment" cols="30" rows="10" tabindex="4"></textarea></p>
                                     <p class="alert"><?php _e('Please enter your message', 'unspoken'); ?></p>

                                     <?php if ( $user_ID ) : ?>

                                     <div class="logged-in-as">
                                         <?php printf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'unspoken' ),
                                         admin_url( 'profile.php' ),
                                         $user_identity,
                                         wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post->ID ) ) ) ); ?>
                                     </div>

                                     <?php endif; // if ( $user_ID ) ?>

                                 </td>
                             </tr>
                             <tr>
                                 <td  class="label"></td>
                                 <td colspan="2" class="submit">
                                     <div>
                                         <input id="submit" type="submit" value="<?php _e('Submit comment', 'unspoken'); ?>" tabindex="5"/><span id="cancel-comment-reply"><?php cancel_comment_reply_link(__('Cancel', 'unspoken')); ?></span>
                                     </div>
                                 </td>
                             </tr>
                         </table>
                         <div class="commentform-extra">
                             <?php do_action('comment_form', $post->ID); ?>
                             <?php comment_id_fields(); ?>
                         </div>
                     </fieldset>
                 </form>
             <?php endif; // if ( get_option('comment_registration') && !$user_ID ) ?>
         </div><!-- .comments-inn -->
    </div><!-- #respond -->

<?php endif; // end comments_open() ?>
