class AiFeedbackApi {
	constructor(private apiRoot: string) {}

	public postStat(type: "like" | "dislike" | "message" | "unlike" | "undislike"): void {
		fetch(this.apiRoot + "municipio/v1/chat/stats", {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({ type }),
		}).catch(() => {
			console.error(`[AiFeedbackApi] Failed to post stat: ${type}`);
		});
	}
}

export default AiFeedbackApi;
