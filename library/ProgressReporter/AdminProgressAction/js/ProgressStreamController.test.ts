/**
 * @jest-environment jsdom
 */

import { IProgressBar } from "./IProgressBar";
import ProgressStreamController from "./ProgressStreamController";
import {
	ProgressStreamCallbacks,
	ProgressStreamSource,
} from "./ProgressStreamSource";

describe("ProgressStreamController", () => {
	beforeEach(() => {
		document.body.innerHTML = "";
	});

	it("updates progress and restores matching triggers on finish", () => {
		const button = createButton();
		const duplicateButton = createButton();
		const source = new FakeProgressStreamSource();
		const progressBar = createProgressBar();
		const controller = new ProgressStreamController(
			button,
			button.dataset.jsProgressUrl ?? "",
			progressBar,
			source,
			"Localized error",
		);

		controller.start();
		source.callbacks?.onMessage("Indexing 1/2");
		source.callbacks?.onProgress(50);
		source.callbacks?.onFinish("Complete");

		expect(progressBar.show).toHaveBeenCalledTimes(1);
		expect(progressBar.update).toHaveBeenCalledWith({ label: "Indexing 1/2", value: null });
		expect(progressBar.update).toHaveBeenCalledWith({ label: null, value: 50 });
		expect(progressBar.update).toHaveBeenCalledWith({ label: "Complete", value: 100 });
		expect(button.disabled).toBe(false);
		expect(duplicateButton.disabled).toBe(false);
		expect(source.stopped).toBe(true);
	});

	it("uses the configured error message", () => {
		const button = createButton();
		const source = new FakeProgressStreamSource();
		const progressBar = createProgressBar();
		new ProgressStreamController(
			button,
			button.dataset.jsProgressUrl ?? "",
			progressBar,
			source,
			"Localized error",
		).start();

		source.callbacks?.onError();

		expect(progressBar.update).toHaveBeenCalledWith({ label: "Localized error", value: 100 });
		expect(button.disabled).toBe(false);
	});
});

class FakeProgressStreamSource implements ProgressStreamSource {
	public callbacks: ProgressStreamCallbacks | null = null;
	public stopped = false;

	public start(callbacks: ProgressStreamCallbacks): void {
		this.callbacks = callbacks;
	}

	public stop(): void {
		this.stopped = true;
	}
}

function createButton(): HTMLButtonElement {
	const button = document.createElement("button");
	button.dataset.jsProgressUrl = "https://example.test/admin-ajax.php?action=build";
	document.body.append(button);
	return button;
}

function createProgressBar(): jest.Mocked<IProgressBar> {
	return {
		update: jest.fn(),
		show: jest.fn(),
		hide: jest.fn(),
	};
}