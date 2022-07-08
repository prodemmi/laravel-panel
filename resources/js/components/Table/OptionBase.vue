<template>
  <div class="relative" v-click-outside="hide">

    <lava-button @click="show" :color="color" class="resource-option__button">
      <slot name="title"></slot>
    </lava-button>

    <div v-show="visibility" class="resource-option__window" :class="{'right-0' : right}">
      <slot name="body"></slot>
    </div>

  </div>
</template>

<script>
export default {
  props: {
    color: {
      default: 'primary'
    },
    right: {
      default: false
    }
  },
  data() {
    return {
      visibility: false
    };
  },
  methods: {
    show() {

      this.visibility = !this.visibility

    },
    hide() {
      this.visibility = false
    }
  },
};
</script>