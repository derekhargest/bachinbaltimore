<div class="events-featured">
	<?php
	$eem_event              = EE_Registry::instance()->load_model( 'Event' );
	$current_date_sql       = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
	$formatted_current_date = $current_date_sql->format( 'Y-m-d H:i:s' );

	$events = $eem_event->get_all(
		array(
			array( 'Datetime.DTT_EVT_start' => array( '>', $formatted_current_date ) ),
			'limit'    => 1,
			'order_by' => array( 'Datetime.DTT_EVT_start' => 'ASC' ),
		)
	);

	$featured_event = null;
	foreach ( $events as $event ) {
		$is_featured = get_post_meta( $event->ID(), '_ee_is_featured', true );
		if ( $is_featured === 'yes' ) {
			$featured_event = $event;
			break;
		}
	}

	if ( ! $featured_event && ! empty( $events ) ) {
		$featured_event = reset( $events );
	}

	if ( $featured_event ) {
		$event_id           = $featured_event->ID();
		$thumbnail_url      = has_post_thumbnail( $event_id ) ? get_the_post_thumbnail_url( $event_id, 'full' ) : get_template_directory_uri() . '/assets/images/featured_event.jpg';
		$event_title        = $featured_event->name();
		$event_permalink    = get_permalink( $event_id );
		$adult_ticket_price = 'N/A';

		$datetimes                  = $featured_event->datetimes_ordered();
		$event_start_date_formatted = '';
		if ( ! empty( $datetimes ) ) {
			$first_datetime             = reset( $datetimes );
			$event_start_date_formatted = $first_datetime->start_date( 'l | F j, Y \a\t g:ia' );
		}

		$venues        = $featured_event->venues();
		$venue_name    = '';
		$venue_address = '';
		if ( ! empty( $venues ) ) {
			$venue = reset( $venues );
			if ( $venue instanceof EE_Venue ) {
				$venue_name    = $venue->name();
				$venue_address = $venue->address() . ' | ' . $venue->city() . ', ' . $venue->state();
			}
		}

		$tickets = $featured_event->tickets();
		foreach ( $tickets as $ticket ) {
			if ( strpos( strtolower( $ticket->name() ), 'adult' ) !== false ) {
				$adult_ticket_price = number_format( $ticket->price(), 2, '.', ',' );
				break;
			}
		}
		?>
	<div class="events-featured__heading">
		<h2>Upcoming Concert</h2>
	</div>
	<div class="events-featured__event">
		<div class="events-featured__image">
			<a href="<?php echo esc_url( $event_permalink ); ?>">
				<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="Featured Event Image">
			</a>
		</div>
		<div class="events-featured__info">
			<header class="events-featured__title">
				<h2 class="event-title"><?php echo esc_html( $event_title ); ?></h2>
			</header>
			<div class="events-featured__date">
				<p><?php echo esc_html( $event_start_date_formatted ); ?></p>
			</div>
			<div class="events-featured__location">
				<p><?php echo esc_html( $venue_name ); ?></p>
				<p><?php echo esc_html( $venue_address ); ?></p>
			</div>
			<div class="events-featured__price">
				<p>$<?php echo esc_html( $adult_ticket_price ); ?></p>
			</div>
			<div class="events-featured__button">
				<a href="<?php echo esc_url( $event_permalink ); ?>" class="button">Purchase Tickets</a>
			</div>
			<div class="events-featured__note">
				<p>For Group Discounts 8+, call <span>410-941-9262</span></p>
			</div>
		</div>
	</div>
		<?php
	} else {
		echo '<p>No upcoming events found.</p>';
	}
	?>
</div>
