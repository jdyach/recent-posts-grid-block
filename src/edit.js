import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
	Spinner,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Editor UI for the Recent Posts Grid block.
 *
 * The canvas itself is just a live PHP preview (ServerSideRender) so
 * what the editor shows always matches the front end exactly — the
 * real "view" is render.php, this component only manages attributes.
 */
export default function Edit( { attributes, setAttributes } ) {
	const {
		postsToShow,
		columns,
		categoryId,
		order,
		displayFeaturedImage,
		displayExcerpt,
		excerptLength,
		displayDate,
		displayAuthor,
	} = attributes;

	const blockProps = useBlockProps();

	// Pull the site's categories from core-data so the editor stays in
	// sync with whatever categories actually exist — no hardcoded list.
	const { categories, isLoadingCategories } = useSelect( ( select ) => {
		const query = { per_page: -1, orderby: 'name', order: 'asc' };
		return {
			categories: select( coreStore ).getEntityRecords(
				'taxonomy',
				'category',
				query
			),
			isLoadingCategories: ! select( coreStore ).hasFinishedResolution(
				'getEntityRecords',
				[ 'taxonomy', 'category', query ]
			),
		};
	}, [] );

	const categoryOptions = [
		{ label: __( 'All categories', 'recent-posts-grid-block' ), value: 0 },
		...( categories || [] ).map( ( category ) => ( {
			label: category.name,
			value: category.id,
		} ) ),
	];

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Content settings', 'recent-posts-grid-block' ) }
				>
					<RangeControl
						label={ __( 'Number of posts', 'recent-posts-grid-block' ) }
						value={ postsToShow }
						onChange={ ( value ) =>
							setAttributes( { postsToShow: value } )
						}
						min={ 1 }
						max={ 12 }
					/>
					<RangeControl
						label={ __( 'Columns', 'recent-posts-grid-block' ) }
						value={ columns }
						onChange={ ( value ) => setAttributes( { columns: value } ) }
						min={ 1 }
						max={ 4 }
					/>
					{ isLoadingCategories ? (
						<Spinner />
					) : (
						<SelectControl
							label={ __( 'Category', 'recent-posts-grid-block' ) }
							value={ categoryId }
							options={ categoryOptions }
							onChange={ ( value ) =>
								setAttributes( { categoryId: Number( value ) } )
							}
						/>
					) }
					<SelectControl
						label={ __( 'Order', 'recent-posts-grid-block' ) }
						value={ order }
						options={ [
							{
								label: __( 'Newest first', 'recent-posts-grid-block' ),
								value: 'desc',
							},
							{
								label: __( 'Oldest first', 'recent-posts-grid-block' ),
								value: 'asc',
							},
						] }
						onChange={ ( value ) => setAttributes( { order: value } ) }
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Display settings', 'recent-posts-grid-block' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __( 'Featured image', 'recent-posts-grid-block' ) }
						checked={ displayFeaturedImage }
						onChange={ ( value ) =>
							setAttributes( { displayFeaturedImage: value } )
						}
					/>
					<ToggleControl
						label={ __( 'Excerpt', 'recent-posts-grid-block' ) }
						checked={ displayExcerpt }
						onChange={ ( value ) =>
							setAttributes( { displayExcerpt: value } )
						}
					/>
					{ displayExcerpt && (
						<RangeControl
							label={ __(
								'Excerpt length (words)',
								'recent-posts-grid-block'
							) }
							value={ excerptLength }
							onChange={ ( value ) =>
								setAttributes( { excerptLength: value } )
							}
							min={ 5 }
							max={ 55 }
						/>
					) }
					<ToggleControl
						label={ __( 'Date', 'recent-posts-grid-block' ) }
						checked={ displayDate }
						onChange={ ( value ) =>
							setAttributes( { displayDate: value } )
						}
					/>
					<ToggleControl
						label={ __( 'Author', 'recent-posts-grid-block' ) }
						checked={ displayAuthor }
						onChange={ ( value ) =>
							setAttributes( { displayAuthor: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<ServerSideRender
					block="jdyach/recent-posts-grid"
					attributes={ attributes }
				/>
			</div>
		</>
	);
}
