import Vue from 'vue';
import Password from '../editor/Components/Password';
import Text from '../editor/Components/Text';

Vue.component('field-password', Password);
Vue.component('editor-text', Text);

new Vue({
  el: '#login-form',
  name: 'BoltLogin',
  components: {
    'editor-text': Text,
    'editor-password': Password,
  },
});
