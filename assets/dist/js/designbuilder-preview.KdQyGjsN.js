jQuery(()=>{document.addEventListener("design-builder:action",event=>{!(event instanceof CustomEvent)||!event.detail?.state||wp.customize.preview.send("tokens:update",JSON.parse(JSON.stringify(event.detail.state)))})});
//# sourceMappingURL=designbuilder-preview.KdQyGjsN.js.map
