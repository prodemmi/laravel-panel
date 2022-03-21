<template>

  <div v-bind="attriubtes" class="card">

    <h4 class="card--header" v-if="!!$slots.header">

      <slot name="header"></slot>

    </h4>

    <div class="card--body">

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