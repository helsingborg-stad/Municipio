import { beforeEach, describe, expect, it } from "@jest/globals";
import { initializeAppleWalletNotice } from "./index";

describe("Apple Wallet expiry notice", () => {
	const noticeSelector = "[data-kulturkortet-wallet-notice]";
	const passUrl = "/ticket/?kulturkortet_apple_pass=fresh";

	beforeEach(() => {
		document.body.innerHTML = `
			<p role="alert" data-kulturkortet-wallet-notice="kulturkortet_apple_pass_expired">Expired pass</p>
			<a href="${passUrl}" data-kulturkortet-apple-wallet>Apple Wallet</a>
			<a href="/google-wallet">Google Wallet</a>
		`;
		window.history.replaceState({ page: "ticket" }, "", "/ticket/?page_id=6&kulturkortet_apple_pass_expired=1#card");
	});

	/** Checks that a reload requests the original page without the expiry flag. */
	it("keeps the notice visible but removes only the expiry flag from the reload URL", () => {
		initializeAppleWalletNotice();

		expect(window.location.pathname + window.location.search + window.location.hash).toBe("/ticket/?page_id=6#card");
		expect(window.history.state).toEqual({ page: "ticket" });
		expect(document.querySelector(noticeSelector)?.textContent).toBe("Expired pass");
	});

	/** Checks that retrying clears the notice without cancelling Safari's default action. */
	it("removes the stale notice on Apple Wallet retry without intercepting the link", () => {
		initializeAppleWalletNotice();
		const link = document.querySelector<HTMLAnchorElement>("a[data-kulturkortet-apple-wallet]");
		expect(link).not.toBeNull();
		const click = new MouseEvent("click", { bubbles: true, cancelable: true });
		document.addEventListener("click", (event) => {
			expect(event.defaultPrevented).toBe(false);
			event.preventDefault();
		}, { once: true });

		link?.dispatchEvent(click);

		expect(document.querySelector(noticeSelector)).toBeNull();
		expect(link?.getAttribute("href")).toBe(passUrl);
	});

	/** Checks that non-Apple links do not dismiss the notice. */
	it("leaves the notice visible when another link is clicked", () => {
		initializeAppleWalletNotice();
		const googleLink = document.querySelector('a[href="/google-wallet"]');
		googleLink?.addEventListener("click", (event) => event.preventDefault(), { once: true });

		googleLink?.dispatchEvent(new MouseEvent("click", { bubbles: true, cancelable: true }));

		expect(document.querySelector(noticeSelector)).not.toBeNull();
	});
});
