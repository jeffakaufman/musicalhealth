<?php
/**
 * @package WordPress
 * @subpackage Toolbox
 * Template Name: About
 */

get_header(); ?>
	<div id="sub_main">
		<div id="primary">
			<div id="interior">
				<div id="left_column">
					<?php if (have_posts()) : while (have_posts()) : the_post();?>
					<div id="page_title"><?php the_title();?></div>
   					<?php the_content(); ?>
 					<?php endwhile; endif; ?>
 					<div id="sub_feature">
 						<div id="sub_left">
 						<h5>We're Hiring</h5>
 						<img src="/wordpress/wp-content/themes/musicalhealthtech-2011/images/hiring.jpg" alt="Now Hiring" width="265" height="184" />
 						<p>Are you one of the 5,000 music therapists licensed to practice in the U.S or an off the charts biz dev or sales wiz with experience in novel approaches to healthcare? <strong><a href="http://www.musicalhealthtech.com/now-hiring/">We're currently hiring.</a></strong></p>
 						<p>Looking forward to hearing from you!</p>
 						</div>
 						<div id="sub_right">
 						<h5>It's Easier to Win When You Have Singing on Your Side</h5>
 						<a href="/wordpress/2011/its-easier-to-win-when-you-have-singing-on-your-side/"><img src="/wordpress/wp-content/themes/musicalhealthtech-2011/images/ata.jpg" alt="American Telemedicine Association Presentation" width="265" height="150" /></a>
 						<p>We won "Most Innovative Presentation" at the American Telemedicine Association conference in Anchorage in fall 2011. 
 						Andy, Musical Health Technologies co-Founder, discussed how SingFit can be used by caregivers to help calm down an agitated patient or family member by engaging them in a song. 
 						<a href="http://www.musicalhealthtech.com/wordpress/2011/its-easier-to-win-when-you-have-singing-on-your-side/"><strong>Watch our presentation!</strong></a></p>
 						</div>
 					</div>
 					<div class="edit"><?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?></div>
				</div><!-- left_column -->
				<div id="sub_right_col">
					<ul>
					<li><a href="<?php echo home_url(); ?>/bios/">Bios</a></li>
					<li><a href="<?php echo home_url(); ?>/now-hiring/">We're Hiring</a></li>
					<li><a href="<?php echo home_url(); ?>/category/news/">News</a></li>
					<li><a href="<?php echo home_url(); ?>/press/">Press Resources</a></li>
					<li><a href="<?php echo home_url(); ?>/privacy-policy/">Privacy Policy</a></li>
					</ul>
				</div>
				<div class="clear"></div>
			</div><!-- #interior -->
		</div><!-- #primary -->
<?php get_footer(); ?>