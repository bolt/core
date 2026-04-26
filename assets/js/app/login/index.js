import Vue from 'vue';
import Password from '../editor/Components/Password';
import Text from '../editor/Components/Text';

Vue.component('FieldPassword', Password);
Vue.component('EditorText', Text);

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
