<?php if ( $wp_query->max_num_pages > 1 ) : ?>
    <div id="pagination">

        <?php
            $pagination_mode = get_option('unspoken_pagination_mode');
            if ( $pagination_mode == 0 ) : // Default ?>

                <div class="default clear">
                    <?php
                        next_posts_link( __( 'Older posts', 'unspoken' ) );
                        previous_posts_link( __( 'Newer posts', 'unspoken' ) );
                    ?>
                </div>

            <?php elseif ( $pagination_mode == 1 ) : // Default + WP Page-Navi ?>

                <div class="default clear">
                    <?php
                        next_posts_link( __( 'Older posts', 'unspoken' ) );
                        previous_posts_link( __( 'Newer posts', 'unspoken' ) );
                        if ( function_exists( 'wp_pagenavi' ) ) wp_pagenavi();
                    ?>
                </div>

            <?php elseif ( $pagination_mode == 2 ) : // Ajax fetching scroll ?>

                <div class="fetch">
                     <?php next_posts_link( __( 'Load more posts', 'unspoken' ) ); ?>
                </div>

            <?php elseif ( $pagination_mode == 3 ) : // Infinite scroll ?>

                <div class="infinitescroll">
                    <?php next_posts_link( __( 'Load more posts', 'unspoken' ) ); ?>
                </div>

            <?php endif; ?>

    </div><!-- .navigation -->
<?php endif; ?>
