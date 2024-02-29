import { useBlockProps } from '@wordpress/block-editor';
import {renderToString} from '@wordpress/element';

export default function save({attributes}) {

	const { imageUrl, heading, subHeading, body } = attributes;

	return (
		<hbg-card
			{...useBlockProps.save()}
			imageUrl={imageUrl}
			heading={heading}
			subheading={subHeading}
			body={body}></hbg-card>
	)
}
