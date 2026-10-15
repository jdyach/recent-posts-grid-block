<?php
/**
 * Demo content for the WordPress Playground "Try it live" link.
 *
 * Runs once inside Playground (blueprint.json embeds this file). Creates
 * categorized posts with featured images and a page showing the block in three
 * configurations, then opens that page in the block editor so the sidebar
 * controls and live server-rendered preview are visible straight away.
 *
 * Not loaded by the plugin.
 *
 * @package RecentPostsGridBlock
 */

// phpcs:disable WordPress.WP.GlobalVariablesOverride -- Standalone demo script that Playground runs once in global scope.

require_once '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/taxonomy.php';

wp_set_current_user( 1 );

foreach ( get_posts(
	array(
		'post_type'   => array( 'post', 'page' ),
		'numberposts' => -1,
		'post_status' => 'any',
	)
) as $old ) {
	wp_delete_post( $old->ID, true );
}

// Featured images: reuse the Fieldnote theme's original artwork when it is active.
$images = array();
foreach ( array( 'studio-shapes', 'field-study', 'notebook' ) as $name ) {
	$source = get_theme_file_path( "assets/images/$name.webp" );
	if ( ! file_exists( $source ) ) {
		continue;
	}
	$upload = wp_upload_bits( "$name.webp", null, file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! empty( $upload['error'] ) ) {
		continue;
	}
	$id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/webp',
			'post_title'     => $name,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	$images[] = $id;
}

$categories = array(
	wp_create_category( 'Process' ),
	wp_create_category( 'Block themes' ),
	wp_create_category( 'Notes' ),
);

$titles = array(
	'Why we moved every client to block themes',
	'A fluid type scale you can actually reason about',
	'Section styles: color schemes without custom CSS',
	'Writing patterns editors will not break',
	'What theme.json replaced in our old functions.php',
	'Notes on handing off a site to a non-developer',
	'Designing for the person who edits the site next',
	'Patterns, not page builders',
	'A checklist for accessible color schemes',
);

foreach ( $titles as $i => $title ) {
	$post_id = wp_insert_post(
		array(
			'post_title'    => $title,
			'post_content'  => '<!-- wp:paragraph --><p>Sample post for the Recent Posts Grid demo.</p><!-- /wp:paragraph -->',
			'post_excerpt'  => 'A short write-up from the studio on building maintainable WordPress sites with blocks, presets and patterns.',
			'post_status'   => 'publish',
			'post_date'     => gmdate( 'Y-m-d H:i:s', time() - ( $i + 1 ) * 4 * DAY_IN_SECONDS ),
			'post_category' => array( $categories[ $i % 3 ] ),
		)
	);
	if ( $images ) {
		set_post_thumbnail( $post_id, $images[ $i % count( $images ) ] );
	}
}

$process = (int) $categories[0];

$content = '<!-- wp:paragraph {"fontSize":"large"} --><p class="has-large-font-size">Three copies of the same block with different settings. Click any grid to see its controls in the sidebar; the preview is rendered by the same PHP as the front end.</p><!-- /wp:paragraph -->

<!-- wp:heading --><h2 class="wp-block-heading">Default: six posts, three columns</h2><!-- /wp:heading -->

<!-- wp:jdyach/recent-posts-grid {"align":"wide"} /-->

<!-- wp:heading --><h2 class="wp-block-heading">One category, two columns, with author</h2><!-- /wp:heading -->

<!-- wp:jdyach/recent-posts-grid {"postsToShow":2,"columns":2,"categoryId":' . $process . ',"displayAuthor":true,"excerptLength":12,"align":"wide"} /-->

<!-- wp:heading --><h2 class="wp-block-heading">Oldest first, text only</h2><!-- /wp:heading -->

<!-- wp:jdyach/recent-posts-grid {"postsToShow":3,"order":"asc","displayFeaturedImage":false,"align":"wide"} /-->';

wp_insert_post(
	array(
		'import_id'    => 100, // Fixed ID so the blueprint can open this page in the editor.
		'post_title'   => 'Recent Posts Grid',
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_content' => $content,
	)
);

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', 100 );
update_option( 'blogname', 'Recent Posts Grid Block' );
update_option( 'blogdescription', 'Live demo' );
// Pretty permalinks. Set them through WP_Rewrite, not update_option(): in
// Playground the routing rules already exist by now, and only this updates
// WordPress's in-memory copy so the flush rebuilds them correctly.
$GLOBALS['wp_rewrite']->set_permalink_structure( '/%postname%/' );
flush_rewrite_rules();

// Skip the editor's welcome guide so visitors land straight on the blocks.
update_user_meta(
	1,
	$GLOBALS['wpdb']->get_blog_prefix() . 'persisted_preferences',
	array(
		'core/edit-post' => array( 'welcomeGuide' => false ),
		'core'           => array( 'welcomeGuide' => false ),
		'_modified'      => gmdate( 'c' ),
	)
);
