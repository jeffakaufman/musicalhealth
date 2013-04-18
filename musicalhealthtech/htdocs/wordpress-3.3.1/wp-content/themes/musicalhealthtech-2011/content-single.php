<?php
/**
 * @package WordPress
 * @subpackage Toolbox
 */
?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-header">
		<h3 class="entry-title"><?php the_title(); ?></h3>

		<div class="entry-meta"><h4>
			<?php
				printf( __( '<span class="sep">Posted on </span><a href="%1$s" rel="bookmark"><span class="entry-date">%3$s</span></a>', 'toolbox' ),
					get_permalink(),
					get_the_date( 'c' ),
					get_the_date(),
					get_author_posts_url( get_the_author_meta( 'ID' ) ),
					sprintf( esc_attr__( 'View all posts by %s', 'toolbox' ), get_the_author() ),
					get_the_author()
				);
			?>
		</h4></div><!-- .entry-meta -->
	</div><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'toolbox' ), 'after' => '</div>' ) ); ?>
	</div><!-- .entry-content -->

	<div class="entry-meta"><h4>
		<?php
			$tag_list = get_the_tag_list( '', ', ' );
			if ( '' != $tag_list ) {
				$utility_text = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'toolbox' );
			} else {
				$utility_text = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'toolbox' );
			}
			printf(
				$utility_text,
				get_the_category_list( ', ' ),
				$tag_list,
				get_permalink(),
				the_title_attribute( 'echo=0' )
			);
		?>

		<?php edit_post_link( __( 'Edit', 'toolbox' ), '<span class="edit-link">', '</span>' ); ?></h4>
	</div><!-- .entry-meta -->
</div><!-- #post-<?php the_ID(); ?> -->
