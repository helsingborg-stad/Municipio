class PollingEventListener<TEvent> {
    private callbacks: { [conversationId: string]: Array<(data: TEvent) => void> } = {};
    private currentEventLength: { [conversationId: string]: number } = {};
    private activeIntervals = new Map<string, ReturnType<typeof setInterval>>();

    public async listen(
        conversationId: string,
        callback: (data: TEvent) => void,
        execute: (conversationId: string) => Promise<TEvent[]>,
        pollIntervalMs: number,
        pollErrorMessage: string,
        alwaysNotify: boolean = false
    ): Promise<void> {
        if (!this.callbacks[conversationId]) {
            this.callbacks[conversationId] = [];
        }

        this.callbacks[conversationId].push(callback);

        const poll = async (): Promise<void> => {
            const data = await execute(conversationId);
            this.notifyOnEvents(conversationId, data, alwaysNotify);
        };

        if (this.activeIntervals.has(conversationId)) {
            clearInterval(this.activeIntervals.get(conversationId));
        }

        await poll();

        const intervalId = setInterval(() => {
            void poll().catch((error: unknown) => {
                console.error(pollErrorMessage, error);
            });
        }, pollIntervalMs);

        this.activeIntervals.set(conversationId, intervalId);
    }

    public stopListening(conversationId: string): void {
        const activeInterval = this.activeIntervals.get(conversationId);

        if (activeInterval) {
            clearInterval(activeInterval);
            this.activeIntervals.delete(conversationId);
        }

        delete this.callbacks[conversationId];
        delete this.currentEventLength[conversationId];
    }

    private notifyOnEvents(conversationId: string, data: TEvent[], alwaysNotify: boolean): void {
        if (!Array.isArray(data)) {
            return;
        }

        const callbacks = this.callbacks[conversationId];

        if (!callbacks || callbacks.length === 0) {
            return;
        }

        if (alwaysNotify) {
            const latestEvent = data[data.length - 1];

            if (!latestEvent) {
                this.currentEventLength[conversationId] = data.length;
                return;
            }

            this.currentEventLength[conversationId] = data.length;
            callbacks.forEach((cb) => cb(latestEvent));
            return;
        }

        const previousLength = this.currentEventLength[conversationId];

        if (previousLength === undefined) {
            this.currentEventLength[conversationId] = data.length;
            return;
        }

        if (data.length <= previousLength) {
            this.currentEventLength[conversationId] = data.length;
            return;
        }

        const newEvents = data.slice(previousLength);
        this.currentEventLength[conversationId] = data.length;

        newEvents.forEach((newEvent: TEvent) => {
            callbacks.forEach((cb) => cb(newEvent));
        });
    }
}

export default PollingEventListener;