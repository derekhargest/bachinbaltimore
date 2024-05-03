<?php get_header(); ?>

<main id="main" class="site-main single-event-page" role="main">

	<?php
	while ( have_posts() ) :
		the_post();
		global $post;
		$event_id         = $post->ID;
		$eem_event        = EEM_Event::instance()->get_one_by_ID( $event_id );
		$datetimes        = $eem_event->datetimes_ordered();
		$first_datetime   = reset( $datetimes );
		$event_start_date = $first_datetime instanceof EE_Datetime ? $first_datetime->start_date( 'l \| F j, Y \a\t g:ia' ) : 'Date not set';
		$venues           = $eem_event->venues();
		$first_venue      = reset( $venues );
		$venue_name       = $first_venue instanceof EE_Venue ? $first_venue->name() : 'Venue not set';
		$venue_address    = $first_venue instanceof EE_Venue ? $first_venue->address() . ' | ' . $first_venue->city() . ', ' . $first_venue->state() : 'Address not set';

		?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> class="single-event-template">
		<div class="single-event__event">
			<!-- Event Featured Image -->
			<div class="single-event__image">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/product-image.png' ); ?>"
					alt="Event Image">
				<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail(); ?>
				<?php endif; ?>
			</div>

			<!-- Event Details -->
			<div class="single-event__info">
				<!-- Event Title -->
				<header class="single-event__header">
					<?php the_title( '<h1 class="event-title">', '</h1>' ); ?>
				</header>
				<div class="single-event__date">
					<p><?php echo esc_html( $event_start_date ); ?></p>
				</div>
				<div class="single-event__location">
					<p><?php echo esc_html( $venue_name ); ?></p>
					<p><?php echo esc_html( $venue_address ); ?></p>
				</div>
				<div class="single-event__buy">
					<div class="single-event__price">
						<p>
							$36.00
						</p>
					</div>
					<?php
						echo do_shortcode( '[ESPRESSO_TICKET_SELECTOR event_id=' . $event_id . ']' );
					?>
				</div>
			</div>
		</div>
		<!-- Event Content -->
		<div class="single-event__content">
			<p>One of the greatest choral works ever composed and a powerful reminder of the strength of faith,
				solidarity, and moral conviction. With over 100 musicians on stage, experience the epic story of the
				Israelites exodus from Egypt. Maestro T. Herbert Dimmock leads the Bach in Baltimore Choir and
				Orchestra, joined by four superb soloists and the Maryland State Boychoir, in <strong>Handel Israel in
					Egypt</strong>.
			</p>
		</div>
		<div class="single-event__notes">
			<p>This concert is sponsored by a generous gift from Preston and Nancy Athey.</p>
			<p><i>Co-sponsored by Carol Macht and Dr. Sheldon Lerman in memory of their parents, Lois and Philip Macht
					and
					Lillian and Dr. Phillip Lerman.</i></p>
		</div>
		<div class="single-event__members">
			<div class="single-event__members__heading">
				<h2 class="event-heading">Featured Soloists</h2>
			</div>
			<ul class="single-event__members__list">
				<li class="single-event__member">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/bio-image.png' ); ?>">
					<div class="single-event__member__info">
						<h3>Test Name</h3>
						<h4>Position</h4>
					</div>
				</li>
				<li class="single-event__member">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/bio-image.png' ); ?>">
					<div class="single-event__member__info">
						<h3>Test Name</h3>
						<h4>Position</h4>
					</div>
				</li>
				<li class="single-event__member">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/bio-image.png' ); ?>">
					<div class="single-event__member__info">
						<h3>Test Name</h3>
						<h4>Position</h4>
					</div>
				</li>
				<li class="single-event__member">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/bio-image.png' ); ?>">
					<div class="single-event__member__info">
						<h3>Test Name</h3>
						<h4>Position</h4>
					</div>
				</li>
			</ul>
		</div>
		<div class="single-event__program">
			<h2 class="event-heading">Program</h2>
			<ul>
				<li>Beethoven Symphony No. 5</li>
			</ul>
		</div>
		<div class="single-event__venue">
			<h2 class="event-heading">Venue</h2>
			<div class="single-event__venue_info">
				<div class="single-event__venue__details">
					<p>Baltimore Hebrew Congregation</p>
					<p>7401 Park Heights Avenue | Baltimore</p>
				</div>
				<div class="single-event__venue__map">
					<img src="" alt="">
				</div>
			</div>
		</div>
		<div class="single-event__special">
			<h2 class="event-heading">Special Acknowledgements</h2>
			<p>*The soprano soloist is endowed in memory of anne Hortense pruitt Dimmock.</p>
			<p>**The alto soloist is endowed in memory of linda D. sadler, given by Family & Friends.</p>
			<p>***The bass soloist is endowed in memory of t. Herbert Dimmock, Jr.</p>
		</div>
		<div class="single-event__passes">
			<h2 class="event-heading">Want to buy a Pass?</h2>
			<a href="#" class="button">Purchase Passes</a>
		</div>
	</article>

	<?php endwhile; ?>

</main><!-- #main -->

<?php
	get_sidebar();
	get_footer(); ?>