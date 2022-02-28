<template>
  <div class="px-2">
    <h2 v-if="!!$slots.header"><slot name="header"></slot></h2>
    <hr />
    <div class="p-2">
      <slot name="body"></slot>
    </div>
  </div>
</template>

<script>

export default {
    name: 'lava-header',
    props: ['data']
}

</script>
