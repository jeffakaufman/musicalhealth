<?php
/**
 * The template used to display Tag Archive pages
 *
 * @package WordPress
 * @subpackage Toolbox
 */

get_header(); ?>
	<div id="main">
		<div id="primary">
			<div id="interior" role="main">
				<div id="left_column">
					<?php the_post(); ?>
					<header class="page-header">
						<div id="page_title"><?php
							printf( __( 'Archive for %s', 'toolbox' ), '<span>' . single_tag_title( '', false ) . '</span>' );
						?></div>
					</header>
				<?php rewind_posts(); ?>
				<?php /* Display navigation to next/previous pages when applicable */ ?>
				<?php if ( $wp_query->max_num_pages > 1 ) : ?>
					<nav id="nav-above">
						<h1 class="section-heading"><?php _e( 'Post navigation', 'toolbox' ); ?></h1>
						<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'toolbox' ) ); ?></div>
						<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'toolbox' ) ); ?></div>
					</nav><!-- #nav-above -->
				<?php endif; ?>
				
				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'content', get_post_format() ); ?>
				<?php endwhile; ?>
				
				<?php /* Display navigation to next/previous pages when applicable */ ?>
				<?php if (  $wp_query->max_num_pages > 1 ) : ?>
					<nav id="nav-below">
						<h1 class="section-heading"><?php _e( 'Post navigation', 'toolbox' ); ?></h1>
						<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'toolbox' ) ); ?></div>
						<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'toolbox' ) ); ?></div>
					</nav><!-- #nav-below -->
				<?php endif; ?>				
			</div><!-- #left column -->
			<?php include (TEMPLATEPATH . '/sidebar-single.php'); ?>
			<div class="clear"></div>	
		</div><!-- #interior -->
	</div><!-- #primary -->
<?php get_footer(); 