<template>

  <option-base>

    <template v-slot:title><i class="ri-eye-line"></i></template>
    
    <template v-slot:body>
      <div
        v-for="(column, index) in headers"
        :key="index"
        class="flex flex-wrap justify-between w-full select-none"
      >
        <span style="min-width: 140px">{{ column.name }}</span>
        <input
          v-model="newShows[index].show"
          @change="$emit('on-change', newShows)"
          type="checkbox"
          class="checkbox"
          :disabled="(newShows[index].show && index <= 2)"
        />
      </div>
    </template>

  </option-base>
  
</template>

<script>

import OptionBase from "./OptionBase";

export default {
  components: {
    OptionBase
  },
  props: ['resource', 'headers', 'shows'],
  data() {
    return {
      newShows: this.shows,
      show_visibility: false
    };
  },
  methods: {
    show() {

      this.show_visibility = !this.show_visibility

    },
    hideVisibility() {
      this.show_visibility = false
    }
  },
};
</script>