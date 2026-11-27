import QRCode from "qrcode";

/** Keeps the expired-pass notice on the current render only, without intercepting the Wallet link. */
export function initializeAppleWalletNotice(): void {
	const notices = document.querySelectorAll<HTMLElement>("[data-kulturkortet-wallet-notice]");
	if (notices.length === 0) {
		return;
	}

	const url = new URL(window.location.href);
	notices.forEach((notice) => {
		const queryParameter = notice.dataset.kulturkortetWalletNotice;
		if (queryParameter) {
			url.searchParams.delete(queryParameter);
		}
	});

	if (url.href !== window.location.href) {
		window.history.replaceState(window.history.state, "", url);
	}

	document.querySelectorAll<HTMLAnchorElement>("a[data-kulturkortet-apple-wallet]").forEach((link) => {
		link.addEventListener("click", () => {
			notices.forEach((notice) => notice.remove());
		}, { once: true });
	});
}

document.addEventListener("DOMContentLoaded", () => {
	initializeAppleWalletNotice();

	document.querySelectorAll("[data-kulturkortet-barcode]").forEach((canvas) => {
		const barcode = canvas.getAttribute("data-kulturkortet-barcode");
		if (barcode) {
			QRCode.toCanvas(
				canvas,
				barcode,
				{ scale: 5, dark: "#000", light: "#fff", margin: 3},
				(error) => {
					if (error) console.error(error);
				},
			);
		}
	});
});
