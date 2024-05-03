<div class="events-special">
	<div class="events-special__heading">
		<h2>Special Events</h2>
	</div>
	<div class="events-special__intro">
		<p>Enjoy these exciting events and help support Bach In Baltimore at the same time! A portion of the proceeds goes to supporting our programs.</p>
	</div>
	<div class="events-special__list">
		<?php
		$args = array(
			'post_type'      => 'espresso_events',
			'posts_per_page' => 3,
			'meta_query'     => array(
				array(
					'key'     => '_ee_is_special_event',
					'value'   => 'yes',
					'compare' => '=',
				),
			),
			'order'          => 'ASC',
		);

		$special_events_query = new WP_Query( $args );

		if ( $special_events_query->have_posts() ) :
			while ( $special_events_query->have_posts() ) :
				$special_events_query->the_post();
				$thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
				if ( empty( $thumbnail_url ) ) {
					$thumbnail_url = get_template_directory_uri() . '/assets/images/default-image.webp';
				}
				?>
				<div class="events-special__item">
					<div class="events-special__image">
						<a href="<?php the_permalink(); ?>">
							<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php the_title_attribute(); ?>">
						</a>
					</div>
				</div>
				<?php
			endwhile;
		else :
			?>
			<p>No special events found.</p>
			<?php
		endif;
		wp_reset_postdata();
		?>
	</div>
</div>
