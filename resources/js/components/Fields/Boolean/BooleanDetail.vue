<template>
  <div v-bind="data.attributes"
       class="rounded-full w-3 h-3"
       :class="value ? 'bg-green-600' : 'bg-red-600'"></div>
</template>

<script>

export default {
  props: ["data", "value"],
};
</script>