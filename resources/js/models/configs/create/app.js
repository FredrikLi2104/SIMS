import {createApp} from 'vue'
import Configs from './Configs.vue'

const app = createApp({})

app
    .component('Configs', Configs)

app.mount('#app');
