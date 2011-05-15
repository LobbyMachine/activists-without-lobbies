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
	wp_enqueue_script('awl-campaign', plugins_url('/activists-without-lobbies/js/campaign.js'));
}

/**
 * meta box for campaign metadata
 * use filter awl_campaign_types to insert additional campaign types
 */
function awl_mb_meta() {
	$id = 0;
	$post = get_post($id);

	$types = apply_filters('awl_campaign_types', array());
	$html = apply_filters('awl_campaign_setup', '');
	$campaign = get_post_meta($post->ID, 'awl_campaign_type', true);
	$show = get_post_meta($post->ID, 'awl_show_comments', true);

	// drop-down for campaign types
	$select = '<select id="awl_campaign_type" name="awl_campaign_type">';
	$select .= '<option value="none">' . __('Select Campaign', 'activists-lobbies') . '</option>';
	foreach ($types as $id => $name) {
		$select .= '<option' . selected($campaign, $id, false) . ' value="' . $id . '">' . __($name, 'activists-lobbies') . '</option>';
	}
	$select .= '</select>';

	// common settings go on top so that they're not confused with type-specific settings
	$top = '<div id="campaign-common" name="campaign-common">' . "\n";
	$top .= '<label for="awl_show_comments" class="selectit"><input name="awl_show_comments" type="checkbox" id="awl_show_comments" value="show" ' . checked($show, true, false) . ' /> ' . __('Show comments.', 'activists-lobbies') . '</label>';
	$top .= '</div>' . "\n";

	echo $top . $select . '<div id="awl_campaign_options">' . $html . '</div>';
}

/**
 * callback to process posted metadata
 *
 * @param int post id
 * @param obj WP_post object
 */
function awl_campaign_meta_postback ($post_id, $post) {
	$req = isset($_REQUEST['post_type']) ? $_REQUEST['post_type'] : '';
	if ( ('awl_campaign' == $req) && current_user_can('edit_campaign', $post_id) ) {
		$type = isset($_POST['awl_campaign_type']) ? $_POST['awl_campaign_type'] : null;
		awl_update_meta('awl_campaign_type', $post_id, $type);

		$show = (isset($_POST['awl_show_comments']) && $_POST['awl_show_comments'] == 'show');
		awl_update_meta('awl_show_comments', $post_id, $show);
	}
}

/**
 * display counts in the diashboard
 * @todo push html to template functions
 * @todo implement highlighting current campaign numbers
 */
function awl_campaign_dashboard() {
	$num_posts = wp_count_posts('awl_campaign');
	$num = number_format_i18n($num_posts->publish);
	$text = _n('Campaign', 'Campaigns', intval($num_posts->publish), 'activists-lobbies');
	if (current_user_can('edit_campaigns')) {
		$num = '<a href="/wp-admin/edit.php?post_type=awl_campaign">' . $num . '</a>';
		$text = '<a href="/wp-admin/edit.php?post_type=awl_campaign">' . $text . '</a>';
	}
	$top = '<div id="campaign_summary">';
	$top .= '<div class="first b b-tags">'.$num.'</div>';
	$top .= '<div class="t tags">' . $text . '</div>';
	$top .= '</div>';
}

function awl_campaign_dashboard_init() {
	if (current_user_can('edit_campaigns')) {
		wp_add_dashboard_widget('campaigns_right_now', __( 'Current Campaigns' ), 'awl_campaign_dashboard');
	}
}

/**
 * initialise and register the actions for product post_type
 */
function awl_init_campaign() {
	awl_campaign_type();
	add_action('wp_dashboard_setup', 'awl_campaign_dashboard_init');
	add_action('save_post', 'awl_campaign_meta_postback', 10, 2);
}
