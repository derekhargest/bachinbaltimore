<?php
$today               = gmdate( 'Y-m-d H:i:s' );
$all_upcoming_events = EEM_Event::instance()->get_all(
	array(
		array( 'Datetime.DTT_EVT_start' => array( '>', $today ) ),
		'order_by' => array( 'Datetime.DTT_EVT_start' => 'ASC' ),
		'limit'    => 10,
	)
);

$upcoming_events = array_filter(
	$all_upcoming_events,
	function ( $event ) {
		$is_special_event = get_post_meta( $event->ID(), '_ee_is_special_event', true );
		return 'yes' !== $is_special_event;
	}
);

$extra_boxes_data = get_post_meta( get_the_ID(), '_extra_boxes_data', true );

if ( ! empty( $upcoming_events ) ) : ?>
<div class="events-list">
	<div class="events-list__heading">
		<h2>The 36th Season</h2>
	</div>
	<div class="events-list__list">
		<?php
		$count = 0;
		foreach ( $upcoming_events as $event ) :
			++$count;
			$event_post_id = $event->ID();
			$thumbnail_url = get_the_post_thumbnail_url( $event_post_id, 'full' );
			if ( empty( $thumbnail_url ) ) {
				$thumbnail_url = get_template_directory_uri() . '/assets/images/default-image.webp';
			}
			$datetimes        = $event->datetimes_ordered();
			$first_datetime   = reset( $datetimes );
			$event_start_date = $first_datetime instanceof EE_Datetime ? $first_datetime->start_date( 'F j, Y' ) : '';
			$event_color      = get_post_meta( $event_post_id, '_event_color_value', true );
			?>
		<div class="events-list__item">
			<a href="<?php echo esc_url( get_permalink( $event->ID() ) ); ?>">
				<div class="events-list__image">
					<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="Event Image" class="events-list__img">
				</div>
				<div class="events-list__info" <?php if ( $event_color ) : ?>
					style="background-color: <?php echo esc_attr( $event_color ); ?>;" <?php endif; ?>>
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

		<?php
		$additional_boxes = 4 - $count;
		for ( $i = 1; $i <= $additional_boxes; $i++ ) :
			$box_title = $extra_boxes_data['title'][ $i ] ?? 'More Info';
			$box_color = $extra_boxes_data['color'][ $i ] ?? '#dedede';
			$box_link  = $extra_boxes_data['link'][ $i ] ?? '#';
			?>
		<div class="events-list__item events-list__item--extra-box"
			style="background-color: <?php echo esc_attr( $box_color ); ?>;">
			<a href="<?php echo esc_url( $box_link ); ?>">
				<div class="events-list__info">
					<div class="events-list__title">
						<h3><?php echo esc_html( $box_title ); ?></h3>
					</div>
				</div>
			</a>
		</div>
		<?php endfor; ?>

	</div>
	<?php if ( $count == 0 ) : ?>
	<p>No recent events found.</p>
	<?php endif; ?>
</div>

<?php endif; ?>