/**
 * @jest-environment jsdom
 */

import EventSourceTrigger from './EventSourceTrigger';
import EventSourceHandlerWithProgressBar from './EventSourceHandlerWithProgressBar';
import ProgressBar from './ProgressBar';
import ProgressBarWithLabel from './UIComponents/ProgressBarWithLabel';

jest.mock('./EventSourceHandlerWithProgressBar');
jest.mock('./ProgressBar');
jest.mock('./UIComponents/ProgressBarWithLabel');

describe('EventSourceTrigger', () => {
    let triggerElement: HTMLElement;
    let eventSourceUrl: string;
    let eventSourceTrigger: EventSourceTrigger;

    beforeEach(() => {
        triggerElement = document.createElement('button');
        eventSourceUrl = 'http://example.com/events';
        eventSourceTrigger = new EventSourceTrigger(triggerElement, eventSourceUrl);
    });

    it('should initialize EventSourceHandlerWithProgressBar on instantiation', () => {
        expect(EventSourceHandlerWithProgressBar).toHaveBeenCalledWith(triggerElement, eventSourceUrl, expect.any(ProgressBar));
    });

    it('should add click event listener to triggerElement', () => {
        const addEventListenerSpy = jest.spyOn(triggerElement, 'addEventListener');
        new EventSourceTrigger(triggerElement, eventSourceUrl);
        expect(addEventListenerSpy).toHaveBeenCalledWith('click', expect.any(Function));
    });

    it('should prevent default action and start EventSourceHandlerWithProgressBar on click', () => {
        const event = new MouseEvent('click', { bubbles: true, cancelable: true });
        const preventDefaultSpy = jest.spyOn(event, 'preventDefault');
        const startSpy = jest.spyOn(eventSourceTrigger['eventSourceHandlerWithProgressBar'], 'start');

        triggerElement.dispatchEvent(event);

        expect(preventDefaultSpy).toHaveBeenCalled();
        expect(startSpy).toHaveBeenCalled();
    });

    it('should create a ProgressBar instance in createProgressBar', () => {
        const progressBar = eventSourceTrigger['createProgressBar']();
        expect(progressBar).toBeInstanceOf(ProgressBar);
    });

    it('should create a ProgressBarWithLabel element in createProgressBar', () => {
        const progressBarElement = document.createElement(ProgressBarWithLabel.customElementName);
        document.createElement = jest.fn().mockReturnValue(progressBarElement);

        eventSourceTrigger['createProgressBar']();

        expect(document.createElement).toHaveBeenCalledWith(ProgressBarWithLabel.customElementName);
    });
});