<?php
/**
 * @package WordPress
 * @subpackage Toolbox
 * Template Name: SingFit
 */

get_header(); ?>
	<div id="sub_main">
		<div id="primary">
			<div id="interior">
				<div id="left_column">
					<?php if (have_posts()) : while (have_posts()) : the_post();?>
					<div id="page_title"><?php the_title();?></div>
   					<?php the_content(); ?>
 					<?php endwhile; endif; ?><br clear="all" />
 					<div id="sub_feature">
 						<div id="sub_left">
 						<h5>SingFit Demonstration Video</h5>
 						<p><iframe width="265" height="164" src="http://www.youtube.com/embed/T46e9xTdF0M" frameborder="0" allowfullscreen></iframe></p>
 						</div>
 						<div id="sub_right">
 						<h5>The SingFit Music Catalog</h5>
 						<p><strong><a href="/singfit-music-catalog/">Click here for a list of songs</a></strong> currently available in the SingFit app. More are being added to the list every day! </p>
 						<p>Sign up for our newsletter to be kept informed of new releases or use <strong><a href="/suggest-a-song/">this form</a></strong> to suggest an addition to our catalog.</p>
 						</div>
 					</div>
 					<div class="edit"><?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?></div>
				</div><!-- left_column -->
				<div id="sub_right_col">
					<ul>
					    <li><a href="<?php echo home_url(); ?>/clinical-practitioners/">Clinical Practitioners</a></li>
    					<li><a href="<?php echo home_url(); ?>/health-care-administrators/">Health Care Admins</a></li>
    					<li><a href="<?php echo home_url(); ?>/musicians/">Musicians</a></li>
    					<li><a href="<?php echo home_url(); ?>/parents-and-caregivers/">Parents and Caregivers</a></li>
    					<li><a href="<?php echo home_url(); ?>/singfit-essentials/">SingFit Essentials</a></li>
						<li><a href="<?php echo home_url(); ?>/singfit-music-catalog/">SingFit Music Catalog</a></li>
						<li><a href="<?php echo home_url(); ?>/support/">Support</a></li>
						<li><a href="<?php echo home_url(); ?>/contact/">Contact</a></li>
					</ul>
				</div>
				<div class="clear"></div>
			</div><!-- #interior -->
		</div><!-- #primary -->
<?php get_footer(); ?>