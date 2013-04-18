<?php
/**
 * @package WordPress
 * @subpackage Toolbox
 * Template Name: Press
 */
 ?>
 
<?php include (TEMPLATEPATH . '/header-press.php'); ?>
	<div id="main">
		<div id="primary">
			<div id="interior" role="main">
				<div id="left_column">
					<?php if (have_posts()) : while (have_posts()) : the_post();?>
					<div id="page_title"><?php the_title();?></div>
   					<?php the_content(); ?>
 					<?php endwhile; endif; ?>
 					<div class="edit"><?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?></div>
				</div><!-- left_column -->
				<?php get_sidebar(); ?>
				<div class="clear"></div>
			</div><!-- #interior -->
		</div><!-- #primary -->
<?php get_footer(); ?>