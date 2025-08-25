import language from './json/language';

/* IE */
interface NavigatorLanguage {
    userLanguage?: string;
}

interface LanguageJson {
    [key: string]: string;
}

function getUserLanguage(): string|null {
    let languageCode = navigator.language || (navigator as NavigatorLanguage).userLanguage;

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

export function initializeLanguageMenu() {
    const languageCode = getUserLanguage();
    if (!languageCode) { return; };

    const translation = getTranslation(languageCode);
    if (!translation) { return; };

    document.addEventListener('DOMContentLoaded', () => {
        changeLanguageMenuButtonLabel(translation, languageCode);
    });
}