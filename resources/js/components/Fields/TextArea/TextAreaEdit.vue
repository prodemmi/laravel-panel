<template>

    <textarea v-bind="data.attributes" v-model="value" class="resize-none" style="height: 200px;"></textarea>

</template>

<script>
    export default {
        name: "text-area-detail",
        props: ['data', 'value']
    }
</script>