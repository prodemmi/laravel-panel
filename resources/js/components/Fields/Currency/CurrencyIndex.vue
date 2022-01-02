<template>

    <div v-bind="data.attributes" v-html="value"></div>

</template>

<script>
    export default {
        name: "currency-index",
        props: ['data', 'value']
    }
</script>