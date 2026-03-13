import { InnerBlocks } from "@wordpress/block-editor";
import rowBlockConfig from '../block.json';
import { registerBlockType } from "@wordpress/blocks";
import { Edit } from "./Edit";

registerBlockType<BackdropBannerRowAttributes>(rowBlockConfig.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
});
