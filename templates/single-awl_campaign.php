<?php
/**
 * single campaign
 *
 * @package activists-lobbies
 * @subpackage templates
 */

get_header(); ?>

		<div id="container">
			<div id="content" role="main">

			<?php
			/* Run the loop to output the post */
			get_template_part( 'loop', 'campaign' );
			?>

			</div>
		</div>

<?php
get_sidebar();
get_footer();
?>
