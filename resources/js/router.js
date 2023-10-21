import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        component: () => import('./src/pages/Home.vue'),
        name: 'home'
    },
    {
        path: '/my-files',
        component: () => import('./Pages/MyFiles.vue'),
        name: 'myFiles'
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
