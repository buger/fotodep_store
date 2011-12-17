            </div> <!-- .middle -->
            <div class="bottom-widgetarea">
                <div class="bottom-widgetarea-inn clear">
                    <?php if ( !dynamic_sidebar('bottom-widget-area') ) : ?>
                    <?php endif; // end bottom-widget-area ?>
                </div>
            </div>
            <div class="footer clear">

            <?php wp_nav_menu(array('menu' => __('Navigation footer', 'unspoken'), 'theme_location' => __('Navigation footer', 'unspoken'), 'depth' => 1, 'container' => 'div', 'container_class' => 'menu-footer clear')); ?>

                <div class="footer-leftpart">
                    <a class="logo-footer" href="<?php bloginfo( 'url' ); ?>">
                        <?php
                            if ( get_option('unspoken_logo_text') ) {
                                echo get_option('unspoken_logo_text');
                            } elseif ( get_option('unspoken_logo_bottom') ) {
                                if ( function_exists('unspoken_get_logo') ) echo unspoken_get_logo(get_option('unspoken_logo_bottom'));
                            } else {
                            ?>
                            <?php
                            if (get_option('unspoken_skin') && get_option('unspoken_skin') != 'default') {
                                echo '<span style="background: url(' . get_bloginfo('template_url') . '/skins/unspoken-' . get_option('unspoken_skin') . '/images/logo-footer.png) 0 0 no-repeat;"></span>';
                            } else {
                                echo '<span style="background: url(' . get_bloginfo('template_url') . '/images/logo-footer.png) 0 0 no-repeat;"></span>';
                            }
                            ?>
                        <?php } ?>
                    </a>
                </div>
                <div class="footer-middlepart">
                    <div class="footer-searchform">
                        <form method="get" id="searchform" action="<?php bloginfo('url'); ?>">
                            <fieldset>
                                <input type="text" name="s" onfocus="if(this.value=='<?php _e( 'Search', 'unspoken' );?>') this.value='';" onblur="if(this.value=='') this.value='<?php _e( 'Search', 'unspoken' );?>';" value="<?php _e( 'Search', 'unspoken' );?>" />
                            </fieldset>
                        </form>
                    </div>
                    <div class="footer-tags">
                        <?php unspoken_get_tags(); ?>
                    </div>
                </div>

            <?php  wp_nav_menu(array('menu' => __('Footer left linkset', 'unspoken'), 'theme_location' => __('Footer left linkset', 'unspoken'), 'depth' => 1, 'container' => false, 'container_class' => false, 'menu_class' => 'footer-linkset')); ?>

            <?php  wp_nav_menu(array('menu' => __('Footer right linkset', 'unspoken'), 'theme_location' => __('Footer right linkset', 'unspoken'), 'depth' => 1, 'container' => false, 'container_class' => false, 'menu_class' => 'footer-linkset')); ?>

            </div><!-- .footer -->
            <div class="copyrights">
                <p><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a> &copy; <?php echo date('Y'); ?> <?php _e('All Rights Reserved', 'unspoken'); ?></p>
                <?php if ( get_option('unspoken_footer_text') ) echo '<p>' . get_option('unspoken_footer_text') . '</p>'; ?>
            </div>
            <div class="credits">
                <p>Designed by <a href="http://wpshower.com">WPSHOWER</a></p>
                <p>Powered by <a href="http://wordpress.org">WordPress</a></p>
            </div>
            <div class="clear"></div>
        </div> <!-- .wrapper -->

        <?php wp_footer(); ?>

        <?php echo (get_option('unspoken_ga')) ? get_option('unspoken_ga') : ''; ?>

	</body>
</html>
