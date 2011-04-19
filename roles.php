<?php
/**
 * roles and capabilities
 * @package activists-lobbies
 */

/**
 * add auths to regular WP roles
 *
 * @todo make own roles and leave WP's alone
 */
function awl_capabilities() {
	$admin = get_role('administrator');
	$admin->add_cap('read_campaigns');
	$admin->add_cap('edit_campaigns');
	$admin->add_cap('edit_published_campaigns');
	$admin->add_cap('publish_campaigns');
	$admin->add_cap('delete_campaigns');
	$admin->add_cap('delete_published_campaigns');

	$editor = get_role('editor');
	$editor->add_cap('read_campaigns');
	$editor->add_cap('delete_campaigns');
	$editor->add_cap('publish_campaigns');
	$editor->add_cap('edit_campaigns');
	$editor->add_cap('edit_published_campaigns');

	$author = get_role('author');
	$author->add_cap('read_campaigns');
	$author->add_cap('publish_campaigns');
	$author->add_cap('edit_campaigns');

	$contrib = get_role('contributor');
	$contrib->add_cap('read_campaigns');
	$contrib->add_cap('edit_campaigns');

	$sub = get_role('subscriber');
	$sub->add_cap('read_campaigns');
	add_filter('map_meta_cap', 'awl_meta_cap', 10, 4);
}

/**
 * Map meta capabilities to primitive capabilities
 *
 * @param array capabilities to check
 * @param string capability
 * @param int user id
 * @param array $args all arguments
 * @return array capabilities to check (modified)
 * @todo make this work
 */
function awl_meta_cap ($caps, $cap, $user_id, $args) {
	// only check capabilities we deal with
	$arr = array('edit_campaign', 'delete_campaign', 'read_campaign');
	if (!in_array($cap, $arr)) {
		return $caps;
	}

	$post = get_post($args[0]);
	$post_type = ($post) ? get_post_type_object($post->post_type) : $_REQUEST['post_type'];

	switch ($cap) {
		// products
		case 'edit_campaign':
			$caps[] = ('published' == $post->post_status) ? $post_type->cap->edit_published_posts : $post_type->cap->edit_posts;
			break;
		case 'delete_campaign':
			$caps[] = $post_type->cap->delete_posts;
			break;
		case 'read_campaign':
			$caps[] = 'read';
			break;
	}
	return $caps;
}
