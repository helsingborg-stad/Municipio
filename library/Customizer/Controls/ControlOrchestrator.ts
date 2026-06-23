type ControlDefinition = {
	tagName: string;
	element: CustomElementConstructor;
};

export class ControlOrchestrator {
	public constructor(private readonly controls: ControlDefinition[]) {}

	public register(): void {
		this.controls.forEach(({ tagName, element }) => {
			if (!customElements.get(tagName)) {
				customElements.define(tagName, element);
			}
		});
	}
}
