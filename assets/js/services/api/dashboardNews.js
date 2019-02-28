import axios from 'axios';

export function getNews() {
  return JSON.parse(localStorage.getItem('dashboardnews'));
}

export function fetchNews() {
  return axios.get('/async/news').then(response => {
    localStorage.setItem('dashboardnews', response.data);
    return response.data;
  });
}
