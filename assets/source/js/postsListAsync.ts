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

const showGhostLoader = (container: HTMLElement): HTMLElement | null => {
    const template = container.querySelector<HTMLTemplateElement>('template[data-posts-list-ghost]');
    if (!template) return null;

    // Clone the ghost loader content
    const ghostContent = template.content.cloneNode(true) as DocumentFragment;
    const ghostWrapper = document.createElement('div');
    ghostWrapper.setAttribute('data-posts-list-ghost-active', 'true');
    ghostWrapper.classList.add('o-layout-grid', 'o-layout-grid--cols-12', 'o-layout-grid--gap-6', 'o-layout-grid--col-span-12');
    ghostWrapper.appendChild(ghostContent);

    // Hide existing content (except filters and template)
    container.querySelectorAll(':scope > .c-element:not(:has(form)):not(:has(template))').forEach(el => {
        (el as HTMLElement).style.display = 'none';
    });

    // Insert ghost loader after filters
    const filtersElement = container.querySelector(':scope > .c-element:has(form)');
    if (filtersElement) {
        filtersElement.after(ghostWrapper);
    } else {
        container.prepend(ghostWrapper);
    }

    return ghostWrapper;
};

const hideGhostLoader = (container: HTMLElement): void => {
    const ghostWrapper = container.querySelector('[data-posts-list-ghost-active]');
    if (ghostWrapper) {
        ghostWrapper.remove();
    }

    // Show hidden content
    container.querySelectorAll(':scope > .c-element').forEach(el => {
        (el as HTMLElement).style.display = '';
    });
};

const fetchAndReplace = async (
    container: HTMLElement,
    attributes: Record<string, unknown>,
    params: Record<string, unknown>
): Promise<void> => {
    container.classList.add('is-loading');
    const ghostWrapper = showGhostLoader(container);

    try {
        const args: PostsListRenderArgs = {
            attributes: JSON.stringify(attributes) as unknown as Record<string, unknown>,
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
        hideGhostLoader(container);
    } finally {
        container.classList.remove('is-loading');
    }
};

export { initPostsListAsync };
