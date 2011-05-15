<?php
/**
 * comments/signatures
 * @package activists-lobbies
 */

/**
 * load custom comment metadata from get_comment
 *
 * @param obj comment object
 * @return obj comment object (modified)
 */
function awl_signature_get ($comment) {
	$post = get_post($comment->comment_post_ID);
	$types = awl_get_option('signature_types', array());
	$type = get_post_meta($post->ID, 'awl_campaign_type', true);

	// not our concern
	if ('awl_campaign' != $post->post_type || !in_array($type, $types)) {
		return $comment;
	}

	$comment->surname = get_comment_meta($comment->comment_ID, 'awl_surname', true);
	$comment->given_name = get_comment_meta($comment->comment_ID, 'awl_given_name', true);
	$comment->postcode = get_comment_meta($comment->comment_ID, 'awl_postcode', true);
	$comment->author = (empty($comment->author)) ? $comment->given_name . ' ' . $comment->surname : $comment->author;
	$comment->comment_author = $comment->author;

	return $comment;
}

/**
 * callback to validate and insert custom comment metadata for campaigns
 *
 * @param int comment id
 * @param obj comment object
 */
function awl_signature_insert ($comment_id, $comment) {
	$post = get_post($comment->comment_post_ID);
	$types = awl_get_option('signature_types', array());
	$type = get_post_meta($post->ID, 'awl_campaign_type', true);

	// not our concern
	if ('awl_campaign' != $post->post_type || !in_array($type, $types)) {
		return;
	}

	$given_name = apply_filters('pre_comment_author_name', $_POST['given_name']);
	$given_name = apply_filters('pre_signature_author_given_name', $given_name);
	$surname = apply_filters('pre_comment_author_name', $_POST['surname']);
	$surname = apply_filters('pre_signature_author_surname', $surname);
	$postcode = apply_filters('pre_signature_author_postcode', $_POST['postcode']);
	awl_update_meta($comment_id, 'awl_given_name', $given_name, 'comment');
	awl_update_meta($comment_id, 'awl_surname', $surname, 'comment');
	awl_update_meta($comment_id, 'awl_postcode', $postcode, 'comment');

	// make comment author's name from given name and surname
	$comment->comment_author = $given_name . ' ' . $surname;
	wp_update_comment(get_object_vars($comment));
}

/**
 * ensure signatures are not unapproved simply because it has an empty content
 * @param int post id
 */
function awl_signature_post ($post_id) {
	$post = get_post($post_id);
	$types = awl_get_option('signature_types', array());
	$type = get_post_meta($post->ID, 'awl_campaign_type', true);

	// only do campaigns with signatures
	if ('awl_campaign' == $post->post_type && in_array($type, $types)) {
		// check for required fields...
		if ( empty($_POST['given_name']) || empty($_POST['surname']) || empty($_POST['postcode']) ) {
			wp_die( __('Error: please fill the required fields (name, postcode).', 'activists-lobbies') );
		}

		// make sure comment text is non-empty
		$text  = (isset($_POST['comment'])) ? trim($_POST['comment']) : null;
		if (empty($text)) {
			$_POST['comment'] = $_POST['given_name'] . $_POST['surname'] . $_POST['postcode'];
		}
	}
}

/**
 * ensure signatures are not unapproved simply because it has an empty content
 * @param bool true if approved
 * @param array comment data
 * @return bool true if approved
 */
function awl_signature_approve ($approved, $commentdata) {
	extract($commentdata, EXTR_SKIP);
	$post = get_post($comment_post_ID);
	$types = awl_get_option('signature_types', array());
	$type = get_post_meta($post->ID, 'awl_campaign_type', true);

	// only do campaign types with signatures
	if ('awl_campaign' == $post->post_type && in_array($type, $types)) {
		// allow if empty signature information (set from  awl_signature_post) is there
		if ($comment_content == $_POST['given_name'] . $_POST['surname'] . $_POST['postcode']) {
			$approved = true;
		}
	}
	return $approved;
}

/**
 * ensure signature comment is empty if previously set to workaround test for empty text
 * @param string comment text
 * @return string comment text (perhaps modified)
 */
function awl_signature_content ($comment) {
	$id = 0;
	$post = get_post($id);
	$types = awl_get_option('signature_types', array());
	$type = get_post_meta($post->ID, 'awl_campaign_type', true);

	// only do campaign types with signatures
	if ('awl_campaign' == $post->post_type && in_array($type, $types)) {
		// allow if empty signature information (set from  awl_signature_post) is there
		if ($comment == $_POST['given_name'] . $_POST['surname'] . $_POST['postcode']) {
			$comment = '';
		}
	}
	return $comment;
}

