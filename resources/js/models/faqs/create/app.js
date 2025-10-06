import {createApp} from 'vue'
import Faqs from './Faqs.vue'

const app = createApp({})

app
    .component('Faqs', Faqs)

app.mount('#app');
