<?php
/**
 * Extra files & functions are hooked here.
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package Avada
 * @subpackage Core
 * @since 1.0 
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

if ( ! defined( 'AVADA_VERSION' ) ) {
	define( 'AVADA_VERSION', '7.10.1' );
}

if ( ! defined( 'AVADA_MIN_PHP_VER_REQUIRED' ) ) {
	define( 'AVADA_MIN_PHP_VER_REQUIRED', '5.6' );
}

if ( ! defined( 'AVADA_MIN_WP_VER_REQUIRED' ) ) {
	define( 'AVADA_MIN_WP_VER_REQUIRED', '4.9' );
}


// Developer mode.
if ( ! defined( 'AVADA_DEV_MODE' ) ) {
	define( 'AVADA_DEV_MODE', false );
}

add_action(
	'AHEE__thank_you_page_overview_template__top',
	function ( $transaction ) {
		// Ensure we have a transaction object.
		if ( ! $transaction instanceof EE_Transaction ) {
			echo 'Transaction data is not available.';
			return;
		}

		$transaction_id = $transaction->ID();

		// Echo the Google event snippet with dynamic transaction_id.
		?>
<!-- Event snippet for Purchase conversion page -->
<script>
gtag('event', 'conversion', {
	'send_to': 'AW-11440538947/cCqqCNu1qYoZEMOKo88q',
	'transaction_id': '<?php echo esc_html( $transaction_id ); ?>'
});
</script>
<?php
	},
	10,
	1
);

function ee_add_event_options_meta_box() {
	add_meta_box(
		'ee_event_options',
		'Event Options',
		'ee_event_options_meta_box_callback',
		'espresso_events',
		'side',
		'high'
	);
}

add_action( 'add_meta_boxes', 'ee_add_event_options_meta_box' );

function ee_event_options_meta_box_callback( $post ) {
	wp_nonce_field( 'ee_save_event_options_meta_data', 'ee_event_options_meta_box_nonce' );

	// Featured Event
	$is_featured = get_post_meta( $post->ID, '_ee_is_featured', true );
	echo '<label for="ee_featured_event_field">Featured Event:</label>';
	echo '<input type="checkbox" name="ee_featured_event_field" value="yes"' . checked( $is_featured, 'yes', false ) . '> Yes<br/>';

	// Special Event
	$is_special_event = get_post_meta( $post->ID, '_ee_is_special_event', true );
	echo '<label for="ee_special_event_field">Special Event:</label>';
	echo '<input type="checkbox" name="ee_special_event_field" value="yes"' . checked( $is_special_event, 'yes', false ) . '> Yes<br/>';

	// Color Picker
	$color_value = get_post_meta( $post->ID, '_event_color_value', true );
	echo '<label for="event_color_value">Event Color:</label>';
	echo '<input type="text" name="event_color_value" class="ee-color-field" value="' . esc_attr( $color_value ) . '" data-default-color="#ffffff">';

	// Enqueue the color picker assets
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	wp_add_inline_script( 'wp-color-picker', 'jQuery(document).ready(function($){ $(".ee-color-field").wpColorPicker(); });' );
}

function ee_save_event_options_meta( $post_id ) {
	if ( ! isset( $_POST['ee_event_options_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['ee_event_options_meta_box_nonce'], 'ee_save_event_options_meta_data' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Save Featured Event
	$is_featured = isset( $_POST['ee_featured_event_field'] ) ? 'yes' : 'no';
	update_post_meta( $post_id, '_ee_is_featured', $is_featured );

	// Save Special Event
	$is_special_event = isset( $_POST['ee_special_event_field'] ) ? 'yes' : 'no';
	update_post_meta( $post_id, '_ee_is_special_event', $is_special_event );

	// Save Color Value
	if ( isset( $_POST['event_color_value'] ) ) {
		update_post_meta( $post_id, '_event_color_value', sanitize_hex_color( $_POST['event_color_value'] ) );
	}
}
add_action( 'save_post_espresso_events', 'ee_save_event_options_meta' );


function wpw_enqueue_color_picker_assets( $hook_suffix ) {
	if ( 'post.php' !== $hook_suffix && 'post-new.php' !== $hook_suffix ) {
		return;
	}
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	wp_add_inline_script( 'wp-color-picker', 'jQuery(document).ready(function($){ $(".ee-color-field").wpColorPicker(); });' );
}
add_action( 'admin_enqueue_scripts', 'wpw_enqueue_color_picker_assets' );



/**
* Compatibility check.
*
* Check that the site meets the minimum requirements for the theme before proceeding.
*
* @since 6.0
*/
if ( version_compare( $GLOBALS['wp_version'], AVADA_MIN_WP_VER_REQUIRED, '<' ) || version_compare(
	PHP_VERSION,
	AVADA_MIN_PHP_VER_REQUIRED,
	'<'
) ) {
	require_once get_template_directory() . '/includes/bootstrap-compat.php';
	return;
} /** * Bootstrap the theme. * * @since 6.0 */ require_once get_template_directory()
	. '/includes/bootstrap.php'; /* Omit closing PHP tag to avoid "Headers already sent" issues. */

