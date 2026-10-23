class GreetingPhrase implements GreetingPhraseInterface {
    constructor(private chat: any, private greetingPhrase: string) {
    }

    public getGreetingPhrase() {
        return this.greetingPhrase;
    }

    public addGreetingPhrase() {
        this.chat.addMessage(this.greetingPhrase, true);
    }
}

export default GreetingPhrase;