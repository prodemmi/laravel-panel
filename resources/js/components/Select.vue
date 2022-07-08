<template>
  <label class="flex items-center justify-between">
    <div v-if="label" style="min-width: 60px">{{label}}</div>
    <VueSelect
      v-model="model"
      @input="change"
      :multiple="multiple"
      :options="ops"
      :placeholder="placeholder"
      :clearable="nullable"
      :selectable="
        () => (multiple ? (_.isArray(model) ? model.length : [model].length) < data.limit : true)
      "
      :push-tags="multiple"
      :reduce="(option) => option"
      label="label"
      class="w-full"
    >
    
      <template #open-indicator><span></span></template>

          <template #option="{ label, danger }">

              <div class="w-full truncate px-2 py-1" :class="{'text-danger': danger}" v-html="label"></div>

          </template>

          <template #spinner="{ loading }">

            <lava-spinner style="width: 60px" v-if="loading" color="primary">

          </lava-spinner>
              
      </template>

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
        required: true
      },
      label: {
        default: null
      },
      placeholder: {
        default: null
      },
      nullable: {
        default: true
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

      if(_.isArray(this.options) && this.options.every(option => typeof option === 'object')){
        this.ops = this.options
      }else{
        this.ops = _.map(this.options , (op, index) => ({label: op, value: index}))
      }

      this.model = this.multiple
        ? this.value
        : _.find(this.ops, (o) => o.value == this.value);

    });
  },
  methods: {
    clear(){
      this.model = null
      this.change(this.model)
    },
    change(e) {

      if(e === null || e === undefined || e.length === 0 ) {
        this.$emit('on-clear')
      }
      
      let value = this.multiple
        ? e
        : e?.value;

      this.$emit("on-change", value);

    }
  },
};
</script>
