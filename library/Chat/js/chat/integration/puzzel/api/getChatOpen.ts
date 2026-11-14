import { throwErrorIfResponseNotOk } from "./helper/throwError";

class GetChatOpen implements GetChatOpenInterface {
    public constructor(
        private puzzelConfig: PuzzelConfigInterface,
        private puzzelAuthenticator: PuzzelAuthenticatorInterface,
    ) {
    }

    public async execute(): Promise<boolean> {
        const accessToken = await this.puzzelAuthenticator.getAccessToken();
        const url = `${this.puzzelConfig.getCommunicationUrl()}/api/time/${encodeURIComponent(this.puzzelConfig.getTimeId())}`;

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