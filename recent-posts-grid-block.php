<?php
/**
 * Plugin Name:       Recent Posts Grid Block
 * Plugin URI:        https://github.com/jdyach/recent-posts-grid-block
 * Description:       A custom Gutenberg block that renders a responsive, configurable grid of your most recent posts. Built as a small demonstration of custom block development (block.json, InspectorControls, ServerSideRender, dynamic PHP rendering).
 * Version:           1.0.1
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            Jacqui Dyach
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       recent-posts-grid-block
 *
 * @package RecentPostsGridBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'RPGB_VERSION', '1.0.1' );
define( 'RPGB_PLUGIN_FILE', __FILE__ );
define( 'RPGB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Register the block using the metadata loaded from `build/block.json`.
 *
 * `register_block_type()` reads block.json, wires up the editor script
 * and stylesheets it declares, and — because block.json specifies
 * `"render": "file:./render.php"` — automatically renders the block
 * dynamically on the front end and in the editor's live preview.
 * No manual render_callback is needed with this (WP 6.5+) approach.
 */
function rpgb_register_block() {
	$build_dir = RPGB_PLUGIN_DIR . 'build';

	if ( ! file_exists( $build_dir . '/block.json' ) ) {
		add_action( 'admin_notices', 'rpgb_missing_build_notice' );
		return;
	}

	register_block_type( $build_dir );
}
add_action( 'init', 'rpgb_register_block' );

/**
 * Admin notice shown if the plugin is activated without running the
 * build step (i.e. `build/block.json` doesn't exist yet).
 */
function rpgb_missing_build_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					/* translators: %s: build command. */
					__( 'Recent Posts Grid Block: no build found. Run %s from the plugin directory, then reload this page.', 'recent-posts-grid-block' ),
					'<code>npm install && npm run build</code>'
				)
			);
			?>
		</p>
	</div>
	<?php
}
