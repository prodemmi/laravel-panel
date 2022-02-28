import Vue from "vue";
import "./plugins";
import "./components";
import Lava from "./Lava";
import axios from "./util/axios";

Vue.config.productionTip = false;
// Vue.config.devtools = false;
Vue.prototype._ = _;
Vue.prototype.window = window;
Vue.prototype.user = window.user;
Vue.prototype.debug = window.debug;
Vue.prototype.license = window.license;
Vue.prototype.Lava = window.Lava;
Vue.prototype.$http = axios;

(function () {
    this.CreateLavaApp = function (id, options) {
        return new Lava(id, options);
    };
}.call(window));

window.Vue = require('vue').default