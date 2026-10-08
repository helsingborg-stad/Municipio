/**
 * AI provider HTTP endpoint path.
 */
export const AI_CHAT_API_ENDPOINT = "municipio/v1/chat";

/**
 * SSE content type expected from AI chat endpoint responses.
 */
export const AI_SSE_CONTENT_TYPE = "text/event-stream";

/**
 * AI-specific SSE event names used by the current backend/frontend protocol.
 */
export const AI_SSE_EVENT = {
	FIRST_CHUNK: "first_chunk",
	TEXT: "text",
	TOOL_CALL: "tool_call",
	ERROR: "error",
} as const;
