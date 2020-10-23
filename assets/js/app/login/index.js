import Vue from 'vue';
import Password from '../editor/Components/Password';
import Text from '../editor/Components/Text';

Vue.component('field-password', Password);
Vue.component('editor-text', Text);

const id = 'login-form';

if (document.getElementById(id)) {
    new Vue({
        el: '#' + id,
        name: 'BoltLogin',
        components: {
            'editor-text': Text,
            'editor-password': Password,
        },
    });
}
