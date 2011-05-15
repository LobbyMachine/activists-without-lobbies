<?php
/**
 * template functions for signatures
 * @package activists-lobbies
 */

/**
 * Retrieve the surname of the signatory.
 *
 * @param int $comment_ID The ID of the comment for which to get the signatory's surname
 * @return string
 */
function get_signature_surname ($comment_ID=0) {
	$comment = get_comment($comment_ID);
	$ret = apply_filters('get_comment_author_name', $comment->surname);
	return apply_filters('get_comment_author_surname', $ret);
}

/**
 * Displays the surname of the current comment author
 *
 * @param int $comment_ID The ID of the comment for which to print the surname
 */
function signature_surname ($comment_ID=0) {
	$name = apply_filters('comment_author', get_signature_surname($comment_ID));
	$name = apply_filters('author_surname', $name);
	echo $name;
}

/**
 * Retrieve the given name of the signatory.
 *
 * @param int $comment_ID The ID of the comment for which to get the signatory's given name
 * @return string
 */
function get_signature_given_name ($comment_ID=0) {
	$comment = get_comment($comment_ID);
	$ret = apply_filters('get_comment_author_name', $comment->given_name);
	return apply_filters('get_comment_author_given_name', $ret);
}

/**
 * Displays the given name of the current comment author
 *
 * @param int $comment_ID The ID of the comment for which to print the given name
 */
function signature_given_name ($comment_ID=0) {
	$name = apply_filters('comment_author', get_signature_given_name($comment_ID));
	$name = apply_filters('author_given_name', $name);
	echo $name;
}

/**
 * Retrieve the postcode of the signatory.
 *
 * @param int $comment_ID The ID of the comment for which to get the signatory's postcode
 * @return string
 */
function get_signature_postcode ($comment_ID=0) {
	$comment = get_comment($comment_ID);
	return apply_filters('get_comment_author_postcode', $comment->postcode);
}

/**
 * Displays the surname of the current comment author
 *
 * @param int $comment_ID The ID of the comment for which to print the author
 */
function signature_postcode ($comment_ID=0) {
	$postcode = apply_filters('comment_author', get_signature_postcode($comment_ID));
	$postcode = apply_filters('author_surname', $postcode);
	echo $postcode;
}
