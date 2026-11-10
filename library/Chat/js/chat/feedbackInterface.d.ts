interface FeedbackFactoryInterface {
    create(messageInstance: any): void;
}

interface FeedbackInterface {
    submit(feedbackData: any): void;
}