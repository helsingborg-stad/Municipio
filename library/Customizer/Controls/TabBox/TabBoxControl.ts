type TabButton = HTMLButtonElement & {
	dataset: DOMStringMap & {
		tabBoxControls?: string;
	};
};

export class TabBoxControlElement extends HTMLElement {
	private readonly handleClick = (event: Event): void => {
		const button = event.target instanceof HTMLElement
			? event.target.closest<TabButton>(".municipio-tab-box__tab")
			: null;

		if (!button) {
			return;
		}

		this.activateTab(button);
	};

	public connectedCallback(): void {
		this.addEventListener("click", this.handleClick);
		this.activateTab(this.getTabs()[0]);
	}

	public disconnectedCallback(): void {
		this.removeEventListener("click", this.handleClick);
		this.getManagedControls().forEach((control) => {
			control.hidden = false;
		});
	}

	private activateTab(activeButton?: TabButton): void {
		if (!activeButton) {
			return;
		}

		const activeControls = this.readControls(activeButton);

		this.getTabs().forEach((button) => {
			button.classList.toggle("is-active", button === activeButton);
			button.setAttribute("aria-selected", button === activeButton ? "true" : "false");
		});

		this.getManagedControls().forEach((control) => {
			control.hidden = !activeControls.includes(control.id.replace("customize-control-", ""));
		});
	}

	private getTabs(): TabButton[] {
		return Array.from(this.querySelectorAll<TabButton>(".municipio-tab-box__tab"));
	}

	private getManagedControls(): HTMLElement[] {
		const controlIds = this.getTabs().flatMap((button) => this.readControls(button));

		return Array.from(new Set(controlIds))
			.map((controlId) => document.getElementById(`customize-control-${controlId}`))
			.filter((control): control is HTMLElement => control instanceof HTMLElement);
	}

	private readControls(button: TabButton): string[] {
		try {
			const controls = JSON.parse(button.dataset.tabBoxControls ?? "[]");

			return Array.isArray(controls)
				? controls.filter((control): control is string => typeof control === "string" && control !== "")
				: [];
		} catch {
			return [];
		}
	}
}
