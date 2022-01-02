<template>

    <input type="text" v-bind="data.attributes" v-model.trim="model" @input="debounce"/>

</template>

<script>
    export default {
        name: "text-detail",
        props: ['data', 'value'],
        data() {

            return {
                model: this.value
            }

        },
        methods: {
            debounce: _.debounce(function () {

                this.$emit('on-change', this.model)

            }, 400)
        }
    }
</script>