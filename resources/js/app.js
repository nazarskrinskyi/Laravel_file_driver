import { createApp } from 'vue';
import App from "./src/App.vue";
import router from "./router.js";
import store from "./src/vuex/store.js";
createApp(App)
    .use(router)
    .use(store)
    .mount('#app')
