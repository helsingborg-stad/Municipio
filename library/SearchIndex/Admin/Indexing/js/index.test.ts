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
		expect(button.nextElementSibling?.querySelector("progress")).not.toBeNull();
		expect(document.querySelector('[role="status"]')?.textContent).toBe(
			"Search indexing complete.",
		);
		expect(document.querySelector("progress")?.value).toBe(100);
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

		expect(document.querySelector('[role="status"]')?.textContent).toBe(
			"Localized error",
		);
		expect(button.disabled).toBe(false);
	});
});

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
