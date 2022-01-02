<template>

    <input type="checkbox" class="checkbox" v-model="model" @change="change">

</template>

<script>
    export default {
        name: "boolean-detail",
        props: ['data', 'value'],
        data() {

            return {
                model: this.value
            }

        },
        methods: {

            change(){

                this.$emit('on-change', this.model)

            }

        }
    }
</script>