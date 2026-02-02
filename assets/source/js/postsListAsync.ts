import { postsListRender, PostsListRenderArgs } from "./restApi/endpoints/postsListRender";

interface PostsListContainer extends HTMLElement {
    dataset: {
        postsListAsync: string;
        postsListAttributes: string;
    };
}

const initPostsListAsync = (): void => {
    console.log('[PostsListAsync] Initializing...');
    const containers = document.querySelectorAll<PostsListContainer>('[data-posts-list-async]');
    console.log('[PostsListAsync] Found containers:', containers.length);

    containers.forEach(container => {
        const attributes = parseAttributes(container);
        if (!attributes) return;

        console.log('[PostsListAsync] Setting up container with attributes:', attributes);
        setupFilterForm(container, attributes);
        setupPagination(container, attributes);
    });
};

const parseAttributes = (container: PostsListContainer): Record<string, unknown> | null => {
    try {
        return JSON.parse(container.dataset.postsListAttributes || '{}');
    } catch {
        console.error('Failed to parse PostsList attributes');
        return null;
    }
};

const setupFilterForm = (container: HTMLElement, attributes: Record<string, unknown>): void => {
    const form = container.querySelector('form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const params = Object.fromEntries(formData.entries());
        await fetchAndReplace(container, attributes, params);
    });
};

const setupPagination = (container: HTMLElement, attributes: Record<string, unknown>): void => {
    container.addEventListener('click', async (e) => {
        const target = e.target as HTMLElement;
        const link = target.closest('a[href*="page"]');

        if (!link) return;

        e.preventDefault();
        const url = new URL(link.getAttribute('href') || '', window.location.origin);
        const params = Object.fromEntries(url.searchParams.entries());
        await fetchAndReplace(container, attributes, params);
    });
};

const fetchAndReplace = async (
    container: HTMLElement,
    attributes: Record<string, unknown>,
    params: Record<string, unknown>
): Promise<void> => {
    container.classList.add('is-loading');

    try {
        const args: PostsListRenderArgs = {
            attributes,
            ...params
        };

        const html = await postsListRender.call(args);

        // Create a temporary container to parse the HTML
        const temp = document.createElement('div');
        temp.innerHTML = html;

        // Find the new content element
        const newContent = temp.firstElementChild as HTMLElement;

        if (newContent) {
            // Preserve async data attributes
            newContent.dataset.postsListAsync = 'true';
            newContent.dataset.postsListAttributes = JSON.stringify(attributes);

            container.replaceWith(newContent);

            // Re-initialize on the new element
            setupFilterForm(newContent, attributes);
            setupPagination(newContent, attributes);

            // Scroll to the container
            newContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    } catch (error) {
        console.error('PostsList async fetch failed:', error);
    } finally {
        container.classList.remove('is-loading');
    }
};

export { initPostsListAsync };
