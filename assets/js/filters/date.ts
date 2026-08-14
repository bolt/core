import { DateTime } from 'luxon';

export function date(string: unknown) {
    if (string) {
        return DateTime.fromISO(String(string)).toLocaleString();
    }

    return undefined;
}

export function datetime(string: unknown) {
    if (string) {
        return DateTime.fromISO(String(string)).toLocaleString(DateTime.DATETIME_MED);
    }

    return undefined;
}
