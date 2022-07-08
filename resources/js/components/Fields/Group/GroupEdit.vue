<template>
  <div v-bind="data.attributes" class="flex flex-wrap items-center" style="max-width: 40%">

    <div class="flex items-center ltr:mr-2 rtl:ml-2" v-for="(option, index) in data.options" :key="index">

      <input
        :type="data.multiple ? 'checkbox' : 'radio'"
        :class="data.multiple ? 'checkbox' : 'radio-input'"
        v-model="model"
        :value="option.value"
        @change="onChange"/>

      <label>{{option.label}}</label>

    </div>

  </div>
</template>

<script>

export default {
  props: ["data", "value"],
  data() {
    return {
      model: null
    }
  },
  mounted() {
    this.$nextTick(() => {

      if(this.data?.multiple){

        this.model = this.value && this.value.length ? [...this.value] : [this.value]

      }else{

        this.model = this.value

      }

    })
  },
  methods: {
    onChange: _.debounce( function () {

      this.$emit( "on-change", { column: this.data?.column, value: this.model } );

    }, 200),
  },
};
</script>
