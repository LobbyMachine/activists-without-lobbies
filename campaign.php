<?php
/**
 * campaign custom_type and related taxonomies
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
			'supports' => array('title', 'editor', 'author', 'revisions', 'thumbnail', 'excerpt', 'trackbacks', 'page-attributes'),
			'rewrite' => array('slug' => $slug, 'pages' => true, 'feeds' => true, 'with_front' => false),
			'register_meta_box_cb' => 'awl_campaign_boxes',
			'taxonomies' => array('post_tag', 'category'),
			'capability_type' => 'campaign',
			'has_archive' => $slug,
// 			'map_meta_cap' => true,
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
	add_meta_box('awl_campaign_meta', __('Additional Settings', 'conference-manager'), 'awl_mb_meta', 'awl_campaign', 'side', 'high');
}

/**
 * meta box for campaign metadata
 * use filter awl_campaign_types to insert additional campaign types
 */
function awl_mb_meta() {
	$types = array('petition' => 'Online Petition');
	$types = apply_filters('awl_campaign_types', $types);

	// drop-down for campaign type
}

/**
 * callback to process posted metadata
 *
 * @param int post id
 */
function awl_campaign_meta_postback ($post_id, $post) {
	if (!wp_verify_nonce($_POST["awl_campaign_meta_nonce"], basename(__FILE__)) || 'awl_campaign' != $post->post_type) {
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
	add_action('save_post', 'awl_campaign_meta_postback', 10, 2);
}
