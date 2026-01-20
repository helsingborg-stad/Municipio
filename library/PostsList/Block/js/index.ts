import blockConfig from '../block.json';
import { registerBlockType } from "@wordpress/blocks";
import { Edit as edit } from "./Edit";

registerBlockType<PostsListAttributes>(blockConfig.name, {
	edit
});
