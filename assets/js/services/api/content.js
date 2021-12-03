import axios from 'axios';

export function getRecords(type) {
    return JSON.parse(localStorage.getItem(`records-${type}`));
}
