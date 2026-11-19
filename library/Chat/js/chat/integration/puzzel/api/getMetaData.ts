import PollingEventListener from "./helper/pollingEventListener";
import { throwErrorIfResponseNotOk } from "./helper/throwError";

class GetMetaData implements ListenForMetaDataInterface {
    private pollingEventListener = new PollingEventListener<PuzzelMetaDataEvent>();

    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
    ) {
    }

    public async execute(conversationId: string): Promise<PuzzelMetaDataEvent[]> {
        const accessToken = await this.puzzelAuthenticator.getAccessToken();
        const metadataUrl = `${this.puzzelConfig.getCommunicationUrl()}/api/metadata/${encodeURIComponent(conversationId)}`;

        const response = await fetch(metadataUrl, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${accessToken}`,
            },
        });

        await throwErrorIfResponseNotOk(response, "Failed to listen for meta data");

        return response.json();
    }

    public async listen(conversationId: string, callback: (data: PuzzelMetaDataEvent) => void): Promise<void> {
        await this.pollingEventListener.listen(
            conversationId,
            callback,
            this.execute.bind(this),
            1000,
            "Failed to poll metadata",
        );
    }

    public stopListening(conversationId: string): void {
        this.pollingEventListener.stopListening(conversationId);
    }
}

export default GetMetaData;