import Vue from 'vue';

Vue.component('paginate', require('.././components/units/PaginateComponent.vue').default)

const routes = [
    {
        path: '/',
        component: Vue.component('index', require('.././components/IndexComponent.vue').default)
    },
    {
        path: '/freenom',
        component: Vue.component('freenom-index', require('.././components/freenom/IndexComponent.vue').default)
    },
    {
        path: '/image',
        component: Vue.component('freenom-image', require('.././components/ImageComponent.vue').default)
    },
    {
        path: '/login',
        component: Vue.component('login', require('.././components/LoginComponent.vue').default)
    }
];

export default routes;