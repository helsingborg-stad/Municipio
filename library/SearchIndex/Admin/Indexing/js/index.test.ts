/**
 * @jest-environment jsdom
 */

import { TextDecoder as NodeTextDecoder } from "util";
import { SearchIndexingClient } from "./index";

const encode = (value: string): Uint8Array =>
	Uint8Array.from(
		Array.from(value).map((character) => character.charCodeAt(0)),
	);

describe("SearchIndexingClient", () => {
	beforeEach(() => {
		document.body.innerHTML = "";
		globalThis.fetch = jest.fn() as jest.MockedFunction<typeof fetch>;
		Object.defineProperty(globalThis, "TextDecoder", {
			value: NodeTextDecoder,
			configurable: true,
		});
	});

	afterEach(() => {
		delete (globalThis as { fetch?: typeof fetch }).fetch;
		delete (globalThis as { TextDecoder?: typeof TextDecoder }).TextDecoder;
	});

	it("posts the nonce and displays streamed progress", async () => {
		const read = jest
			.fn()
			.mockResolvedValueOnce({
				done: false,
				value: encode(
					"event: message\ndata: Indexing 1/2\n\n" +
						"event: progress\ndata: 50\n\n" +
						"event: finish\ndata: Search indexing complete.\n\n",
				),
			})
			.mockResolvedValueOnce({ done: true, value: undefined });
		const cancel = jest.fn().mockResolvedValue(undefined);
		const fetchMock = jest.mocked(globalThis.fetch).mockResolvedValue({
			ok: true,
			body: { getReader: () => ({ read, cancel }) },
		} as unknown as Response);
		const button = createButton();
		const client = new SearchIndexingClient(button);

		await client.start();

		const request = fetchMock.mock.calls[0];
		staticAssert(request[0] === "https://example.test/wp-admin/admin-ajax.php");
		expect(request[1]?.method).toBe("POST");
		expect(request[1]?.body?.toString()).toContain(
			"action=municipio_search_index_build",
		);
		expect(request[1]?.body?.toString()).toContain("_ajax_nonce=nonce-value");
		const progressBar = getProgressBar();
		expect(button.nextElementSibling).toBe(progressBar);
		expect(progressBar.style.display).toBe("block");
		expect(progressBar.style.marginTop).toBe("8px");
		expect(progressBar.getAttribute("label")).toBe("Search indexing complete.");
		expect(progressBar.getAttribute("progress")).toBe("100");
		expect(progressBar.shadowRoot?.querySelector("progress")).not.toBeNull();
		expect(button.disabled).toBe(false);
		expect(cancel).toHaveBeenCalledTimes(1);
	});

	it("displays a localized error and re-enables the button", async () => {
		jest
			.mocked(globalThis.fetch)
			.mockRejectedValue(new Error("Network failure"));
		const button = createButton();
		const client = new SearchIndexingClient(button);

		await client.start();

		const progressBar = getProgressBar();
		expect(progressBar.getAttribute("label")).toBe("Localized error");
		expect(progressBar.getAttribute("progress")).toBe("100");
		expect(button.disabled).toBe(false);
	});
});

function getProgressBar(): HTMLElement {
	const progressBar = document.querySelector<HTMLElement>(
		'progress-bar-with-label[role="status"]',
	);
	staticAssert(progressBar !== null);
	return progressBar;
}

function createButton(): HTMLButtonElement {
	const button = document.createElement("button");
	button.dataset.endpoint = "https://example.test/wp-admin/admin-ajax.php";
	button.dataset.nonce = "nonce-value";
	button.dataset.errorMessage = "Localized error";
	document.body.append(button);
	return button;
}

function staticAssert(condition: boolean): asserts condition {
	expect(condition).toBe(true);
}
