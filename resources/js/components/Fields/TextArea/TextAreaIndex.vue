<template>

    <div v-bind="data.attributes"
         v-bind.prop="getValue"
         :class="{ 'truncate' : data.more }"
         :style="{ 'width': data.more ? '300px' : null }">
    </div>

</template>

<script>

    import {asHtmlMixin} from '../../../mixins'

    export default {
        props: ['data', 'value'],
        mixins: [asHtmlMixin]
    }
</script>