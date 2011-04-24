<?php
/**
 * The loop that displays posts.
 *
 * @package activists-lobbies
 * @subpackage templates
 */
?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<div id="nav-above" class="navigation">
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Next Page', 'activists-lobbies' ) ); ?></div>
		<div class="nav-next"><?php previous_posts_link( __( 'Previous Page <span class="meta-nav">&rarr;</span>', 'activists-lobbies' ) ); ?></div>
	</div><!-- #nav-above -->
<?php endif; ?>

<?php /* If there are no posts to display, such as an empty archive page */ ?>
<?php if ( ! have_posts() ) : ?>
	<div id="post-0" class="post error404 not-found">
		<h1 class="entry-title"><?php _e( 'Not Found', 'activists-lobbies' ); ?></h1>
		<div class="entry-content">
			<p><?php _e( 'No campaigns were found.', 'activists-lobbies' ); ?></p>
			<?php get_search_form(); ?>
		</div><!-- .entry-content -->
	</div><!-- #post-0 -->
<?php endif; ?>

<?php /* Start the Loop. */ ?>
<?php while (have_posts()) {
	the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

			<div class="entry-meta">
				<?php twentyten_posted_on(); ?>
			</div><!-- .entry-meta -->

	<?php if ( is_archive() || is_search() ) { ?>
			<div class="entry-summary">
				<?php the_excerpt(); ?>
			</div><!-- .entry-summary -->
	<?php } else { ?>
			<div class="entry-content">
				<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
				<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
			</div><!-- .entry-content -->
	<?php } ?>

			<div class="entry-utility">
				<?php if ( count( get_the_category() ) ) { ?>
					<span class="cat-links">
						<?php printf( __( '<span class="%1$s">Posted in</span> %2$s', 'twentyten' ), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list( ', ' ) ); ?>
					</span>
					<span class="meta-sep">|</span>
				<?php } ?>
				<?php
					$tags_list = get_the_tag_list( '', ', ' );
					if ( $tags_list ) {
				?>
					<span class="tag-links">
						<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', 'twentyten' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
					</span>
					<span class="meta-sep">|</span>
				<?php } ?>
				<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
			</div>
		</div>
<?php } ?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<div id="nav-above" class="navigation">
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Next Page', 'activists-lobbies' ) ); ?></div>
		<div class="nav-next"><?php previous_posts_link( __( 'Previous Page <span class="meta-nav">&rarr;</span>', 'activists-lobbies' ) ); ?></div>
	</div><!-- #nav-above -->
<?php endif; ?>
