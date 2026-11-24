import { throwErrorIfResponseNotOk } from "./helper/throwError";

class GetChatOpen implements GetChatOpenInterface {
    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
    ) {
    }

    public async execute(): Promise<boolean | null> {
        const timeId = this.puzzelConfig.getTimeId();
        if (!timeId) {
            // A missing time ID means no opening-hours schedule is configured.
            return null;
        }

        const accessToken = await this.puzzelAuthenticator.getAccessToken();
        const url = `${this.puzzelConfig.getCommunicationUrl()}/api/time/${encodeURIComponent(timeId)}`;

        const response = await fetch(url, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${accessToken}`,
            },
        });

        await throwErrorIfResponseNotOk(response, "Failed to get chat open status");

        const data: PuzzelChatOpenResponse = await response.json();

        return data.status === "open";
    }
}

export default GetChatOpen;