<template>

    <lava-tooltip :text="value">
        <div v-bind="data.attributes" 
             class="w-6 h-6 cursor-pointer rounded-md"
             @click="copyToClipboard(value)"
             :style="{ backgroundColor : value}"></div>
    </lava-tooltip>

</template>

<script>

    export default {
        props: ['data', 'value']
    }

</script>