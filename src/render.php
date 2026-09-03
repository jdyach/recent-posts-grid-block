<?php
/**
 * Server-side render for the Recent Posts Grid block.
 *
 * Because block.json declares `"render": "file:./render.php"`, WordPress
 * includes this file itself (both on the front end and for the editor's
 * ServerSideRender preview) and makes the following variables available:
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content (unused — this block has no children).
 * @var WP_Block $block      Full block instance, including context.
 *
 * @package RecentPostsGridBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

$posts_to_show          = isset( $attributes['postsToShow'] ) ? absint( $attributes['postsToShow'] ) : 6;
$columns                = isset( $attributes['columns'] ) ? absint( $attributes['columns'] ) : 3;
$category_id            = isset( $attributes['categoryId'] ) ? absint( $attributes['categoryId'] ) : 0;
$order                  = isset( $attributes['order'] ) && 'asc' === $attributes['order'] ? 'asc' : 'desc';
$display_featured_image = ! empty( $attributes['displayFeaturedImage'] );
$display_excerpt        = ! empty( $attributes['displayExcerpt'] );
$excerpt_length         = isset( $attributes['excerptLength'] ) ? absint( $attributes['excerptLength'] ) : 20;
$display_date           = ! empty( $attributes['displayDate'] );
$display_author         = ! empty( $attributes['displayAuthor'] );

$columns = max( 1, min( 4, $columns ) );

$query_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => max( 1, min( 12, $posts_to_show ) ),
	'orderby'             => 'date',
	'order'               => $order,
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
);

if ( $category_id > 0 ) {
	$query_args['cat'] = $category_id;
}

$recent_posts_query = new WP_Query( $query_args );

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'wp-block-jdyach-recent-posts-grid',
	)
);

/**
 * Filters the excerpt length (in words) used by the Recent Posts Grid block.
 *
 * @param int $excerpt_length Number of words.
 */
$excerpt_length = apply_filters( 'recent_posts_grid_block_excerpt_length', $excerpt_length );

ob_start();
?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- generated safely by WP core. ?>>
	<?php if ( $recent_posts_query->have_posts() ) : ?>
		<div
			class="wp-block-jdyach-recent-posts-grid__grid"
			style="--rpg-columns: <?php echo esc_attr( $columns ); ?>;"
		>
			<?php
			while ( $recent_posts_query->have_posts() ) :
				$recent_posts_query->the_post();
				?>
				<article class="wp-block-jdyach-recent-posts-grid__card">
					<?php if ( $display_featured_image && has_post_thumbnail() ) : ?>
						<a
							class="wp-block-jdyach-recent-posts-grid__thumbnail-link"
							href="<?php the_permalink(); ?>"
							tabindex="-1"
							aria-hidden="true"
						>
							<?php
							the_post_thumbnail(
								'medium_large',
								array( 'class' => 'wp-block-jdyach-recent-posts-grid__thumbnail' )
							);
							?>
						</a>
					<?php endif; ?>

					<div class="wp-block-jdyach-recent-posts-grid__body">
						<h3 class="wp-block-jdyach-recent-posts-grid__title">
							<a href="<?php the_permalink(); ?>">
								<?php the_title(); ?>
							</a>
						</h3>

						<?php if ( $display_date || $display_author ) : ?>
							<div class="wp-block-jdyach-recent-posts-grid__meta">
								<?php if ( $display_date ) : ?>
									<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
										<?php echo esc_html( get_the_date() ); ?>
									</time>
								<?php endif; ?>
								<?php if ( $display_author ) : ?>
									<span>
										<?php
										echo esc_html(
											sprintf(
												/* translators: %s: post author display name. */
												__( 'by %s', 'recent-posts-grid-block' ),
												get_the_author()
											)
										);
										?>
									</span>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( $display_excerpt ) : ?>
							<p class="wp-block-jdyach-recent-posts-grid__excerpt">
								<?php
								echo esc_html(
									wp_trim_words( get_the_excerpt(), $excerpt_length )
								);
								?>
							</p>
						<?php endif; ?>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
		<?php wp_reset_postdata(); ?>
	<?php else : ?>
		<p class="wp-block-jdyach-recent-posts-grid__empty">
			<?php esc_html_e( 'No posts found.', 'recent-posts-grid-block' ); ?>
		</p>
	<?php endif; ?>
</div>
<?php
echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup above is escaped field-by-field.
