<?php
/**
 * @package WordPress
 * @subpackage Toolbox
 */

get_header(); ?>
	<div id="main">
		<div id="primary">
			<div id="interior" role="main">
				<div id="left_column">
					<article id="post-0" class="post error404 not-found">
						<header class="entry-header">
							<h1 class="entry-title"><?php _e( 'This is somewhat embarrassing, isn&rsquo;t it?', 'toolbox' ); ?></h1>
						</header>
						<div class="entry-content">
							<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching, or one of the links below, can help.', 'toolbox' ); ?></p>
							<?php get_search_form(); ?>
							<?php the_widget( 'WP_Widget_Recent_Posts' ); ?>
							<div class="widget">
								<h2 class="widgettitle"><?php _e( 'Most Used Categories', 'toolbox' ); ?></h2>
								<ul>
								<?php wp_list_categories( array( 'orderby' => 'count', 'order' => 'DESC', 'show_count' => 'TRUE', 'title_li' => '', 'number' => '10' ) ); ?>
								</ul>
							</div>
							<?php
							$archive_content = '<p>' . sprintf( __( 'Try looking in the monthly archives. %1$s', 'toolbox' ), convert_smilies( ':)' ) ) . '</p>';
							the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$archive_content" );
							?>
							<?php the_widget( 'WP_Widget_Tag_Cloud' ); ?>
						</div>
					</article>
				</div><!-- left_column -->
				<?php get_sidebar(); ?>
				<div class="clear"></div>
			</div><!-- #interior -->
		</div><!-- #primary -->
<?php get_footer(); ?>