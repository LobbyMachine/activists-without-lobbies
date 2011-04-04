<?php
/**
 * wrapper for TheyWorkForYou library
 * @package activists-lobbies
 * @author Christopher Roussel <christopher@impleri.net>
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
		$org = ($org == 'MSP' || $org == 'MLA') ? $org => 'MP';

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
