<?php
/**
 * campaign custom_type and related taxonomies
 * @package activists-lobbies
 * @author Christopher Roussel <christopher@impleri.net>
 */

/**
 * custom product post_type
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
// 			'register_meta_box_cb' => 'awl_campaign_boxes',
			'taxonomies' => array('category'),
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
 * generic taxonomy (all of this just to rename 'post tags' to simply 'tags'!)
 */
function awl_tag_tax() {
	$slug = aml_get_option('slug_campaign');

	$labels = array(
		'name' => _x('Tags', 'taxonomy general name', 'amazon-library'),
		'singular_name' => _x('Tag', 'taxonomy singular name', 'amazon-library'),
	);

	$capabilities = array(
		'manage_terms' => 'manage_tags',
		'edit_terms' => 'edit_tags',
		'delete_terms' => 'edit_tags',
		'assign_terms' => 'edit_campaigns',
	);

	$args = array(
		'rewrite' => array('slug' => "$slug/tag", 'pages' => true, 'feeds' => false, 'with_front' => false),
		'capabilities' => $capabilities,
		'query_var' => 'awl_tag',
	 	'hierarchical' => false,
		'labels' => $labels,
		'public' => true,
	);
	register_taxonomy( 'awl_tag', 'awl_campaign', $args);
	add_filter('taxonomy_template', 'awl_tag_taxonomy_template');
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
	awl_tag_tax();
	add_action('right_now_content_table_end', 'awl_campaign_right_now');
}
