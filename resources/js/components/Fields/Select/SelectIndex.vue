<template>

    <div v-bind="data.attributes" v-bind.prop="getValue"></div>

</template>

<script>

    import {asHtmlMixin} from '../../../mixins'
    
    export default {
        props: ['data', 'value'],
        mixins: [asHtmlMixin]
    }
</script>