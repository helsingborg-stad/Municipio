class FeedbackApi {
    constructor(private apiRoot: string) {}

    public postStat(type: 'like' | 'dislike' | 'message'): void {
        fetch(this.apiRoot + 'municipio/v1/chat/stats', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ type }),
        }).catch(() => {});
    }
}

export default FeedbackApi;
