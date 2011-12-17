            <div id="sidebar">
                <ul>
						<?php if ( get_option('imbalance_sidebar_off') == 'checked') { /* do nothing */ } else { include TEMPLATEPATH . '/sidebar-featured.php'; }  ?>

			<?php 	/* Widgetized sidebar, if you have the plugin installed. */
					if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
		</ul>

		<ul>
			<?php /* If this is the frontpage */ if ( is_home() || is_page() ) { ?>
				<?php wp_list_bookmarks(); ?>

				<li><h2><?php _e('Meta'); ?></h2>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
				</ul>
				</li>
			<?php } ?>

			<?php endif; ?>
                </ul>
            </div>
 