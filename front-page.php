<?php
/**
 * lmcn template for displaying the Front-Page
 *
 * @package WordPress
 * @subpackage lmcn
 * @since lmcn 1.0
 */

get_header(); ?>

<?php if ( is_home() && !get_option('ss_disable') ) get_template_part('slideshow'); ?>
</div>	
<?php query_posts(array(
        'post__not_in' => $exl_posts,
        'paged' => $paged,
    )
); ?>
	<section class="page-content primary" role="main">
		<?php
			if ( have_posts() ) :

				while ( have_posts() ) : the_post();

					get_template_part( 'loop', get_post_format() );

				endwhile;

			else :

				get_template_part( 'loop', 'empty' );

			endif;
		?>
		
	</section>
<?php wp_reset_query(); ?>
<?php get_footer(); ?>