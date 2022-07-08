<template>

    <div class="flex-center">
        <input v-bind="data.attributes" class="color-input" type="color" v-model="model" @input="onChange"/>
        <input class="text-input" type="text" v-model="model" @input="onChange"/>
    </div>

</template>

<script>

    import {FormMixin} from '../../../mixins'

    export default {
        props: ['data', 'value'],
        mixins: [FormMixin]
    }
</script>