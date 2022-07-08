<template>

    <div v-if="data && !data.asHtml">
        <img class="inline-block shadow h-7 w-7 object-cover ring-2 ring-white"
             v-bind="data.attributes"
             v-lazy="value" alt="">
    </div>
    <div v-else >
        <img class="inline-block shadow h-7 w-7 object-cover ring-2 ring-white"
             v-bind="data.attributes"
             v-bind.prop="getValue" alt="">
    </div>

</template>

<script>

    import {asHtmlMixin} from '../../../mixins'

    export default {
        props: ['data', 'value'],
        mixins: [asHtmlMixin]
    }
</script>