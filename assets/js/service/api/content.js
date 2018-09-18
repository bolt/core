import axios from 'axios';

export default {
    getAll () {
        return axios.get('/api/contents.json');
    },
    getPages () {
        return axios.get('/api/contents.json?contenttype=pages');
    },
    getEntries () {
        return axios.get('/api/contents.json?contenttype=entries');
    },
    getShowcases () {
        return axios.get('/api/contents.json?contenttype=showcases');
    },
    getBlocks () {
        return axios.get('/api/contents.json?contenttype=blocks');
    },
}