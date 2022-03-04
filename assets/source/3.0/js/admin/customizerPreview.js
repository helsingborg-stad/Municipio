(function(api) {
  if(typeof customizerPanelPreviewUrls === 'object') {
    for (const property in customizerPanelPreviewUrls) {
      api.section(property, function( section ) {
        var previousUrl, clearPreviousUrl, previewUrlValue;
        previewUrlValue = api.previewer.previewUrl;
        
        clearPreviousUrl = function() {
            previousUrl = null;
        };
        section.expanded.bind( function( isExpanded ) {
            if (isExpanded) {
                previousUrl = previewUrlValue.get();
                previewUrlValue.set(customizerPanelPreviewUrls[property]);
                previewUrlValue.bind(clearPreviousUrl);
            } else {
                previewUrlValue.unbind(clearPreviousUrl);
                if (previousUrl) {
                  previewUrlValue.set(previousUrl);
                }
            }
        });
      });
    }
  }
} (wp.customize));