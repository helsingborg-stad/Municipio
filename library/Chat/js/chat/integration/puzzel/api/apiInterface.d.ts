interface StartConversationInterface {
    execute(conversationId: string): Promise<RouteConversationResponse>;
}

interface SendMetadataInterface {
    execute(conversationId: string, event: string): Promise<void>;
}

interface ListenForMetaDataInterface {
    execute(conversationId: string): Promise<PuzzelMetaDataEvent[]>;
    listen(conversationId: string, callback: (data: PuzzelMetaDataEvent) => void): Promise<void>;
    stopListening(conversationId: string): void;
}

interface ListenForConversationInterface {
    execute(conversationId: string): Promise<PuzzelConversationEvent[]>;
    listen(conversationId: string, callback: (data: PuzzelConversationEvent) => void): Promise<void>;
    stopListening(conversationId: string): void;
}

interface SendUserMessageInterface {
    execute(conversationId: string, message: string): Promise<void>;
}

interface SendUserTypingInterface {
    execute(conversationId: string): Promise<void>;
}

interface GetConversationStatusInterface {
    execute(conversationId: string): Promise<PuzzelConversationStatusResponse[]>;
}