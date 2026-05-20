import QRCode from "qrcode";

document.addEventListener("DOMContentLoaded", () => {
	document.querySelectorAll("[data-kulturkortet-barcode]").forEach((canvas) => {
		const barcode = canvas.getAttribute("data-kulturkortet-barcode");
		if (barcode) {
			QRCode.toCanvas(
				canvas,
				barcode,
				{ scale: 5, dark: "#000", light: "#fff" },
				(error) => {
					if (error) console.error(error);
				},
			);
		}
	});
});
