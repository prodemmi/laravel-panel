<template>
  <VueSelect
    v-bind="data.attributes"
    v-model="model"
    @input="change"
    @search="fetchOptions"
    @search:focus="fetchOptions"
    :multiple="data.multiple && !data.searchable"
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
import { FormMixin } from "../../../mixins";
export default {
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

      if (this.data.searchable && !(this.value === undefined || this.value === null)) {
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
      
      let value = this.data.attributes.multiple
        ? this.model
        : _.indexOf(this.options, this.model);

      if (this.data.searchable && !this.data.attributes.multiple) {
        value = this.model?.value || null;
      }

      this.$emit("on-change", {
        column: this.data.column,
        value
      });
    },
    fetchOptions: _.debounce(function (search, loading) {
      if (this.data.searchable) {
        this.$http
          .post("/api/searchable-select", {
            resource: this.activeTool().resource,
            field: this.data.column,
            search: this.init ? this.value : search,
            init: this.init
          })
          .then((res) => {
            this.options = _.toArray(res.data);
            if (this.data.searchable && this.init) {
              this.model = _.find(this.options, { value: this.value });
            }
            if (loading) loading(false);
            this.init = false;
            
          });
      }
    }, 400),
  },
};
</script>
