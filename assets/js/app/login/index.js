import { createApp } from 'vue';
import Password from '../editor/Components/Password';
import Text from '../editor/Components/Text';

const id = 'login-form';

if (document.getElementById(id)) {
    const app = createApp({
        name: 'BoltLogin',
    });
    app.config.compilerOptions.whitespace = 'preserve';

    app.component('field-password', Password);
    app.component('editor-text', Text);
    app.component('editor-password', Password);

    app.mount('#' + id);
}
