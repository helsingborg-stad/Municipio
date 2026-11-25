export function getLocalizedString(key: keyof MunicipioChatLocale, fallback: string): string {
    const locale = (globalThis as { municipioChatLocale?: Partial<MunicipioChatLocale> }).municipioChatLocale;
    const translated = locale?.[key];

    return typeof translated === "string" && translated.length > 0 ? translated : fallback;
}