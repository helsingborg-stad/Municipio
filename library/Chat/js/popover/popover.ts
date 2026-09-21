class Popover {
	private readonly breakpointQuery: MediaQueryList;
	private previousState: boolean | null = null;
    private coverRanOnce: boolean = false;

	public constructor(private readonly popoveDetail: any, private readonly chatContainer: HTMLElement) {
		this.breakpointQuery = window.matchMedia("(max-width: 768px)");

		this.breakpointQuery.addEventListener(
			"change",
			this.handleBreakpointChange,
		);

		this.handleBreakpointChange(this.breakpointQuery);
	}

	private handleBreakpointChange = (
		breakpoint: MediaQueryList | MediaQueryListEvent,
	): void => {
		const currentState = breakpoint.matches;

		if (currentState === this.previousState) {
			return;
		}

		this.previousState = currentState;

		if (!currentState && this.coverRanOnce) {
			this.popoveDetail.popover.setCover(false);
			this.chatContainer.style.width = "";
			this.chatContainer.style.height = "";
		} else {
			this.coverRanOnce = true;
			this.popoveDetail.popover.setCover(true);
			this.chatContainer.style.width = "100%";
			this.chatContainer.style.height = "100%";
		}
	};
}

export default Popover;
