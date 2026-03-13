import type rowBlockConfig from '../block.json';
declare global {
	export type BackdropBannerRowAttributes = {
		[K in keyof typeof rowBlockConfig.attributes]: typeof rowBlockConfig.attributes[K]['default'];
	};

	export type BackdropBannerRowEditProps = import('@wordpress/blocks').BlockEditProps<BackdropBannerRowAttributes>;
}
