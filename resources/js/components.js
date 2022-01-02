import Vue from "vue";

Vue.component("dashboard", require("./components/Dashboard/Dashboard.vue").default);

Vue.component("lava-button", require("./components/Button.vue").default);
Vue.component("lava-spinner", require("./components/Spinner.vue").default);
Vue.component("lava-card", require("./components/Card.vue").default);
Vue.component("lava-tooltip", require("./components/Tooltip.vue").default);
Vue.component("resource-table", require("./components/Table/ResourceTable.vue").default);


const fields = require.context("./components/Fields", true, /\.vue$/);

fields.keys().forEach(fileName => {

    fileName = fileName.replace(/(\.\/|\.vue)/g, "");
    const tagName = _.snakeCase(_.split(fileName, '/').pop()).replaceAll('_', '-');

    Vue.component(tagName, require(`./components/Fields/${fileName}.vue`).default);

});