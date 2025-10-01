import {createApp} from 'vue'
import ActionTypes from './ActionTypes.vue'

const app = createApp({})

app
    .component('ActionTypes', ActionTypes)

app.mount('#app');
