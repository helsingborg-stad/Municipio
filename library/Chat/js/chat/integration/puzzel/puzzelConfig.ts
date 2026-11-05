class PuzzelConfig implements PuzzelConfigInterface {
    public constructor(
        private customerId: string,
        private chatQueueKey: string,
        private clientId: string,
        private visualQueueId: string,
        private timeId: string,
        private name: string,
        private authenticationUrl: string = 'https://app-consumeridp.puzzel.com',
        private communicationUrl: string = 'https://app-commsrv.puzzel.com',
        private contentUploadUrl: string = 'https://app-contentupload.puzzel.com',
    ) {}

    public getVisualQueueId(): string {
        return this.visualQueueId;
    }

    public getTimeId(): string {
        return this.timeId;
    }

    public getClientId(): string {
        return this.clientId;
    }

    public getAuthenticationUrl(): string {
        return this.authenticationUrl;
    }

    public getCommunicationUrl(): string {
        return this.communicationUrl;
    }

    public getContentUploadUrl(): string {
        return this.contentUploadUrl;
    }

    public getCustomerId(): string {
        return this.customerId;
    }

    public getChatQueueKey(): string {
        return this.chatQueueKey;
    }

    public getName(): string {
        return this.name;
    }
}

export default PuzzelConfig;