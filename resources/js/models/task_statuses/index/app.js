import {createApp} from 'vue'
import TaskStatuses from './TaskStatuses.vue'

const app = createApp({})

app
    .component('TaskStatuses', TaskStatuses)

app.mount('#app');
