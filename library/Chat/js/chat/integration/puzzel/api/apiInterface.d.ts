interface StartConversationInterface {
    execute(conversationId: string): Promise<RouteConversationResponse>;
}

interface SendUserJoinedConversationInterface {
    execute(conversationId: string): Promise<void>;
}

interface ListenForMetaDataInterface {
    execute(conversationId: string): Promise<any>;
    listen(conversationId: string, callback: (data: any) => void): Promise<void>;
}

interface ListenForConversationInterface {
    execute(conversationId: string): Promise<any>;
    listen(conversationId: string, callback: (data: any) => void): Promise<void>;
}

interface SendUserMessageInterface {
    execute(conversationId: string, message: string): Promise<void>;
}