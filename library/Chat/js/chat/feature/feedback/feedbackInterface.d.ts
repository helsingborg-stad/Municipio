interface FeedbackFactoryInterface {
    create(messageInstance: any): FeedbackInterface;
}

interface FeedbackInterface {
}

type FeedbackStatType = 'like' | 'dislike' | 'message' | 'unlike' | 'undislike';
interface FeedbackApiInterface {
    postStat(type: FeedbackStatType): void;
}