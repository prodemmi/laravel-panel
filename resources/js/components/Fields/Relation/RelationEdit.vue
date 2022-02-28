<template>

    <lava-search 
        class="w-full"
        :multiple="data.multiple"
        :column="data.column"
        :value="getValue"
        :resource="findResource"
        :placeholder="getlabel"
        :firstSearch="env !== 'create'"
        @on-change="changed"
        uri="api/select-search"/>

</template>

<script>

    import ResourceTable from '../../Table/ResourceTable'

    export default {
        name: "relation-edit",
        components: {
            ResourceTable
        },
        props: {
            data: Object,
            value: null,
            env: String,
            resource: String
        },
        computed: {
            getValue() {

                return this.data.multiple ? _.map(this.value, this.findResource.modelKey) : this.value

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
                    relationType: this.data.relation,
                    relationModel: this.findResource.model,
                    relationPrimaryKey: this.findResource.primaryKey,
                    column,
                    value
                })

            }
        }
    }
</script>