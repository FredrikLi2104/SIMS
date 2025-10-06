import {createApp} from "vue";
import Currencies from "./Currencies.vue";

const app = createApp({});
app.component('Currencies', Currencies);
app.mount('#app');
