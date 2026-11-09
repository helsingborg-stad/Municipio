interface ChatBubbleOpenStateChat {
	getElement?: () => HTMLElement | null;
}

interface ChatBubbleOpenState {
	isOpen: boolean;
	updatedAt: number;
}

interface ChatBubbleOpenStates {
	[chatBubbleName: string]: ChatBubbleOpenState | boolean;
}
