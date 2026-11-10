class GreetingPhrase implements GreetingPhraseInterface {
	constructor(private chat: any, private greetingPhrase: string) {
	}

	public addGreetingPhrase() {
		this.chat.addMessage(this.greetingPhrase, true);
	}
}

export default GreetingPhrase;