function add_events_page_meta_boxes() {
	global $post;
	if ( 'template-events.php' === get_post_meta( $post->ID, '_wp_page_template', true ) ) {
		add_meta_box(
			'events_extra_boxes',
			'Extra Boxes for Events Page',
			'events_extra_boxes_callback',
			'page',
			'normal',
			'high'
		);
	}
}

add_action( 'add_meta_boxes', 'add_events_page_meta_boxes' );

function events_extra_boxes_callback( $post ) {
	// Nonce field for security
	wp_nonce_field( 'events_extra_boxes_data', 'events_extra_boxes_nonce' );

	// Get existing values
	$extra_boxes_data = get_post_meta( $post->ID, '_extra_boxes_data', true );

	// Check if data exists
	$extra_boxes = is_array( $extra_boxes_data ) ? $extra_boxes_data : array();

	// Output fields for 3 extra boxes
	for ( $i = 1; $i <= 3; $i++ ) {
		?>
<p>
	<label for="extra_box_title_<?php echo $i; ?>">Title for Box
		<?php echo $i; ?>:</label>
	<input type="text" id="extra_box_title_<?php echo $i; ?>" name="extra_box_title_<?php echo $i; ?>"
		value="<?php echo esc_attr( $extra_boxes['title'][ $i ] ?? '' ); ?>" class="widefat">
</p>
<p>
	<label for="extra_box_color_<?php echo $i; ?>">Background Color
		for Box <?php echo $i; ?>:</label>
	<input type="color" id="extra_box_color_<?php echo $i; ?>" name="extra_box_color_<?php echo $i; ?>"
		value="<?php echo esc_attr( $extra_boxes['color'][ $i ] ?? '#ffffff' ); ?>">
</p>
<p>
	<label for="extra_box_link_<?php echo $i; ?>">Link for Box
		<?php echo $i; ?>:</label>
	<input type="url" id="extra_box_link_<?php echo $i; ?>" name="extra_box_link_<?php echo $i; ?>"
		value="<?php echo esc_url( $extra_boxes['link'][ $i ] ?? '' ); ?>" class="widefat">
</p>
<?php
	}
}

