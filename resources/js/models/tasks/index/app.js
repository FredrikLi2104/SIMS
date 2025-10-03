import {createApp} from "vue";
import Tasks from "./Tasks.vue";

const app = createApp({});
app.component('Tasks', Tasks);
app.mount('#app');
