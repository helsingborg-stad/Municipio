import EventSourceTrigger from "./EventSourceTrigger";
import ProgressBarWithLabel from "./UIComponents/ProgressBarWithLabel";

export default (() => {

    /**
     * Register the custom element for the progress bar
     */
    customElements.define(ProgressBarWithLabel.customElementName, ProgressBarWithLabel);

    /**
     * Setup the progress bar for each element with the data-js-progress-url attribute
     */
    document.querySelectorAll('[data-js-progress-url]').forEach((element) => {
        new EventSourceTrigger(element as HTMLElement, element.getAttribute('data-js-progress-url') as string);
    });

})();