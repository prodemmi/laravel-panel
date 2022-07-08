<template>

  <VueSelect
      v-bind="data.attributes"
      v-model="model"
      @input="change"
      :options="_.values(data.options)"
      :reduce="(option) => option">

    <template #spinner="{ loading }">

      <lava-spinner style="width: 60px" v-if="loading" color="primary"></lava-spinner>
        
    </template>

    <template #option="{ label, subtitle }">

        <div class="flex flex-col px-2 py-1">
            <span class="text-lg">{{ label }}</span>
            <span v-if="subtitle" class="text-sm">{{ subtitle }}</span>
        </div>

    </template>

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