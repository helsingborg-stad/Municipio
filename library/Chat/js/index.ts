import Chat from "./chat";
import { ChatSessionFactory } from "./ChatSessionFactory";

document.addEventListener("chat:initialized", (e: any) => {
	new Chat(
		new ChatSessionFactory(wpApiSettings.root),
		e.detail.chat,
	).init();
});