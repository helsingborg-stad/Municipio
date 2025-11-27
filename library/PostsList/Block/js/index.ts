import { Edit } from "./Edit";
const { registerBlockType } = window.wp.blocks;

registerBlockType('municipio/posts-list-block', {
    edit: Edit
});