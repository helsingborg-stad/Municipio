import { PostsListAsync } from "./PostsListAsync";

export const initPostsListAsync = (): void => {
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll<HTMLElement>('[data-posts-list-async]').forEach(container => {
            new PostsListAsync(container);
        });
    });
};
