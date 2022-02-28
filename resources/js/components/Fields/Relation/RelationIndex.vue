<template>

    <div v-bind="data.attributes"
         class="rounded-full w-2 h-2"
         :class="value ? 'bg-green-600' : 'bg-red-600'"></div>

</template>

<script>
    export default {
        name: "relation-index",
        props: ['data', 'value']
    }
</script>