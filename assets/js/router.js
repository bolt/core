import Vue from "vue";
import VueRouter from "vue-router";
import DashboardContentList from "./Components/DashboardContentList";

Vue.use(VueRouter);

export default new VueRouter({
    mode: "history",
    routes: [
        // { path: '/bolt', component: DashboardContentList },
        // { path: '*', redirect: '/home' }
    ]
});
