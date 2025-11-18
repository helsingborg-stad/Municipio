import Edit from "./edit";
const { registerBlockType } = window.wp.blocks;

registerBlockType('municipio/posts-list-block', {
    edit: Edit
});