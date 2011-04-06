<?php
/**
 * common functions
 * @package activists-lobbies
 */

/**
 * shortcut for handling meta updates
 *
 * @param string name of meta field
 * @param int post id for meta
 * @param mixed new value (default is null)
 * @param string meta type (post or comment)
 * @return bool true on success
 */
function awl_update_meta ($field, $post, $new=null, $type='post') {
	$old = get_metadata($type, $post, $field, true);
	if(empty($new)) {
		$ret = delete_metadata($type, $post, $field, $old);
	}
	elseif (empty($old)) {
		$ret = add_metadata($type, $post, $field, $new);
	}
	elseif ($new != $old) {
		$ret = update_metadata($type, $post, $field, $new, $old);
	}
	else {
		$ret = false;
	}

	return $ret;
}

/**
 * shortcut for getting option from the awl_options array
 *
 * @param string name of option
 * @param mixed default value override (if null, will give from awl_default_options)
 * @return mixed option value
 */
function awl_get_option ($key='', $def=null) {
	static $options;
	static $defaults;

	if (!is_array($defaults)) {
		$defaults = awl_default_options();
	}

	if (!is_array($options)) {
		$options = get_option('awl_options', $defaults);
	}

	$def = (is_null($def) && isset($defaults[$key])) ? $defaults[$key] : $def;

	return (isset($options[$key])) ? $options[$key] : $def;
}

/**
 * hack to use our templates
 *
 * @param string found template (passed from the filter)
 * @param string type of taxonomy to check
 * @param string type of page (archive, single, or taxonomy)
 * @return string path to template
 */
function awl_insert_template ($template, $check, $page='archive') {
	if ($page == 'taxonomy') {
		$term = get_queried_object();
		$type = $term->taxonomy;
	}
	else {
		$type = get_query_var('post_type');
	}

	// not ours to worry about!
	if ($check != $type) {
		return $template;
	}

	$file = $page.'-'.$check.'.php';

	// template not found in theme folder, so insert our default
	if ($file != basename($template)) {
		$path = dirname(__FILE__) . '/templates/' . $file;
		if ( file_exists($path)) {
			return $path;
		}
	}

	return $template;
}
