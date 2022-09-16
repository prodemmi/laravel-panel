<template>
  <div class="relative" v-click-outside="hide">

    <lava-button @click="show" :color="color" class="resource-option__button">
      <slot name="title"></slot>
    </lava-button>

    <transition name="fade">

      <div v-if="visibility" class="resource-option__window" :class="{'right-0' : right}">
        <slot name="body"></slot>
      </div>

    </transition>

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
      if(this.visibility)
        this.visibility = false
    }
  },
};
</script>