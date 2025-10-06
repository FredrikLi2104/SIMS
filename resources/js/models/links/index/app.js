import {createApp} from 'vue'
import Links from './Links.vue'

const app = createApp({})

app
    .component('Links', Links)

app.mount('#app');
