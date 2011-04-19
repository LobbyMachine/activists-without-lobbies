<?php
/**
 * template functions for campaigns
 * @package activists-lobbies
 */

/**
 * wrapper to template hack for archive-awl_campaign
 *
 * @param string found template
 * @return string path to template
 */
function awl_campaign_archive_template ($template) {
	return awl_insert_template ($template, 'awl_campaign', 'archive');
}

/**
 * wrapper to template hack for single-awl_product
 *
 * @param string found template
 * @return string path to template
 */
function awl_campaign_single_template ($template) {
	return awl_insert_template ($template, 'awl_campaign', 'single');
}
