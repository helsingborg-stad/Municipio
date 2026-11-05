interface ChatActionFactoryInterface {
    addMinimize(): ChatAction;
    addClear(): ChatAction;
    addClose(): ChatAction;
}

interface ChatAction {
    add(): void;
    remove(): void;
    addListener(listener: (event: Event) => void): void;
}