function save_events_extra_boxes_data( $post_id ) {
	if ( ! isset( $_POST['events_extra_boxes_nonce'] ) || ! wp_verify_nonce( $_POST['events_extra_boxes_nonce'], 'events_extra_boxes_data' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Prepare data to save
	$extra_boxes_data = array(
		'title' => array(),
		'color' => array(),
		'link'  => array(),
	);
	for ( $i = 1; $i <= 3; $i++ ) {
		$extra_boxes_data['title'][ $i ] = sanitize_text_field( $_POST[ 'extra_box_title_' . $i ] ?? '' );
		$extra_boxes_data['color'][ $i ] = sanitize_hex_color( $_POST[ 'extra_box_color_' . $i ] ?? '#ffffff' );
		$extra_boxes_data['link'][ $i ]  = esc_url_raw( $_POST[ 'extra_box_link_' . $i ] ?? '' );
	}

	// Save data
	update_post_meta( $post_id, '_extra_boxes_data', $extra_boxes_data );
}

add_action( 'save_post', 'save_events_extra_boxes_data' );

add_action('add_meta_boxes', 'add_artist_meta_box');

function add_artist_meta_box() {
    add_meta_box(
        'artist_meta_box', // Unique ID
        'Select Artists', // Box title
        'display_artist_meta_box', // Content callback
        'espresso_events', // Post type
        'side' // Context
    );
}

function display_artist_meta_box($post) {
    // Get selected artists
    $selected_artists = get_post_meta($post->ID, '_selected_artists', true);
    $selected_artists = is_array($selected_artists) ? $selected_artists : array();

    // Get list of artists
    $artists = get_posts(array(
        'post_type' => 'post',
        'category_name' => 'Artist',
        'numberposts' => -1,
    ));

    // Display artists checkboxes
    echo '<label>Select Artists:</label><br>';
    foreach ($artists as $artist) {
        $checked = in_array($artist->ID, $selected_artists) ? 'checked' : '';
        echo '<input type="checkbox" name="selected_artists[]" value="' . $artist->ID . '" ' . $checked . '> ' . $artist->post_title . '<br>';
    }
}

add_action('save_post', 'save_artist_meta_box');

function save_artist_meta_box($post_id) {
    if (array_key_exists('selected_artists', $_POST)) {
        update_post_meta($post_id, '_selected_artists', $_POST['selected_artists']);
    } else {
        // If no artists are selected, delete the meta key to remove all selections
        delete_post_meta($post_id, '_selected_artists');
    }
}


function display_event_artists($post_id) {
    $selected_artists = get_post_meta($post_id, '_selected_artists', true);

    if (!empty($selected_artists)) {
        echo '<h4>Artists:</h4>';
        foreach ($selected_artists as $artist_id) {
            $artist_name = get_the_title($artist_id);
            echo '<p>' . $artist_name . '</p>';
        }
    }
}


function add_artist_position_meta_box() {
	add_meta_box(
		'artist_position_box',        // ID of the meta box
		'Artist Position',            // Title of the meta box
		'artist_position_meta_box_callback',   // Callback function
		'post',                       // Post type
		'side',                       // Context
		'default'                     // Priority
	);
}

function artist_position_meta_box_callback( $post ) {
	// Use nonce for verification to secure the data being saved
	wp_nonce_field( basename( __FILE__ ), 'artist_position_nonce' );

	// Get the current value of the field
	$artist_position = get_post_meta( $post->ID, '_artist_position', true );

	// HTML for the meta box
	echo '<label for="artist_position">Position:</label>';
	echo '<input type="text" id="artist_position" name="artist_position" value="' . esc_attr( $artist_position ) . '" style="height: auto;">';
}

add_action( 'add_meta_boxes', 'add_artist_position_meta_box' );

function save_artist_position( $post_id ) {
	// Verify nonce
	if ( ! isset( $_POST['artist_position_nonce'] ) ||
		! wp_verify_nonce( $_POST['artist_position_nonce'], basename( __FILE__ ) ) ) {
		return;
	}

	// Check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Store or delete the data
	if ( isset( $_POST['artist_position'] ) ) {
		update_post_meta( $post_id, '_artist_position', sanitize_text_field( $_POST['artist_position'] ) );
	} else {
		delete_post_meta( $post_id, '_artist_position' );
	}
}

add_action( 'save_post', 'save_artist_position' );

function customize_espresso_ticket_selector_styles() {
	$event_id    = get_the_ID();  // Make sure this is the correct ID for your event
	$event_color = get_post_meta( $event_id, '_event_color_value', true ); // Assuming '_event_color' is your meta key

	// Basic CSS to change the button color
	$custom_css = "
        .ticket-selector-submit-btn {
            background-color: {$event_color} !important;
        }
		.event-color--text{
			color: {$event_color} !important;
		}
		.event-color--background{
			background-color: {$event_color} !important;
		}
		.single-event__content strong{
			color: {$event_color} !important;
		}

    ";

	wp_add_inline_style( 'espresso_default', $custom_css ); // 'espresso_default' should be the handle for the Event Espresso styles
}
add_action( 'wp_enqueue_scripts', 'customize_espresso_ticket_selector_styles' );

// Hook to add meta boxes
add_action( 'add_meta_boxes', 'add_program_meta_box' );
function add_program_meta_box() {
	add_meta_box(
		'event_program',          // ID of the meta box
		'Event Program',          // Title of the meta box
		'event_program_callback', // Callback function
		'espresso_events',        // Post type
		'normal',                 // Context
		'default'                 // Priority
	);
}

// Callback function for displaying the meta box
function event_program_callback( $post ) {
	$content   = get_post_meta( $post->ID, '_event_program', true );
	$editor_id = 'event_program_editor';
	$settings  = array(
		'textarea_name' => 'event_program',
		'media_buttons' => true,
		'textarea_rows' => 10,
		'teeny'         => false,
	);

	// Output the nonce field for validation
	wp_nonce_field( 'event_program_save', 'event_program_nonce' );

	wp_editor( htmlspecialchars_decode( $content ), $editor_id, $settings );
}

// Hook to save the meta box data
add_action( 'save_post', 'save_event_program' );
function save_event_program( $post_id ) {
	// Check if the nonce is set and verify it
	if ( ! isset( $_POST['event_program_nonce'] ) || ! wp_verify_nonce( $_POST['event_program_nonce'], 'event_program_save' ) ) {
		return;
	}

	// Check if this is an autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check user permissions
	if ( isset( $_POST['post_type'] ) && 'espresso_events' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Update the meta field in the database
	if ( isset( $_POST['event_program'] ) ) {
		update_post_meta( $post_id, '_event_program', htmlspecialchars( $_POST['event_program'], ENT_QUOTES, 'UTF-8' ) );
	}
}

// Hook to add meta boxes
add_action( 'add_meta_boxes', 'add_special_acknowledgements_meta_box' );
function add_special_acknowledgements_meta_box() {
	add_meta_box(
		'special_acknowledgements_meta',      // Unique ID for the meta box
		'Special Acknowledgements',           // Meta box title
		'special_acknowledgements_callback',  // Callback function that will echo the content
		'espresso_events',                    // Post type where the meta box will appear
		'normal',                             // Context where the box will appear ('normal', 'side', 'advanced')
		'high'                                // Priority within the context where the boxes should show ('high', 'low')
	);
}

// Display the meta box
function special_acknowledgements_callback( $post ) {
	$special_acknowledgements = get_post_meta( $post->ID, '_special_acknowledgements', true );
	$editor_id                = 'special_acknowledgements_editor';
	$settings                 = array(
		'textarea_name' => 'special_acknowledgements',
		'media_buttons' => true,
		'textarea_rows' => 10,
		'teeny'         => false,
	);

	// Display the editor with saved content
	wp_editor( htmlspecialchars_decode( $special_acknowledgements ), $editor_id, $settings );
}

// Save the data from the meta box
add_action( 'save_post', 'save_special_acknowledgements' );
function save_special_acknowledgements( $post_id ) {
	if ( array_key_exists( 'special_acknowledgements', $_POST ) ) {
		update_post_meta( $post_id, '_special_acknowledgements', htmlspecialchars( $_POST['special_acknowledgements'] ) );
	}
}
function ee_custom_ticket_selector_shortcode( $atts ) {
    // Extract shortcode attributes
    $atts = shortcode_atts(
        array(
            'event_id' => '',
            'button_text' => 'Register Now',
        ),
        $atts,
        'custom_espresso_ticket_selector'
    );

    // Ensure an event ID is provided
    if ( empty( $atts['event_id'] ) ) {
        return 'Event ID is required.';
    }

    // Capture the original button text filter
    add_filter( 'FHEE__EE_Ticket_Selector__display_ticket_selector_submit_button__btn_text', function( $text ) use ( $atts ) {
        return $atts['button_text'];
    });

    // Return the ticket selector shortcode
    return do_shortcode( '[ESPRESSO_TICKET_SELECTOR event_id="' . $atts['event_id'] . '"]' );
}
add_shortcode( 'custom_espresso_ticket_selector', 'ee_custom_ticket_selector_shortcode' );