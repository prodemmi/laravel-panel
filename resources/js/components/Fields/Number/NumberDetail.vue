<template>

    <div v-bind="data.attributes" v-html="value"></div>

</template>

<script>
    export default {
        name: "number-detail",
        props: ['data', 'value']
    }
</script>