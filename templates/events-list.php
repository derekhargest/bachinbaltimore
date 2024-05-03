<?php
$today               = gmdate( 'Y-m-d H:i:s' );
$all_upcoming_events = EEM_Event::instance()->get_all(
	array(
		array( 'Datetime.DTT_EVT_start' => array( '>', $today ) ),
		'order_by' => array( 'Datetime.DTT_EVT_start' => 'ASC' ),
		'limit'    => 10,
	)
);

// Filter out special events from the list
$upcoming_events = array_filter(
	$all_upcoming_events,
	function( $event ) {
		$is_special_event = get_post_meta( $event->ID(), '_ee_is_special_event', true );
		return $is_special_event !== 'yes'; // Keep the event if it's not marked as special
	}
);

if ( ! empty( $upcoming_events ) ) : ?>
<div class="events-list">
	<div class="events-list__heading">
		<h2>The 36th Season</h2>
	</div>
	<div class="events-list__list">
		<?php
		foreach ( $upcoming_events as $event ) :
			$event_post_id = $event->ID();
			$thumbnail_url = get_the_post_thumbnail_url( $event_post_id, 'full' );
			if ( empty( $thumbnail_url ) ) {
				$thumbnail_url = get_template_directory_uri() . '/assets/images/default-image.webp';
			}
			$datetimes        = $event->datetimes_ordered();
			$first_datetime   = reset( $datetimes );
			$event_start_date = $first_datetime instanceof EE_Datetime ? $first_datetime->start_date( 'F j, Y' ) : '';
			?>
		<div class="events-list__item" style="background-color: <?php echo esc_attr( get_post_meta( $event_post_id, '_event_color_value', true ) ); ?>">
			<a href="<?php echo esc_url( get_permalink( $event->ID() ) ); ?>">
				<div class="events-list__image">
					<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="Event Image" class="events-list__img">
				</div>
				<div class="events-list__info">
					<div class="events-list__date">
						<p><?php echo esc_html( $event_start_date ); ?></p>
					</div>
					<div class="events-list__title">
						<h3><?php echo esc_html( $event->name() ); ?></h3>
					</div>
				</div>
			</a>
		</div>
		<?php endforeach; ?>
	</div>
<?php else : ?>
	<p>No recent events found.</p>
<?php endif; ?>
</div>
