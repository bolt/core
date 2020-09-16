import axios from 'axios';

export function getRecords(type) {
  return JSON.parse(localStorage.getItem(`records-${type}`));
}

export function fetchRecords(type) {
  // @todo this is probably never used
  console.warn('remove above todo');
  return axios.get(`/api/contents.json?contentType=${type}&pageSize=5`).then(response => {
    localStorage.setItem(`records-${type}`, JSON.stringify(response.data));
    return response.data;
  });
}
