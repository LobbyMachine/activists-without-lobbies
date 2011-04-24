<?php
/**
 * Basic template for displaying campaign archives
 * Adapted from standard archive.php in TwentyTen
 *
 * @package activists-lobbies
 * @subpackage templates
 */

get_header(); ?>

		<div id="container">
			<div id="content" role="main">
			<h1 class="page-title"><?php _e( 'Our Campaigns', 'activists-lobbies' ); ?></h1>

<?php
	/* Run the loop for the archives page to output the posts.
	 * If you want to overload this in a child theme then include a file
	 * called loop-campaign.php and that will be used instead.
	 */
	 get_template_part( 'loop', 'campaign' );
?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
