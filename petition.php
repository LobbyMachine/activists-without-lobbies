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

	$html .= '<div class="hide-if-js" id="petition" name="petition">' . "\n";
	$html .= '<div id="petition_goalwrap">' . "\n";
	$html .= '<label id="petition_goal-prompt-text" for="petition_goal">' . __('Target Signatures Count', 'activists_library') . '</label>' . "\n";
	$html .= '<input type="text" size="20" id="petition_goal" name="petition_goal" value="' . $goal . '" autocomplete="off" />' . "\n";
	$html .= '</div>' . "\n"; // petition_goal
	$html .= '</div>' . "\n"; // petition

	return $html;
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
	$goal = isset($_POST['petition_goal']) ? intval($_POST['petition_goal']) : null;

	awl_update_meta('awl_petition_goal', $post_id, $goal);
}

/**
 * initialise petition campaign type
 */
function awl_petition_init() {
	add_filter('awl_campaign_types', 'awl_petition_type');
	add_filter('awl_campaign_setup', 'awl_petition_setup');
	add_action('save_post', 'awl_campaign_meta_postback', 10, 2);
	awl_init_signature('petition');
}

add_action('init', 'awl_petition_init');
