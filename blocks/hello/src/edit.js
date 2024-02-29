import { __ } from '@wordpress/i18n';
import { PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import { InspectorControls, useBlockProps, MediaReplaceFlow } from '@wordpress/block-editor';
import './editor.scss';

const ALLOWED_MEDIA_TYPES = [ 'image' ];

export default function Edit({attributes, setAttributes}) {

	const { imageUrl, imageId, heading, subHeading, body } = attributes;

	return (
		<>
            <InspectorControls>
                <PanelBody title={ __( 'Settings', 'municipio' ) }>
					<MediaReplaceFlow
						mediaUrl={ imageUrl }
						mediaId={ imageId }
						allowedTypes={ ALLOWED_MEDIA_TYPES }
						onSelect={ ( media ) => {
							setAttributes( {
								imageId: media.id,
								imageUrl: media.url
							} );
						} }
						onSelectURL={ ( url ) => {
							setAttributes( { imageUrl: url } );
						} }/>
					<TextControl label={ __( 'Heading', 'municipio' ) } value={ heading || '' } onChange={ ( heading ) => setAttributes( { heading } ) } />
					<TextControl label={ __( 'Sub Heading', 'municipio' ) } value={ subHeading || '' } onChange={ ( subHeading ) => setAttributes( { subHeading } ) } />
					<TextareaControl label={ __( 'Body', 'municipio' ) } value={ body || '' } onChange={ ( body ) => setAttributes( { body } ) } />
                </PanelBody>
            </InspectorControls>

			<hbg-card
				{ ...useBlockProps() }
				imageUrl={imageUrl}
				heading={heading}
				subheading={subHeading}
				body={body}></hbg-card>
		</>
	);
}
