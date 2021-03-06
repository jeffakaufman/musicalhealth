<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
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
	<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:400,700|Open+Sans:400,600,700|Maiden+Orange' rel='stylesheet' type='text/css' />	
	<!--stylesheets-->
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
	<?php if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' ); ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<link rel="alternate" type="application/rss+xml" title="Musical Health Technologies RSS Feed" href="<?php echo home_url(); ?>/feed/" />
	<!--[if lt IE 9]>
		<script src="<?php bloginfo( 'template_directory' ); ?>/html5.js" type="text/javascript"></script>
	<![endif]-->
	<?php wp_head(); ?>
	<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-26426392-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</head><!--#head-->

<body <?php body_class(); ?>>
<div id="page" class="hfeed">
	<div id="intheader">
		<div id="intheader_cont">
			<a id="logo" href="<?php echo home_url(); ?>"></a>
			<div id="suckernav"><ul id="pressnav">
				<li><a href="<?php echo home_url(); ?>/press/about-us/">Bios</a></li>
				<li><a href="<?php echo home_url(); ?>/press/faq/">FAQ</a></li>
				<li><a href="<?php echo home_url(); ?>/press/press-release/">Press Release</a></li>
				<li><a href="<?php echo home_url(); ?>/press/resources/">Resources</a></li>
				<li><a href="<?php echo home_url(); ?>/press/">Videos</a></li>
			</ul></div><!-- suckerfish -->
			<div id="tagline">SingFit: Sing Your Way to Wellness</div>
			<div id="tag_buttons">
				<div id="app"><a href="http://itunes.apple.com/us/app/singfit/id442827581?ls=1&mt=8"><img src="<?php bloginfo('template_directory'); ?>/images/app-store.png" alt="" width="135" height="40" /></a></div>
				<div id="submit-song"></div>
				<div id="socnet">
					<a id="facebook" href="http://www.facebook.com/pages/SingFit/225624990782421"></a>
					<a id="twitter" href="http://twitter.com/musicalhealtht"></a>
					<a id="stumble" href="http://www.stumbleupon.com/submit?url=<?php the_permalink(); ?>&title=<?php the_title(); ?>"></a>
					<a id="rss" href="<?php bloginfo('rss2_url'); ?>"></a>
				</div>
			</div><!-- tag_buttons -->
		</div><!--#intheader_cont-->
	</div><!--#intheader-->
	<div id="header_divider"></div>