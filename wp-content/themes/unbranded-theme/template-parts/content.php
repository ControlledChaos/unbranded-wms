<?php
/**
 * Template part for displaying posts
 *
 * @package    WebApp
 * @subpackage UB_Theme
 * @since      1.0.0
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">
	<header class="entry-header">
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;

		if ( 'post' === get_post_type() ) :
			?>
			<div class="entry-meta">
				<?php
				UB_Theme\Tags\posted_on();
				UB_Theme\Tags\posted_by();
				?>
			</div>
		<?php endif; ?>
	</header>

	<?php UB_Theme\Tags\post_thumbnail(); ?>

	<div class="entry-content" itemprop="articleBody">
		<?php
		the_content( sprintf(
			wp_kses(
				__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'unbranded' ),
				[
					'span' => [
						'class' => [],
					],
				]
			),
			get_the_title()
		) );

		wp_link_pages( [
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'unbranded' ),
			'after'  => '</div>',
		] );
		?>
	</div>

	<footer class="entry-footer">
		<?php UB_Theme\Tags\entry_footer(); ?>
	</footer>
</article>
