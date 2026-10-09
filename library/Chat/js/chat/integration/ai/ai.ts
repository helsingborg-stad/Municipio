import MarkdownIt from "markdown-it";

class Ai implements ChatIntegration {
    public constructor(
        private readonly sessionFactory: ChatSessionFactory,
        private readonly markdownParser: MarkdownIt,
        private readonly feedbackFactory: FeedbackFactoryInterface,
        private readonly feedbackApi: FeedbackApiInterface,
        private readonly assistantName: string | null = null,
        private readonly persistSession: boolean = true,
    ) {
        
    }

    public setup() {

    }
}

export default Ai;