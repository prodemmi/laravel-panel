<template>

    <div v-bind="data.attributes" v-html="value"></div>

</template>

<script>
    export default {
        name: "ckeditor-index",
        props: ['data', 'value']
    }
</script>