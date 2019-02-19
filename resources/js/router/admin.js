
import Vue from 'vue';

const routes = [
    {
        path: '/',
        component: Vue.component('index', require('.././components/IndexComponent.vue').default)
    },
    {
        path: '/freenom',
        component: Vue.component('freenom-index', require('.././components/freenom/IndexComponent.vue').default)
    }
];

export default routes;