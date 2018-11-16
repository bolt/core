import axios from 'axios';

export default {
  getRecords(type) {
    // where = where || [];
    // implode
    let records = JSON.parse(localStorage.getItem('records-' + type));
    return records;
  },

  fetchRecords(type) {
    return axios
      .get('/api/contents.json?contentType=' + type + '&pageSize=5')
      .then(response => {
        // save to localstorage _and_ return data
        localStorage.setItem('records-' + type, JSON.stringify(response.data));
        return response.data;
      });
  }
};
