<template>

    <div v-if="!data.asHtml">
        <img class="inline-block shadow h-7 w-7 ring-2 ring-white"
             v-bind="data.attributes"
             v-lazy="value" alt="">
    </div>
    <div v-else v-bind.prop="getValue"></div>

</template>

<script>

    import {asHtmlMixin} from '../../../mixins'

    export default {
        props: ['data', 'value'],
        mixins: [asHtmlMixin]
    }
</script>