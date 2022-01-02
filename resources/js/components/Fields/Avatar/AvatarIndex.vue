<template>

    <div v-bind="data.attributes">
        <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white"
             v-lazy="value" alt="">
    </div>

</template>

<script>
    export default {
        name: "avatar-index",
        props: ['data', 'value']
    }
</script>