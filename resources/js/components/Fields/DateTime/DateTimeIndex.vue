<template>

    <div v-bind="data.attributes" v-html="value"></div>

</template>

<script>
    export default {
        name: "date-time-index",
        props: ['data', 'value']
    }
</script>