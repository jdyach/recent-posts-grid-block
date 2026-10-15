#!/usr/bin/env node
/**
 * Builds _playground/blueprint.json from _playground/seed.php.
 *
 * The seed lives in a real PHP file so it can be linted and tested; this
 * script embeds it into the blueprint's runPHP step. Run: npm run blueprint
 */
const fs = require( 'fs' );
const path = require( 'path' );

const root = path.join( __dirname, '..' );
const seed = fs.readFileSync( path.join( root, '_playground/seed.php' ), 'utf8' );

const blueprint = {
	$schema: 'https://playground.wordpress.net/blueprint-schema.json',
	meta: {
		title: 'Recent Posts Grid Block demo',
		description: 'A dynamic Gutenberg block: responsive grid of recent posts with editor controls and a live server-rendered preview.',
		author: 'jdyach',
		categories: [ 'Block' ],
	},
	// Open the demo page in the block editor so the block's controls are visible.
	landingPage: '/wp-admin/post.php?post=100&action=edit',
	preferredVersions: { php: '8.3', wp: 'latest' },
	features: { networking: true },
	login: true,
	steps: [
		{
			step: 'installTheme',
			themeData: {
				resource: 'git:directory',
				url: 'https://github.com/jdyach/fieldnote',
				ref: 'main',
			},
			options: { activate: true },
		},
		{
			step: 'installPlugin',
			pluginData: {
				resource: 'git:directory',
				url: 'https://github.com/jdyach/recent-posts-grid-block',
				ref: 'main',
			},
			options: { activate: true },
		},
		{ step: 'runPHP', code: seed },
	],
};

fs.writeFileSync(
	path.join( root, '_playground/blueprint.json' ),
	JSON.stringify( blueprint, null, '\t' ) + '\n'
);
// eslint-disable-next-line no-console -- CLI script output.
console.log( 'Wrote _playground/blueprint.json' );
