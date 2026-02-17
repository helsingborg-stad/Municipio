wp.blocks.registerBlockType('municipio/footer', {
    edit: () => wp.element.createElement( 
        wp.serverSideRender.ServerSideRender, { 
            block: 'municipio/footer' 
        } 
    ),
    save: () => null
})