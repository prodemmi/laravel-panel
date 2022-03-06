<template>

    <textarea v-bind="data.attributes"
              v-model.trim="model"
              @input="onChange"
              class="text-area-input"
              style="min-height: 200px;"></textarea>

</template>

<script>

    import {FormMixin} from '../../../mixins'

    export default {
        props: ['data', 'value'],
        mixins: [FormMixin]
    }
</script>