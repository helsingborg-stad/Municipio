/**
 * @jest-environment jsdom
 */

import ProgressBar from './ProgressBar';
import ProgressBarWithLabel from './UIComponents/ProgressBarWithLabel';
import { ProgressBarUpdate } from './IProgressBar';

describe('ProgressBar', () => {
    let progressBar: ProgressBar;
    let element: ProgressBarWithLabel;
    let insertAfterElement: HTMLElement;

    beforeEach(() => {
        element = document.createElement('progress-bar-with-label') as ProgressBarWithLabel;
        insertAfterElement = document.createElement('div');
        document.body.appendChild(insertAfterElement);
        progressBar = new ProgressBar(element, insertAfterElement);
    });

    afterEach(() => {
        document.body.removeChild(insertAfterElement);
    });

    test('should update label and progress value', () => {
        const event: ProgressBarUpdate = { label: 'Loading', value: 50 };

        progressBar.update(event);

        expect(element.getAttribute('label')).toBe('Loading');
        expect(element.getAttribute('progress')).toBe('50');
    });

    test('should show the progress bar', () => {
        progressBar.show();

        expect(insertAfterElement.nextSibling).toBe(element);
    });

    test('should hide the progress bar', () => {
        progressBar.show();
        progressBar.hide();

        expect(document.body.contains(element)).toBe(false);
    });
});