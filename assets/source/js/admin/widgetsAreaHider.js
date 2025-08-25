document.addEventListener("DOMContentLoaded", function() {
  const config = { attributes: true, childList: true, subtree: true };

  const callback = function(mutationsList, observer) {
    for (const mutation of mutationsList) {
      if (mutation.type !== "childList") {
        continue;
      }
      if (mutation.addedNodes.length === 0) {
        continue;
      }
      for (const node of mutation.addedNodes) {
        if (node.nodeType !== Node.ELEMENT_NODE) {
          continue;
        }
        if (
          node.hasAttribute("data-type") &&
          node.getAttribute("data-type") === "core/widget-area"
        ) {
            const footerAreaColumn = node.querySelector('[data-widget-area-id^="footer-area-column"]');
            if (!footerAreaColumn) {
                continue;
            }
            const matches = footerAreaColumn.getAttribute('data-widget-area-id').match(/footer-area-column-(\d)/i);
            if (matches.length !== 2) {
                continue;
            }
            if (matches[1] >= municipioSidebars.footerColumns) {
                node.style.display = "none";
            }
        }
      }
    }
  };

  const observer = new MutationObserver(callback);
  const widgetsEditor = document.querySelector("#widgets-editor");
  if (widgetsEditor !== null) {
    observer.observe(widgetsEditor, config);
  }
  
});
