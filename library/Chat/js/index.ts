import { createChatSession } from "./ChatSession";
import { createChatUtils } from "./ChatUtils";
import { initGlobalChat } from "./ui/GlobalChatUI";
import { initPageModuleChats } from "./ui/PageModuleChatUI";

document.addEventListener("DOMContentLoaded", () => {
	const utils = createChatUtils();
	const deps: ChatUIDependencies = {
		utils,
		sessionFactory: createChatSession,
		apiRoot: wpApiSettings.root,
		strings: municipioChatStrings,
	};

	initGlobalChat(deps);
	initPageModuleChats(deps);
});
