<template>

    <div class="cursor-pointer">
        <img v-if="data && !data.asHtml"
             class="inline-block shadow h-14 w-14 object-cover ring-2 ring-white"
             v-bind="data.attributes"
             @click="previewImage(value)"
             v-lazy="value" alt="">
        <img v-else class="inline-block shadow h-14 w-14 object-cover ring-2 ring-white"
             v-bind="data.attributes"
             @click="previewImage(getValue)"
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