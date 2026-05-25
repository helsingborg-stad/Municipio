import { TextDecoder, TextEncoder } from "util";

if (typeof globalThis.TextEncoder === "undefined") {
	(globalThis as unknown as { TextEncoder: typeof TextEncoder }).TextEncoder =
		TextEncoder;
}
if (typeof globalThis.TextDecoder === "undefined") {
	(globalThis as unknown as { TextDecoder: typeof TextDecoder }).TextDecoder =
		TextDecoder;
}

import { createChatSession } from "./ChatSession";

const encoder = new TextEncoder();

const makeSseResponse = (chunks: string[]): Response => {
	let i = 0;
	const body = {
		getReader: () => ({
			read: async () => {
				if (i >= chunks.length) return { done: true, value: undefined };
				const value = encoder.encode(chunks[i++]);
				return { done: false, value };
			},
		}),
	} as unknown as ReadableStream<Uint8Array>;

	return {
		ok: true,
		status: 200,
		headers: {
			get: (name: string) =>
				name.toLowerCase() === "content-type" ? "text/event-stream" : null,
		},
		body,
		text: async () => "",
	} as unknown as Response;
};

const makeJsonErrorResponse = (status: number, payload: unknown): Response => {
	const text = JSON.stringify(payload);
	return {
		ok: status >= 200 && status < 300,
		status,
		headers: {
			get: (name: string) =>
				name.toLowerCase() === "content-type" ? "application/json" : null,
		},
		body: null,
		text: async () => text,
	} as unknown as Response;
};

const makeStrings = (): MunicipioChatStrings => ({
	sending: "sending…",
	writing: "writing…",
	usingTools: "tools…",
	error: "ERR",
});

const makeEventsRecorder = () => {
	const calls: Array<[string, unknown]> = [];
	const events: ChatSessionEvents = {
		onMessageAdded: (role) => calls.push(["onMessageAdded", role]),
		onUserMessageText: (text) => calls.push(["onUserMessageText", text]),
		onAssistantMessageText: (html) =>
			calls.push(["onAssistantMessageText", html]),
		onStateChanged: (state) => calls.push(["onStateChanged", state]),
		onStatusTextChanged: (status) => calls.push(["onStatusTextChanged", status]),
	};
	return { events, calls };
};

describe("ChatSession", () => {
	const strings = makeStrings();
	const utils: Pick<ChatUtilsApi, "renderMarkdown"> = {
		renderMarkdown: (text: string) => `<md>${text}</md>`,
	};

	it("emits user text via onUserMessageText (plain) and assistant via onAssistantMessageText", async () => {
		const { events, calls } = makeEventsRecorder();
		const fetchMock = jest.fn(async () =>
			makeSseResponse([
				'event: first_chunk\ndata: {"session_id":"abc"}\n\n',
				'event: text\ndata: {"answer":"Hello"}\n\n',
			]),
		);

		const session = createChatSession({
			assistantId: null,
			apiRoot: "/api/",
			strings,
			events,
			utils,
			fetchImpl: fetchMock as unknown as typeof fetch,
		});

		await session.ask("<b>hi</b>");

		expect(calls).toContainEqual(["onUserMessageText", "<b>hi</b>"]);
		expect(calls).toContainEqual([
			"onAssistantMessageText",
			"<md>Hello</md>",
		]);
	});

	it("parses SSE events split across chunk boundaries", async () => {
		const { events, calls } = makeEventsRecorder();
		const fetchMock = jest.fn(async () =>
			makeSseResponse([
				'event: first_chunk\ndata: {"session_id":"s1"}\n\nevent: text\nda',
				'ta: {"answer":"Hel"}\n\n',
				'event: text\ndata: {"answer":"lo"}\n\n',
			]),
		);

		const session = createChatSession({
			assistantId: null,
			apiRoot: "/api/",
			strings,
			events,
			utils,
			fetchImpl: fetchMock as unknown as typeof fetch,
		});

		await session.ask("hi");

		const assistantCalls = calls.filter(
			([name]) => name === "onAssistantMessageText",
		);
		expect(assistantCalls).toEqual([
			["onAssistantMessageText", "<md>Hel</md>"],
			["onAssistantMessageText", "<md>Hello</md>"],
		]);
	});

	it("sends subsequent messages with the session_id from first_chunk", async () => {
		const { events } = makeEventsRecorder();
		const fetchMock = jest
			.fn()
			.mockResolvedValueOnce(
				makeSseResponse([
					'event: first_chunk\ndata: {"session_id":"abc"}\n\nevent: text\ndata: {"answer":"Hi"}\n\n',
				]),
			)
			.mockResolvedValueOnce(
				makeSseResponse([
					'event: first_chunk\ndata: {"session_id":"abc"}\n\nevent: text\ndata: {"answer":"Hi"}\n\n',
				]),
			);

		const session = createChatSession({
			assistantId: "asst",
			apiRoot: "/api/",
			strings,
			events,
			utils,
			fetchImpl: fetchMock as unknown as typeof fetch,
		});

		await session.ask("first");
		await session.ask("second");

		const secondBody = JSON.parse(fetchMock.mock.calls[1][1].body);
		expect(secondBody.session_id).toBe("abc");
	});

	it("emits the error string when response is not SSE", async () => {
		const { events, calls } = makeEventsRecorder();
		const fetchMock = jest.fn(async () =>
			makeJsonErrorResponse(400, { message: "bad request" }),
		);

		const consoleError = jest
			.spyOn(console, "error")
			.mockImplementation(() => {});

		const session = createChatSession({
			assistantId: null,
			apiRoot: "/api/",
			strings,
			events,
			utils,
			fetchImpl: fetchMock as unknown as typeof fetch,
		});

		await session.ask("hi");

		expect(calls).toContainEqual(["onAssistantMessageText", "ERR"]);
		expect(calls).toContainEqual([
			"onStateChanged",
			{ isMessagePending: false },
		]);

		consoleError.mockRestore();
	});

	it("clear() resets session_id so next ask sends null", async () => {
		const { events } = makeEventsRecorder();
		const fetchMock = jest
			.fn()
			.mockResolvedValueOnce(
				makeSseResponse([
					'event: first_chunk\ndata: {"session_id":"abc"}\n\nevent: text\ndata: {"answer":"Hi"}\n\n',
				]),
			)
			.mockResolvedValueOnce(
				makeSseResponse([
					'event: first_chunk\ndata: {"session_id":"xyz"}\n\nevent: text\ndata: {"answer":"Hi"}\n\n',
				]),
			);

		const session = createChatSession({
			assistantId: null,
			apiRoot: "/api/",
			strings,
			events,
			utils,
			fetchImpl: fetchMock as unknown as typeof fetch,
		});

		await session.ask("first");
		session.clear();
		await session.ask("second");

		const secondBody = JSON.parse(fetchMock.mock.calls[1][1].body);
		expect(secondBody.session_id).toBeNull();
	});

	it("ask() does not hit the network for empty messages", async () => {
		const { events, calls } = makeEventsRecorder();
		const fetchMock = jest.fn();

		const session = createChatSession({
			assistantId: null,
			apiRoot: "/api/",
			strings,
			events,
			utils,
			fetchImpl: fetchMock as unknown as typeof fetch,
		});

		await session.ask("   ");

		expect(fetchMock).not.toHaveBeenCalled();
		expect(
			calls.filter(([name]) => name === "onMessageAdded"),
		).toEqual([]);
		expect(
			calls.filter(([name]) => name === "onUserMessageText"),
		).toEqual([]);
	});
});
