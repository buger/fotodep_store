<?php get_header(); ?>

	<div id="container">
		<div id="content" role="main">

			<div id="post-0" class="post error404 not-found">
				<h1 class="entry-title"><?php _e( '404! We couldn\'t find the page!', 'imbalance2' ); ?></h1>
				<div class="entry-content">
					<p><?php _e( 'The page you\'ve requested can not be displayed. It appears you\'ve missed your intended destination, either through a bad or outdated link, or a typo in the page you were hoping to reach.', 'imbalance2' ); ?></p>
					<p><?php _e( 'If you were looking for specific content, please try searching for it in the search box above.', 'imbalance2' ); ?></p>
					<a href="/">Back to Homepage</a>
					<br /><br />
				</div><!-- .entry-content -->
			</div><!-- #post-0 -->

		</div><!-- #content -->
	</div><!-- #container -->
	<script type="text/javascript">
		// focus on search field after it has loaded
		document.getElementById('s') && document.getElementById('s').focus();
	</script>

<?php get_footer(); ?>