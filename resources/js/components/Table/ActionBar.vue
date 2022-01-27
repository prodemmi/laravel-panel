<template>
  <div
    class="bg-white rounded shadow-lg p-2 z-100"
    :class="
      block
        ? 'block'
        : 'fixed top-8 left-1/2 transform -translate-x-1/2 -translate-y-1/2'
    "
  >
    {{ block }}
    <div class="py-2 flex items-center justify-between w-full">
      Choose action

      <i class="ri-close-line cursor-pointer" @click="$emit('on-close')"></i>
    </div>

    <div class="flex items-center">
      <select class="select" style="width: 220px" v-model="selected_action">
        <template v-for="(action, index) in actions">
          <option v-if="!action.onlyOnTable" :value="action" :key="index">
            {{ action.name }}
          </option>
        </template>
      </select>

      <lava-button
        :disabled="!selected_action"
        @click="$emit('handle-action', selected_action, selected)"
        >Go
      </lava-button>
    </div>
  </div>
</template>

<script>
export default {
  props: ["actions", "selected", "block"],
  data() {
    return {
      selected_action: undefined,
    };
  }
};
</script>
