import { registerBlockType } from "@wordpress/blocks";
import { Edit } from "./Edit";

registerBlockType("municipio/posts-list-block", {
	edit: Edit,
});
