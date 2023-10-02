import { createRouter, createWebHistory } from 'vue-router'
import login from "@/components/login";
import dashboard from "@/components/dashboard";
import adminPanel from "@/components/adminPanel";
import register from "@/components/register";

const routes = [
    {
        path: '/signup',
        name: 'signup',
        meta: { title: "Sign up" },
        component: register
    },
    {
        path: '/',
        name: 'login',
        meta: { title: "Login" },
        component: login
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        meta: { title: "Dashboard" },
        component: dashboard
    },
    {
        path: '/adminPanel',
        name: 'adminPanel',
        meta: { title: 'Admin' },
        component: adminPanel
    }
]

const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes
})

router.beforeEach((to, from, next) => {
    document.title = to.meta.title || "Your Default Title";
    next();
})

export default router