import Vue from 'vue';
import VueRouter from 'vue-router';

import reg from './components/register';
//import About from './views/About.vue';

Vue.use(VueRouter);

const routes = [
    {
        path: '/register',
        name: 'register',
        component: reg
    }
];

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes
});

export default router;