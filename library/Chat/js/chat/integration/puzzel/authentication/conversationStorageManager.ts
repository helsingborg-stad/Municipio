import { SESSION_ID_KEY } from "../../../enums";
import { readJsonObject, writeJsonObject } from "../../../helper/jsonStorage";

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
        this.localStorageData = data.puzzel ?? null;
        return this.localStorageData;
    }

    public setLocalStorage(conversationResponse: RouteConversationResponse, authentication: AuthenticationData): void {
        this.localStorageData = null;

        const data = readJsonObject(SESSION_ID_KEY.GLOBAL_CHAT_SESSIONS);

        data.puzzel = {
            ...data.puzzel,
            conversationData: conversationResponse,
            authenticationData: authentication
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