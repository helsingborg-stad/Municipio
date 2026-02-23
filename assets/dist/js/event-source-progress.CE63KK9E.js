var __defProp=Object.defineProperty;var __defNormalProp=(obj,key,value)=>key in obj?__defProp(obj,key,{enumerable:!0,configurable:!0,writable:!0,value}):obj[key]=value;var __name=(target,value)=>__defProp(target,"name",{value,configurable:!0});var __publicField=(obj,key,value)=>__defNormalProp(obj,typeof key!="symbol"?key+"":key,value);const _EventSourceHandlerWithProgressBar=class _EventSourceHandlerWithProgressBar{constructor(target,url,progressBar){this.target=target,this.url=url,this.progressBar=progressBar}source=null;start(){this.source=new EventSource(this.url),this.disableAllTriggersWithSameUrl(),this.progressBar.show(),this.addEventListeners(this.source)}disableAllTriggersWithSameUrl(){document.querySelectorAll(`[data-js-progress-url="${this.url}"]`).forEach(element=>{element.setAttribute("disabled","disabled")})}addEventListeners(source){source.addEventListener("message",this.updateLabel.bind(this)),source.addEventListener("progress",this.updateProgress.bind(this)),source.addEventListener("finish",this.finish.bind(this)),source.addEventListener("error",this.handleError.bind(this))}removeEventListeners(source){source.removeEventListener("message",this.updateLabel.bind(this)),source.removeEventListener("progress",this.updateProgress.bind(this)),source.removeEventListener("finish",this.finish.bind(this)),source.removeEventListener("error",this.handleError.bind(this))}updateLabel(event){this.progressBar.update({label:event.data,value:null})}updateProgress(event){this.progressBar.update({label:null,value:event.data})}finish(event){this.progressBar.update({label:event.data,value:100}),this.source.close(),this.removeEventListeners(this.source)}handleError(event){this.progressBar.update({label:"An error occurred",value:100}),this.source.close(),this.removeEventListeners(this.source)}};__name(_EventSourceHandlerWithProgressBar,"EventSourceHandlerWithProgressBar");let EventSourceHandlerWithProgressBar=_EventSourceHandlerWithProgressBar;const _ProgressBar=class _ProgressBar{constructor(element,insertAfterElement){this.element=element,this.insertAfterElement=insertAfterElement}update(event){event.label!==null&&this.element.setAttribute("label",event.label),event.value!==null&&this.element.setAttribute("progress",event.value.toString())}show(){this.insertAfterElement.insertAdjacentElement("afterend",this.element)}hide(){this.element.remove()}};__name(_ProgressBar,"ProgressBar");let ProgressBar=_ProgressBar;const _ProgressBarWithLabel=class _ProgressBarWithLabel extends HTMLElement{progressElement;labelElement;label;progress;root;uniqueId="";constructor(){super(),this.uniqueId=this.getUniqueID(),this.root=this.attachShadow({mode:"open"}),this.root.innerHTML=`
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
        `,this.progressElement=this.root.querySelector("progress"),this.labelElement=this.root.querySelector("label"),this.label="",this.progress=0,this.progressElement.setAttribute("value",this.progress.toString()),this.progressElement.setAttribute("max","100")}getElement(){return this}setProgress(value){this.progress=Math.min(100,Math.max(0,value)),isNaN(this.progress)&&(this.progress=0),this.progressElement.setAttribute("value",this.progress.toString())}setLabel(label){this.label=label,this.labelElement.textContent=this.label}getUniqueID(){return Math.random().toString(36).substr(2,9)}static get observedAttributes(){return["label","progress"]}attributeChangedCallback(name,oldValue,newValue){if(oldValue!==newValue)switch(name){case"label":this.setLabel(newValue);break;case"progress":this.setProgress(Number(newValue));break}}connectedCallback(){this.hasAttribute("label")&&this.setLabel(this.getAttribute("label")),this.hasAttribute("progress")&&this.setProgress(Number(this.getAttribute("progress")))}};__name(_ProgressBarWithLabel,"ProgressBarWithLabel"),__publicField(_ProgressBarWithLabel,"customElementName","progress-bar-with-label");let ProgressBarWithLabel=_ProgressBarWithLabel;const _EventSourceTrigger=class _EventSourceTrigger{constructor(triggerElement,eventSourceUrl){this.triggerElement=triggerElement,this.eventSourceUrl=eventSourceUrl,this.eventSourceHandlerWithProgressBar=new EventSourceHandlerWithProgressBar(this.triggerElement,this.eventSourceUrl,this.createProgressBar()),this.triggerElement.addEventListener("click",this.handleClick.bind(this))}eventSourceHandlerWithProgressBar;handleClick(event){event.preventDefault(),this.eventSourceHandlerWithProgressBar.start()}createProgressBar(){const progressBarElement=document.createElement(ProgressBarWithLabel.customElementName);return new ProgressBar(progressBarElement,this.triggerElement)}};__name(_EventSourceTrigger,"EventSourceTrigger");let EventSourceTrigger=_EventSourceTrigger;customElements.define(ProgressBarWithLabel.customElementName,ProgressBarWithLabel),document.querySelectorAll("[data-js-progress-url]").forEach(element=>{new EventSourceTrigger(element,element.getAttribute("data-js-progress-url"))});
//# sourceMappingURL=event-source-progress.CE63KK9E.js.map
