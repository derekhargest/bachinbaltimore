<div class="events-featured">
	<?php
    $eem_event              = EE_Registry::instance()->load_model('Event');
    $formatted_current_date = ( new DateTime('now', new DateTimeZone('UTC')) )->format('Y-m-d | H:i:s');

    $events = $eem_event->get_all(
        array(
            array( 'Datetime.DTT_EVT_start' => array( '>', $formatted_current_date ) ),
            'order_by' => array( 'Datetime.DTT_EVT_start' => 'ASC' ),
            'limit'    => 10,
        )
    );

    $featured_event = current(
        array_filter(
            $events,
            function ( $event ) {
                    return get_post_meta($event->ID(), '_ee_is_featured', true) === 'yes';
            }
        )
    );

    if ($featured_event ) {
        $event_id           = $featured_event->ID();
        $thumbnail_url      = has_post_thumbnail($event_id) ? get_the_post_thumbnail_url($event_id, 'full') : get_template_directory_uri() . '/assets/images/featured_event.jpg';
        $event_title        = $featured_event->name();
        $event_permalink    = get_permalink($event_id);
        $adult_ticket_price = 'N/A';

        $first_datetime = current($featured_event->datetimes_ordered());
        if ($first_datetime ) {
            $date_object                = new DateTime($first_datetime->start_date());
            $event_start_date_formatted = $date_object->format('l | F j, Y \a\t g:ia');

            $event_start_date_formatted = preg_replace_callback(
                '/([a-zA-Z]+) \| ([a-zA-Z]+) (\d+, \d+ at \d+:\d+[ap]m)/',
                function ( $matches ) {
                    return '<span>' . $matches[1] . '</span> | <span>' . $matches[2] . '</span> ' . $matches[3];
                },
                $event_start_date_formatted
            );
        }

        $venue         = current($featured_event->venues());
        $venue_name    = $venue ? $venue->name() : '';
        $venue_address = $venue ? $venue->address() . ' | ' . $venue->city() . ', ' . $venue->state() : '';

        $adult_ticket       = current(
            array_filter(
                $featured_event->tickets(),
                function ( $ticket ) {
                        return strpos(strtolower($ticket->name()), 'adult') !== false;
                }
            )
        );
        $adult_ticket_price = $adult_ticket ? number_format($adult_ticket->price(), 2, '.', ',') : $adult_ticket_price;
        $event_color        = get_post_meta($event_id, '_event_color_value', true);
        ?>
	<div class="events-featured__heading">
		<h2>Upcoming Concert</h2>
	</div>
	<div class="events-featured__event">
		<div class="events-featured__image">
			<a href="<?php echo esc_url($event_permalink); ?>">
				<img src="<?php echo esc_url($thumbnail_url); ?>" alt="Featured Event Image">
			</a>
		</div>
		<div class="events-featured__info">
			<header class="events-featured__title">
				<h2 class="event-title" style="color: <?php echo esc_attr($event_color); ?>">
					<?php echo esc_html($event_title); ?></h2>
			</header>
			<div class="events-featured__date">
				<p><?php echo wp_kses_post($event_start_date_formatted); ?></p>
			</div>
			<div class="events-featured__location">
				<p><?php echo esc_html($venue_name); ?></p>
				<p><?php echo esc_html($venue_address); ?></p>
			</div>
			<div class="events-featured__button">
				<a href="<?php echo esc_url($event_permalink); ?>" class="button"
					style="background-color: <?php echo esc_attr($event_color); ?>">Purchase Tickets</a>
			</div>
			<div class="events-featured__note">
				<p>For Group Discounts 8+, call <span
						style="color: <?php echo esc_attr($event_color); ?>">410-941-9262</span></p>
			</div>
		</div>
	</div>
	<?php
    } else {
        echo '<p>No upcoming events found.</p>';
    }
    ?>
</div>