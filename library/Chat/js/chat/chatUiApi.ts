export type UserMessageHandler = (message: string) => void | Promise<void>;

export interface ChatUiApi {
	addMessage(content: string, isReply: boolean): any;
	addPendingMessage(): any;
	updateMessage(content: string, messageInstance: any): void;
	removeMessage(messageInstance: any): void;
	enableInput(): void;
	disableInput(): void;
	clearMessages(): void;
	getMessages(): any[];
	attachFeedback(messageInstance: any): void;
}
