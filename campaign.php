<?php
/**
 * campaign custom_type and petition campaign type
 * @package activists-lobbies
 */

/**
 * campaign post_type
 */
function awl_campaign_type() {
	$slug = awl_get_option('slug_campaign');

	$labels = array(
		'name' => _x('Campaigns', 'post type general name', 'activists-lobbies'),
		'singular_name' =>  _x('Campaign', 'post type singular name', 'activists-lobbies'),
		'add_new_item' => __('Add New Campaign', 'activists-lobbies'),
		'edit_item' => __('Edit Campaign', 'activists-lobbies'),
		'new_item' => __('New Campaign', 'activists-lobbies'),
		'view_item' => __('View Campaign', 'activists-lobbies'),
		'search_items' => __('Search Campaigns', 'activists-lobbies'),
		'not_found' => __('No campaigns found', 'activists-lobbies'),
		'not_found_in_trash' => __('No campaigns found in Trash', 'activists-lobbies'),
	);

	$args = array(
			'description' => __('Campaign information.', 'activists-lobbies'),
			'supports' => array('title', 'editor', 'author', 'revisions', 'thumbnail', 'excerpt', 'trackbacks', 'page-attributes', 'comments'),
			'rewrite' => array('slug' => $slug, 'pages' => true, 'feeds' => true, 'with_front' => false),
			'register_meta_box_cb' => 'awl_campaign_boxes',
			'taxonomies' => array('post_tag', 'category'),
			'capability_type' => 'campaign',
			'has_archive' => $slug,
			'map_meta_cap' => true,
			'hierarchical' => true,
			'query_var' => true,
			'labels' => $labels,
			'public' => true,
		);
	register_post_type('awl_campaign', $args);
	add_filter('archive_template', 'awl_campaign_archive_template');
	add_filter('single_template', 'awl_campaign_single_template');
}

/**
 * callback from registering awl_campaign to generate meta boxes on an edit page
 * use action add_meta_boxes_awl_campaign_types to insert additional campaign types
 */
function awl_campaign_boxes() {
	add_meta_box('awl_campaign_meta', __('Campaign Setup', 'activists-lobbies'), 'awl_mb_meta', 'awl_campaign', 'side', 'high');
}

/**
 * meta box for campaign metadata
 * use filter awl_campaign_types to insert additional campaign types
 * @todo would this work better as individual metaboxes?
 */
function awl_mb_meta() {
	$types = apply_filters('awl_campaign_types', array());

	// drop-down for campaign types
	$select = '<select id="awl_campaign_type" name="awl_campaign_type">';
	$select .= '<option value="none">' . __('Select Campaign', 'amazon-library') . '</option>';
	foreach ($types as $id => $name) {
		$select .= '<option value="' . $id . '">' . __($name, 'amazon-library') . '</option>';
	}
	$select .= '</select>';

	// add extra fields from campaign type
	$html = apply_filters('awl_campaign_setup', '');
	if (!empty($html)) {
		echo $select . $html;
	}
}

/**
 * callback to process posted metadata
 *
 * @param int post id
 */
function awl_campaign_meta_postback ($post_id, $post) {
	$req = isset($_REQUEST['post_type']) ? $_REQUEST['post_type'] : '';
	if ( ('awl_campaign' != $req) || !current_user_can( 'edit_campaign', $post_id ) ) {
		return $post_id;
	}
	$image = isset($_POST['awl_image']) ? $_POST['awl_image'] : null;

	awl_update_meta('awl_image', $post_id, $image);
}

/**
 * display counts in the diashboard
 * @todo push html to template functions
 * @todo move to separate dashboard box to highlight current campaign numbers
 */
function awl_campaign_right_now() {
	$num_posts = wp_count_posts('awl_campaign');
	$num = number_format_i18n($num_posts->publish);
	$text = _n('Campaign', 'Campaigns', intval($num_posts->publish), 'activists-lobbies');
	if (current_user_can('edit_campaigns')) {
		$num = '<a href="/wp-admin/edit.php?post_type=awl_campaign">' . $num . '</a>';
		$text = '<a href="/wp-admin/edit.php?post_type=awl_campaign">' . $text . '</a>';
	}

	echo '<tr>';
	echo '<td class="first b b-tags">'.$num.'</td>';
	echo '<td class="t tags">' . $text . '</td>';
	echo '</tr>';
}

/**
 * initialise and register the actions for product post_type
 */
function awl_init_campaign() {
	awl_campaign_type();
	add_action('right_now_content_table_end', 'awl_campaign_right_now');
}
