<template>

    <div v-bind="data.attributes"
         style="padding: 2px 4px 2px 4px"
         class="text-white text-center rounded"
         :class="data.types[data.data[value]]"
         v-html="value">

    </div>

</template>

<script>
    export default {
        name: "badge-index",
        props: ['data', 'value']
    }
</script>