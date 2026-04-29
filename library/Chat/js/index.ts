import Chat from "./chat";
import { ChatSessionFactory } from "./ChatSessionFactory";
import ClearButton from "./clearButton";

document.addEventListener("chat:initialized", (e: any) => {
	const chat = e.detail.chat;
	const clearButtonElement = e.detail.chat.getElement().querySelector("[data-js-chat-clear]") as HTMLElement;

	if (clearButtonElement) {
		new ClearButton(clearButtonElement, chat);
	}

	new Chat(
		new ChatSessionFactory(wpApiSettings.root),
		chat,
	).init();
});