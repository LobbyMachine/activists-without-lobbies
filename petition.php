<?php
/**
 * petition campaign type
 * @package activists-lobbies
 * @subpackage petition-type
 */

/**
 * insert petition type to campaign element
 * array is simple format of id_string => i10n_string
 *
 * @param array $types
 * @return array $types (modified)
 */
function awl_petition_type ($types) {
	$types['petition'] = 'Online Petition';
	return $types;
}

/**
 * insert html into setup metabox
 * div wrapper should use the hide-if-js class and id specified in setup type (div will be revealed when type is chosen)
 *
 * @param string $html
 * @return string $html (modified)
 */
function awl_petition_setup ($html) {
	global $post;

	$goal = get_post_meta($post->ID, 'awl_petition_goal', true);
	$show = get_post_meta($post->ID, 'awl_petition_show_names', true);

	$html .= '<div class="hidden" id="petition" name="petition">' . "\n";
	$html .= '<div id="petition_goalwrap">' . "\n";
	$html .= '<label id="petition_goal-prompt-text" for="petition_goal">' . __('Target Signatures Count', 'activists-library') . '</label>' . "\n";
	$html .= '<input type="text" size="20" id="petition_goal" name="petition_goal" value="' . $goal . '" />' . "\n";
	$html .= '</div>' . "\n"; // petition_goal
	$html .= '<label for="show_names" class="selectit"><input name="show_names" type="checkbox" id="show_names" value="show" ' . checked($show, 'show') . ' /> ' . __('Show names of signatories.', 'activists-lobbies') . '</label>';
	$html .= '</div>' . "\n"; // petition

	return $html;
}

/**
 * insert html into setup metabox
 * div wrapper should use the hide-if-js class and id specified in setup type (div will be revealed when type is chosen)
 *
 * @param string $html
 * @return string $html (modified)
 */
function awl_petition_content ($text) {
	global $post;
	if ('awl_campaign' != $post->post_type) {
		return $text;
	}

	$type = get_post_meta($post->ID, 'awl_campaign_type', true);
	if ('petition' != $type) {
		return $text;
	}

	$goal = get_post_meta($post->ID, 'awl_petition_goal', true);
	$sigs = get_comment_count($post->ID);
	$html = '<div style="float:right;" id="petition" name="petition">' . "\n";
	$html .= awl_signature_progress($sigs['approved'], $goal) . "\n";
	$html .= '</div>' . "\n"; // petition

	return $html . $text;
}

/**
 * create a progress bar for signatures
 *
 * @param int signatures collected
 * @param int signatures goal
 * @param string css class base (-box and -bar will be added) [default is 'petition-progress']
 * @param int box width [default is 150]
 * @param int box height [default is 15]
 * @return string $html
 * @todo add markers (0, 1/4, 1/2, 3/4, 1)
 */
function awl_signature_progress ($current, $total, $class='petition-progress', $width=150, $height=15) {
	$pct = round(100*$current/$total);
	$pct = ($pct > 100) ? 100 : $pct;
	$pct = ($pct < 0) ? 0 : $pct;
	$ret = '<div class="' . $class . '-box" style="width:' . $width . 'px;height:' . $height . 'px;">';
	$ret .= '<div class="' . $class . '-bar" style="width:' . $pct . '%;height:100%;"></div>';
	$ret .= '</div>';
	$ret .= '<div class="' . $class . '-text">' . sprintf(__('%s signatures collected out of %s', 'activists-lobbies'), $current, $total) . '</div>';
	return $ret;
}

/**
 * callback to process posted metadata
 *
 * @param int post id
 * @param object WP_post object
 */
function awl_petition_meta_postback ($post_id, $post) {
	$req = isset($_REQUEST['post_type']) ? $_REQUEST['post_type'] : '';
	if ( ('awl_campaign' != $req) || !current_user_can( 'edit_campaign', $post_id ) ) {
		return $post_id;
	}

	$type = isset($_POST['awl_campaign_type']) ? $_POST['awl_campaign_type'] : '';
	if ('petition' != $type) {
		return $post_id;
	}

	$goal = isset($_POST['petition_goal']) ? intval($_POST['petition_goal']) : 0;
	$show = (isset($_POST['show_names']) && $_POST['show_names'] == 'show') ? 'show' : '';

	awl_update_meta('awl_petition_goal', $post_id, $goal);
	awl_update_meta('awl_petition_show_names', $post_id, $show);
}

/**
 * filter out signatory name when necessary
 *
 * @param string html to display
 * @return string html (modified)
 */
function awl_petition_show_name ($html) {
	$id = 0;
	$post = get_post($id);
	$types = awl_get_option('signature_types', array());
	$type = get_post_meta($post->ID, 'awl_campaign_type', true);

	// not our concern
	if ('awl_campaign' != $post->post_type || 'petition' != $type) {
		return $html;
	}

	$show_name = get_post_meta($post->ID, 'awl_petition_show_names', true);
	return ('show' == $show_name) ? $html : '';
}

/**
 * initialise petition campaign type
 */
function awl_init_petition() {
	add_filter('awl_campaign_types', 'awl_petition_type');
	add_filter('awl_campaign_setup', 'awl_petition_setup');
	add_filter('the_content', 'awl_petition_content');
	add_filter('get_comment_author_link', 'awl_petition_show_name');
	add_action('save_post', 'awl_petition_meta_postback', 10, 2);
	awl_init_signature('petition');
}

