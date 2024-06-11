<?php get_header(); ?>

<main id="main" class="site-main single-event-page" role="main">

	<?php
	while ( have_posts() ) :
		the_post();
		global $post;
		$event_id       = $post->ID;
		$eem_event      = EEM_Event::instance()->get_one_by_ID( $event_id );
		$datetimes      = $eem_event->datetimes_ordered();
		$first_datetime = reset( $datetimes );

		if ( $first_datetime instanceof EE_Datetime ) {
			$date_object      = new DateTime( $first_datetime->start_date() );
			$event_start_date = $date_object->format( 'l | F j, Y \a\t g:ia' );

			$event_start_date = preg_replace_callback(
				'/([a-zA-Z]+) \| ([a-zA-Z]+) (\d+, \d+ at \d+:\d+[ap]m)/',
				function ( $matches ) {
					return '<span>' . $matches[1] . '</span> | <span>' . $matches[2] . '</span> ' . $matches[3];
				},
				$event_start_date
			);
		} else {
			$event_start_date = 'Date not set';
		}

		$venues                   = $eem_event->venues();
		$first_venue              = reset( $venues );
		$venue_name               = $first_venue instanceof EE_Venue ? $first_venue->name() : 'Venue not set';
		$venue_address            = $first_venue instanceof EE_Venue ? $first_venue->address() . ' | ' . $first_venue->city() . ', ' . $first_venue->state() : 'Address not set';
		$venue_address_stripped   = $first_venue instanceof EE_Venue ? $first_venue->address() . '<br />' . $first_venue->city() . ', ' . $first_venue->state() : 'Address not set';
		$event_color              = get_post_meta( $event_id, '_event_color_value', true );
		$event_program            = html_entity_decode( get_post_meta( $event_id, '_event_program', true ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$special_acknowledgements = get_post_meta( $event_id, '_special_acknowledgements', true );
		$special_acknowledgements = htmlspecialchars_decode( $special_acknowledgements, ENT_QUOTES | ENT_HTML5 );

		?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> class="single-event-template">
		<div class="single-event__event">
			<!-- Event Featured Image -->
			<div class="single-event__image">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail(); ?>
				<?php endif; ?>
			</div>

			<!-- Event Details -->
			<div class="single-event__info">
				<!-- Event Title -->
				<header class="single-event__header">
					<h1 class="event-title event-color--text"><?php the_title(); ?></h1>
				</header>

				<div class="single-event__date">
					<p><?php echo wp_kses_post( $event_start_date ); ?></p>
				</div>

				<div class="single-event__location">
					<p><?php echo esc_html( $venue_name ); ?></p>
					<p><?php echo esc_html( $venue_address ); ?></p>
				</div>
				<div class="single-event__buy">
					<?php
						echo do_shortcode( '[ESPRESSO_TICKET_SELECTOR event_id=' . $event_id . ']' );
					?>
					<div class="single-event__buy__note">
						<p>For Group Discounts 8+, call <strong <?php if ( $event_color ) : ?>
								style="color: <?php echo esc_attr( $event_color ); ?>;"
								<?php endif; ?>>410-941-9262</strong>.
						</p>
					</div>
				</div>
			</div>
		</div>
		<!-- Event Content -->
		<div class="single-event__content">
			<?php the_content(); ?>
		</div>

		<?php
		$selected_artists = get_post_meta( $event_id, '_selected_artists', true );

		if ( ! empty( $selected_artists ) ) :
			?>
		<div class="single-event__members">
			<div class="single-event__members__heading">
				<h2 class="event-heading event-color--text">
					Featured Soloists
				</h2>
			</div>
			<ul class="single-event__members__list">
				<?php
				foreach ( $selected_artists as $artist_id ) {
						$artist_name      = get_the_title( $artist_id );
						$artist_position  = get_post_meta( $artist_id, '_artist_position', true );
						$artist_image_url = get_the_post_thumbnail_url( $artist_id, 'thumbnail' ) ?: get_template_directory_uri() . '/assets/images/bio-image.png'; // Default image if none set
					?>
				<li class="single-event__member">
					<a href="<?php echo esc_url( get_permalink( $artist_id ) ); ?>">
						<img src="<?php echo esc_url( $artist_image_url ); ?>">
						<div class="single-event__member__info">
							<h3 class="event-color--text">
								<?php echo esc_html( $artist_name ); ?>
							</h3>
							<h4 class="event-color--text">
								<?php echo esc_html( $artist_position ); ?>
							</h4>
						</div>
					</a>
				</li>
					<?php
				}
				?>
		</div>
			<?php
				endif;
		?>
		<?php
		if ( $event_program ) :
			?>
		<div class="single-event__program">
			<h2 class="event-heading event-color--text">Program</h2>
			<?php echo esc_html( $event_program ); ?>
		</div>
		<?php endif; ?>
		<div class="single-event__venue">
			<h2 class="event-heading" <?php if ( $event_color ) : ?>
				style="color: <?php echo esc_attr( $event_color ); ?>;" <?php endif; ?>>Venue</h2>
			<div class="single-event__venue_info">
				<div class="single-event__venue__details">
					<p><?php echo esc_html( $venue_name ); ?></p>
					<p><?php echo esc_html( $venue_address_stripped ); ?></p>
				</div>
				<div class="single-event__venue__map">
					<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none">
						<path fill="<?php if ( $event_color ) : ?>
							<?php echo esc_attr( $event_color ); ?> 
			<?php endif; ?>" fill-rule="evenodd"
							d="M11.291 21.706 12 21l-.709.706zM12 21l.708.706a1 1 0 0 1-1.417 0l-.006-.007-.017-.017-.062-.063a47.708 47.708 0 0 1-1.04-1.106 49.562 49.562 0 0 1-2.456-2.908c-.892-1.15-1.804-2.45-2.497-3.734C4.535 12.612 4 11.248 4 10c0-4.539 3.592-8 8-8 4.408 0 8 3.461 8 8 0 1.248-.535 2.612-1.213 3.87-.693 1.286-1.604 2.585-2.497 3.735a49.583 49.583 0 0 1-3.496 4.014l-.062.063-.017.017-.006.006L12 21zm0-8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"
							clip-rule="evenodd" />
					</svg>
					<a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo urlencode( $venue_address ); ?>"
						target="_blank" rel="noopener noreferrer" <?php if ( $event_color ) : ?>
						style="color: <?php echo esc_attr( $event_color ); ?>" <?php endif; ?>>Get Directions</a>

				</div>
			</div>
		</div>
		<div class="single-event__special">
			<?php if ( ! empty( $special_acknowledgements ) ) : ?>
			<h2 class="event-heading" <?php if ( $event_color ) : ?>
				style="color: <?php echo esc_attr( $event_color ); ?>;" <?php endif; ?>>
				Special Acknowledgements
			</h2>
				<?php echo wp_kses_post( $special_acknowledgements ); ?>
			<?php endif; ?>
		</div>
		<div class="single-event__passes">
			<h2 class="event-heading" <?php if ( $event_color ) : ?>
				style="color: <?php echo esc_attr( $event_color ); ?>;" <?php endif; ?>>Want to buy a Pass?</h2>
			<a href="#" class="button" <?php if ( $event_color ) : ?>
				style="background-color: <?php echo esc_attr( $event_color ); ?>;" <?php endif; ?>>Purchase
				Passes</a>
		</div>
	</article>

	<?php endwhile; ?>

</main><!-- #main -->

<?php
get_sidebar();
get_footer();
?>