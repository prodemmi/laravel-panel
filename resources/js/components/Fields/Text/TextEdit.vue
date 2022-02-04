<template>

    <input class="text-input" type="text" v-bind="data.attributes" v-model="model" @input="onChange"/>

</template>

<script>

    import {FormMixin} from '../../../mixins'

    export default {
        name: "text-edit",
        props: ['data', 'value'],
        mixins: [FormMixin]
    }
</script>