type TabButton = HTMLButtonElement & {
	dataset: DOMStringMap & {
		tabBoxControls?: string;
	};
};

export class TabBoxControlElement extends HTMLElement {
	private controlObserver?: MutationObserver;

	private readonly handleClick = (event: Event): void => {
		const button =
			event.target instanceof HTMLElement
				? event.target.closest<TabButton>(".municipio-tab-box__tab")
				: null;

		if (!button) {
			return;
		}

		this.activateTab(button);
	};

	public connectedCallback(): void {
		this.addEventListener("click", this.handleClick);
		this.initializeActiveTab();
	}

	public disconnectedCallback(): void {
		this.removeEventListener("click", this.handleClick);
		this.controlObserver?.disconnect();
		this.getManagedControls().forEach((control) => {
			control.hidden = false;
		});
	}

	private initializeActiveTab(): void {
		if (this.activateTab(this.getTabs()[0])) {
			return;
		}

		this.controlObserver = new MutationObserver(() => {
			if (this.activateTab(this.getTabs()[0])) {
				this.controlObserver?.disconnect();
			}
		});

		this.controlObserver.observe(document.body, {
			childList: true,
			subtree: true,
		});
	}

	private activateTab(activeButton?: TabButton): boolean {
		if (!activeButton) {
			return false;
		}

		const activeControls = this.readControls(activeButton);
		const managedControls = this.getManagedControls();

		if (managedControls.length === 0) {
			return false;
		}

		this.getTabs().forEach((button) => {
			button.classList.toggle("is-active", button === activeButton);
			button.setAttribute(
				"aria-selected",
				button === activeButton ? "true" : "false",
			);
		});

		managedControls.forEach((control) => {
			control.hidden = !activeControls.includes(
				control.id.replace("customize-control-", ""),
			);
		});

		return true;
	}

	private getTabs(): TabButton[] {
		return Array.from(
			this.querySelectorAll<TabButton>(".municipio-tab-box__tab"),
		);
	}

	private getManagedControls(): HTMLElement[] {
		const controlIds = this.getTabs().flatMap((button) =>
			this.readControls(button),
		);

		return Array.from(new Set(controlIds))
			.map((controlId) =>
				document.getElementById(`customize-control-${controlId}`),
			)
			.filter(
				(control): control is HTMLElement => control instanceof HTMLElement,
			);
	}

	private readControls(button: TabButton): string[] {
		try {
			const controls = JSON.parse(button.dataset.tabBoxControls ?? "[]");

			return Array.isArray(controls)
				? controls.filter(
						(control): control is string =>
							typeof control === "string" && control !== "",
					)
				: [];
		} catch {
			return [];
		}
	}
}
