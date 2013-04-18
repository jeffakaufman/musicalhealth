<?php
/**
 * @package WordPress
 * @subpackage Toolbox
 */

get_header(); ?>
	<div id="main">
		<div id="primary">
			<div id="interior">
				<div id="left_column">
					<div id="page_title">
						<?php
							printf( __( '%s', 'toolbox' ), '<span>' . single_cat_title( '', false ) . '</span>' );
						?>
					</div><!-- #page-title -->
					<?php $categorydesc = category_description(); if ( ! empty( $categorydesc ) ) echo apply_filters( 'archive_meta', '<div class="archive-meta">' . $categorydesc . '</div>' ); ?>
					<?php /* Start the Loop */ ?>
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'content', get_post_format() ); ?>
					<?php endwhile; ?>
					<?php /* Display navigation to next/previous pages when applicable */ ?>
					<?php if (  $wp_query->max_num_pages > 1 ) : ?>
						<div id="nav-below">
							<h1 class="section-heading"><?php _e( '', 'toolbox' ); ?></h1>
							<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&laquo;</span> Older posts', 'toolbox' ) ); ?></div>
							<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&raquo;</span>', 'toolbox' ) ); ?></div>
						</div><!-- #nav-below -->
					<?php endif; ?>
				</div><!-- #left column -->
				<?php include (TEMPLATEPATH . '/sidebar-single.php'); ?>
				<div class="clear"></div>	
			</div><!-- #interior -->
		</div><!-- #primary -->
	<?php get_footer(); ?>