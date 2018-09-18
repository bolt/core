import axios from 'axios';

export default {
    getAll (type) {
        // where = where || [];
        // implode
        return axios.get('/api/contents.json?contentType='+type);
    },
}