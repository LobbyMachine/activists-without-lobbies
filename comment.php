<?php
/**
 * comments/signatures
 * @package activists-lobbies
 */

/**
 * Add meta data field to a comment.
 * @param int $comment_id Comment ID.
 * @param string $meta_key Metadata name.
 * @param mixed $meta_value Metadata value.
 * @param bool $unique Optional, default is false. Whether the same key should not be added.
 * @return bool False for failure. True for success.
add_comment_meta($comment_id, $meta_key, $meta_value, $unique = false);

/**
 * Remove metadata matching criteria from a comment.
 * @param int $comment_id comment ID
 * @param string $meta_key Metadata name.
 * @param mixed $meta_value Optional. Metadata value.
 * @return bool False for failure. True for success.
delete_comment_meta($comment_id, $meta_key, $meta_value = '');

/**
 * Retrieve comment meta field for a comment.
 * @param int $comment_id Comment ID.
 * @param string $key The meta key to retrieve.
 * @param bool $single Whether to return a single value.
 * @return mixed Will be an array if $single is false. Will be value of meta data field if $single
 *  is true.
get_comment_meta($comment_id, $key, $single = false);

wp_allow_comment($commentdata);
	$approved = apply_filters( 'pre_comment_approved', $approved, $commentdata );
*/

/**
 * open and close comments based on campaign status rather than WP's settings
 * @param bool true if open
 * @param int post id
 * @return bool true if open
 * @todo implement
 */
function awl_open_comments ($open, $post_id) {
	$post = get_post($post_id);
	return $open;
}

/**
 * callback to validate and insert custom comment metadata for campaigns
 *
 * @param int comment id
 * @param obj comment object
 */
function awl_insert_comment ($comment_ID, $comment) {
	$post = get_post($comment->comment_post_ID);
	$twitter = isset($_GET['twitter']) ? $_GET['twitter'] : false;
	update_comment_meta($comment_ID,'_twitter',$twitter);
}

/**
 * insert custom comment fields into form for campaigns
 *
 * @param array field display defaults
 * @return array field defaults (modified)
 */
function awl_comment_form ($defaults) {
	$email = $defaults['fields']['email'];
	$label = __('Twitter');
	$value = isset($_GET['twitter']) ? $_GET['twitter'] : false;
	$defaults['fields']['twitter'] = '<p class="comment-form-twitter">
	<label for="twitter">'.$label.'</label>
	<input id="twitter" name="twitter" type="text" value="'.$value.'" size="30" /></p>';
	return $defaults;
}

function awl_comment_init() {
	add_filter('comment_form_defaults', 'awl_comment_form');
	add_action('wp_insert_comment', 'awl_insert_comment', 10, 2);
	add_filter('comments_open', '', 10, 2);
}
