<?php
/**
 * awl root file
 * @package activists-lobbies
 */

/*
Plugin Name: Activists without Lobbies
Version: 0.2.0
Plugin URI: http://github.com/impleri/activists-without-lobbies/
Description: Lobby like the big organisations!
Author: Christopher Roussel and Alex Andrews
*/

// Keep this file short and sweet; leave the clutter for elsewhere!
define('AWL_VERSION', '0.2.0');
load_plugin_textdomain( 'activists-lobbies', false, basename(dirname(__FILE__)) . '/lang' );

// Options and auths need to be loaded first/always
require_once dirname(__FILE__) . '/options.php';
require_once dirname(__FILE__) . '/functions.php';

/**
 * Checks if install/upgrade needs to run by checking version in db
 * @todo implement events
 * @todo templates
 * @todo roles and capabilities
 * @todo widgets
 * @todo make backend pluggable (i.e. for different countries -- e.g. http://services.sunlightlabs.com/docs/Sunlight_Congress_API/, http://www.govtrack.us/developers/api.xpd)
 */
function awl_install() {
	$options = get_option('awl_options');

	// boring install
	if (false === $options) {
 		add_option('awl_options', awl_default_options());
 		return;
	}
}

/**
 * initialise!
 */
function awl_init() {
	// auths first!
	require_once dirname(__FILE__) . '/roles.php';
	awl_capabilities();

	// next the campaign post_type
	require_once dirname(__FILE__) . '/campaign.php'; // adds campaigns and petitions
	require_once dirname(__FILE__) . '/campaign-template.php';
	awl_init_campaign();

	// and the signature 'comment type'
	require_once dirname(__FILE__) . '/signature.php'; // adds signature/supporters
// 	require_once dirname(__FILE__) . '/signature-template.php';

	// campaign types must come after signature
	require_once dirname(__FILE__) . '/petition.php'; // petition campaign type
// 	require_once dirname(__FILE__) . '/twfy.php'; // twfy campaign type

	// followed by the event post_type
// 	require_once dirname(__FILE__) . '/event.php'; // adds events and locations
// 	require_once dirname(__FILE__) . '/event-template.php';
// 	awl_init_event();

	// finally the widgets and ajax
// 	include_once dirname(__FILE__) . '/widgets.php';
// 	require_once dirname(__FILE__) . '/ajax.php';

	// also add base css for styling
// 	wp_enqueue_style('awl-style', plugins_url('/css/awl.css', dirname(__FILE__) ));
}

// all of our hooks come last (i.e. here)
register_activation_hook(basename(dirname(__FILE__)) . '/' . basename(__FILE__), 'awl_install');
add_action('init', 'awl_init');

// Pure PHP files should not have a closing PHP tag!!
