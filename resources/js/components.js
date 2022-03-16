import Vue from "vue";

Vue.component("dashboard", require("./components/Dashboard/Dashboard.vue").default);

Vue.component("lava-file-input", require("./components/FileInput.vue").default);
Vue.component("lava-button", require("./components/Button.vue").default);
Vue.component("lava-select", require("./components/Select.vue").default);
Vue.component("lava-spinner", require("./components/Spinner.vue").default);
Vue.component("lava-card", require("./components/Card.vue").default);
Vue.component("lava-collapse", require("./components/Collapse.vue").default);
Vue.component("lava-tooltip", require("./components/Tooltip.vue").default);
Vue.component("lava-stack", require("./components/Stack.vue").default);
Vue.component("lava-header", require("./components/Header.vue").default);
Vue.component("lava-dialog", require("./components/Dialog.vue").default);
Vue.component("lava-search", require("./components/Search.vue").default);
Vue.component("lava-search-bar", require("./components/SearchBar.vue").default);
Vue.component("lava-breadcrumb", require("./components/Header/Breadcrumb.vue").default);
Vue.component("resource-table", require("./components/Table/ResourceTable.vue").default);

Vue.component("form-error", require("./components/FormError.vue").default);

const fields = require.context("./components/Fields", true, /\.vue$/);

fields.keys().forEach(fileName => {

    fileName = fileName.replace(/(\.\/|\.vue)/g, "");
    const tagName = _.snakeCase(_.split(fileName, '/').pop()).replaceAll('_', '-');

    Vue.component(tagName, require(`./components/Fields/${fileName}.vue`).default);

});