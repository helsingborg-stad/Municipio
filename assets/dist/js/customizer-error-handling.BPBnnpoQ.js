wp.customize&&document.addEventListener("DOMContentLoaded",()=>{const publishButton=document.getElementById("save");publishButton&&publishButton.addEventListener("click",e=>{const controls=wp.customize.control._value;console.log("Settings: ",wp.customize.settings),Object.keys(controls).forEach(key=>{const control=controls[key];control.setting&&control.setting.bind("error",message=>{console.error("Customizer error message: ",message,`
Control: `,control)})})})});
//# sourceMappingURL=customizer-error-handling.BPBnnpoQ.js.map
