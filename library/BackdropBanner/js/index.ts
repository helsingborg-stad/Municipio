import blockConfig from '../block.json';
import { registerBlockType } from "@wordpress/blocks";
import { Edit as edit } from "./Edit";

registerBlockType<BackdropBannerAttributes>(blockConfig.name, {
	edit
});
