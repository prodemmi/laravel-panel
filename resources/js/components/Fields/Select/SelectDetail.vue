<template>

    <div v-bind="data.attributes" v-html="value"></div>

</template>

<script>
    export default {
        name: "select-detail",
        props: ['data', 'value']
    }
</script>