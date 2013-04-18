<?php
/**
 * @package WordPress
 * @subpackage Toolbox
 * Template Name: Blog
 */

get_header(); ?>
		<div id="main">
		<div id="primary">
			<div id="interior" role="main">
				<div id="left_column">
				<div id="page_title">Blog</div>
				<?php /* Display navigation to next/previous pages when applicable */ ?>
				<?php if ( $wp_query->max_num_pages > 1 ) : ?>
					<nav id="nav-above">
						<h1 class="section-heading"><?php _e( 'Post navigation', 'toolbox' ); ?></h1>
						<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'toolbox' ) ); ?></div>
						<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'toolbox' ) ); ?></div>
					</nav><!-- #nav-above -->
				<?php endif; ?>
					<?php query_posts('cat=1&showposts'); ?>
						<?php while (have_posts()) : the_post(); ?>
						<h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
						<h4><?php the_time(get_option('date_format')); ?></h4>
						<?php echo content(80); ?>
						<a class="continue" href="<?php the_permalink() ?>">CONTINUE READING</a>
						<div class="clear"></div>
					<?php endwhile;?>
 					
 				<?php /* Display navigation to next/previous pages when applicable */ ?>
				<?php if (  $wp_query->max_num_pages > 1 ) : ?>
					<nav id="nav-below">
						<h1 class="section-heading"><?php _e( 'Post navigation', 'toolbox' ); ?></h1>
						<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'toolbox' ) ); ?></div>
						<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'toolbox' ) ); ?></div>
					</nav><!-- #nav-below -->
				<?php endif; ?>	
				</div>
				<?php include (TEMPLATEPATH . '/sidebar-single.php'); ?>
				<div class="clear"></div>
			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>