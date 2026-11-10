describe("Design Builder Customizer bridge", () => {
	it("sends independent Default, Dark and Contrast changes from replaced elements", () => {
		const send = jest.fn();
		Object.assign(globalThis, {
			jQuery: (ready: () => void) => ready(),
			wp: { customize: { preview: { send } } },
		});
		jest.isolateModules(() => require("./designbuilderPreview"));

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
			expect(send).toHaveBeenLastCalledWith("tokens:update", state);
		}
		state._dark["--color--primary"] = "#abcdef";
		expect(send.mock.calls[1][1]._dark["--color--primary"]).toBe("#123456");
		expect(send).toHaveBeenCalledTimes(3);
		document.body.replaceChildren();
	});
});
