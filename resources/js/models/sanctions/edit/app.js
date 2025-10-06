window.axios = require('axios');

import {createApp} from 'vue'
import Quill from './Quill.vue'

const app = createApp({})

app
    .component('Quill', Quill)

app.mount('#app');
