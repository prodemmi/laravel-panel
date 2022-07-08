<template>

  <action-bar :actions="actions" :selected="selected" :showClose="false" @handle-action="(action, rows) => $emit('handle-action', action, rows)"/>

</template>

<script>

import ActionBar from "../Table/ActionBar";

export default {
  components: {
    ActionBar
  },
  props: ['actions', 'selected']
};
</script>
