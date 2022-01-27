<template>
  <VueSelect
    v-bind="data.attributes"
    v-model="model"
    @input="change"
    @search="fetchOptions"
    :options="options"
    :selectable="
      () => (data.attributes.multiple ? (_.isArray(model) ? model.length : [model].length) < data.limit : true)
    "
    :push-tags="data.attributes.multiple"
    :reduce="(option) => option"
    label="label"
  >
    <slot name="spinner">
      <lava-spinner />
    </slot>
  </VueSelect>
</template>

<script>
import VueSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import { FormMixin } from "../../../mixins";
export default {
  name: "select-detail",
  props: ["data", "value"],
  mixins: [FormMixin],
  components: {
    VueSelect,
  },
  data() {
    return {
      model: undefined,
      options: [],
      init: true,
    };
  },
  mounted() {
    this.$nextTick(() => {
      if (this.data.searchable) {
        this.fetchOptions(this.model);
      } else {
        this.model = this.data.attributes.multiple
          ? this.value
          : _.find(this.data.options, { value: this.model });

        this.options = this.data.options;
        this.init = false;
      }
    });
  },
  methods: {
    change(e) {
      console.log(this.model);
      let value = this.data.attributes.multiple
        ? this.model
        : _.indexOf(this.options, this.model);

      if (this.data.searchable && !this.data.attributes.multiple) {
        value = this.model.value;
      }

      this.$emit("on-change", value, this.data.column);
    },
    fetchOptions: _.debounce(function (search, loading) {
      if (this.data.searchable) {
        this.$http
          .post("/api/select", {
            resource: this.activeTool().resource,
            field: this.data.column,
            search,
            init: this.init
          })
          .then((res) => {
            this.options = _.toArray(res.data);
            if (this.data.searchable && this.init) {
              this.model = _.find(res.data, { value: this.model });
            }
            if (loading) loading(false);
            this.init = false;
            
          });
      }
    }, 400),
  },
};
</script>
