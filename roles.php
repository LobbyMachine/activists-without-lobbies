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
}
