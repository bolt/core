import { Settings } from 'luxon';

/*
 ** Make Luxon format dates (incl. relative ones like "7 days ago") in the locale of the Bolt admin UI,
 ** instead of the browser's locale. Bolt uses locale codes like 'pt_BR', while Intl expects 'pt-BR'.
 ** An invalid locale would make Luxon throw on every date it formats, so those keep the browser's locale.
 */
const locale = document.documentElement.lang.replace(/_/g, '-');

if (locale) {
    try {
        Settings.defaultLocale = Intl.getCanonicalLocales(locale)[0];
    } catch {
        // Keep the browser's locale
    }
}
