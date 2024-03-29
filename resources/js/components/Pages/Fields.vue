<template>

    <div>

        <template v-for="(field, index) in fields">

            <template v-if="show(field)">

                <template v-if="field.forDesign">

                    <component :key="ukey()"
                            :data="field"
                            :is="field.component"
                            v-if="field.component === 'lava-tab'"
                            :env="env"
                            v-bind="field.attributes">

                        <div v-for="(tab, tabIndex) in field.tabs_data"
                            :tab="tabIndex"
                            :tabText="tab.title"
                            :key="ukey()">

                            <fields v-for="(tabField, fieldsIndex) in tab.fields"
                                    :key="ukey()"
                                    :data="data"
                                    :fields="tabField.fields && tabField.fields.length ? tabField.fields : [tabField]"
                                    :errors="errors"
                                    :env="env"
                                    @on-change="changed"/>

                        </div>

                    </component>

                    <component :key="ukey()"
                            :data="field"
                            :is="field.component"
                            
                            v-else
                            v-bind="field.attributes">

                        <template v-slot:header>{{ field.title }}</template>

                        <template v-slot:body>

                            <fields :data="data"
                                    :fields="field.fields"
                                    :errors="errors"
                                    :env="env"
                                    @on-change="changed"/>

                        </template>

                    </component>

                </template>

                <div :key="ukey()"
                    class="flex justify-start my-2 px-1 text-lg"
                    :class="[nextLine(field) ? 'flex-col' : '', ['switch', 'badge'].includes(field.component) ? 'items-center' : 'items-start']"
                    v-else>

                    <div class="inline-flex"
                        style="width: 140px">

                        <span>{{ field.name }}</span> <span v-if="isMobile">:</span>

                        <span v-if="field.rules.includes('required') && env !== 'detail'"
                            class="text-danger"> * </span>

                        <lava-tooltip v-if="field.help"
                                    :text="field.help"
                                    class="ltr:ml-2 rtl:mr-2">
                            <i class="ri-information-line cursor-help"></i>
                        </lava-tooltip>

                    </div>

                    <div class="flex flex-col justify-start w-full"
                        :class="[ isMobile ? '' : 'px-2', ['file', 'select', 'relation', 'search', 'badge'].includes(field.component) ? 'overflow-visible' : 'overflow-hidden']">

                        <div v-if="field.custom" v-html="resourceValue(data, field, false)"></div>

                        <component v-else
                                v-bind="field.attributes"
                                :is="field.component + component"
                                :data="field"
                                :value="resourceValue(data, field, env === 'edit')"
                                :env="env"
                                :first-search="true"
                                :dir="$store.getters.getConfig.rtl ? 'rtl': 'ltr'"
                                :resource="field.resource"
                                :openable="true"
                                @on-change="changed"/>

                        <form-error v-if="errors" :errors="errors[field.column]" :key="ukey()" />

                    </div>

                </div>

            </template>

        </template>

    </div>

</template>

<script>

export default {
    name    : 'fields',
    props   : [ 'data', 'fields', 'errors', 'env' ],
    computed: {
        component() {

            if ( this.env === 'create' ) {
                return '-edit'
            }

            return '-' + this.env
        }
    },
    methods : {
        show(field) {

            if ( field.showOnDetail && this.env === 'detail' )
                return true
            else if ( field.showOnIndex && this.env === 'index' )
                return true
            else if ( field.showOnForms && _.includes(['edit', 'create'], this.env) )
                return true
            else
                return false

        },
        changed(data) {
            this.$emit('on-change', data)
        },
        nextLine(field) {
            return this.isMobile && (
                this.resourceValue(this.data, field, this.env === 'edit')?.length > 12 || (
                    field.relation && !_.isEmpty(this.resourceValue(this.data, field, this.env === 'edit'))
                )
            )
        }
    }
}
</script>
