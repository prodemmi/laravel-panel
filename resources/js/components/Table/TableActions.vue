<template>
  <td class="resource-table__td">
    <div class="flex items-center justify-start text-lg">

      <lava-tooltip v-for="action in actions" :text="disabled(action) ? action.name : null" :key="action.name" >
        <div
          v-html="icon(action.icon)"
          @click="$emit('handle-action', action, [row.rows])"
          style="height: 100%"
          :class="[{'pointer-events-none opacity-30': disabled(action)}, `text-${action.color}`]"
          class="cursor-pointer pr-1 hover:text-gray-400"
        ></div>
      </lava-tooltip>
    </div>
  </td>
</template>

<script>
export default {
  props: {
    actions: {
      type: [Object, Array],
      default: () => []
    },
    row: {
      type: Object,
      default: () => []
    }
  },
  methods: {
    disabled(action){
      return !_.find(this.row.actions, { name: action.class }).show
    }
  },
};
</script>
