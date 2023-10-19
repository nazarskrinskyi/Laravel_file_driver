import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        component: () => import('./src/pages/Home.vue'),
        name: 'home'
    },
    {
        path: '/info',
        component: () => import('./src/pages/Info.vue'),
        name: 'info'
    },
    {
        path: '/about',
        component: () => import('./src/pages/About.vue'),
        name: 'about'
    },
];

const router = createRouter({
    history: createWebHistory(),
    linkActiveClass: 'active',
    routes
});

export default router;
