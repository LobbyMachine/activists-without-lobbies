<?php
/**
 * TheyWorkForYou campaign type and wrapper
 * @package activists-lobbies
 * @subpackage twfy-type
 * @todo make into a separate plugin?
 */

/**
 * TheWorkForYou wrapper
 *
 * acts as a simple middle layer between AwL and TWFYAPI.
 * @static
 */
class awl_twfy {
	/**
	 * @var template for html error messages
	 */
	private static $error = '<div class="">%s</div>';

	/**
	 * TWFYAPI instance
	 *
	 * gets and holds a single TWFYAPI instance
	 * @return object TWFYAPI object
	 */
	public static function &get() {
		static $twfy;

		if (empty($twfy)) {
			if (!class_exists('TWFYAPI')) {
				require dirname(__FILE__) . '/lib/twfy.class.php';
			}
			$key = awl_get_option('mysociety_key');
			if (!empty($key)) {
				$twfy = new TWFYAPI($key);
			}
			else {
				$twfy = false;
			}
		}
		return $twfy;
	}

	/**
	 * Local MP Lookup
	 *
	 * searches TWFY for MPs for given postcode
	 * @param string Postcode to search
	 * @param string Which elected body? (MP, MSP, MLA) [default is MP]
	 * @return string parsed html from aml_amazon::parse
	 */
	public static function getMemberByPostcode ($postcode, $org='MP') {
		$twfy = self::get();
		if (!$twfy) {
			return __('Error loading TWFYAPI library', 'activists-lobbies');
		}

		$org = strtoupper($org);
		$org = ($org == 'MSP' || $org == 'MLA') ? $org : 'MP';

		$member = $twfy->query('get'.$org, $args = array('postcode' => $postcode, 'always_return' => '1', 'output' => 'php'));
		$member = unserialize($member);

		if (isset($member['error'])) {
			return sprintf(__('Error reported from TheyWorkForYou: %s', 'activists-lobbies'), $member['error']);
		}

		return self::parseMember($member);
	}

	/**
	 * elected member parser
	 *
	 * parse an result member array into an html listing
	 * @param array
	 * @return string formatted html
	 */
	public static function parseMember ($member) {
		// todo still (my MP)
		$sample_response = array(
			'member_id' => '40274',
			'house' => '1',
			'first_name' => 'William',
			'last_name' => 'Bain',
			'constituency' => 'Glasgow North East',
			'party' => 'Labour',
			'entered_house' => '2010-05-06',
			'left_house' => '9999-12-31',
			'entered_reason' => 'general_election',
			'left_reason' => 'still_in_office',
			'person_id' => '24697',
			'title' => '',
			'lastupdate' => '2010-05-07 12:04:53',
			'full_name' => 'William Bain',
			'url' => '/mp/william_bain/glasgow_north_east',
			'image' => '/images/mpsL/24697.jpeg',
			'image_height' => 118,
			'image_width' => 92,
		);

		$ret = '';
	}
}

/**
 * add to awl default options
 *
 * @param array $defaults
 * @return array $defaults (modified)
 */
function awl_twfy_defaults ($defaults) {
	$default['mysociety_key'] = '';
	return $defaults;
}

/**
 * mySociety key field
 */
function awl_mysociety_key_field() {
?>
<input type="text" size="50" id="awl_mysociety_key" name="awl_options[mysociety_key]" value="<?php echo htmlentities(awl_get_option('mysociety_key'), ENT_QUOTES, "UTF-8"); ?>" />
<p><?php echo sprintf(__('Required to use mySociety\'s TheyWorkForYou (for contacting and locating MPs).  It is free to sign up. Register <a href="%s">here</a>.', 'activists-lobbies'), 'http://www.theyworkforyou.com/api/'); ?></p>
<?php }

/**
 * callback to process posted metadata
 *
 * @param int post id
 * @param object WP_post object
 */
function awl_twfy_meta_postback ($post_id, $post) {
	$req = isset($_REQUEST['post_type']) ? $_REQUEST['post_type'] : '';
	if ( ('awl_campaign' != $req) || !current_user_can( 'edit_campaign', $post_id ) ) {
		return $post_id;
	}
	$image = isset($_POST['awl_image']) ? $_POST['awl_image'] : null;

	awl_update_meta('awl_image', $post_id, $image);
}

/**
 * validates additional posted options
 *
 * @param array $_POST data passed from WP
 * @return array validated options
 */
function awl_twfy_options_postback ($awl_post, $valid) {
	$valid['mysociety_key'] = isset($awl_post['mysociety_key']) ? sanitize_text_field($awl_post['mysociety_key']) : null;
	return $valid;
}

/**
 * check sanitised data for missing requirements
 *
 * @param array $valid data passed through filtering and validation
 */
function awl_twfy_validate ($valid) {
	// Throw an error if no AWS info
	if (empty($valid['mysociety_key'])) {
		add_settings_error('awl_options', 'activists-lobbies', sprintf(__('MySociety key is required for AwL to function properly: %s', 'activists-lobbies'), 'http://www.theyworkforyou.com/api/key'));
	}
}

/**
 * initialise twfy options
 */
function awl_twfy_options() {
	add_filter('awl_options_postback', 'awl_twfy_options_postback', 10, 2);
	add_action('awl_options_validate', 'awl_twfy_validate');
	add_settings_field('awl_mysociety_key', __('mySociety TheyWorkForYou key', 'activists-lobbies'), 'awl_mysociety_key_field', 'awl_options', 'awl_options_basic');
}

/**
 * initialise twfy campaign type
 */
function awl_twfy_init() {
	add_filter('awl_default_options', 'awl_twfy_defaults');
	add_filter('awl_campaign_types', 'awl_twfy_type');
	add_filter('awl_campaign_setup', 'awl_twfy_setup');
	add_action('awl_init_options', 'awl_twfy_options');
// 	add_action('save_post', 'awl_twfy_meta_postback', 10, 2);
}

add_action('init', 'awl_twfy_init');
