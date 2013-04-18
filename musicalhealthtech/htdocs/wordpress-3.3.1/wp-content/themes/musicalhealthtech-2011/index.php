<?php include (TEMPLATEPATH . '/header-home.php'); ?>
<div id="main">
	<div id="primary">
		<div id="home">
			<div id="left_column">
				<h2>How SingFit Works</h2>
				<div class="space-bot"><iframe width="295" height="166" src="http://www.youtube.com/embed/YNbVzcftrXo" frameborder="0" allowfullscreen></iframe></div>
				<div style="clear:both"></div>
				<h2>SingFit Essentials</h2>
				<?php if(function_exists('iinclude_page')) iinclude_page(701); ?>
				<a class="continue" href="/singfit-essentials/">CONTINUE READING</a>
			</div><!-- #left column -->
			<div id="middle_column">	
				<h2>Fresh Off the Blog</h2>
				<?php query_posts('cat=10&showposts=1'); ?>
				<?php while (have_posts()) : the_post(); ?>
				<h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
				<?php the_excerpt(); ?>
				<?php endwhile;?>
				<a class="continue" href="/blog/">MORE BLOG POSTS</a>
				<div style="clear:both"></div>
				<h2>Latest Tweet</h2>
				<div id="jtweet">
					<?php $jltw_args = array(
						'username'	=> 'MusicalHealthT',
						'nb_tweets'	=> 1,
						'avatar'	=> false,
						'cache'		=> 120,
						'transition'	=> false,
						'delay'		=> 8,
						'links'		=> false
					);
			
					/* display widget */
					jltw($jltw_args);
					?>
				</div>
				<!-- <div id="more_tw"><a class="read_more" href="http://twitter.com/musicalhealtht">MORE TWEETS</a></div> -->
			</div><!-- middle column -->
			<?php get_sidebar(); ?>
		</div><!-- #home -->
	</div><!-- #primary -->
<?php get_footer(); ?>