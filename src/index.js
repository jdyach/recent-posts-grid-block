import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import Edit from './edit';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

/**
 * Register the block.
 *
 * This is a *dynamic* block: the front end is rendered entirely by PHP
 * (see render.php), so there is no save() callback here — the block
 * only needs an edit() component and, for editing, a live PHP preview
 * via ServerSideRender.
 */
registerBlockType( metadata.name, {
	...metadata,
	title: __( 'Recent Posts Grid', 'recent-posts-grid-block' ),
	description: __(
		'Display a responsive grid of your most recent posts.',
		'recent-posts-grid-block'
	),
	edit: Edit,
	save: () => null,
} );
