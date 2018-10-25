import Vue from "vue";
/**
 * Editor Components
 */
import Sidebar from "../Components/Admin/Sidebar";
import Topbar from "../Components/Admin/Topbar";
import DashboardNews from "../Components/Admin/DashboardNews";
import DashboardContentList from "../Components/Admin/DashboardContentList";
/**
 * Register Components
 */
Vue.component("admin-sidebar", Sidebar);
Vue.component("admin-topbar", Topbar);
Vue.component("dashboardnews", DashboardNews);
Vue.component("dashboardcontentlist", DashboardContentList);

new Vue({ el: "header", name: "admin-header" });
new Vue({ el: "#sidebar", name: "admin-sidebar" });
new Vue({ el: "#vuecontent", name: "admin-content" });
new Vue({ el: "dashboardnews", name: "admin-news" });
