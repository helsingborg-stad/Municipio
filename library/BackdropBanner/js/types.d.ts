import blockConfig from '../block.json';
export {};
declare global {
	export interface SlideItem {
		id: string;
		title: string;
		description: string;
		url: string;
		imageId: number;
		imageUrl: string;
	}

	export type BackdropBannerAttributes = Omit<{
		[K in keyof typeof blockConfig.attributes]: typeof blockConfig.attributes[K]['default'];
	}, 'slides'> & {
		slides: SlideItem[];
	};
}
