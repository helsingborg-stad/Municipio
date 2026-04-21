const SSE_CONTENT_TYPE = "text/event-stream";

const extractErrorMessage = (rawBody: string): string => {
	try {
		const parsed = JSON.parse(rawBody) as {
			message?: unknown;
			error?: unknown;
		};
		if (typeof parsed.message === "string" && parsed.message.length > 0) {
			return parsed.message;
		}
		if (typeof parsed.error === "string" && parsed.error.length > 0) {
			return parsed.error;
		}
	} catch {
		// Fall through to raw body
	}
	return rawBody;
};

export const createChatSession: CreateChatSession = ({
	assistantId,
	apiRoot,
	strings,
	events,
	utils,
	fetchImpl,
}): ChatSession => {
	const doFetch = fetchImpl ?? fetch.bind(globalThis);
	let sessionId: string | null = null;

	const dispatchSseData = (
		eventType: string,
		dataPart: string,
		accumRef: { value: string },
	): void => {
		const data = JSON.parse(dataPart);
		switch (eventType) {
			case "first_chunk":
				sessionId = data.session_id;
				events.onStatusTextChanged(strings.writing);
				break;
			case "text":
				accumRef.value += data.answer;
				events.onAssistantMessageText(utils.renderMarkdown(accumRef.value));
				events.onStatusTextChanged(strings.writing);
				break;
			case "tool_call":
				events.onStatusTextChanged(strings.usingTools);
				break;
			case "error":
				throw new Error(`Chat error: ${dataPart}`);
		}
	};

	const readStream = async (res: Response): Promise<void> => {
		if (!res.body) {
			throw new Error("Response has no body");
		}
		const reader = res.body.getReader();
		const decoder = new TextDecoder("utf-8");
		let buffer = "";
		const accumRef = { value: "" };
		let eventType = "";

		const processLines = (lines: string[]): void => {
			for (const line of lines) {
				if (line === "") {
					eventType = "";
					continue;
				}
				if (line.startsWith("event: ")) {
					eventType = line.slice(7).trim();
				} else if (line.startsWith("data: ")) {
					const dataPart = line.slice(6).trim();
					try {
						dispatchSseData(eventType, dataPart, accumRef);
					} catch (e) {
						throw new Error(
							`Failed to handle SSE line: ${dataPart}. Error: ${e}`,
						);
					}
				}
			}
		};

		while (true) {
			const { done, value } = await reader.read();
			if (done) break;
			buffer += decoder.decode(value, { stream: true });
			const lines = buffer.split("\n");
			buffer = lines.pop() ?? "";
			processLines(lines);
		}
	};

	const ensureSseResponse = async (res: Response): Promise<void> => {
		const contentType = res.headers.get("Content-Type") ?? "";
		if (res.ok && contentType.includes(SSE_CONTENT_TYPE)) {
			return;
		}
		const body = await res.text();
		throw new Error(extractErrorMessage(body));
	};

	return {
		async ask(message: string): Promise<void> {
			try {
				const messageText = message.trim();
				if (messageText.length === 0) {
					return;
				}

				events.onStateChanged({ isMessagePending: true });
				events.onMessageAdded("user");
				events.onUserMessageText(messageText);
				events.onStatusTextChanged(strings.sending);

				events.onMessageAdded("assistant");

				const res = await doFetch(`${apiRoot}municipio/v1/chat`, {
					method: "POST",
					headers: { "Content-Type": "application/json" },
					body: JSON.stringify({
						message: messageText,
						session_id: sessionId,
						assistant_id: assistantId,
					}),
				});

				await ensureSseResponse(res);
				await readStream(res);

				events.onStatusTextChanged(null);
			} catch (error) {
				console.error("Error during chat", error);
				events.onAssistantMessageText(strings.error);
			} finally {
				events.onStateChanged({ isMessagePending: false });
			}
		},

		clear(): void {
			sessionId = null;
			events.onAssistantMessageText("");
			events.onStatusTextChanged(null);
			events.onStateChanged({ isMessagePending: false });
		},
	};
};
