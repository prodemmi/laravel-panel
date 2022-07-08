<template>

  <div v-bind="attriubtes">

    <h4 v-if="!!$slots.header" class="font-bold leading my-1">

      <slot name="header"></slot>

    </h4>

    <div class="bg-white p-2 rounded-md">

      <slot name="body"></slot>

      <div class="card--footer" v-if="!!$slots.footer">

        <slot name="footer"></slot>

      </div>

    </div>

  </div>

</template>

<script>
export default {
  props: {
    data: {
      default: () => {}
    }
  },
  computed: {
    attriubtes(){
      if(this.data && this.data.attributes){
        return this.data.attributes
      }
      return {}
    }
  }
}
</script>