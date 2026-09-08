import ProgressActionTrigger from "./ProgressActionTrigger";
import ProgressBarWithLabel from "./UIComponents/ProgressBarWithLabel";

export default (() => {
	/**
	 * Register the custom element for the progress bar
	 */
	if (!customElements.get(ProgressBarWithLabel.customElementName)) {
		customElements.define(
			ProgressBarWithLabel.customElementName,
			ProgressBarWithLabel,
		);
	}

	document.querySelectorAll("[data-js-progress-url]").forEach((element) => {
		new ProgressActionTrigger(element as HTMLElement);
	});
})();
