
import Vue from 'vue';

const index = Vue.component('index', require('.././components/freenom/IndexComponent.vue').default);

const routes = [
    {
        path: '/example',
        component: Vue.component('example', require('.././components/ExampleComponent.vue').default)
    },
    {
        path: '/',
        component: Vue.component('example', require('.././components/ExampleComponent.vue').default)
    },
    {
        path: '/index',
        component: index
    }
];

export default routes;