/**
 * hide comments when needed
 *
 * @param array comments
 * @return array comments (modified)
 */
function awl_signature_show ($comments) {
	$id = 0;
	$post = get_post($id);
	$types = awl_get_option('signature_types', array());
	$type = get_post_meta($post->ID, 'awl_campaign_type', true);

	// not our concern
	if ('awl_campaign' != $post->post_type || !in_array($type, $types)) {
		return $comments;
	}

	$show = get_post_meta($post->ID, 'awl_show_comments', true);
	$comments = ($show) ? $comments : array();
	return $comments;
}

/**
 * insert custom comment fields into form for campaigns
 *
 * @param array field display defaults
 * @return array field defaults (modified)
 */
function awl_signature_form ($defaults) {
	global $id;
	$post = get_post($id);
	$types = awl_get_option('signature_types', array());
	$type = get_post_meta($id, 'awl_campaign_type', true);

	// not our concern
	if ('awl_campaign' != $post->post_type || !in_array($type, $types)) {
		return $defaults;
	}

	// get commenter cookie (again) to get a name
	$commenter = wp_get_current_commenter();
	$pos = strpos($commenter['comment_author'], ' ');
	$given_name = ($pos > 0)?  substr($commenter['comment_author'], 0, $pos) : $commenter['comment_author'];
	$surname = ($pos > 0) ? substr($commenter['comment_author'], $pos) : '';

	$fields =  array(
		'author' => '<input id="author" name="author" type="hidden" value="' . esc_attr($commenter['comment_author']) . '" size="30" /></p>',
		'given_name' => '<p class="comment-form-author">' . '<label for="given_name">' . __('Given Name', 'activists-lobbies') . '</label> <span class="required">*</span><input id="given_name" name="given_name" type="text" value="' . esc_attr($given_name) . '" size="30" aria-required="true" /></p>',
		'surname' => '<p class="comment-form-author">' . '<label for="surname">' . __('Surname', 'activists-lobbies') . '</label> <span class="required">*</span><input id="surname" name="surname" type="text" value="' . esc_attr($surname) . '" size="30" aria-required="true" /></p>',
		'postcode' => '<p class="comment-form-postcode">' . '<label for="postcode">' . __('Postcode', 'activists-lobbies') . '</label> <span class="required">*</span><input id="postcode" name="postcode" type="text" value="" size="30" aria-required="true" /></p>',
	);
	$defaults['fields'] = $fields + $defaults['fields'];

	if (is_user_logged_in()) {
		$user = wp_get_current_user();
		$given_name = ($user->first_name) ? $user->first_name : $user->display_name;
		$surname = ($user->last_name) ? $user->last_name : '';

		$logged_in =  '<input id="author" name="author" type="hidden" value="' . esc_attr($user->display_name) . '" size="30" /></p>' .
		'<p class="comment-form-author">' . '<label for="given_name">' . __('Given Name', 'activists-lobbies') . '</label> <span class="required">*</span><input id="given_name" name="given_name" type="text" value="' . esc_attr($given_name) . '" size="30" aria-required="true" /></p>' .
		'<p class="comment-form-author">' . '<label for="surname">' . __('Surname', 'activists-lobbies') . '</label> <span class="required">*</span><input id="surname" name="surname" type="text" value="' . esc_attr($surname) . '" size="30" aria-required="true" /></p>' .
		'<p class="comment-form-postcode">' . '<label for="postcode">' . __('Postcode', 'activists-lobbies') . '</label> <span class="required">*</span><input id="postcode" name="postcode" type="text" value="" size="30" aria-required="true" /></p>';
		$defaults['logged_in_as'] .= $logged_in;
	}

	return $defaults;
}

/**
 * initialise signature for a campaign type
 *
 * @param string campaign type
 */
function awl_init_signature($type) {
	static $active = false;

	$types = awl_get_option('signature_types', array());
	if (!in_array($type, $types)) {
		$types[] = $type;
		$options = get_option('awl_options');
		$options['signature_types'] = $types;
		update_option('awl_options', $options);
	}

	if (!$active) {
		add_filter('get_comment', 'awl_signature_get');
		add_action('pre_comment_on_post', 'awl_signature_post');
		add_action('wp_insert_comment', 'awl_signature_insert', 10, 2);
		add_filter('pre_comment_content', 'awl_signature_content');
		add_filter('pre_comment_approved', 'awl_signature_approve', 10, 2);
		add_filter('the_comments', 'awl_signature_show');
		add_filter('comment_form_defaults', 'awl_signature_form');
		$active = true;
	}
}
