<template>

    <span v-bind="data.attributes" v-html="value"></span>


</template>

<script>
    export default {
        name: "number-index",
        props: ['data', 'value']
    }
</script>