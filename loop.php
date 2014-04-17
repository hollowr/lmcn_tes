<?php
/**
 * lmcn template for displaying the standard Loop
 *
 * @package WordPress
 * @subpackage lmcn
 * @since lmcn 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<section class='postcontent imageindex'>
<?php
	if ( '' != get_the_post_thumbnail() ) : ?>
		<?php the_post_thumbnail(); ?><?php
		endif; 
	?>
	</section>
<section class='postcontent contentindex'>
	<div class="post-metadate"><?php
		lmcn_post_date(); ?>
	</div>
	<h1 class="post-title"><?php

		if ( is_singular() ) :
			the_title();
		else : ?>

			<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php
				the_title(); ?>
			</a><?php

		endif; ?>

	</h1>
	<?php

		if ( is_home() ) echo "<hr class='line'>";

	?>
	<div class="post-meta"><?php
		lmcn_post_category(); ?>
	</div>

	<div class="post-content">

		<?php if ( is_front_page() || is_category() || is_archive() || is_search() ) : ?>

			<?php the_excerpt(); ?>
			<p>
				<a class="suite" href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
					LIRE L'ARTICLE
					<?php
						// the_title(); 
					?>
				</a>
			</p>

		<?php else : ?>

			<?php the_content( __( 'Lire la suite &raquo', 'lmcn' ) ); ?>

		<?php endif; ?>

		<?php
			wp_link_pages(
				array(
					'before'           => '<div class="linked-page-nav"><p>'. __( 'This article has more parts: ', 'lmcn' ),
					'after'            => '</p></div>',
					'next_or_number'   => 'number',
					'separator'        => ' ',
					'pagelink'         => __( '&lt;%&gt;', 'lmcn' ),
				)
			);
		?>

	</div>
</section>
</article>