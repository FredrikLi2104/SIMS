window.axios = require("axios");

import { createApp } from "vue";
import OrganisationReview from "./OrganisationReview.vue";

const app = createApp({});

app.component("OrganisationReview", OrganisationReview);

app.mount("#app");
