wp.domReady(() => {

  //Remove assymetric grids
  wp.blocks.unregisterBlockVariation('core/columns', 'three-columns-wider-center');

  wp.blocks.registerBlockVariation(
    'core/columns', {
      name: 'four-columns',
      icon: '<svg width="48" height="48" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true" focusable="false"><path fill-rule="nonzero" d="M39 12a2 2 0 011.995 1.85L41 14v20a2 2 0 01-1.85 1.995L39 36H9a2 2 0 01-1.995-1.85L7 34V14a2 2 0 011.85-1.995L9 12h30zm-24 2H9v20h6V14zm8 0h-6v20h6V14zm2 0v20h6V14h-6zm8 20h6V14h-6v20z" /></svg>',
      title: '25/25/25/25',
      scope: ['block'], // Highlight
      innerBlocks: [
        ['core/column'],
        ['core/column'],
        ['core/column'],
        ['core/column'],
      ],
    }
  );
});