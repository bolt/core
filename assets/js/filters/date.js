import { DateTime } from 'luxon';

function formatAsDate(string) {
    if (string) {
        return DateTime.fromISO(String(string)).toLocaleString();
    }
}

function formatAsDateTime(string) {
    if (string) {
        return DateTime.fromISO(String(string)).toLocaleString(DateTime.DATETIME_MED);
    }
}

export default {
    formatAsDate,
    formatAsDateTime,
};
