import Vue from 'vue';
/**
 * VueX Store
 */
import store from './store';
/**
 * Components
 */
import Text from './Components/Editor/Text/Text';
import Slug from './Components/Editor/Slug/Slug';
import Date from './Components/Editor/Date/Date';
import Select from './Components/Editor/Select/Select';
import Number from './Components/Editor/Number/Number';
import Html from './Components/Editor/Html/Html';
import Markdown from './Components/Editor/Markdown/Markdown';
import Textarea from './Components/Editor/Textarea/Textarea';
import Embed from './Components/Editor/Embed/Embed';
import Image from './Components/Editor/Image/Image';
import Email from './Components/Editor/Email/Email';
import Password from './Components/Editor/Password/Password';
import ThemeSelect from './Components/Editor/ThemeSelect/ThemeSelect';
import Language from './Components/Editor/Language/Language';

new Vue({
  store,
  el: '#editor',
  name: 'BoltEditor',
  components: {
    'editor-date': Date,
    'editor-embed': Embed,
    'editor-email': Email,
    'editor-password': Password,
    'editor-html': Html,
    'editor-image': Image,
    'editor-markdown': Markdown,
    'editor-number': Number,
    'editor-select': Select,
    'editor-slug': Slug,
    'editor-text': Text,
    'editor-textarea': Textarea,
    'general-language': Language,
    'theme-select': ThemeSelect,
  },
});
