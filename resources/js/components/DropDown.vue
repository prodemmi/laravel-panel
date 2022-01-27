<template>
  <div class="dropdown">
    <lava-button v-click-outside="hideOptions" @click="showOptions = !showOptions">Actions</lava-button>

    <div v-show="showOptions">
      <ul v-if="!_.isEmpty(options)" class="dropdown--list">
        <li v-for="(option, index) in options" :key="index" @click="$emit('click', option)">
          <span class="dropdown--item__item">Action</span>
        </li>
      </ul>

      <slot v-else></slot>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    options: {
      type: Array,
      required: false,
      default: () => [],
    },
  },
  data() {
    return {
      showOptions: false,
    };
  },
  methods: {
    hideOptions() {
      this.showOptions = false;
    },
  },
};
</script>
