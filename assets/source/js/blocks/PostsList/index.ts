import { registerBlockType } from "@wordpress/blocks";
import blockConfig from "../../../../../library/PostsList/Block/block.json";
import { Edit as edit } from "./Edit";

export type PostsListAttributes = {
	[K in keyof typeof blockConfig.attributes]: (typeof blockConfig.attributes)[K]["default"];
};

export default function init() {
	registerBlockType<PostsListAttributes>(blockConfig.name, {
		edit,
	});
}
