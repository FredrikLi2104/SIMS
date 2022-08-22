window.axios = require('axios');

import {createApp} from 'vue'
import Sanctions from './Sanctions.vue'

const app = createApp({})

app
    .component('Sanctions', Sanctions)

app.mount('#app');
