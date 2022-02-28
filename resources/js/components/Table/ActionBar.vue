<template>
    <div class="flex items-center">
      <select class="select" style="width: 220px" v-model="selected_action">
        <option :value="null" disabled>Select action</option>
        <template v-for="(action, index) in actions">
          <option v-if="!action.onlyOnTable" :value="action" :key="index">
            {{ action.name }} {{ _.isEmpty(selected) ? 'all' : '' }}
          </option>
        </template>
      </select>

      <lava-button
        @click="$emit('handle-action', selected_action, selected)"
        :disabled="!selected_action"
        :color="selected_action && selected_action.danger ? 'danger' : null"
        no-padding
        >Go
      </lava-button>

      <lava-button
        v-if="showClose"
        @click="$emit('on-close')"
        no-padding
        ><i class="ri-close-line cursor-pointer" ></i>
      </lava-button>
      
    </div>
</template>

<script>
export default {
  props: {
    actions :{
      type: [Object, Array],
      required: false,
      default: () => [],
    },
    selected :{
      type: Array,
      required: false,
      default: () => [],
    },
    showClose: {
      type: Boolean,
      default: true,
    }
  },
  data() {
    return {
      selected_action: null,
    };
  }
};
</script>
