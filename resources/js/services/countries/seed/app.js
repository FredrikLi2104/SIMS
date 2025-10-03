window.axios = require('axios');

import {createApp} from 'vue'
import Countries from './Countries.vue'

const app = createApp({})

app
    .component('Countries', Countries)

app.mount('#app');
