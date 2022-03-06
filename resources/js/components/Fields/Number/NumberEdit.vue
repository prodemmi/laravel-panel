<template>

    <input class="number-input" type="number" v-bind="data.attributes" v-model.trim="model" @input="onChange"/>

</template>

<script>

    import {FormMixin} from '../../../mixins'

    export default {
        mixins: [FormMixin],
        props: ['data', 'value']
    }
</script>