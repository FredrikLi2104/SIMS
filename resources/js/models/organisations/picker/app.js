import {createApp} from "vue";
import Picker from './Picker.vue';

const app = createApp({});
app.component('Picker', Picker);
app.mount('#org-picker');
