describe("Design Builder Customizer bridge", () => {
	let listeners: jest.SpyInstance;
	beforeEach(() => {
		listeners = jest.spyOn(document, "addEventListener");
	});
	afterEach(() => {
		for (const [name, listener] of listeners.mock.calls) {
			if (name === "design-builder:action")
				document.removeEventListener(name, listener);
		}
		listeners.mockRestore();
	});
	it("sends independent Default, Dark and Contrast changes from replaced elements", () => {
		const send = jest.fn();
		const events: Record<string, () => void> = {};
		Object.assign(globalThis, {
			wp: {
				customize: {
					bind: (name: string, callback: () => void) => {
						events[name] = callback;
					},
					preview: {
						send,
						bind: (name: string, callback: () => void) => {
							events[name] = callback;
						},
					},
				},
			},
		});
		jest.isolateModules(() => require("./designbuilderPreview"));
		events["preview-ready"]();

		const state: Record<
			"token" | "_dark" | "_contrast" | "component",
			Record<string, string>
		> = { token: {}, _dark: {}, _contrast: {}, component: {} };
		for (const scheme of ["token", "_dark", "_contrast"] as const) {
			const builder = document.createElement("design-builder");
			document.body.replaceChildren(builder);
			state[scheme]["--color--primary"] = "#123456";
			builder.dispatchEvent(
				new CustomEvent("design-builder:action", {
					bubbles: true,
					composed: true,
					detail: { action: "change", state },
				}),
			);
			if (scheme === "token") {
				expect(send).not.toHaveBeenCalled();
				events.active();
			}
			expect(send).toHaveBeenLastCalledWith("tokens:update", state);
		}
		state._dark["--color--primary"] = "#abcdef";
		expect(send.mock.calls[1][1]._dark["--color--primary"]).toBe("#123456");
		expect(send).toHaveBeenCalledTimes(3);
		document.body.replaceChildren();
	});

	it("retains the latest footer palette edit until WordPress activates the preview", () => {
		const send = jest.fn();
		const events: Record<string, () => void> = {};
		Object.assign(globalThis, {
			wp: {
				customize: {
					bind: (name: string, callback: () => void) => {
						events[name] = callback;
					},
					preview: {
						send,
						bind: (name: string, callback: () => void) => {
							events[name] = callback;
						},
					},
				},
			},
		});
		jest.isolateModules(() => require("./designbuilderPreview"));
		events["preview-ready"]();
		const state = {
			token: {},
			component: {},
			componentSchemes: {
				dark: {
					__general__: {
						footer: { "--c-footer--color--surface": "var(--color--primary)" },
					},
				},
			},
		};
		const change = () =>
			document.dispatchEvent(
				new CustomEvent("design-builder:action", {
					detail: { action: "change", state },
				}),
			);
		change();
		state.componentSchemes.dark.__general__.footer[
			"--c-footer--color--surface"
		] = "var(--color--secondary)";
		change();
		expect(send).not.toHaveBeenCalled();
		// Mutation after the event must not change the queued snapshot.
		state.componentSchemes.dark.__general__.footer[
			"--c-footer--color--surface"
		] = "var(--color--primary)";
		events.active();
		expect(send).toHaveBeenCalledTimes(1);
		expect(
			send.mock.calls[0][1].componentSchemes.dark.__general__.footer[
				"--c-footer--color--surface"
			],
		).toBe("var(--color--secondary)");
		events.active();
		expect(send).toHaveBeenCalledTimes(1);
	});
});
