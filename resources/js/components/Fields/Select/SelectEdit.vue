<template>

    <select class="select" v-bind="data.attributes" v-model="model" @change="change">

        <option v-for="(option , index) in data.options" :value="index">{{ option }}</option>

    </select>

</template>

<script>
    export default {
        name: "select-detail",
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