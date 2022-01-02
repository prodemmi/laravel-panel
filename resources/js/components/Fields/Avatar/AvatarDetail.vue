<template>

    <div v-bind="data.attributes">
        <lava-tooltip text="Click to copy url">

            <img @click="copyToClipboard(value)"
                 class="inline-block h-8 w-8 rounded-full cursor-pointer ring-2 ring-gray-400"
                 v-lazy="value" alt="">

        </lava-tooltip>
    </div>

</template>

<script>
    export default {
        name: "avatar-detail",
        props: ['data', 'value']
    }
</script>