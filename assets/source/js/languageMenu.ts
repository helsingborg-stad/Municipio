import language from './json/language';

/* IE */
interface NavigatorLanguage {
    userLanguage?: string;
}

interface LanguageJson {
    [key: string]: string;
}

function getUserLanguage(): string|null {
    const languageCode = navigator.language || (navigator as NavigatorLanguage).userLanguage;

    return languageCode ? languageCode.split('-')[0] : null;
}

function getTranslation(languageCode: string): string|null {
    return language && (language as LanguageJson)[languageCode] ? (language as LanguageJson)[languageCode] : null;
}

function changeLanguageMenuButtonLabel(translation: string, languageCode: string) {
    const languageLabel = document.querySelector('#site-language-menu-button .c-button__label-text');

    if (languageLabel) {
        languageLabel.textContent = translation;
        languageLabel.setAttribute('lang', languageCode);

    }
}

function initializeLanguageMenuPopover(): void {
    const menu = document.querySelector<HTMLElement>('.site-language-menu');
    const trigger = document.querySelector<HTMLElement>('#site-language-menu-button');
    const popover = document.querySelector<HTMLElement>('#site-language-menu-popover');

    if (!menu || !trigger || !popover) {
        return;
    }

    if (typeof (popover as HTMLElement & { showPopover?: () => void }).showPopover !== 'function') {
        return;
    }

    const positionPopover = (): void => {
        const triggerRect = trigger.getBoundingClientRect();
        const top = Math.round(triggerRect.bottom);
        const right = Math.max(0, Math.round(window.innerWidth - triggerRect.right));

        popover.style.top = `${top}px`;
        popover.style.right = `${right}px`;
        popover.style.left = 'auto';
    };

    const syncPositionWhenOpen = (): void => {
        if (popover.matches(':popover-open')) {
            positionPopover();
        }
    };

    trigger.addEventListener('click', () => {
        window.requestAnimationFrame(syncPositionWhenOpen);
    });

    window.addEventListener('resize', syncPositionWhenOpen);
    window.addEventListener('scroll', syncPositionWhenOpen, { passive: true });

    popover.addEventListener('toggle', () => {
        if (popover.matches(':popover-open')) {
            menu.classList.add('is-expanded');
            positionPopover();
        } else {
            menu.classList.remove('is-expanded');
        }
    });
}

export function initializeLanguageMenu() {
    const languageCode = getUserLanguage();
    if (!languageCode) { return; };

    const translation = getTranslation(languageCode);
    if (!translation) { return; };

    document.addEventListener('DOMContentLoaded', () => {
        changeLanguageMenuButtonLabel(translation, languageCode);
        initializeLanguageMenuPopover();
    });
}