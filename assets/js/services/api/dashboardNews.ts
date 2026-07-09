import axios from 'axios';

type DashboardNews = string | number | boolean | null | DashboardNews[] | { [key: string]: DashboardNews };

export function getNews(): DashboardNews | null {
    const news = localStorage.getItem('dashboardnews');
    return news ? (JSON.parse(news) as DashboardNews) : null;
}

export function fetchNews() {
    return axios.get('/async/news').then(response => {
        localStorage.setItem('dashboardnews', response.data);
        return response.data;
    });
}
