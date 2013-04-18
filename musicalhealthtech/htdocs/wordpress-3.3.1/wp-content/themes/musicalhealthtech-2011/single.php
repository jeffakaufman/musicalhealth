<?php
/**
 * @package WordPress
 * @subpackage Toolbox
 * Template Name: Interior
 */

get_header(); ?>
	<div id="main">
		<div id="primary">
			<div id="interior">
				<div id="left_column">
				<?php while ( have_posts() ) : the_post(); ?>
					<div id="page_title"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></div>
					<?php get_template_part( 'content', 'single' ); ?>
					<?php comments_template( '', true ); ?>
					<?php endwhile; // end of the loop. ?>
				</div>
				<?php include (TEMPLATEPATH . '/sidebar-single.php'); ?>
				<div class="clear"></div>	
			</div><!-- #content -->
		</div><!-- #primary -->
<?php get_footer(); ?>