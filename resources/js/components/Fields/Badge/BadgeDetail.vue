<template>

    <div v-bind="data.attributes"
         class="text-white text-center rounded py-0.5 px-3"
         :class="data.types[data.data[value]]"
         v-html="value">

    </div>
</template>

<script>
    export default {
        name: "badge-detail",
        props: ['data', 'value']
    }
</script>