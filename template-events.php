<?php
/**
 * Template Name: Events Page Template
 * A full-width template.
 *
 * @package Avada
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php get_header(); ?>
<section id="content" class="full-width">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php
			echo fusion_render_rich_snippets_for_pages(); // phpcs:ignore WordPress.Security.EscapeOutput 
		?>
		<div class="post-content events-template">
			<?php get_template_part( 'templates/events', 'hero' ); ?>
			<div class="events-intro">
				<div class="events-intro__text">
					<?php the_content(); ?>
				</div>
			</div>
			<?php get_template_part( 'templates/events', 'featured' ); ?>
			<?php get_template_part( 'templates/events', 'note' ); ?>
			<?php get_template_part( 'templates/events', 'list' ); ?>
			<?php get_template_part( 'templates/events', 'special' ); ?>
		</div>
		<?php fusion_link_pages(); ?>
	</div>
		<?php if ( ! post_password_required( $post->ID ) ) : ?>
			<?php if ( Avada()->settings->get( 'comments_pages' ) ) : ?>
				<?php
				comments_template();
			endif;
		endif;
	endwhile;
	?>
</section>
<?php get_footer(); ?>
