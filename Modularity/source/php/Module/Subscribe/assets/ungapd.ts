interface SubscriptionInterface {
    Contact: { Email: string },
    ConsentText: string,
    ListIds: string[],
    DoubleOptIn?: { Issue: { IssueId: string } },
    ConfirmationIssue?: { IssueId: string },
    SubscriptionConfirmedUrl?: string,
    SubscriptionFailedUrl?: string
}

class Ungpd {
    form: HTMLElement;
    id: string;
    notices: Node[] = [];
    successTemplate: HTMLTemplateElement | null = null;
    errorTemplate: HTMLTemplateElement | null = null;
    consent: HTMLInputElement;
    email: HTMLInputElement;

    constructor(form: HTMLElement) {
        this.form = form;
        this.id = form.getAttribute('id') || '';
        this.email = this.form.querySelector('input[name="email"]') as HTMLInputElement;
        this.consent = this.form.querySelector('input[name="user_consent"]') as HTMLInputElement;
        this.successTemplate = document.querySelector<HTMLTemplateElement>(`template[id="${this.id}-success"]`);
        this.errorTemplate = document.querySelector<HTMLTemplateElement>(`template[id="${this.id}-error"]`);

        this.setupEventListener();
    }

    private handleSuccess() {
        const successElement = this.successTemplate?.content.cloneNode(true);
        if (this.successTemplate?.parentNode) {
            this.notices.push(this.successTemplate.parentNode.appendChild(successElement!));
        }
    }

    private handleError(message: string | number) {
        const errorElement = this.errorTemplate?.content.cloneNode(true);
        const messageElement = (errorElement as HTMLElement)?.querySelector('.message');
        if (messageElement) {
            messageElement.innerHTML = message.toString();
        }
        if (this.errorTemplate?.parentNode) {
            this.notices.push(this.errorTemplate.parentNode.appendChild(errorElement!));
        }
    }

    private clearNotices() {
        this.notices.forEach(notice => notice.parentNode?.removeChild(notice));
    }
    
    private setupEventListener() {
        this.form.addEventListener("submit", this.handleFormSubmit.bind(this));
    }
    
    private async handleFormSubmit(event: Event) {
        event.preventDefault();
        this.clearNotices();
    
        const subscription = this.subscriptionData();
    
        try {
            await this.submitSubscription(subscription);
            this.handleSuccess();
        } catch (error: any) {
            this.handleError(error.message);
        }
    }
    
    private subscriptionData() {
        const listIds = this.form.getAttribute('data-js-ungpd-list-ids');
        const doubleOptInIssueId = this.form.getAttribute('data-js-ungpd-double-opt-in-issue-id');
        const confirmationIssueId = this.form.getAttribute('data-js-ungpd-confirmation-issue-id');
        const subscriptionConfirmedUrl = this.form.getAttribute('data-js-ungpd-subscription-confirmed-url');
        const subscriptionFailedUrl = this.form.getAttribute('data-js-ungpd-subscription-failed-url');
    
        const lists = listIds ? listIds.split(",").map((listId) => listId.trim()) : [];
    
        const subscription: SubscriptionInterface = {
            Contact: { Email: this.email.value },
            ConsentText: this.consent.value,
            ListIds: lists
        };
    
        if (doubleOptInIssueId) subscription.DoubleOptIn = { Issue: { IssueId: doubleOptInIssueId } };
        if (confirmationIssueId) subscription.ConfirmationIssue = { IssueId: confirmationIssueId };
        if (subscriptionConfirmedUrl) subscription.SubscriptionConfirmedUrl = subscriptionConfirmedUrl;
        if (subscriptionFailedUrl) subscription.SubscriptionFailedUrl = subscriptionFailedUrl;
    
        return subscription;
    }
    
    private async submitSubscription(subscription: SubscriptionInterface) {
        const accountId = this.form.getAttribute('data-js-ungpd-id');
        
        const response = await fetch("https://ui.ungpd.com/Api/Subscriptions/" + accountId, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(subscription)
        });
    
        if (!response.ok) {
            throw new Error(`Failed to subscribe: ${response.status}`);
        }
    
        this.consent.checked = false;
        this.email.value = "";
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const ungpdForms = [...document.querySelectorAll("[data-js-ungpd-id]")];
    ungpdForms.forEach(form => {
        new Ungpd(form as HTMLElement);
    });
});
