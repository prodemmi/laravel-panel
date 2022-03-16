<template>
  <label class="flex items-center justify-between">
    <div v-if="label" style="min-width: 60px">{{label}}</div>
    <VueSelect
      v-model="model"
      @input="change"
      :multiple="multiple"
      :options="ops"
      :selectable="
        () => (multiple ? (_.isArray(model) ? model.length : [model].length) < data.limit : true)
      "
      :push-tags="multiple"
      :reduce="(option) => option"
      label="label"
      class="w-full"
    >
      <slot name="spinner">
        <lava-spinner />
      </slot>
    </VueSelect>

  </label>
</template>

<script>
import VueSelect from "vue-select";
import { FormMixin } from "../mixins";
export default {
  props: {
      value: {
          default: null
      },
      multiple: {
          default: false
      },
      options: {
        reqiored: true
      },
      label: {
        default: null
      }
  },
  mixins: [FormMixin],
  components: {
    VueSelect,
  },
  data() {
    return {
      model: undefined,
      ops: []
    };
  },
  mounted() {
    this.$nextTick(() => {

      if(this.multiple){
        this.ops = this.options
        this.model = this.value
      }else{
        this.ops = _.map(this.options , (op, index) => ({label: op, value: index}))
        this.model = _.find(this.ops, { value: this.model })
      }

    });
  },
  methods: {
    change(e) {
      
      let value = this.multiple
        ? this.model
        : _.indexOf(this.ops, this.model);

      this.$emit("on-change", value);

    }
  },
};
</script>
