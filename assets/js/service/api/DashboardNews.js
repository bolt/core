import axios from "axios";

export default {
    getNews() {
        // return data from localstorage
        let news = JSON.parse(localStorage.getItem("dashboardnews"));
        return news;
    },

    fetchNews() {
        return axios.get("/async/news").then(response => {
            // save to localstorage _and_ return data
            localStorage.setItem(
                "dashboardnews",
                JSON.stringify(response.data)
            );
            return response.data;
        });
    }
};
