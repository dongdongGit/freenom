/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import App from './App.vue';
import global from './config/global.js';
import unit from './unit/helpers.js';
import routes from './router/admin.js';
import VueRouter from 'vue-router';
import Vuelidate from 'vuelidate';
import ElementUI from 'element-ui';
import VueMoment from 'vue-moment';
import 'element-ui/lib/theme-chalk/index.css';
import locale from 'element-ui/lib/locale/lang/zh-CN';

Vue.use(VueMoment);
Vue.use(Vuelidate);
Vue.use(VueRouter);
Vue.use(ElementUI, { locale });

Vue.prototype.GLOBAL = global;
Vue.prototype.axiosInstance = axios.create();
Vue.prototype.axiosInstance.defaults.timeout = 6000;
Vue.prototype.axiosInstance.defaults.baseURL = global.baseUri;

var loading;

function startLoading() {
    loading = Vue.prototype.$loading({
        lock: true,
        text: "Loading...",
        target: document.querySelector('.loading')//设置加载动画区域
    });
}

function endLoading() {
    loading.close();
}

let needLoadingRequestCount = 0;
function showFullScreenLoading() {
    if (needLoadingRequestCount === 0) {
        startLoading();
    }
    needLoadingRequestCount++;
};
function tryHideFullScreenLoading() {
    if (needLoadingRequestCount <= 0) return;
    needLoadingRequestCount--;
    if (needLoadingRequestCount === 0) {
      endLoading();
    }
};

Vue.prototype.axiosInstance.interceptors.request.use(function (config) {
    showFullScreenLoading();
    return config;
}, function (error) {
    tryHideFullScreenLoading();
    return Promise.reject(error);
});
Vue.prototype.axiosInstance.interceptors.response.use(function (res) {
    tryHideFullScreenLoading();
    return res;
}, function (error) {
    tryHideFullScreenLoading();
    var result = JSON.parse(error.request.response);
    switch (error.request.status) {
        case 422:
            let message = '';
            result.data.forEach(element => {
                message = message + '<p>' + element['content'] + "</p>";
            });

            Vue.prototype.$message({
                showClose: true,
                dangerouslyUseHTMLString: true,
                message: message,
                type: "error",
                duration: 10000
            });

            break;
        case 500:
            Vue.prototype.$message({
                showClose: true,
                message: result.message || "请求错误",
                type: "error"
            });
            break;
        default:
            break;
    }

    return Promise.reject(error);
});
Vue.prototype.$unit = unit;

if (!Array.isArray) {
    Array.isArray = function (arg) {
        return Object.prototype.toString.call(arg) === '[object Array]'
    }
}

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const router = new VueRouter({
    routes // （缩写）相当于 routes: routes
})

const app = new Vue({
    router,
    render: h => h(App)
}).$mount('#app');
