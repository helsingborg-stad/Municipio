import { throwErrorIfResponseNotOk } from "./helper/throwError";

class GetQueueInfo implements GetQueueInfoInterface {
    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
    ) {
    }

    public async execute(): Promise<boolean> {
        const accessToken = await this.puzzelAuthenticator.getAccessToken();
        const url = `${this.puzzelConfig.getCommunicationUrl()}/api/queue/info/${encodeURIComponent(this.puzzelConfig.getVisualQueueId())}`;

        const response = await fetch(url, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${accessToken}`,
            },
        });

        await throwErrorIfResponseNotOk(response, "Failed to get queue info");

        const data: PuzzelQueueInfoResponse = await response.json();

        const queueOpenForWeb = data.openForWebAccess === true;
        const availableAgents = data.agentsReady ?? data.agentsLoggedIn ?? 0;

        return queueOpenForWeb && availableAgents > 0;
    }
}

export default GetQueueInfo;