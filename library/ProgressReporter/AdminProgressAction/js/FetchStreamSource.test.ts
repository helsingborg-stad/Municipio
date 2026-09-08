/**
 * @jest-environment jsdom
 */

import { TextDecoder as NodeTextDecoder } from "util";
import FetchStreamSource from "./FetchStreamSource";
import { ProgressStreamCallbacks } from "./ProgressStreamSource";

const encode = (value: string): Uint8Array =>
	Uint8Array.from(Array.from(value).map((character) => character.charCodeAt(0)));

describe("FetchStreamSource", () => {
	beforeEach(() => {
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

	it("posts the nonce and emits streamed events", async () => {
		const read = jest
			.fn()
			.mockResolvedValueOnce({
				done: false,
				value: encode(
					"event: message\ndata: Indexing 1/2\n\n" +
						"event: progress\ndata: 50\n\n" +
						"event: finish\ndata: Complete\n\n",
				),
			})
			.mockResolvedValueOnce({ done: true, value: undefined });
		const cancel = jest.fn().mockResolvedValue(undefined);
		const fetchMock = jest.mocked(globalThis.fetch).mockResolvedValue({
			ok: true,
			body: { getReader: () => ({ read, cancel }) },
		} as unknown as Response);
		const callbacks = createCallbacks();

		new FetchStreamSource("https://example.test/admin-ajax.php?action=build", "nonce-value").start(callbacks);
		await flushPromises();

		expect(fetchMock).toHaveBeenCalledWith(
			"https://example.test/admin-ajax.php?action=build",
			expect.objectContaining({
				method: "POST",
				body: expect.any(URLSearchParams),
			}),
		);
		expect(fetchMock.mock.calls[0][1]?.body?.toString()).toBe("_ajax_nonce=nonce-value");
		expect(callbacks.onMessage).toHaveBeenCalledWith("Indexing 1/2");
		expect(callbacks.onProgress).toHaveBeenCalledWith(50);
		expect(callbacks.onFinish).toHaveBeenCalledWith("Complete");
		expect(cancel).toHaveBeenCalledTimes(1);
	});

	it("reports request failures", async () => {
		jest.mocked(globalThis.fetch).mockRejectedValue(new Error("Network failure"));
		const callbacks = createCallbacks();

		new FetchStreamSource("https://example.test/admin-ajax.php", "nonce-value").start(callbacks);
		await flushPromises();

		expect(callbacks.onError).toHaveBeenCalledTimes(1);
	});
});

function createCallbacks(): jest.Mocked<ProgressStreamCallbacks> {
	return {
		onMessage: jest.fn(),
		onProgress: jest.fn(),
		onFinish: jest.fn(),
		onError: jest.fn(),
	};
}

async function flushPromises(): Promise<void> {
	await new Promise((resolve) => setTimeout(resolve, 0));
}