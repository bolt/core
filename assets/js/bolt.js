"use strict";

import Vue from "vue";
import router from "./router";
// import './registerServiceWorker'

// Bolt Components
import Sidebar from "./Components/Sidebar";
import Topbar from "./Components/Topbar";
import DashboardNews from "./Components/DashboardNews";
import DashboardContentList from "./Components/DashboardContentList";
import App from "./Components/App";
import "../scss/bolt.scss";

// Vue.use(SuiVue);
Vue.component("sidebar", Sidebar);
Vue.component("topbar", Topbar);
Vue.component("dashboardnews", DashboardNews);
Vue.component("app", App);
Vue.component("dashboardcontentlist", DashboardContentList);

// This loads jquery, And sets a global $ and jQuery variable
const $ = require("jquery");
global.$ = global.jQuery = $;

new Vue({ el: "header", router });
new Vue({ el: "#sidebar", router });
new Vue({ el: "#vuecontent", router });

new Vue({ el: "dashboardnews" });

$(document).ready(function() {
  // $(".ui.dropdown").dropdown({});
  // $("#sidebar .ui.dropdown").dropdown({
  //   on: "hover",
  //   transition: "slide right"
  // });
  // $(".ui.dropdown.fileselector").dropdown({
  //   transition: "slide down",
  //   fullTextSearch: "exact",
  //   preserveHTML: true
  // });
  // $(".ui.calendar").calendar({
  //   ampm: false
  // });

    var lightbox = $('a.lightbox').simpleLightbox();
});
