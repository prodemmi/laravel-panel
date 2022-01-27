<template>

    <div>

        <template v-for="(field, index) in fields">

            <div v-if="field.showOnForm" :key="field.name" class="w-full">

                <div v-if="field.forDesign">

                    <component :data="field" :is="field.component">

                        <template v-slot:header>{{ field.title }}</template>

                        <template v-slot:body>

                                <fields data="data"
                                class="w-full"
                                        :class="{ 'flex justify-between': field.stack,
                                                  'flex-col' : field.stack && field.direction == 'column' }"
                                        :fields="field.fields"
                                        :errors="errors"
                                        env="create"
                                        @on-change="$emit('on-change')"
                                        :key="index"/>

                        </template>

                    </component>

                </div>

                <div class="flex justify-start p-2 text-lg" v-else>

                    <div style="width: 18vw">{{ field.name }}
                        <span v-if="field.rules.includes('required')" class="text-danger">*</span>
                    </div>

                    <div class="flex flex-col w-full">

                        <component
                            class="w-full"
                            v-bind="field.attributes"
                            :key="index"
                            v-if="field.showOnForm"
                            :is="field.component + component"
                            :data="field"
                            :value="resourceValue(data, field.column)"
                            env="create"
                            @on-change="changed"
                        />

                        <form-error v-if="errors[field.column]" :errors="errors[field.column]"></form-error>
                        
                    </div>

                </div>

            </div>

        </template>

    </div>
    
</template>

<script>

    export default {
        name: "fields",
        props: ['data', 'fields', 'errors', 'env'],
        computed:{
            component() {
                return this.env == 'create' ? '-edit' : '-detail'
            }
        },
        methods:{
            changed(value, column) {
                this.$emit('on-change', value, column)
            }
        }
    }
</script>

<style scoped>

</style>