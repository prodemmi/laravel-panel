<template>

    <input type="text" v-bind="data.attributes" v-model="value"/>

</template>

<script>
    export default {
        name: "badge-detail",
        props: ['data', 'value']
    }
</script>