import {createApp} from 'vue'
import Features from './Features.vue'

const app = createApp({})
app
    .component('Features', Features)
app.mount('#app');
