<template>

    <div>

        <template v-for="(field, index) in fields">

            <template v-if="field.showOnForm">

                <template v-if="field.forDesign">

                    <component :key="index" 
                               :data="field"
                               :is="field.component" 
                               v-bind="field.attributes"
                               :style="{ width: isMobile && field.component !== 'lava-card' ? '100%' : null }">

                        <template v-slot:header>{{ field.title }}</template>

                        <template v-slot:body>

                            <fields :data="data"
                                    :fields="field.fields"
                                    :class="{ 'flex justify-start items-stretch': (field.stack && !isMobile),
                                              'flex-col' : (field.stack && !isMobile) && field.direction === 'column' }"
                                    :errors="errors"
                                    :env="env"
                                    @on-change="changed"/>

                        </template>

                    </component>

                </template>

                <div :key="index" 
                     class="flex justify-start items-start my-2 px-1 text-lg" 
                     :class="nextLine(field) ? 'flex-col' : ''" 
                     v-else>

                    <div style="width: 140px">

                        <span>{{ field.name }}</span> <span v-if="isMobile">:</span>

                        <span v-if="field.rules.includes('required') && env !== 'detail'" class="text-danger">*</span>

                    </div>

                    <div class="flex flex-col justify-start w-full" 
                         :class="[ env === 'detail' ? 'overflow-hidden' : 'overflow-visible', isMobile ? '' : 'px-2']">
                        
                        <component v-bind="field.attributes"
                                   v-if="field.showOnForm"
                                   :is="field.component + component"
                                   :data="field"
                                   :value="resourceValue(data, field, env === 'edit')"
                                   :env="env"
                                   :dir="$store.getters.getConfig.rtl ? 'rtl': 'ltr'"
                                   :resource="field.resource"
                                   @on-change="changed"/>

                        <form-error v-if="errors[field.column]" :errors="errors[field.column]"/>

                    </div>

                </div>

            </template>

        </template>

    </div>

</template>

<script>

    export default {
        name: 'fields',
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
            changed(data) {
                console.log(data)
                this.$emit('on-change', data)
            },
            nextLine(field){
                return this.isMobile && (this.resourceValue(this.data, field, this.env === 'edit')?.length > 12 || (field.relation && !_.isEmpty(this.resourceValue(this.data, field, this.env === 'edit'))))
            }
        }
    }
</script>