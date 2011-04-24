jQuery(document).ready(function(jQuery) {
	var type = jQuery("#awl_campaign_type").val();
	jQuery('#' + type).removeClass('hidden');

	jQuery("#awl_campaign_type").live('change', function() {
		var type = jQuery(this).val();
		jQuery('#awl_campaign_options').children().addClass('hidden');
		jQuery('#' + type).removeClass('hidden');
	});
});
