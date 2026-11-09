export enum SESSION_ID_KEY {
    GLOBAL_CHAT_SESSIONS = "municipio:chat:global-chat:sessions",
    GLOBAL_CHAT_BUBBLE_OPEN_STATES = "municipio:chat:global-chat:bubble-open-states"
}

export enum CHAT_BUBBLE_DEFAULTS {
	NAME = "global-chat-bubble",
	DESKTOP_MIN_WIDTH = 769,
	OPEN_STATE_MAX_AGE_MS = 60 * 60 * 1000,
}