<template>

    <input type="number" v-bind="data.attributes" v-model="value"/>

</template>

<script>
    export default {
        name: "number-detail",
        props: ['data', 'value']
    }
</script>