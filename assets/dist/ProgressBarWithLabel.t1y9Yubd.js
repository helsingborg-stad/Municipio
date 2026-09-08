var __defProp=Object.defineProperty;var __defNormalProp=(obj,key,value)=>key in obj?__defProp(obj,key,{enumerable:!0,configurable:!0,writable:!0,value}):obj[key]=value;var __name=(target,value)=>__defProp(target,"name",{value,configurable:!0});var __publicField=(obj,key,value)=>__defNormalProp(obj,typeof key!="symbol"?key+"":key,value);const _ProgressBar=class _ProgressBar{constructor(element,insertAfterElement){this.element=element,this.insertAfterElement=insertAfterElement}element;insertAfterElement;update(event){event.label!==null&&this.element.setAttribute("label",event.label),event.value!==null&&this.element.setAttribute("progress",event.value.toString())}show(){this.insertAfterElement.insertAdjacentElement("afterend",this.element)}hide(){this.element.remove()}};__name(_ProgressBar,"ProgressBar");let ProgressBar=_ProgressBar;const _ProgressBarWithLabel=class _ProgressBarWithLabel extends HTMLElement{progressElement;labelElement;label;progress;root;uniqueId="";constructor(){super(),this.uniqueId=this.getUniqueID(),this.root=this.attachShadow({mode:"open"}),this.root.innerHTML=`
            <style> 
                progress {
                    border: none;
                    border-radius: 3px;
                    background-color: #f3f3f3;
                    margin-right: 8px;
                    min-width: 192px;
                }

                ::-webkit-progress-bar {
                    background-color: rgba(0, 0, 0, 0.15);
                    border-radius: 3px;
                    border: none;
                }

                ::-webkit-progress-value {
                    background-color: #2271b1;
                    border-radius: 3px;
                    transition: width 1s;
                }

                label {
                    font-style: italic;
                } 
            </style>
            <progress part="progress-bar" id="${this.uniqueId}"></progress>
            <label part="label" for="${this.uniqueId}"></label>
        `,this.progressElement=this.root.querySelector("progress"),this.labelElement=this.root.querySelector("label"),this.label="",this.progress=0,this.progressElement.setAttribute("value",this.progress.toString()),this.progressElement.setAttribute("max","100")}getElement(){return this}setProgress(value){this.progress=Math.min(100,Math.max(0,value)),isNaN(this.progress)&&(this.progress=0),this.progressElement.setAttribute("value",this.progress.toString())}setLabel(label){this.label=label,this.labelElement.textContent=this.label}getUniqueID(){return Math.random().toString(36).substr(2,9)}static get observedAttributes(){return["label","progress"]}attributeChangedCallback(name,oldValue,newValue){if(oldValue!==newValue)switch(name){case"label":this.setLabel(newValue);break;case"progress":this.setProgress(Number(newValue));break}}connectedCallback(){this.hasAttribute("label")&&this.setLabel(this.getAttribute("label")),this.hasAttribute("progress")&&this.setProgress(Number(this.getAttribute("progress")))}};__name(_ProgressBarWithLabel,"ProgressBarWithLabel"),__publicField(_ProgressBarWithLabel,"customElementName","progress-bar-with-label");let ProgressBarWithLabel=_ProgressBarWithLabel;export{ProgressBarWithLabel as P,ProgressBar as a};
//# sourceMappingURL=ProgressBarWithLabel.t1y9Yubd.js.map
