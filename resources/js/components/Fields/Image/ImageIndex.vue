<template>

    <div v-bind="data.attributes">
        <img class="inline-block shadow h-6 w-6 ring-2 ring-white"
             :class="{ 'rounded-md': data.rounded, 'rounded-full': data.fullRounded }"
             v-lazy="value" alt="">
    </div>

</template>

<script>
    export default {
        name: "image-index",
        props: ['data', 'value']
    }
</script>