import { postsListAsync } from "./PostsListAsync";

/**
 * Initializes PostsListAsync for all containers with data-posts-list-async="true".
 * Should be called once on page load.
 */
export const initPostsListAsync = (): void => {
	document.addEventListener("DOMContentLoaded", () => {
		document
			.querySelectorAll<HTMLElement>('[data-posts-list-async="true"]')
			.forEach((container) => {
				const postsContainer =
					container.querySelector<HTMLElement>(".js-async-posts");
				const form = container.querySelector<HTMLFormElement>("form");
				postsContainer && form
					? postsListAsync(container, postsContainer, form)
					: console.error("[PostsList]: Failed to locate posts container.");
			});
	});
};
