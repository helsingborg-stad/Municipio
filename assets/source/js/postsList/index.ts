import { PostsListAsync } from "./PostsListAsync";

/**
 * Initializes PostsListAsync for all containers with data-posts-list-async="true".
 * Should be called once on page load.
 */
export const initPostsListAsync = (): void => {
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll<HTMLElement>('[data-posts-list-async="true"]').forEach(container => {
            new PostsListAsync(container);
        });
    });
};
