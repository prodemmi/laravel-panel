<template>

    <div v-bind="data.attributes" v-html="value"></div>

</template>

<script>
    export default {
        props: ['data', 'value']
    }
</script>