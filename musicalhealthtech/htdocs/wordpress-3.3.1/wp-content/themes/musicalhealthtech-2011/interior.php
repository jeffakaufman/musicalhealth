<?php
/**
 * @package WordPress
 * @subpackage Toolbox
 * Template Name: Interior
 */

get_header(); ?>
	<div id="main">
		<div id="primary">
			<div id="interior" role="main">
				<div id="left_column">
					<?php if (have_posts()) : while (have_posts()) : the_post();?>
					<div id="page_title"><?php the_title();?></div>
   					<?php the_content(); ?>
 					<?php endwhile; endif; ?>
 					<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
				</div>
				<?php get_sidebar(); ?>
				<div class="clear"></div>
			</div><!-- #content -->
		</div><!-- #primary -->
<?php get_footer(); ?>