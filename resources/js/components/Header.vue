<template>
  <div class="px-2">
    <h2 ><slot name="header"></slot></h2>
    <hr />
  </div>
</template>

<script>

export default {
    name: 'lava-header',
    props: ['data']
}

</script>
