import axios from 'axios';

export default {
    getAll () {
        return axios.get('/api/contents');
    },
}