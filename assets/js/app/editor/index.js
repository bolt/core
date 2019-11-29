import Vue from 'vue';
/**
 * VueX Store
 */
import store from './store';
/**
 * Components
 */
import Text from './Components/Text';
import Slug from './Components/Slug';
import Date from './Components/Date';
import Select from './Components/Select';
import Number from './Components/Number';
import Html from './Components/Html';
import Markdown from './Components/Markdown';
import Textarea from './Components/Textarea';
import Embed from './Components/Embed';
import Image from './Components/Image';
import Imagelist from './Components/Imagelist';
import Email from './Components/Email';
import Password from './Components/Password';
import ThemeSelect from './Components/ThemeSelect';
import Language from './Components/Language';
import File from './Components/File';
import Filelist from './Components/Filelist';
import Collection from './Components/Collection';
import Set from './Components/Set'

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
    'editor-imagelist': Imagelist,
    'editor-file': File,
    'editor-filelist': Filelist,
    'editor-markdown': Markdown,
    'editor-number': Number,
    'editor-select': Select,
    'editor-slug': Slug,
    'editor-text': Text,
    'editor-textarea': Textarea,
    'editor-collection': Collection,
    'editor-set': Set,
    'general-language': Language,
    'theme-select': ThemeSelect,
  },
});
