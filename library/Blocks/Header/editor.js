wp.blocks.registerBlockType('municipio/header', {
    edit: () => wp.element.createElement( 
        wp.serverSideRender.ServerSideRender, { 
            block: 'municipio/header' 
        } 
    ),
    save: () => null
})