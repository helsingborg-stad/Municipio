class GreetingPhrase {
    constructor(private chat: any, private greetingPhrase: string) {
        if (this.chat.getMessages().length === 0) {
            this.addGreetingPhrase();
        }
    }

    private addGreetingPhrase() {
        this.chat.addMessage(this.greetingPhrase, true);
    }
}

export default GreetingPhrase;