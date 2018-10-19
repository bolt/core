import Vue from "vue";
/**
 * Editor Components
 */
import Sidebar from "../Components/Sidebar";
import Topbar from "../Components/Topbar";
import DashboardNews from "../Components/DashboardNews";
import DashboardContentList from "../Components/DashboardContentList";
import App from "../Components/App";
/**
 * Register Components
 */
Vue.component("sidebar", Sidebar);
Vue.component("topbar", Topbar);
Vue.component("dashboardnews", DashboardNews);
Vue.component("app", App);
Vue.component("dashboardcontentlist", DashboardContentList);

new Vue({ el: "header", name: "admin-header" });
new Vue({ el: "#sidebar", name: "admin-sidebar" });
new Vue({ el: "#vuecontent", name: "admin-content" });
new Vue({ el: "dashboardnews", name: "admin-news" });
