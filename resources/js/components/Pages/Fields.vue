<template>

    <div>

        <template v-for="(field, index) in fields">

            <template v-if="field.showOnForm">

                <template v-if="field.forDesign">

                    <component :data="field" :is="field.component" v-bind="field.attributes">

                        <template v-slot:header>{{ field.title }}</template>

                        <template v-slot:body>

                            <fields :data="data"
                                    class="w-full"
                                    :class="{ 'flex justify-start items-stretch': field.stack,
                                              'flex-col' : field.stack && field.direction === 'column' }"
                                    :fields="field.fields"
                                    :errors="errors"
                                    :env="env"
                                    @on-change="changed"
                                    :key="index"/>

                        </template>

                    </component>

                </template>

                <div class="flex justify-start my-2 mx-1 text-lg" v-else>

                    <div style="width: 140px">

                        <span>{{ field.name }}</span>

                        <span v-if="field.rules.includes('required') && env !== 'detail'" class="text-danger">*</span>

                    </div>

                    <div class="flex flex-col justify-start w-full">

                        <component v-bind="field.attributes"
                                   :key="index"
                                   v-if="field.showOnForm"
                                   :is="field.component + component"
                                   :data="field"
                                   :value="resourceValue(data, field, env === 'edit')"
                                   :env="env"
                                   :resource="field.resource"
                                   @on-change="changed"/>

                        <form-error v-if="errors[field.column]" :errors="errors[field.column]"></form-error>

                    </div>

                </div>

            </template>

        </template>

    </div>

</template>

<script>

    export default {
        name: "fields",
        props: ['data', 'fields', 'errors', 'env'],
        computed: {
            component() {

                if (this.env === 'create') {
                    return '-edit'
                }

                return '-' + this.env
            }
        },
        methods: {
            changed(value, column) {
                this.$emit('on-change', value, column)
            }
        }
    }
</script>

<style scoped>

</style>