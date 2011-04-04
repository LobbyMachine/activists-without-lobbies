<?php
/**
 * event post_type
 * @package activists-lobbies
 */

/**
 * event post_type
 */
function awl_event_type() {
	$slug = awl_get_option('slug_event');

	$labels = array(
		'name' => _x('Events', 'post type general name', 'activists-lobbies'),
		'singular_name' =>  _x('Event', 'post type singular name', 'activists-lobbies'),
		'add_new_item' => __('Add New Event', 'activists-lobbies'),
		'edit_item' => __('Edit Event', 'activists-lobbies'),
		'new_item' => __('New Event', 'activists-lobbies'),
		'view_item' => __('View Event', 'activists-lobbies'),
		'search_items' => __('Search Events', 'activists-lobbies'),
		'not_found' => __('No events found', 'activists-lobbies'),
		'not_found_in_trash' => __('No events found in Trash', 'activists-lobbies'),
	);

	$args = array(
			'description' => __('Information on local events and actions.', 'activists-lobbies'),
			'supports' => array('title', 'editor', 'author', 'revisions', 'thumbnail', 'excerpt', 'trackbacks', 'page-attributes', 'comments'),
			'rewrite' => array('slug' => $slug, 'pages' => true, 'feeds' => true, 'with_front' => false),
			'register_meta_box_cb' => 'awl_event_boxes',
			'taxonomies' => array('post_tag', 'category'),
			'capability_type' => 'event',
			'has_archive' => $slug,
// 			'map_meta_cap' => true,
			'hierarchical' => true,
			'query_var' => true,
			'labels' => $labels,
			'public' => true,
		);
	register_post_type('awl_event', $args);
	add_filter('archive_template', 'awl_event_archive_template');
	add_filter('single_template', 'awl_event_single_template');
}

/**
 * callback from registering awl_event to generate meta boxes on an edit page
 * use action add_meta_boxes_awl_event_types to insert additional event types
 */
function awl_event_boxes() {
	add_meta_box('awl_event_meta', __('Additional Settings', 'conference-manager'), 'awl_mb_meta', 'awl_event', 'side', 'high');
}

/**
 * meta box for event metadata
 * use filter awl_event_types to insert additional event types
 */
function awl_mb_meta() {
	$types = array('petition' => 'Online Petition');
	$types = apply_filters('awl_event_types', $types);

	// drop-down for event type
}

/**
 * callback to process posted metadata
 *
 * @param int post id
 */
function awl_event_meta_postback ($post_id, $post) {
	if (!wp_verify_nonce($_POST["awl_event_meta_nonce"], basename(__FILE__)) || 'awl_event' != $post->post_type) {
		return $post_id;
	}

	$image = $_POST['cm_image'];
	$asin = $_POST['cm_asin'];
	$type = $_POST['cm_type'];
	$link = $_POST['cm_link'];

	cm_update_meta('cm_asin', $post_id, $asin);
	cm_update_meta('cm_type', $post_id, $type);
	cm_update_meta('cm_link', $post_id, $link);
	cm_update_meta('cm_image', $post_id, $image);
}

/**
 * display counts in the diashboard
 * @todo push html to template functions
 */
function awl_event_right_now() {
	$num_posts = wp_count_posts('awl_event');
	$num = number_format_i18n($num_posts->publish);
	$text = _n('Event', 'Events', intval($num_posts->publish), 'activists-lobbies');
	if (current_user_can('edit_events')) {
		$num = '<a href="/wp-admin/edit.php?post_type=awl_event">' . $num . '</a>';
		$text = '<a href="/wp-admin/edit.php?post_type=awl_event">' . $text . '</a>';
	}

	echo '<tr>';
	echo '<td class="first b b-tags">'.$num.'</td>';
	echo '<td class="t tags">' . $text . '</td>';
	echo '</tr>';
}

/**
 * initialise and register the actions for product post_type
 */
function awl_init_event() {
	awl_event_type();
	add_action('right_now_content_table_end', 'awl_event_right_now');
	add_action('save_post', 'awl_event_meta_postback', 10, 2);
}
