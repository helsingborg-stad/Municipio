import type blockConfig from '../block.json';
declare global {
	export interface RowItem {
		id: string;
		title: string;
		subtitle: string;
		description: string;
		url: string;
		imageId: number;
		imageUrl: string;
		focalPointX: number;
		focalPointY: number;
	}

	export type BackdropBannerAttributes = Omit<{
		[K in keyof typeof blockConfig.attributes]: typeof blockConfig.attributes[K]['default'];
	}, 'rows'> & {
		rows: RowItem[];
	};
}

export type BackdropBannerEditProps = BlockEditProps<BackdropBannerAttributes>;

export type SelectedBackdropBannerBlock = {
	selectedBlockClientId: string | null;
	selectedBlockName: string | null;
	selectedRowClientId: string | null;
	isWithinBanner: boolean;
};