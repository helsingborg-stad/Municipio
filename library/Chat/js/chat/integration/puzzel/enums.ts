export enum PUZZEL_CHAT_STATUS {
    DISCONNECTED = "Disconnected",
    IN_QUEUE = "InQueue",
    ALLOCATED = "Allocated",
    CONNECTED = "Connected",
    FAILURE = "Failure"
}

/** Stored Puzzel conversations are discarded after this long without any messages. */
export const PUZZEL_CONVERSATION_MAX_IDLE_MS = 60 * 60 * 1000;
