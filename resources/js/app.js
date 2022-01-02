import Vue from "vue";
import "./plugins";
import "./components";
import Lava from "./Lava";
import axios from "./util/axios";

Vue.config.productionTip = false;
// Vue.config.devtools = false;
Vue.prototype._ = _;
Vue.prototype.Lava = window.Lava;
Vue.prototype.$http = axios;
Vue.prototype.config = window.config;
Vue.prototype.user = window.user;

(function () {
    this.CreateLavaApp = function () {
        return new Lava();
    };
}.call(window));
