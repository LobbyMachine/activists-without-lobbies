<?php
/**
 * comments/signatures
 * @package activists-lobbies
 */

/**
 * ensure signatures are not unapproved simply because it has an empty content
 * @param bool true if approved
 * @param array comment data
 * @return bool true if approved
 * @todo implement
 */
function awl_approve_comment ($approved, $comment) {
	$post = get_post($comment->comment_post_ID);

	// not our concern
	if ('awl_campaign' != $post->post_type) {
		return $approved;
	}
	return $approved;
}

/**
 * open and close comments based on campaign status rather than WP's settings
 * @param bool true if open
 * @param int post id
 * @return bool true if open
 * @todo implement
 */
function awl_open_comments ($open, $post_id) {
	$post = get_post($post_id);

	// not our concern
	if ('awl_campaign' != $post->post_type) {
		return $open;
	}

	return $open;
}

/**
 * load custom comment metadata from get_comment
 *
 * @param obj comment object
 * @return obj comment object (modified)
 */
function awl_load_comment ($comment) {
	$post = get_post($comment->comment_post_ID);

	// not our concern
	if ('awl_campaign' != $post->post_type) {
		return $comment;
	}

	$comment->surname = get_comment_meta($comment->comment_id, '_surname', true);
	$comment->given_name = get_comment_meta($comment->comment_id, '_given_name', true);
	$comment->postcode = get_comment_meta($comment->comment_id, '_postcode', true);
	$comment->author = (empty($comment->author)) ? $comment->given_name . ' ' . $comment->surname : $comment->author;

	return $comment;
}

/**
 * callback to validate and insert custom comment metadata for campaigns
 *
 * @param int comment id
 * @param obj comment object
 */
function awl_insert_comment ($comment_id, $comment) {
	$post = get_post($comment->comment_post_ID);

	// not our concern
	if ('awl_campaign' != $post->post_type) {
		return;
	}

	$given_name = apply_filters('pre_comment_author_name', $_GET['given_name']);
	$surname = apply_filters('pre_comment_author_name', $_GET['surname']);
	$postcode = apply_filters('pre_comment_author_postcode', $_GET['postcode']);
	awl_update_meta($comment_id, '_given_name', $given_name, 'comment');
	awl_update_meta($comment_id, '_surname', $surname, 'comment');
	awl_update_meta($comment_id, '_postcode', $postcode, 'comment');

	// make comment author's name from given name and surname
	$comment->comment_author = $given_name . ' ' . $surname;
	wp_update_comment(get_object_vars($comment));
}

/**
 * format postcode -- no validation!
 *
 * @param array field display defaults
 * @return array field defaults (modified)
 * @todo implement
 */
function awl_filter_postcode ($postcode) {
	return $postcode;
}

/**
 * insert custom comment fields into form for campaigns
 *
 * @param array field display defaults
 * @return array field defaults (modified)
 */
function awl_comment_form ($defaults) {
	// get commenter cookie (again) to get a name
	$commenter = wp_get_current_commenter();
	$pos = strpos(' ', $commenter['comment_author']);
	$given_name = ($pos > 0)?  substr($commenter['comment_author'], 0, $pos) : '';
	$surname = ($pos > 0) ? substr($commenter['comment_author'], $pos) : '';

	$fields =  array(
		'author' => '<input id="author" name="author" type="hidden" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" /></p>',
		'surname' => '<p class="comment-form-author">' . '<label for="surname">' . __('Surname', 'activists-lobbies') . '</label> <span class="required">*</span><input id="surname" name="surname" type="text" value="' . esc_attr($surname) . '" size="30" aria-required="true" /></p>',
		'given_name' => '<p class="comment-form-author">' . '<label for="given_name">' . __('Given Name', 'activists-lobbies') . '</label> <span class="required">*</span><input id="given_name" name="given_name" type="text" value="' . esc_attr($given_name) . '" size="30" aria-required="true" /></p>',
		'postcode' => '<p class="comment-form-postcode">' . '<label for="postcode">' . __('Postcode', 'activists-lobbies') . '</label> <span class="required">*</span><input id="postcode" name="postcode" type="text" value="" size="30" aria-required="true" /></p>',
	);

	$defaults['fields'] = array_merge($defaults['fields'], $fields);
	return $defaults;
}

function awl_init_comment() {
	add_filter('get_comment', 'awl_load_comment');
	add_filter('comments_open', 'awl_open_comment', 10, 2);
	add_filter('pre_comment_approved', 'awl_approve_comment', 10, 2);
	add_action('wp_insert_comment', 'awl_insert_comment', 10, 2);
	add_filter('pre_comment_author_postcode', 'awl_filter_postcode');
	add_filter('comment_form_defaults', 'awl_comment_form');
}
