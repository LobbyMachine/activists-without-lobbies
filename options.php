<?php
/**
 * admin menus and some plugin options
 * @package activists-lobbies
 */

/**
 * default options
 */
function awl_default_options() {
	return array(
		'mysociety_key' => '',
		'slug_campaign' => 'campaign',
		'slug_event' => 'event',
		'version' => AWL_VERSION,
	);
}

/**
 * options page display
 */
function awl_options_page() {
?>
	<div class="wrap">
	<form method="post" action="options.php">
		<h2>Activists without Lobbies</h2>
		 <?php settings_fields('activists_lobbies'); ?>
		<?php do_settings_sections('awl_options'); ?>

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
	</div>
<?php
}

/**
 * validates posted options
 *
 * @param array $_POST data passed from WP
 * @return array validated options
 */
function awl_options_validate ($awl_post) {
	$options = get_option('awl_options');
	$defaults = awl_default_options();
	$valid = array();
	//TODO: more validation!

	$valid['mysociety_key'] = ($awl_post['awl_mysociety_key']) ? sanitize_text_field($awl_post['awl_mysociety_key']) : null;
	$valid['slug_campaign'] = ($awl_post['awl_slug_campaign']) ? sanitize_text_field($awl_post['awl_slug_campaign']) : null;
	$valid['slug_event'] = ($awl_post['awl_slug_event']) ? sanitize_text_field($awl_post['awl_slug_event']) : null;

	// merge (defaults, current, and new values) into one array
	$valid = array_merge($defaults, $options, $valid);
	// Throw an error if no AWS info
	if (empty($valid['mysociety_key'])) {
		add_settings_error('awl_options', 'activists-lobbies', __('MySociety key is required for AwL to function properly!', 'activists-lobbies'));
	}
	return $valid;
}

/**
 * basic options header display
 */
function awl_options_basic() {
?>
<p><?php _e('Basic settings for Activists without Lobbies.', 'activists-lobbies'); ?></p>
<?php }

/**
 * mySociety key field
 */
function awl_mysociety_key_field() {
?>
<input type="text" size="50" id="awl_mysociety_key" name="awl_options[mysociety_key]" value="<?php echo htmlentities(awl_get_option('mysociety_key'), ENT_QUOTES, "UTF-8"); ?>" />
<p><?php echo sprintf(__('Required to use mySociety\'s TheyWorkForYou (for contacting and locating MPs).  It is free to sign up. Register <a href="%s">here</a>.', 'activists-lobbies'), 'http://www.theyworkforyou.com/api/'); ?></p>
<?php }

/**
 * template for slug field display
 *
 * @param string key
 * @param string description
 */
function awl_slug_field ($option, $text) {
?>
<input type="text" size="50" id="awl_<?php echo $option; ?>" name="awl_options[<?php echo $option; ?>]" value="<?php echo awl_get_option($option); ?>" />
<p><?php _e($text, 'activists-lobbies'); ?></p>
<p><?php _e('NB: Only Alpha-numerics and dashes are allowed.', 'activists-lobbies'); ?></p>
<?php }

/**
 * campaign slug field
 */
function awl_slug_campaign_field() {
	awl_slug_field('slug_campaign', 'Tag prepended for campaign pages. Default is campaign.');
}

/**
 * event slug field display
 */
function awl_slug_event_field() {
	awl_slug_field('slug_event', 'Tag prepended for event pages. Default is event.');
}

/**
 * initialises options by inserting missing options and registering with WP settings api
 * @todo check once more
 */
function awl_options_init() {
	$default_options = awl_default_options();
	$options = get_option('awl_options', awl_default_options());
	$options = (false === $options) ? array() : $options;
	$options = array_merge($default_options, $options);
	update_option('awl_options', $options);

	register_setting('activists_lobbies', 'awl_options', 'awl_options_validate');

	add_options_page(__('Activists without Lobbies', 'activists-lobbies'), __('Activists without Lobbies', 'activists-lobbies'), 'manage_options', 'awl_options', 'awl_options_page');

	add_settings_section('awl_options_basic', __('Basic Settings', 'activists-lobbies'), 'awl_options_basic', 'awl_options');

	// Amazon field definitions
	add_settings_field('awl_mysociety_key', __('mySociety TheyWorkForYou key', 'activists-lobbies'), 'awl_mysociety_key_field', 'awl_options', 'awl_options_basic');
	add_settings_field('awl_slug_campaign', __('Campaign permalink base', 'activists-lobbies'), 'awl_slug_campaign_field', 'awl_options', 'awl_options_basic');
	add_settings_field('awl_slug_event', __('Event permalink base', 'activists-lobbies'), 'awl_slug_event_field', 'awl_options', 'awl_options_basic');
}
