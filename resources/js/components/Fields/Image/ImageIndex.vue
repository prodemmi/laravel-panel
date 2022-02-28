<template>

    <div v-if="!data.asHtml" v-bind="data.attributes">
        <img class="inline-block shadow h-6 w-6 ring-2 ring-white"
             :class="{ 'rounded-md': data.rounded, 'rounded-full': data.fullRounded }"
             v-lazy="value" alt="">
    </div>
    <div v-else v-bind.prop="getValue"></div>

</template>

<script>

    import {asHtmlMixin} from '../../../mixins'

    export default {
        name: "image-index",
        props: ['data', 'value'],
        mixins: [asHtmlMixin]
    }
</script>