<template>

    <div v-bind="data.attributes">{{ value }}</div>

</template>

<script>
    export default {
        name: "select-index",
        props: ['data', 'value']
    }
</script>