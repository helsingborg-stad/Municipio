import {
	applyHeaderLogoScrollAspectRatioValue,
	aspectRatioUpdateEvent,
	initializeHeaderLogoScrollAspectRatioSync,
} from "./HeaderLogoScrollAspectRatioSync";

describe("HeaderLogoScrollAspectRatioSync", () => {
	let settingValue = "";
	let readyCallback: (() => void) | null = null;
	let previewBindSpy: jest.Mock;
	let isReadyState = false;

	beforeEach(() => {
		settingValue = "";
		readyCallback = null;
		previewBindSpy = jest.fn();
		isReadyState = false;

		(global as typeof global & { wp: unknown }).wp = {
			customize: Object.assign(
				(
					settingId: string,
					callback: (setting: {
						get: () => string;
						set: (value: string) => void;
					}) => void,
				) => {
					if (settingId === "header_logo_scroll_aspect_ratio") {
						callback({
							get: () => settingValue,
							set: (value: string) => {
								settingValue = value;
							},
						});
					}
				},
				{
					bind: (eventName: string, callback: () => void) => {
						if (eventName === "ready") {
							readyCallback = callback;
						}
					},
					state: (id: string) => ({
						get: () => id === "ready" && isReadyState,
					}),
					previewer: {
						bind: previewBindSpy,
					},
				},
			),
		};
	});

	it("applies the incoming aspect ratio value to the hidden setting", () => {
		applyHeaderLogoScrollAspectRatioValue("3.703704");

		expect(settingValue).toBe("3.703704");
	});

	it("binds the preview update event and stores incoming values", () => {
		initializeHeaderLogoScrollAspectRatioSync();
		readyCallback?.();

		expect(previewBindSpy).toHaveBeenCalledWith(
			aspectRatioUpdateEvent,
			expect.any(Function),
		);

		const previewHandler = previewBindSpy.mock.calls[0][1] as (
			value: string,
		) => void;
		previewHandler("2.5");

		expect(settingValue).toBe("2.5");
	});

	it("binds immediately when customizer ready state is already true", () => {
		isReadyState = true;

		initializeHeaderLogoScrollAspectRatioSync();

		expect(previewBindSpy).toHaveBeenCalledWith(
			aspectRatioUpdateEvent,
			expect.any(Function),
		);
	});
});
