interface ChatActionFactoryInterface {
    addMinimize(): ChatAction;
    addClear(): ChatAction;
}

interface ChatAction {
    add(): void;
    remove(): void;
    addListener(listener: (event: Event) => void): void;
}