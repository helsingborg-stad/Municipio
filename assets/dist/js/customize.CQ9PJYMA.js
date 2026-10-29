wp.customize.bind("ready",()=>{wp.customize.previewer.bind("tokens:update",data=>{wp.customize("tokens",setting=>{setting.set(JSON.stringify(data))})})});
//# sourceMappingURL=customize.CQ9PJYMA.js.map
