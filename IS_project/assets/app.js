
import Vue from 'vue';
import axios from 'axios';
import router from './router';

Vue.prototype.$http = axios;
import Example from './components/view'
/**
 * Create a fresh Vue Application instance
 */
new Vue({
    router,
    render: h => h(App)
}).$mount('#app');

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import view from "./components/view";
