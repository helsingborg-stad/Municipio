import { SESSION_ID_KEY } from "../../../enums";
import { readJsonObject, writeJsonObject } from "../../../helper/jsonStorage";
import { PUZZEL_CONVERSATION_MAX_IDLE_MS } from "../enums";

const CHAT_BUBBLE_NAME = "global-chat-bubble";

class ConversationStorageManager implements ConversationStorageManagerInterface {
    private localStorageData: PuzzelLocalStorageData | null = null;

    public getConversationId(): string | null {
        return this.getConversationData()?.conversationId ?? null;
    }

    public getAccessToken(): string | null {
        return this.getAuthenticationData()?.access_token ?? null;
    }

    public getConversationData(): RouteConversationResponse | null {
        return this.getLocalStorage()?.conversationData ?? null;
    }

    public getAuthenticationData(): AuthenticationData | null {
        return this.getLocalStorage()?.authenticationData ?? null;
    }

    public clearLocalStorage(): void {
        this.localStorageData = null;
        const data = readJsonObject(SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS);

        if (Object.keys(data).length === 0) {
            return;
        }

        delete data.puzzel;
        writeJsonObject(SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS, data);

        this.persistBubbleInitializeState(false);
    }

    public getLocalStorage(): PuzzelLocalStorageData | null {
        if (this.localStorageData) {
            return this.localStorageData;
        }

        const data = readJsonObject(SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS);
        const puzzelData: PuzzelLocalStorageData | null = data.puzzel ?? null;

        if (puzzelData && this.isExpired(puzzelData)) {
            this.clearLocalStorage();
            return null;
        }

        this.localStorageData = puzzelData;
        return this.localStorageData;
    }

    /**
     * Marks the stored conversation as active now, so it does not expire.
     */
    public updateLastActivity(): void {
        const data = readJsonObject(SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS);

        if (!data.puzzel) {
            return;
        }

        data.puzzel.lastActivityAt = Date.now();
        writeJsonObject(SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS, data);

        if (this.localStorageData) {
            this.localStorageData.lastActivityAt = data.puzzel.lastActivityAt;
        }
    }

    /**
     * A conversation without a timestamp predates this check and is treated as expired.
     */
    private isExpired(puzzelData: PuzzelLocalStorageData): boolean {
        const lastActivityAt = puzzelData.lastActivityAt;

        if (typeof lastActivityAt !== "number") {
            return true;
        }

        return Date.now() - lastActivityAt > PUZZEL_CONVERSATION_MAX_IDLE_MS;
    }

    public setLocalStorage(conversationResponse: RouteConversationResponse, authentication: AuthenticationData): void {
        this.localStorageData = null;

        const data = readJsonObject(SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS);

        data.puzzel = {
            ...data.puzzel,
            conversationData: conversationResponse,
            authenticationData: authentication,
            lastActivityAt: Date.now(),
        };

        writeJsonObject(SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS, data);

        this.persistBubbleInitializeState(true);
    }

    private persistBubbleInitializeState(shouldInitialize: boolean): void {
        const initializeStateData = readJsonObject(SESSION_ID_KEY.GLOBAL_CHAT_BUBBLE_INITIALIZE_STATES);
        initializeStateData[CHAT_BUBBLE_NAME] = shouldInitialize;
        writeJsonObject(SESSION_ID_KEY.GLOBAL_CHAT_BUBBLE_INITIALIZE_STATES, initializeStateData);
    }

}

export default ConversationStorageManager;