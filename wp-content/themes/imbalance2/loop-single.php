<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div class="post_title">
					<h1 class="entry-title"><?php the_title(); ?></h1>

					<div id="nav-above" class="navigation">
						<div class="nav-previous">
	<?php if (get_previous_post(false) != null): ?>
							<?php previous_post_link( '%link', '« Previous' ); ?>
	<?php else: ?>
							« Previous
	<?php endif ?>
						</div>
						<span class="main_separator">/</span>
						<div class="nav-next">
	<?php if (get_next_post(false) != null): ?>
							<?php next_post_link( '%link', 'Next »' ); ?>
	<?php else: ?>
							Next »
	<?php endif ?>
						</div>
					</div><!-- #nav-above -->

					<div class="entry-meta">
						<?php imbalance2_posted_by() ?>
						<span class="main_separator">/</span>
						<?php imbalance2_posted_on() ?>
						<span class="main_separator">/</span>
						<?php imbalance2_posted_in() ?>
						<span class="main_separator">/</span>
	<?php if ( get_comments_number() != 0 ) : ?>
						<a href="#comments"><?php printf( _n( 'One Comment', '%1$s Comments', get_comments_number(), 'imbalance2' ),
							number_format_i18n( get_comments_number() )
						); ?></a>
	<?php else: ?>
						<a href="#comments">No Comments</a>
	<?php endif ?>
					</div><!-- .entry-meta -->
				</div>

				<div id="wides"></div>

				<table id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<tr>
						<td class="entry-aside">
							
						</td>
						<td class="entry-content-right">
							<?php the_content(); ?>
							<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'imbalance2' ), 'after' => '</div>' ) ); ?>

		<?php if ( get_the_author_meta( 'description' ) ) : // If a user has filled out their description, show a bio on their entries  ?>
							<div id="entry-author-info">
								<div id="author-avatar">
									<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'imbalance2_author_bio_avatar_size', 60 ) ); ?>
								</div><!-- #author-avatar -->
								<div id="author-description">
									<h2><?php printf( esc_attr__( 'About %s', 'imbalance2' ), get_the_author() ); ?></h2>
									<?php the_author_meta( 'description' ); ?>
									<div id="author-link">
										<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
											<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'imbalance2' ), get_the_author() ); ?>
										</a>
									</div><!-- #author-link	-->
								</div><!-- #author-description -->
							</div><!-- #entry-author-info -->
		<?php endif; ?>
							<div class="clear"></div>

							<div class="entry-utility">
								<?php imbalance2_tags() ?>
								<?php edit_post_link( __( 'Edit', 'imbalance2' ), '<span class="edit-link">', '</span>' ); ?>
							</div><!-- .entry-utility -->

							<div id="social">
								<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a>
								<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>

								<div id="fb-root"></div>
								<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
								<fb:like href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ?>" send="true" width="450" show_faces="false" font=""></fb:like>
							</div>
						</td>
					</tr>
				</table><!-- #post-## -->

				<?php comments_template( '', true ); ?>

<?php endwhile; ?>

<?php $imbalance2_theme_options = get_option('imbalance2_theme_options');
	if ( $imbalance2_theme_options['related'] != 0 ) :
?>
				
<div class="recent clear">
	<?php
		$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
		query_posts(
			array(
				'works' => $term->slug,
				'posts_per_page' => 10,
				'post__not_in' => array($post->ID)
			)
		);
	?>
	
	<div id="related">
	
	<?php while ( have_posts() ) : the_post(); ?>
	
	<div class="box">
		<div class="rel">
			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('homepage-thumb', array('alt' => '', 'title' => '')) ?></a>
	<?php if ($imbalance2_theme_options['images_only'] == 0): ?>
			<div class="categories"><?php imbalance2_posted_in(); ?></div>
			<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
		<?php the_excerpt() ?>
			<div class="posted"><?php imbalance2_posted_on() ?> <span class="main_separator">/</span>
				<?php echo comments_popup_link( __( 'No comments', 'imbalance2' ), __( 'One Comment', 'imbalance2' ), __( '% Comments', 'imbalance2' ) ); ?>
			</div>
	<?php endif ?>
			<div class="texts">
	<?php if ($imbalance2_theme_options['images_only'] == 1): ?>
				<a class="transparent" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('homepage-thumb', array('alt' => '', 'title' => '')) ?></a>
	<?php endif ?>
				<div class="abs">
	<?php if ($imbalance2_theme_options['images_only'] == 0): ?>
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('homepage-thumb', array('alt' => '', 'title' => '')) ?></a>
	<?php endif ?>
					<div class="categories"><?php imbalance2_posted_in(); ?></div>
					<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
		<?php the_excerpt() ?>
					<div class="posted"><?php imbalance2_posted_on() ?> <span class="main_separator">/</span>
					<?php echo comments_popup_link( __( 'No comments', 'imbalance2' ), __( 'One Comment', 'imbalance2' ), __( '% Comments', 'imbalance2' ) ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<?php endwhile ?>
	
	</div>
	
	<?php wp_reset_query(); ?>
</div>

<?php endif ?>