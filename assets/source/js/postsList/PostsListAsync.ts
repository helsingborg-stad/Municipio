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
            this.setupResetButton();
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
            const params: Record<string, unknown> = {};

            // Handle multiselect inputs by collecting all values for each key
            const uniqueKeys = [...new Set(formData.keys())];
            uniqueKeys.forEach(key => {
                const values = formData.getAll(key);
                // For multiple values, use array notation in parameter name for PHP
                if (values.length > 1) {
                    const arrayKey = key.endsWith('[]') ? key : `${key}[]`;
                    params[arrayKey] = values;
                } else {
                    params[key] = values[0];
                }
            });

            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.forEach((value, key) => {
                if (key.endsWith('_page') && !isNaN(Number(value))) {
                    params[key] = '';
                }
            });

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
     * Sets up click handler for reset button.
     * The reset button clears all filters and reloads the default posts list.
     */
    private setupResetButton(): void {
        this.container.addEventListener('click', async (e) => {
            const target = e.target as HTMLElement;
            const resetLink = target.closest('a[href]:not([href*="page"])');

            if (!resetLink) return;

            // Check if this is inside the filter form area (reset button)
            const form = this.container.querySelector('form');
            if (!form || !form.contains(resetLink)) return;

            // Skip if it's a pagination link
            if (resetLink.getAttribute('href')?.includes('page')) return;

            e.preventDefault();
            // Reset filters by calling with empty params and clearing URL
            await this.fetchAndReplace({}, true);
        });
    }

    /**
     * Shows the preloader by adding u-preloader--inner class to list items.
     */
    private showPreloader(): void {
        this.container.querySelectorAll('[data-js-posts-list-item]').forEach(el => {
            el.classList.add('u-preloader--inner', 'u-preloader--exclude');
        });
    }

    /**
     * Hides the preloader by removing u-preloader--inner class from elements.
     */
    private hidePreloader(): void {
        this.container.querySelectorAll('.u-preloader--inner').forEach(el => {
            el.classList.remove('u-preloader--inner', 'u-preloader--exclude');
        });
    }

    /**
     * Updates browser URL with current filter/pagination params using History API.
     * @param params - The parameters to set in the URL.
     * @param clearAll - If true, clears all search params before setting new ones.
     */
    private updateUrlParams(params: Record<string, unknown>, clearAll: boolean = false): void {
        const url = new URL(window.location.href);

        // Clear all search params if requested (used by reset)
        if (clearAll) {
            url.search = '';
        }

        Object.entries(params).forEach(([key, value]) => {
            // Handle array values (multiselect) - key should already have [] suffix
            if (Array.isArray(value)) {
                // Clean up both with and without [] suffix
                const baseKey = key.replace(/\[\]$/, '');
                url.searchParams.delete(baseKey);
                url.searchParams.delete(key);

                if (value.length > 0) {
                    value.forEach(v => {
                        if (v !== null && v !== undefined && v !== '') {
                            url.searchParams.append(key, String(v));
                        }
                    });
                }
            } else if (value !== null && value !== undefined && value !== '') {
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
     * @param clearUrlParams - If true, clears all URL params (used by reset).
     */
    private async fetchAndReplace(params: Record<string, unknown>, clearUrlParams: boolean = false): Promise<void> {
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

                this.updateUrlParams(params, clearUrlParams);

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
