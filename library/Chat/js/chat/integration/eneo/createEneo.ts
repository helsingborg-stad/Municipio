import MarkdownIt from "markdown-it";
import { EneoSessionFactory } from "./eneoSessionFactory";
import FeedbackApi from "../../feature/feedback/feedbackApi";
import FeedbackFactory from "../../feature/feedback/feedbackFactory";
import GreetingPhrase from "../../feature/greetingsPhrase/greetingPhrase";
import Eneo from "./eneo";

function createMarkdownParser(): MarkdownIt {
    const parser = new MarkdownIt({ html: false, linkify: false, typographer: false });

    parser.validateLink = (url: string): boolean => /^(https?:|mailto:|tel:|\/|#)/i.test(url);

    return parser;
}

export function createEneo(
    chat: any,
    uiManager: ChatUiManagerInterface,
    assistantName: string,
    greetingsPhrase: string | null,
    avatarUrl: string | null,
): ChatIntegrationInterface {
    const chatElement = chat.getElement() as HTMLElement;
    const feedbackTemplate = chatElement.querySelector("[data-js-chat-feedback]") as HTMLTemplateElement | null;
    const persistentAttribute = chatElement.getAttribute("data-js-chat-persistent");
    const markdownParser = createMarkdownParser();
    const eneoSessionFactory = new EneoSessionFactory(wpApiSettings.root);
    const feedbackApi = new FeedbackApi(wpApiSettings.root);
    const feedbackFactory = new FeedbackFactory(chat, feedbackTemplate, feedbackApi);

    chat.getMessages().forEach((message: any, index: number) => {
        if (!message.getIsReply()) {
            return;
        }

        if (index === 0 && greetingsPhrase === message.getContent()) {
            return;
        }

        feedbackFactory.create(message);
    });

    if (greetingsPhrase) {
        if (chat.getMessages().length === 0) {
            new GreetingPhrase(chat, greetingsPhrase).addGreetingPhrase();
        }
    }

    return new Eneo(
        eneoSessionFactory,
        chat,
        markdownParser,
        feedbackFactory,
        feedbackApi,
        uiManager,
        assistantName,
        avatarUrl,
        persistentAttribute !== null && persistentAttribute !== "false"
    );
}
