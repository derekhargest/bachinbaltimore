<div class="events-hero">
	<div class="events-hero__image">
		<?php if ( has_post_thumbnail() ) : ?>
		<img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ); ?>"
			alt="<?php the_title_attribute(); ?>">
		<?php else : ?>
		<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/events_hero.jpg' ); ?>"
			alt="Event Image">
		<?php endif; ?>
	</div>
</div>