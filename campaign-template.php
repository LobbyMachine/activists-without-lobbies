<?php
/**
 * template functions for campaigns
 * @package activists-lobbies
 * @author Christopher Roussel <christopher@impleri.net>
 */

/**
 * wrapper to template hack for archive-awl_campaign
 *
 * @param string found template
 * @return string path to template
 */
function awl_product_archive_template ($template) {
	return awl_insert_template ($template, 'awl_campaign', 'archive');
}

/**
 * wrapper to template hack for single-awl_product
 *
 * @param string found template
 * @return string path to template
 */
function awl_product_single_template ($template) {
	return awl_insert_template ($template, 'awl_campaign', 'single');
}

/**
 * wrapper to template hack for taxonomy-awl_tag
 *
 * @param string found template
 * @return string path to template
 */
function awl_tag_taxonomy_template ($template) {
	return awl_insert_template ($template, 'awl_tag', 'taxonomy');
}
