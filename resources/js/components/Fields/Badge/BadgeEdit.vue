<template>

  <VueSelect
      v-bind="data.attributes"
      v-model="model"
      @input="change"
      :options="_.values(data.options)"
      :reduce="(option) => option">
  </VueSelect>

</template>

<script>
import VueSelect from "vue-select";
export default {
  props: ["data", "value"],
  components: {
    VueSelect
  },
  data(){
    return {
      model: null
    }
  },
  mounted(){

    this.$nextTick(() => {

      this.model = this.data.options[this.value]

    })

  },
  methods: {
    change(e) {

      this.$emit("on-change", {
        column: this.data.column,
        value: _.invert(_.values(_.keyBy(this.data.options)))[e]
      });

    },
  }
};
</script>