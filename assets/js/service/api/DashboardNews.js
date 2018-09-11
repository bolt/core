import axios from 'axios'

export default {
    getNews () {
        return axios.get('/en/async/news')
            .then(response => {
                return response.data
            })
    }
}