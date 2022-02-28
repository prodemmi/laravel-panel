<template>

    <span v-bind="data.attributes" v-bind.prop="getValue"></span>


</template>

<script>

    import {asHtmlMixin} from '../../../mixins'

    export default {
        name: "number-index",
        props: ['data', 'value'],
        mixins: [asHtmlMixin]
    }
</script>