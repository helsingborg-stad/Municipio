/**
 * @jest-environment jsdom
 */

import "./ProgressBarWithLabel";
import ProgressBarWithLabel from "./ProgressBarWithLabel";

describe("<progress-bar-with-label>", () => {

    beforeAll(() => {
        customElements.define("progress-bar-with-label", ProgressBarWithLabel);
    });

    it("contains a <progress> element'", () => {
        const progressBar = document.createElement("progress-bar-with-label");
        expect(progressBar.shadowRoot!.querySelector("progress")).toBeTruthy();
    });

    it("contains a <label> element'", () => {
        const progressBar = document.createElement("progress-bar-with-label");
        expect(progressBar.shadowRoot!.querySelector("label")).toBeTruthy();
    });

    it("initializes with progress set to 0", () => {
        const progressBar = document.createElement("progress-bar-with-label");
        expect(progressBar.shadowRoot!.querySelector("progress")!.getAttribute("value")).toBe("0");
    });

    it("can update progress value", () => {
        const progressBar = document.createElement("progress-bar-with-label");
        progressBar.setAttribute("progress", "50");
        expect(progressBar.shadowRoot!.querySelector("progress")!.getAttribute("value")).toBe("50");
    });

    it("initializes with label set to empty string", () => {
        const progressBar = document.createElement("progress-bar-with-label");
        expect(progressBar.shadowRoot!.querySelector("label")!.textContent).toBe("");
    });

    it("can update label value", () => {
        const progressBar = document.createElement("progress-bar-with-label");
        progressBar.setAttribute("label", "Loading...");
        expect(progressBar.shadowRoot!.querySelector("label")!.textContent).toBe("Loading...");
    });

    it("handles invalid progress value gracefully", () => {
        const progressBar = document.createElement("progress-bar-with-label");
        progressBar.setAttribute("progress", "invalid");
        expect(progressBar.shadowRoot!.querySelector("progress")!.getAttribute("value")).toBe("0");
    });
});