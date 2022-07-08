import Vue from "vue";

Vue.component("dashboard", require("./components/Dashboard.vue").default);

Vue.component("lava-file-input", require("./components/FileInput.vue").default);
Vue.component("lava-button", require("./components/Button.vue").default);
Vue.component("lava-select", require("./components/Select.vue").default);
Vue.component("lava-spinner", require("./components/Spinner.vue").default);
Vue.component("lava-card", require("./components/Card.vue").default);
Vue.component("lava-section", require("./components/Section.vue").default);
Vue.component("lava-collapse", require("./components/Collapse.vue").default);
Vue.component("lava-tooltip", require("./components/Tooltip.vue").default);
Vue.component("lava-layout", require("./components/Layout.vue").default);
Vue.component("lava-tab", require("./components/Tab.vue").default);
Vue.component("lava-header", require("./components/Header.vue").default);
Vue.component("lava-dialog", require("./components/Dialog.vue").default);
Vue.component("lava-search", require("./components/Search.vue").default);
Vue.component("lava-search-bar", require("./components/SearchBar.vue").default);
Vue.component("lava-breadcrumb", require("./components/Breadcrumb.vue").default);
Vue.component("lava-loading", require("./components/Loading.vue").default);
Vue.component("resource-table", require("./components/Table/ResourceTable.vue").default);

Vue.component("value-metric", require("./components/Metrics/ValueMetric.vue").default);
Vue.component("trend-metric", require("./components/Metrics/TrendMetric.vue").default);

Vue.component("form-error", require("./components/FormError.vue").default);

const fields = require.context("./components/Fields", true, /\.vue$/);

fields.keys().forEach(fileName => {

    fileName = fileName.replace(/(\.\/|\.vue)/g, "");
    const tagName = _.snakeCase(_.split(fileName, '/').pop()).replaceAll('_', '-');

    Vue.component(tagName, require(`./components/Fields/${fileName}.vue`).default);

});
