<?php
/**
 * @package WordPress
 * @subpackage Toolbox
 * Template Name: Videos
 */
 ?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php
		global $page, $paged;
		
		wp_title( '|', true, 'right' );
		
		bloginfo( 'name' );
		
		$site_description = get_bloginfo( 'description', 'display' );
		
		if ( $site_description && ( is_home() || is_front_page() ) )
			echo " | $site_description";

		if ( $paged >= 2 || $page >= 2 )
			echo ' | ' . sprintf( __( 'Page %s', 'toolbox' ), max( $paged, $page ) );

	?></title><!--#title-->	
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="shortcut icon" href="<?php bloginfo('stylesheet_directory'); ?>/favicon.ico" />
	<!--add webfonts-->
	<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:700|Open+Sans:400,600,700|Maiden+Orange' rel='stylesheet' type='text/css'>	
	<!--stylesheets-->
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
	<?php if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' ); ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<link rel="alternate" type="application/rss+xml" title="Diamond Empowerment Fund RSS Feed" href="http://www.diamondempowerment.org/feed/" />
	<!--[if lt IE 9]>
		<script src="<?php bloginfo( 'template_directory' ); ?>/html5.js" type="text/javascript"></script>
	<![endif]-->
	<?php wp_head(); ?>
</head><!--#head-->

<body <?php body_class(); ?>>
<div id="page" class="hfeed">
	<div id="vheader"><a id="vlogo" href="http://www.musicalhealthtech.com/"></a></div><!--#vheader-->
	<div id="header_divider"></div>

	<div id="vmain">
		<div id="primary">
			<?php if (have_posts()) : while (have_posts()) : the_post();?>
			<?php the_content(); ?>
			<?php endwhile; endif; ?>
			<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
		</div><!-- #primary -->

<?php get_footer(); ?>