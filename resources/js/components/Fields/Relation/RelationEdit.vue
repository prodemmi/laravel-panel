<template>

    <lava-search 
        class="w-full"
        :multiple="data.multiple"
        :column="data.column"
        :value="getValue"
        :resource="findResource"
        :placeholder="getlabel"
        :firstSearch="env !== 'create'"
        :disabled="disabled"
        @on-change="changed"
        uri="api/select-search"/>

</template>

<script>

    import ResourceTable from '../../Table/ResourceTable'

    export default {
        components: {
            ResourceTable
        },
        props: {
            data: Object,
            value: null,
            env: String,
            resource: String,
            disabled: Boolean
        },
        computed: {
            getValue() {

                var key = this.findResource.modelKey

                return this.data.multiple ? _.map(this.value, key) : _.get(this.value, key, this.value)

            },
            getlabel(){

                var res = this.findResource

                var label = `Search in ${res.route} by ${res.primaryKey}`

                var subtitle = res?.subtitle
                if(!_.isEmpty(subtitle)){
                    label += ` and ${subtitle}`
                }

                return label

            },
            findResource() {

                return this.getResource(this.resource)

            }
        },
        methods: {
            changed(value, column) {

                this.$emit('on-change', {
                    relationType: this.data.relationType,
                    relationModel: this.findResource.model,
                    relationPrimaryKey: this.findResource.primaryKey,
                    update_column: this.data.update_column,
                    column,
                    value
                })

            }
        }
    }
</script>