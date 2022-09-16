<template>
  <td
    v-show="show"
    :style="{
      width: resource.actions.length && index < data.headers.length - 1 ? 0 : null,
    }"
    class="resource-table__td"
  >
    <component
      v-if="getField(originalResource, header.column)"
      :is="
        getField(originalResource, header.column).component +
        (getField(originalResource, header.column).component === 'div'
          ? ''
          : '-index')
      "
      :data="getField(originalResource, header.column)"
      :value="resourceValue(row.rows, header)"
    >
      <div
        v-if="getField(originalResource, header.column).component === 'div'"
        v-html="resourceValue(row.rows, header)"
      ></div>
    </component>
  </td>
</template>

<script>
export default {
    props: ['show', 'resource', 'originalResource', 'data', 'header', 'row', 'index']
};
</script>

<style>
</style>