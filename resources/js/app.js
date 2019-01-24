
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import App from './App.vue';
import global from './components/tool/Global.vue';
import VueRouter from 'vue-router';
import Vuelidate from 'vuelidate';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import locale from 'element-ui/lib/locale/lang/zh-CN';

Vue.use(Vuelidate);
Vue.use(VueRouter);
Vue.use(ElementUI, { locale });
Vue.prototype.GLOBAL = global;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
// 移动到router.json
const index = Vue.component('index', require('./components/IndexComponent.vue').default);
const loading = Vue.component('loading', require('./components/Loading.vue').default);
const routes = [
    {
        path: '/example',
        component: Vue.component('example', require('./components/ExampleComponent.vue').default)
    },
    {
        path: '/',
        component: Vue.component('example', require('./components/ExampleComponent.vue').default)
    },
    {
        path: '/index',
        component: index
    }
];

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const router = new VueRouter({
    routes // （缩写）相当于 routes: routes
})

const baseUri = 'http://test.freenom.local/';

const app = new Vue({
    router,
    // el:'#app',
    render: h => h(App)
}).$mount('#app');
// });
