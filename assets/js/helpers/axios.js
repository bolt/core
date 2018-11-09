import Vue from "vue";
import Axios from 'axios'

Axios.defaults.headers.common = {
  "accept": "application/vnd.api+json",
};
export default Vue.prototype.$axios = Axios;