<template>

  <div v-if="visible" v-bind="attriubtes" class="card">

    <div class="flex-center px-1">

      <div class="card__header">

        <slot name="header"></slot>

      </div>

      <i v-if="closable" @click="close" class="cursor-pointer text-white ri-close-line"></i>

    </div>

    <div class="card__body">

      <slot name="body"></slot>

      <div class="card__footer" v-if="!!$slots.footer">
        
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
      },
      closable: {
        default: false
      }
    },
    data() {
      return {
        visible: true,
      }
    },
    computed: {
      attriubtes(){
        if(this.data && this.data.attributes){
          return this.data.attributes
        }
        return null
      }
    },
    methods: {
      close() {

        this.visible = false
        this.$emit('on-close', this.data)

      }
    },
  }
</script>