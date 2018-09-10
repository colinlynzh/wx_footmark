
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.VueRouter = require("vue-router");

import Vue from 'vue';

import { AlertPlugin, ToastPlugin } from 'vux'

Vue.use(AlertPlugin)
Vue.use(ToastPlugin)

import Vuex from 'vuex';  
import vuexI18n from 'vuex-i18n';  
import VueResource from 'vue-resource'
Vue.use(VueResource)  
Vue.use(Vuex) 
const store = new Vuex.Store({  
    modules: {  
        i18n: vuexI18n.store  
    }  
});  
Vue.use(vuexI18n.plugin, store);  
const translationsEn = {  
    "content": "This is some {type} content"  
};  
  
// translations can be kept in separate files for each language  
// i.e. resources/i18n/de.json.  
// add translations directly to the application  
Vue.i18n.add('en', translationsEn);  
  
// set the start locale to use  
Vue.i18n.set('en');


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('tabbar-component', require('./components/TabbarComponent.vue'));
//Vue.component('create-article-component', require('./components/CreateArticleComponent.vue'));

//router-link 单页应用
import VueRouter from 'vue-router'
import routers from './routers'
Vue.use(VueRouter)

const router = new VueRouter({
//   mode: 'history',
  routes: routers
})

// const app = new Vue({
//     el: '#app',
//     router,
// });

const app = new Vue({  
    router: router,  
    // render: h => h(App)  
}).$mount('#app')  
