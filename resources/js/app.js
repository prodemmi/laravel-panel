import Vue from "vue";
import "./plugins";
import "./components";
import Lava from "./Lava";
import axios from "./util/axios";
// import "./tools/file_manager/file-manager-tool"

Vue.config.productionTip = false;
// Vue.config.devtools = false;
Vue.prototype._ = _;
Vue.prototype.version = "1.0.0";
Vue.prototype.window = window;
Vue.prototype.user = window.user;
Vue.prototype.debug = window.debug;
Vue.prototype.license = window.license;
Vue.prototype.Lava = window.Lava;
Vue.prototype.$http = axios;


window.Lava   = new Lava()
window.Vue    = require('vue').default
window.helper = require("./util/helpers")
