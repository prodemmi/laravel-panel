<template>

    <input type="file" v-bind="data.attributes" @change=""/>

</template>

<script>
    export default {
        name: "avatar-detail",
        props: ['data', 'value']
    }
</script>