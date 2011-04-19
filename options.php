<?php
/**
 * admin menus and some plugin options
 * @package activists-lobbies
 */

/**
 * default options
 */
function awl_default_options() {
	$defaults = array(
		'signature_types' => array(),
		'slug_campaign' => 'campaign',
		'slug_event' => 'event',
		'version' => AWL_VERSION,
	);
	$defaults = apply_filters('awl_default_options', $defaults);
	return $defaults;
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

	$valid['slug_campaign'] = isset($awl_post['slug_campaign']) ? sanitize_text_field($awl_post['slug_campaign']) : null;
	$valid['slug_event'] = isset($awl_post['slug_event']) ? sanitize_text_field($awl_post['slug_event']) : null;
	$valid = apply_filters('awl_options_postback', $awl_post, $valid);
	$valid = array_merge($defaults, $options, $valid);
	do_action('awl_options_validate', $valid);
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
function awl_init_options() {
	register_setting('activists_lobbies', 'awl_options', 'awl_options_validate');
	add_options_page(__('Activists without Lobbies', 'activists-lobbies'), __('Activists without Lobbies', 'activists-lobbies'), 'manage_options', 'awl_options', 'awl_options_page');
	add_settings_section('awl_options_basic', __('Basic Settings', 'activists-lobbies'), 'awl_options_basic', 'awl_options');

	add_settings_field('awl_slug_campaign', __('Campaign permalink base', 'activists-lobbies'), 'awl_slug_campaign_field', 'awl_options', 'awl_options_basic');
	add_settings_field('awl_slug_event', __('Event permalink base', 'activists-lobbies'), 'awl_slug_event_field', 'awl_options', 'awl_options_basic');
	do_action('awl_options_init');
}

add_action('admin_menu', 'awl_init_options');
