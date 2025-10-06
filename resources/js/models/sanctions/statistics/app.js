import {createApp} from 'vue'
import Statistics from './Statistics.vue'

const app = createApp({})
app.component('Statistics', Statistics)
app.mount('#app');
