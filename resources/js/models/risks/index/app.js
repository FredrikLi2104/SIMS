window.axios = require('axios');

import {createApp} from 'vue'
import Risks from './Risks.vue'

const app = createApp({})

app
    .component('Risks', Risks)

app.mount('#app');
