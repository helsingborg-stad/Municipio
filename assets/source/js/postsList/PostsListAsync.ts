import { postsListRender } from "../restApi/endpoints/postsListRender";

/**
 * Handles async rendering of PostsList with support for filtering, searching, and pagination.
 * Replaces page content via REST API calls without full page reloads.
 */
export class PostsListAsync {
    private container: HTMLElement;
    private attributes: Record<string, unknown>;

    /**
     * Creates a new PostsListAsync instance.
     * @param container - The container element with data-posts-list-async attribute.
     */
    constructor(container: HTMLElement) {
        this.container = container;
        this.attributes = this.parseAttributes();

        if (this.attributes) {
            this.setupAccessibility();
            this.setupFilterForm();
            this.setupPagination();
        }
    }

    /**
     * Sets up ARIA attributes for screen reader announcements.
     */
    private setupAccessibility(): void {
        this.container.setAttribute('aria-live', 'polite');
        this.container.setAttribute('aria-atomic', 'true');
    }

    /**
     * Parses block attributes from the container's data attribute.
     * @returns The parsed attributes object.
     */
    private parseAttributes(): Record<string, unknown> {
        try {
            return JSON.parse(this.container.dataset.postsListAttributes || '{}');
        } catch {
            return {};
        }
    }

    /**
     * Sets up form submit handler for filter/search functionality.
     */
    private setupFilterForm(): void {
        const form = this.container.querySelector('form');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const params = Object.fromEntries(formData.entries());
            await this.fetchAndReplace(params);
        });
    }

    /**
     * Sets up click handler for pagination links.
     */
    private setupPagination(): void {
        this.container.addEventListener('click', async (e) => {
            const target = e.target as HTMLElement;
            const link = target.closest('a[href*="page"]');

            if (!link) return;

            e.preventDefault();
            const url = new URL(link.getAttribute('href') || '', window.location.origin);
            const params = Object.fromEntries(url.searchParams.entries());
            await this.fetchAndReplace(params);
        });
    }

    /**
     * Shows the preloader by adding u-preloader class to list items.
     */
    private showPreloader(): void {
        this.container.querySelectorAll('[data-js-posts-list-item]').forEach(el => {
            el.classList.add('u-preloader');
        });
    }

    /**
     * Hides the preloader by removing u-preloader class from elements.
     */
    private hidePreloader(): void {
        this.container.querySelectorAll('.u-preloader').forEach(el => {
            el.classList.remove('u-preloader');
        });
    }

    /**
     * Updates browser URL with current filter/pagination params using History API.
     * @param params - The parameters to set in the URL.
     */
    private updateUrlParams(params: Record<string, unknown>): void {
        const url = new URL(window.location.href);

        Object.entries(params).forEach(([key, value]) => {
            if (value !== null && value !== undefined && value !== '') {
                url.searchParams.set(key, String(value));
            } else {
                url.searchParams.delete(key);
            }
        });

        history.pushState({ postsListAsync: true }, '', url.toString());
    }

    /**
     * Fetches new content from the API and replaces the container.
     * @param params - The filter/pagination parameters to send.
     */
    private async fetchAndReplace(params: Record<string, unknown>): Promise<void> {
        this.container.classList.add('is-loading');
        this.container.setAttribute('aria-busy', 'true');
        this.showPreloader();

        try {
            const html = await postsListRender.call({
                attributes: JSON.stringify(this.attributes) as unknown as Record<string, unknown>,
                ...params
            });

            const temp = document.createElement('div');
            temp.innerHTML = html;

            const newContent = temp.firstElementChild as HTMLElement;

            if (newContent) {
                newContent.dataset.postsListAsync = 'true';
                newContent.dataset.postsListAttributes = JSON.stringify(this.attributes);

                this.container.replaceWith(newContent);

                new PostsListAsync(newContent);

                this.updateUrlParams(params);

                newContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } catch {
            this.hidePreloader();
        } finally {
            this.container.classList.remove('is-loading');
            this.container.setAttribute('aria-busy', 'false');
        }
    }
}
