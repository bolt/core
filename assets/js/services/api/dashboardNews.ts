import axios from 'axios';

export function getNews() {
    // See getRecords() in ./content for why this goes through String() and why
    // the local is typed.
    const news: unknown = JSON.parse(String(localStorage.getItem('dashboardnews')));

    return news;
}

export function fetchNews() {
    return axios.get('/async/news').then(response => {
        localStorage.setItem('dashboardnews', response.data);
        const news: unknown = response.data;

        return news;
    });
}
