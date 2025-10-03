window.axios = require('axios');

import {createApp} from 'vue'
import Tags from './Tags.vue'

const app = createApp({})

app
    .component('Tags', Tags)

app.mount('#app');
