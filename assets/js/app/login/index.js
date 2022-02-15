import { createApp } from 'vue';
import Vue from 'vue';
import Password from '../editor/Components/Password';
import Text from '../editor/Components/Text';

const id = 'login-form';

const login = {
    data() {
        return {
            el: '#' + id,
            name: 'BoltLogin',
            components: {
                'editor-text': Text,
                'editor-password': Password,
            },
        };
    },
};
if (document.getElementById(id)) {
    const app = createApp(login);

    app.component('EditorPassword', Password);
    app.component('EditorText', Text);

    app.mount('#' + id);
}
