import Vue from "vue";
/**
 * Editor Components
 */
import Toolbar from "../Components/Admin/Toolbar";
import Sidebar from "../Components/Admin/Sidebar";
import Header from "../Components/Admin/Header";
import DashboardNews from "../Components/Admin/DashboardNews";
import DashboardContentList from "../Components/Admin/DashboardContentList";
/**
 * Register Components
 */
Vue.component("admin-toolbar", Toolbar);
Vue.component("admin-sidebar", Sidebar);
Vue.component("admin-header", Header);
Vue.component("dashboardnews", DashboardNews);
Vue.component("dashboardcontentlist", DashboardContentList);

new Vue({ el: "#toolbar", name: "admin-toolbar" });
new Vue({ el: "#header", name: "admin-header" });
new Vue({ el: "#sidebar", name: "admin-sidebar" });
// new Vue({ el: "#vuecontent", name: "admin-content" });
// new Vue({ el: "dashboardnews", name: "admin-news" });
