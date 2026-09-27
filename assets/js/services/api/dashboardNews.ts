import axios from 'axios';

export function getNews() {
    // See getRecords() in ./content for why this goes through String().
    return JSON.parse(String(localStorage.getItem('dashboardnews')));
}

export function fetchNews() {
    return axios.get('/async/news').then(response => {
        localStorage.setItem('dashboardnews', response.data);
        return response.data;
    });
}
