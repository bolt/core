import Vue from 'vue';
import { DateTime } from 'luxon';

// Vue.filter('date', string => {
//     if (string) {
//         return DateTime.fromISO(String(string)).toLocaleString();
//     }
// });

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

// Vue.filter('datetime', string => {
//     if (string) {
//         return DateTime.fromISO(String(string)).toLocaleString(DateTime.DATETIME_MED);
//     }
// });
