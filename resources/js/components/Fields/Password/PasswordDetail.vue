<template>

    <div v-bind="data.attributes" class="truncate" v-html="value"></div>

</template>

<script>
    export default {
        name: "password-detail",
        props: ['data', 'value']
    }
</script>

<style scoped>

    .truncate{
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 400px;
    }

</style>