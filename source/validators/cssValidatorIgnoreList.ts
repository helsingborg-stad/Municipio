export interface CssValidatorIgnoreRule {
	bundle?: string;
	messageIncludes: string;
	contextIncludes?: string;
	type?: 'error' | 'warning';
}

/**
 * W3C validator limitations and intentional compatibility declarations.
 * Keep this list narrow: every ignored error must have a concrete reason.
 */
export const cssValidatorIgnoreList: CssValidatorIgnoreRule[] = [
	// W3C's validator does not yet recognize every modern CSS feature used by
	// supported browsers. These are deliberately explicit so invalid property
	// names are still reported.
	{
		messageIncludes: '"corner-shape" doesn\'t exist',
		type: 'error',
	},
	...['@starting-style', '@property', '@container', '@scope', '@view-transition'].map((atRule) => ({
		messageIncludes: `Unrecognized at-rule "${atRule}"`,
		type: 'error' as const,
	})),
	...[
		'animation-composition',
		'animation-range',
		'animation-range-end',
		'animation-range-start',
		'animation-timeline',
		'backdrop-filter',
		'anchor-name',
		'anchor-scope',
		'clip-path',
		'contain-intrinsic-block-size',
		'contain-intrinsic-height',
		'contain-intrinsic-inline-size',
		'contain-intrinsic-size',
		'contain-intrinsic-width',
		'content-visibility',
		'container-name',
		'container-type',
		'field-sizing',
		'fill',
		'interpolate-size',
		'line-clamp',
		'mask',
		'mask-image',
		'mask-position',
		'mask-repeat',
		'mask-size',
		'pointer-events',
		'position-anchor',
		'position-area',
		'position-try',
		'position-try-fallbacks',
		'position-try-order',
		'position-visibility',
		'scrollbar-color',
		'scrollbar-width',
		'scroll-timeline',
		'stroke',
		'stroke-width',
		'timeline-scope',
		'transition-behavior',
		'view-timeline',
		'view-timeline-axis',
		'view-timeline-inset',
		'view-timeline-name',
		'view-transition-class',
		'view-transition-group',
		'view-transition-image-pair',
		'view-transition-name',
		'view-transition-new',
		'view-transition-old',
	].map((property) => ({
		messageIncludes: `Property "${property}" doesn't exist`,
		type: 'error' as const,
	})),
	{
		messageIncludes: '"allow-discrete" is not a "transition" value',
		type: 'error',
	},
	{
		messageIncludes: 'text-wrap',
		type: 'error',
	},
	{
		messageIncludes: 'anchor(',
		type: 'error',
	},
];
