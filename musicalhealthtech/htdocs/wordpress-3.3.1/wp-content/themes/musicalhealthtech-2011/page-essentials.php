<?php
/**
 * @package WordPress
 * @subpackage Toolbox
 * Template Name: Essentials
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
						<p><iframe width="570" height="320" src="http://www.youtube.com/embed/T46e9xTdF0M" frameborder="0" allowfullscreen></iframe></p>
						<h2>SingFit Quick Start Guide to Assisting the Singer:</h2>
						<ul><li>Watch the I Am The Singer tutorial in the More section of the app to understand how to work SingFit's controls.</li>
						<li>Always be supportive and never force anyone to sing.</li>
						<li>Find out the singer's favorite music and help him or her choose songs based on his or her interests.</li>
						<li>Check the output volume in the headphones to make sure they are appropriate for the singer.</li>
						<li><strong><a href="/wordpress/wp-content/uploads/MHT_AssistingTheSingFitSinger.pdf">Download our PDF</a></strong> for more details or start singing and enjoy!</li>
						</ul>
 					</div>
 					<div class="edit"><?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?></div>
				</div><!-- left_column -->
				<div id="sub_right_col">
					<ul>
					    <li><a href="<?php echo home_url(); ?>/faq/">FAQ</a></li>
					    <li><a href="<?php echo home_url(); ?>/wordpress/wp-content/uploads/MHT_AssistingTheSingFitSinger.pdf">Instructions (PDF)</a></li>
					    <li><a href="<?php echo home_url(); ?>/how-to-share-your-recordings/">How to Share Your Recordings</a></li>
					    <li><a href="<?php echo home_url(); ?>/suggest-a-song/">Suggest a Song</a></li>
					    <li><a href="<?php echo home_url(); ?>/singfit-music-catalog/">SingFit Music Catalog</a></li>
					</ul>
				</div>
				<div class="clear"></div>
			</div><!-- #interior -->
		</div><!-- #primary -->
<?php get_footer(); ?>