class PuzzelConfig implements PuzzelConfigInterface {
    public constructor(
        private authenticationUrl: string = 'https://app-consumeridp.puzzel.com',
        private communicationUrl: string = 'https://app-commsrv.puzzel.com',
        private contentUploadUrl: string = 'https://app-contentupload.puzzel.com',
        private customerId: string = '462568',
        private chatQueueKey: string = 'q_chat_test',
        private clientId: string = 'oneplatform_engage'
    ) {}

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
}

export default PuzzelConfig;