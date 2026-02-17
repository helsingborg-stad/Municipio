import { registerBlockType } from "@wordpress/blocks";
import blockConfig from "../../../../../library/Blocks/Footer/block.json";
import { Edit } from "./Edit";

export type FooterAttributes = {
	[K in keyof typeof blockConfig.attributes]: (typeof blockConfig.attributes)[K]["default"];
};

export default function init() {
	registerBlockType(blockConfig.name, {
		edit: Edit,
		example: {
			attributes: {},
		},
	});
}
