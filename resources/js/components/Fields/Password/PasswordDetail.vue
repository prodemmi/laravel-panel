<template>

    <div v-bind="data.attributes" class="truncate" v-bind.prop="getValue"></div>

</template>

<script>

    import {asHtmlMixin} from '../../../mixins'

    export default {
        props: ['data', 'value'],
        mixins: [asHtmlMixin],
    }
</script>

<style scoped>

    .truncate{
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 400px;
    }

</style>